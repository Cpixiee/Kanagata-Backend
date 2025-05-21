<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\Logsheet;
use App\Models\TutorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TutorController extends Controller
{
    public function index()
    {
        $tutors = Tutor::where('is_active', true)->get();
        return view('tutor', compact('tutors'));
    }

    public function edit(Tutor $tutor)
    {
        return response()->json([
            'id' => $tutor->id,
            'name' => $tutor->name,
            'email' => $tutor->email,
            'phone' => $tutor->phone,
            'address' => $tutor->address,
            'birth_year' => $tutor->birth_year,
            'description' => $tutor->description,
            'photo_url' => $tutor->photo_url,
            'is_active' => $tutor->is_active
        ]);
    }

    public function update(Request $request, Tutor $tutor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'birth_year' => 'required|integer|min:1950|max:' . date('Y'),
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $data = $request->except('photo');

            if ($request->hasFile('photo')) {
                // Hapus foto lama jika ada
                if ($tutor->photo && $tutor->photo !== 'default.png') {
                    $oldPhotoPath = public_path('img/tutor-img/' . $tutor->photo);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }

                $photo = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                
                // Pastikan direktori ada
                $uploadPath = public_path('img/tutor-img');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                // Pindahkan file
                $photo->move($uploadPath, $filename);
                $data['photo'] = $filename;
            }

            $tutor->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data tutor berhasil diperbarui',
                'photo_url' => $tutor->photo_url // Tambahkan URL foto yang baru
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data tutor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableDates(Request $request, Tutor $tutor)
    {
        $request->validate([
            'logsheet_id' => 'required|exists:logsheets,id',
            'month' => 'required|date_format:Y-m'
        ]);

        $logsheet = Logsheet::findOrFail($request->logsheet_id);
        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Ambil jadwal yang sudah ada
        $existingSchedules = $tutor->schedules()
            ->whereBetween('schedule_date', [$startDate, $endDate])
            ->get()
            ->pluck('schedule_date')
            ->map(function($date) {
                return $date->format('Y-m-d');
            })
            ->toArray();

        // Hitung sesi yang tersedia
        $totalSessions = $logsheet->seq;
        $usedSessions = $tutor->schedules()
            ->where('logsheet_id', $logsheet->id)
            ->count();
        $remainingSessions = $totalSessions - $usedSessions;

        return response()->json([
            'existing_schedules' => $existingSchedules,
            'remaining_sessions' => $remainingSessions,
            'total_sessions' => $totalSessions
        ]);
    }

    public function getUnscheduledLogsheets(Tutor $tutor)
    {
        $logsheets = Logsheet::where('tutor', $tutor->name)
            ->get()
            ->map(function($logsheet) use ($tutor) {
                // Hitung sesi yang sudah digunakan
                $usedSessions = TutorSchedule::where('tutor_id', $tutor->id)
                    ->where('logsheet_id', $logsheet->id)
                    ->count();
                
                // Hitung sesi yang tersisa
                $remainingSessions = $logsheet->seq - $usedSessions;
                
                // Buat array nomor sesi yang tersedia
                $availableSessions = [];
                if ($remainingSessions > 0) {
                    $usedSessionNumbers = TutorSchedule::where('tutor_id', $tutor->id)
                        ->where('logsheet_id', $logsheet->id)
                        ->pluck('session_number')
                        ->toArray();
                    
                    for ($i = 1; $i <= $logsheet->seq; $i++) {
                        if (!in_array($i, $usedSessionNumbers)) {
                            $availableSessions[] = $i;
                        }
                    }
                }

                return [
                    'id' => $logsheet->id,
                    'activity' => $logsheet->activity,
                    'customer' => $logsheet->customer,
                    'total_sessions' => $logsheet->seq,
                    'remaining_sessions' => $remainingSessions,
                    'available_sessions' => $availableSessions
                ];
            })
            ->filter(function($logsheet) {
                return $logsheet['remaining_sessions'] > 0;
            })
            ->values();

        return response()->json($logsheets);
    }
} 