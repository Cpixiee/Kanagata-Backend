<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\Logsheet;
use App\Models\TutorSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TutorScheduleController extends Controller
{
    public function index(Tutor $tutor)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $schedules = $tutor->getSchedulesByDateRange($startDate, $endDate);
        $unscheduledLogsheets = $tutor->getUnscheduledLogsheets();

        return response()->json([
            'schedules' => $schedules,
            'unscheduledLogsheets' => $unscheduledLogsheets
        ]);
    }

    public function store(Request $request, Tutor $tutor)
    {
        try {
            $validated = $request->validate([
                'logsheet_id' => 'required|exists:logsheets,id',
                'schedule_date' => 'required|date|after_or_equal:today',
                'session_number' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ], [
                'logsheet_id.required' => 'Logsheet harus dipilih',
                'logsheet_id.exists' => 'Logsheet tidak valid',
                'schedule_date.required' => 'Tanggal harus diisi',
                'schedule_date.date' => 'Format tanggal tidak valid',
                'schedule_date.after_or_equal' => 'Tanggal tidak boleh kurang dari hari ini',
                'session_number.required' => 'Nomor sesi harus dipilih',
                'session_number.integer' => 'Nomor sesi harus berupa angka',
                'session_number.min' => 'Nomor sesi minimal 1'
            ]);

            // Verify if the session number is available
            $logsheet = Logsheet::findOrFail($request->logsheet_id);
            
            // Verifikasi apakah tutor adalah tutor yang ditugaskan untuk logsheet ini
            if ($logsheet->tutor !== $tutor->name) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tutor tidak sesuai dengan logsheet yang dipilih'
                ], 422);
            }

            // Verifikasi apakah nomor sesi valid
            if ($request->session_number > $logsheet->seq) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor sesi melebihi total sesi yang tersedia'
                ], 422);
            }

            // Verifikasi apakah sesi sudah digunakan
            $existingSession = TutorSchedule::where('tutor_id', $tutor->id)
                ->where('logsheet_id', $logsheet->id)
                ->where('session_number', $request->session_number)
                ->first();

            if ($existingSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi ini sudah dijadwalkan'
                ], 422);
            }

            // Check if the tutor already has a schedule on the same date
            $existingSchedule = $tutor->schedules()
                ->whereDate('schedule_date', $request->schedule_date)
                ->first();

            if ($existingSchedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tutor sudah memiliki jadwal pada tanggal ini'
                ], 422);
            }

            $schedule = TutorSchedule::create([
                'tutor_id' => $tutor->id,
                'logsheet_id' => $request->logsheet_id,
                'schedule_date' => $request->schedule_date,
                'session_number' => $request->session_number,
                'notes' => $request->notes,
                'status' => 'scheduled'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil ditambahkan',
                'schedule' => $schedule->load('logsheet')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Tutor $tutor, TutorSchedule $schedule)
    {
        $request->validate([
            'schedule_date' => 'required|date|after_or_equal:today',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        // Check if the new date conflicts with existing schedules
        if ($request->schedule_date != $schedule->schedule_date) {
            $existingSchedule = $tutor->schedules()
                ->where('id', '!=', $schedule->id)
                ->whereDate('schedule_date', $request->schedule_date)
                ->first();

            if ($existingSchedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tutor already has a schedule on this date'
                ], 422);
            }
        }

        $schedule->update([
            'schedule_date' => $request->schedule_date,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'schedule' => $schedule->load('logsheet')
        ]);
    }

    public function destroy(Tutor $tutor, TutorSchedule $schedule)
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function getAvailableSessions(Request $request, Tutor $tutor)
    {
        $request->validate([
            'logsheet_id' => 'required|exists:logsheets,id'
        ]);

        $logsheet = Logsheet::findOrFail($request->logsheet_id);
        $availableSessions = TutorSchedule::getAvailableSessionNumbers(
            $tutor->id,
            $logsheet->id,
            $logsheet->seq
        );

        return response()->json([
            'available_sessions' => $availableSessions
        ]);
    }

    public function getAvailableDates(Request $request, Tutor $tutor)
    {
        $request->validate([
            'logsheet_id' => 'required|exists:logsheets,id',
            'month' => 'required|date_format:Y-m'
        ]);

        $logsheet = Logsheet::findOrFail($request->logsheet_id);
        
        // Hitung total sesi yang tersedia
        $totalSessions = $logsheet->seq;
        
        // Hitung sesi yang sudah digunakan
        $usedSessions = TutorSchedule::where('tutor_id', $tutor->id)
            ->where('logsheet_id', $logsheet->id)
            ->count();
        
        // Hitung sesi yang tersisa
        $remainingSessions = $totalSessions - $usedSessions;

        // Ambil tanggal yang sudah dijadwalkan
        $startDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $existingSchedules = TutorSchedule::where('tutor_id', $tutor->id)
            ->where('logsheet_id', $logsheet->id)
            ->whereBetween('schedule_date', [$startDate, $endDate])
            ->pluck('schedule_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        return response()->json([
            'remaining_sessions' => $remainingSessions,
            'total_sessions' => $totalSessions,
            'existing_schedules' => $existingSchedules
        ]);
    }

    public function getUnscheduledLogsheets(Tutor $tutor)
    {
        // Get only logsheets assigned to this tutor
        $logsheets = Logsheet::where('tutor', $tutor->name)
            ->get()
            ->map(function($logsheet) use ($tutor) {
                // Calculate used sessions
                $usedSessions = TutorSchedule::where('tutor_id', $tutor->id)
                    ->where('logsheet_id', $logsheet->id)
                    ->count();
                
                // Calculate remaining sessions
                $remainingSessions = $logsheet->seq - $usedSessions;
                
                // Get available session numbers
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