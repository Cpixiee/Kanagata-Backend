<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tutor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'photo',
        'address',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return asset('img/tutor-img/default.png');
        }
        return asset('img/tutor-img/' . $this->photo);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TutorSchedule::class);
    }

    public function logsheets(): HasMany
    {
        return $this->hasMany(Logsheet::class, 'tutor', 'name');
    }

    public function getSchedulesByDateRange($startDate, $endDate)
    {
        return $this->schedules()
            ->whereBetween('schedule_date', [$startDate, $endDate])
            ->with('logsheet')
            ->get()
            ->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => "Sesi {$schedule->session_number} - {$schedule->logsheet->activity}",
                    'start' => $schedule->schedule_date->format('Y-m-d'),
                    'className' => "status-{$schedule->status}",
                    'extendedProps' => [
                        'logsheetId' => $schedule->logsheet_id,
                        'sessionNumber' => $schedule->session_number,
                        'status' => $schedule->status,
                        'notes' => $schedule->notes
                    ]
                ];
            });
    }

    public function getUnscheduledLogsheets()
    {
        return $this->logsheets()
            ->whereRaw('seq > (
                SELECT COUNT(*) 
                FROM tutor_schedules 
                WHERE tutor_schedules.logsheet_id = logsheets.id
            )')
            ->select('id', 'activity', 'customer', 'seq')
            ->get()
            ->map(function($logsheet) {
                $usedSessions = TutorSchedule::where('logsheet_id', $logsheet->id)
                    ->pluck('session_number')
                    ->toArray();
                
                $availableSessions = [];
                for ($i = 1; $i <= $logsheet->seq; $i++) {
                    if (!in_array($i, $usedSessions)) {
                        $availableSessions[] = $i;
                    }
                }
                
                $logsheet->available_sessions = $availableSessions;
                return $logsheet;
            });
    }
} 