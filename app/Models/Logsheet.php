<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logsheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'coa',
        'customer',
        'activity',
        'prodi',
        'grade',
        'seq',
        'quantity_1',
        'rate_1',
        'ar_status',
        'tutor',
        'tutor_id',
        'quantity_2',
        'rate_2',
        'ap_status'
    ];

    protected $casts = [
        'quantity_1' => 'integer',
        'rate_1' => 'decimal:2',
        'revenue' => 'decimal:2',
        'quantity_2' => 'integer',
        'rate_2' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    protected $appends = ['ar_amount', 'ap_amount'];

    // Constants for enum values
    public static function getCustomerOptions()
    {
        return [
            'SMKN 20', 'SMKN 59', 'SMKN 43', 'SMKN 70', 'SMKN 22', 'SMKN 18', 'SMKN 37'
        ];
    }

    public static function getActivityOptions()
    {
        return [
            'INKUBASI', 'WORKSHOP', 'Kelas SDNR'
        ];
    }

    public static function getProdiOptions()
    {
        return [
            'BD', 'RPL', 'MM', 'TKJ', 'GNRL'
        ];
    }

    public static function getGradeOptions()
    {
        return [
            'kelas 10', 'kelas 11', 'kelas 12', 'guru'
        ];
    }

    public static function getTutorOptions()
    {
        return [
            'Muhammad Andar Rahman',
            'wit urrohman',
            'Rizal Ramadhanu',
            'danu muhammad',
            'michale sudarsono',
            'brilian krisna mora',
            'ageng prasetyo'
        ];
    }

    public static function getStatusOptions()
    {
        return ['PAID', 'LISTING', 'PENDING'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    // Calculate AR amount based on status
    public function getArAmountAttribute()
    {
        $total = $this->quantity_1 * $this->rate_1;
        
        // If status is Listing, amount goes to AR OS
        // If status is Paid, amount goes to AR Paid
        switch ($this->ar_status) {
            case 'Listing':
                return [
                    'paid' => 0,
                    'os' => $total
                ];
            case 'Paid':
                return [
                    'paid' => $total,
                    'os' => 0
                ];
            default: // Pending or other statuses
                return [
                    'paid' => 0,
                    'os' => 0
                ];
        }
    }

    // Calculate AP amount based on status
    public function getApAmountAttribute()
    {
        $total = $this->quantity_2 * $this->rate_2;
        
        // If status is Listing, amount goes to AP OS
        // If status is Paid, amount goes to AP Paid
        switch ($this->ap_status) {
            case 'Listing':
                return [
                    'paid' => 0,
                    'os' => $total
                ];
            case 'Paid':
                return [
                    'paid' => $total,
                    'os' => 0
                ];
            default: // Pending or other statuses
                return [
                    'paid' => 0,
                    'os' => 0
                ];
        }
    }

    // Get total AR Paid for a set of logsheets
    public static function getTotalArPaid($logsheets)
    {
        return $logsheets->sum(function ($logsheet) {
            return $logsheet->ar_amount['paid'];
        });
    }

    // Get total AR OS for a set of logsheets
    public static function getTotalArOs($logsheets)
    {
        return $logsheets->sum(function ($logsheet) {
            return $logsheet->ar_amount['os'];
        });
    }

    // Get total AP Paid for a set of logsheets
    public static function getTotalApPaid($logsheets)
    {
        return $logsheets->sum(function ($logsheet) {
            return $logsheet->ap_amount['paid'];
        });
    }

    // Get total AP OS for a set of logsheets
    public static function getTotalApOs($logsheets)
    {
        return $logsheets->sum(function ($logsheet) {
            return $logsheet->ap_amount['os'];
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($logsheet) {
            // Update project financials after save
            if ($logsheet->project) {
            $logsheet->project->updateFinancials();
            }
        });

        static::deleted(function ($logsheet) {
            // Update project financials after delete
            if ($logsheet->project) {
                $logsheet->project->updateFinancials();
            }
        });
    }

    // Accessor to format revenue
    public function getRevenueAttribute($value)
    {
        return round($value, 2);
    }

    // Accessor to format cost
    public function getCostAttribute($value)
    {
        return round($value, 2);
    }
} 