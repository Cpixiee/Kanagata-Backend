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
            ->with('logsheet') // Eager load logsheet
            ->orderBy('schedule_date')
            ->get()
            ->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'logsheet_id' => $schedule->logsheet_id,
                    'logsheet_activity' => $schedule->logsheet->activity ?? '',
                    'session_number' => $schedule->session_number,
                    'schedule_date' => $schedule->schedule_date,
                    'status' => $schedule->status,
                    'notes' => $schedule->notes
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
    public function getAvailableSessions(Request $request, Tutor $tutor)
    {
        try {
            // Get the logsheet
            $logsheet = \App\Models\Logsheet::findOrFail($request->logsheet_id);
            
            // Get used session numbers for this logsheet
            $usedSessions = $tutor->schedules()
                ->where('logsheet_id', $request->logsheet_id)
                ->pluck('session_number')
                ->toArray();
            
            // Next available session is the next number in sequence
            $nextSession = count($usedSessions) + 1;
            
            // Only return next session if it's within the total sequences
            $availableSessions = [];
            if ($nextSession <= $logsheet->seq) {
                $availableSessions[] = $nextSession;
            }
            
            return response()->json([
                'success' => true,
                'total_sequences' => $logsheet->seq,
                'available_sessions' => $availableSessions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting available sessions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available dates for a tutor.
     */
    public function getAvailableDates(Request $request, Tutor $tutor)
    {
        try {
            // Get the logsheet
            $logsheet = \App\Models\Logsheet::findOrFail($request->logsheet_id);
            
            // Get total sequences from logsheet seq
            $totalSequences = $logsheet->seq;
            
            // Get existing schedules for this logsheet
            $existingSchedules = $tutor->schedules()
                ->where('logsheet_id', $request->logsheet_id)
                ->count();
            
            // Get next sequence number
            $nextSequence = $existingSchedules + 1;
            
            // Get all scheduled dates for this tutor (across all logsheets)
            $allScheduledDates = $tutor->schedules()
                ->where('schedule_date', '>=', now()->toDateString())
                ->pluck('schedule_date')
                ->sort()
                ->values();

            return response()->json([
                'success' => true,
                'total_sequences' => $totalSequences,
                'next_sequence' => $nextSequence,
                'remaining_sessions' => $totalSequences - $existingSchedules,
                'existing_schedules' => $allScheduledDates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting available dates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get logsheets for a tutor.
     */
    public function getLogsheets(Tutor $tutor)
    {
        try {
            $logsheets = \App\Models\Logsheet::where('tutor_id', $tutor->id)
                ->with(['schedules' => function($query) use ($tutor) {
                    $query->where('tutor_id', $tutor->id);
                }])
                ->get()
                ->map(function($logsheet) {
                    $scheduledSessions = $logsheet->schedules->count();
                    $availableSessions = [];
                    
                    // If we haven't scheduled all sessions, get the next session number
                    if ($scheduledSessions < $logsheet->seq) {
                        $availableSessions[] = $scheduledSessions + 1;
                    }
                    
                    return [
                        'id' => $logsheet->id,
                        'activity' => $logsheet->activity,
                        'customer' => $logsheet->customer,
                        'total_sequences' => $logsheet->seq,
                        'scheduled_sessions' => $scheduledSessions,
                        'available_sessions' => $availableSessions
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $logsheets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting logsheets: ' . $e->getMessage()
            ], 500);
        }
    }
}
