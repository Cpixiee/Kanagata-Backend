<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorSchedule extends Model
{
    protected $fillable = [
        'tutor_id',
        'logsheet_id',
        'schedule_date',
        'session_number',
        'status',
        'notes'
    ];

    protected $casts = [
        'schedule_date' => 'date'
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function logsheet(): BelongsTo
    {
        return $this->belongsTo(Logsheet::class);
    }

    // Helper method untuk mendapatkan jadwal yang tersedia untuk tutor dan logsheet tertentu
    public static function getAvailableSessionNumbers($tutorId, $logsheetId, $totalSessions)
    {
        $usedSessions = self::where('tutor_id', $tutorId)
            ->where('logsheet_id', $logsheetId)
            ->pluck('session_number')
            ->toArray();

        $availableSessions = [];
        for ($i = 1; $i <= $totalSessions; $i++) {
            if (!in_array($i, $usedSessions)) {
                $availableSessions[] = $i;
            }
        }

        return $availableSessions;
    }
} 