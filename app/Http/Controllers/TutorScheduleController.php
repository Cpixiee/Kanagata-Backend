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
    public function index(Tutor $tutor)
    {
        $schedules = $tutor->schedules()->orderBy('date')->get();
        
        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    /**
     * Store a newly created tutor schedule.
     */
    public function store(Request $request, Tutor $tutor)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        $schedule = $tutor->schedules()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Schedule created successfully',
            'data' => $schedule
        ], 201);
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
