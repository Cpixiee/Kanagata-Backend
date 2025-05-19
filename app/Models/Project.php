<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'coa',
        'customer',
        'activity',
        'prodi',
        'grade',
        'quantity_1',
        'rate_1',
        'quantity_2',
        'rate_2',
        'gt_rev',
        'gt_cost',
        'gt_margin',
        'todo',
        'ar_ap',
        'sum_ar',
        'ar_paid',
        'ar_os',
        'sum_ap',
        'ap_paid',
        'ap_os'
    ];

    protected $appends = [
        'total_revenue',
        'total_cost',
        'total_margin',
        'ar_paid',
        'ar_os',
        'ap_paid',
        'ap_os'
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            // Calculate GT REV
            $project->gt_rev = $project->quantity_1 * $project->rate_1;
            
            // Calculate GT COST
            $project->gt_cost = $project->quantity_2 * $project->rate_2;
            
            // Calculate GT MARGIN
            $project->gt_margin = $project->gt_rev - $project->gt_cost;
            
            // Initialize AR/AP values
            $project->sum_ar = 0;
            $project->ar_paid = 0;
            $project->ar_os = 0;
            $project->sum_ap = 0;
            $project->ap_paid = 0;
            $project->ap_os = 0;
        });

        static::updating(function ($project) {
            // Recalculate GT REV
            $project->gt_rev = $project->quantity_1 * $project->rate_1;
            
            // Recalculate GT COST
            $project->gt_cost = $project->quantity_2 * $project->rate_2;
            
            // Recalculate GT MARGIN
            $project->gt_margin = $project->gt_rev - $project->gt_cost;
        });
    }

    public function logsheets(): HasMany
    {
        return $this->hasMany(Logsheet::class);
    }

    // Getter untuk total revenue
    public function getTotalRevenueAttribute()
    {
        return $this->logsheets->sum('revenue');
    }

    // Getter untuk total cost
    public function getTotalCostAttribute()
    {
        return $this->logsheets->sum('cost');
    }

    // Getter untuk total margin
    public function getTotalMarginAttribute()
    {
        return $this->total_revenue - $this->total_cost;
    }

    // Getter untuk AR Paid
    public function getArPaidAttribute()
    {
        return $this->logsheets->sum(function ($logsheet) {
            return $logsheet->ar_amount['paid'];
        });
    }

    // Getter untuk AR Outstanding
    public function getArOsAttribute()
    {
        return $this->logsheets->sum(function ($logsheet) {
            return $logsheet->ar_amount['os'];
        });
    }

    // Getter untuk AP Paid
    public function getApPaidAttribute()
    {
        return $this->logsheets->sum(function ($logsheet) {
            return $logsheet->ap_amount['paid'];
        });
    }

    // Getter untuk AP Outstanding
    public function getApOsAttribute()
    {
        return $this->logsheets->sum(function ($logsheet) {
            return $logsheet->ap_amount['os'];
        });
    }

    public function getSumArAttribute()
    {
        return $this->logsheets->sum(function($logsheet) {
            return $logsheet->rate_1 * $logsheet->seq;
        });
    }

    public function getSumApAttribute()
    {
        return $this->logsheets->sum(function($logsheet) {
            return $logsheet->rate_2 * $logsheet->seq;
        });
    }

    public function updateFinancials()
    {
        $this->load('logsheets'); // Memastikan relasi ter-load
        $this->touch(); // Memaksa update timestamps
    }

    public static function getCustomerOptions()
    {
        return [
            'SMKN 20',
            'SMKN 59',
            'SMKN 43',
            'SMKN 70',
            'SMKN 22',
            'SMKN 18',
            'SMKN 37'
        ];
    }

    public static function getActivityOptions()
    {
        return [
            'INKUBASI',
            'WORKSHOP',
            'Kelas SDNR',
            'Seminar',
            'Sinkronisasi'
        ];
    }

    public static function getProdiOptions()
    {
        return [
            'BD',
            'RPL',
            'MM',
            'TKJ',
            'GNRL'
        ];
    }

    public static function getGradeOptions()
    {
        return [
            'kelas 10',
            'kelas 11',
            'kelas 12',
            'guru'
        ];
    }
}