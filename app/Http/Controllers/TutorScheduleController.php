<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutor;
use App\Models\TutorSchedule;
use Illuminate\Http\JsonResponse;

class TutorScheduleController extends Controller
{
    /**
     * Display a listing of tutor schedules.
     */
    public function index(Tutor $tutor, Request $request)
    {
        $schedules = $tutor->schedules()
            ->when($request->has('logsheet_id'), function($query) use ($request) {
                return $query->where('logsheet_id', $request->logsheet_id);
            })
            ->orderBy('schedule_date')
            ->get()
            ->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => "Sesi {$schedule->session_number}",
                    'start' => $schedule->schedule_date,
                    'className' => "status-{$schedule->status}",
                    'extendedProps' => [
                        'status' => $schedule->status,
                        'notes' => $schedule->notes,
                        'session_number' => $schedule->session_number
                    ]
                ];
            });
        
        return response()->json([
            'success' => true,
            'schedules' => $schedules
        ]);
    }

    /**
     * Store a newly created tutor schedule.
     */
    public function store(Request $request, Tutor $tutor)
    {
        $validated = $request->validate([
            'logsheet_id' => 'required|exists:logsheets,id',
            'session_number' => 'required|integer|min:1',
            'schedule_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string'
        ]);

        // Cek apakah sesi sudah digunakan
        $existingSchedule = $tutor->schedules()
            ->where('logsheet_id', $validated['logsheet_id'])
            ->where('session_number', $validated['session_number'])
            ->first();

        if ($existingSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor sesi ini sudah digunakan'
            ], 422);
        }

        // Cek apakah tanggal sudah ada jadwal
        $existingDateSchedule = $tutor->schedules()
            ->where('schedule_date', $validated['schedule_date'])
            ->first();

        if ($existingDateSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal ini sudah memiliki jadwal'
            ], 422);
        }

        try {
            $schedule = $tutor->schedules()->create([
                'logsheet_id' => $validated['logsheet_id'],
                'session_number' => $validated['session_number'],
                'schedule_date' => $validated['schedule_date'],
                'notes' => $validated['notes'],
                'status' => 'scheduled'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dibuat',
                'data' => $schedule
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified tutor schedule.
     */
    public function update(Request $request, Tutor $tutor, TutorSchedule $schedule)
    {
        // Ensure the schedule belongs to this tutor
        if ($schedule->tutor_id !== $tutor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found for this tutor'
            ], 404);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'data' => $schedule
        ]);
    }

    /**
     * Remove the specified tutor schedule.
     */
    public function destroy(Tutor $tutor, TutorSchedule $schedule)
    {
        // Ensure the schedule belongs to this tutor
        if ($schedule->tutor_id !== $tutor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found for this tutor'
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    /**
     * Get available sessions for a tutor.
     */
    public function getAvailableSessions(Tutor $tutor)
    {
        $availableSessions = $tutor->schedules()
            ->where('is_available', true)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $availableSessions
        ]);
    }

    /**
     * Get available dates for a tutor.
     */
    public function getAvailableDates(Tutor $tutor)
    {
        $availableDates = $tutor->schedules()
            ->where('is_available', true)
            ->where('date', '>=', now()->toDateString())
            ->distinct()
            ->pluck('date')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $availableDates
        ]);
    }
}
