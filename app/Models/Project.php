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
        'sum_ar',
        'ar_paid',
        'ar_os',
        'sum_ap',
        'ap_paid',
        'ap_os',
        'todo',
        'ar_ap'
    ];

    protected $appends = [
        'total_revenue',
        'total_cost',
        'total_margin'
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

            // Calculate todo and ar_ap
            $project->todo = $project->gt_rev - ($project->sum_ar ?? 0);
            $project->ar_ap = ($project->sum_ar ?? 0) - ($project->sum_ap ?? 0);
        });

        static::updating(function ($project) {
            // Recalculate GT REV
            $project->gt_rev = $project->quantity_1 * $project->rate_1;
            
            // Recalculate GT COST
            $project->gt_cost = $project->quantity_2 * $project->rate_2;
            
            // Recalculate GT MARGIN
            $project->gt_margin = $project->gt_rev - $project->gt_cost;

            // Recalculate todo and ar_ap
            $project->todo = $project->gt_rev - ($project->sum_ar ?? 0);
            $project->ar_ap = ($project->sum_ar ?? 0) - ($project->sum_ap ?? 0);
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
        // Hitung total AR dari semua logsheet
        return $this->logsheets->sum(function($logsheet) {
            return $logsheet->rate_1 * $logsheet->quantity_1;
        });
    }

    public function getSumApAttribute()
    {
        return $this->logsheets->sum(function($logsheet) {
            return $logsheet->rate_2 * $logsheet->quantity_2;
        });
    }

    public function updateFinancials()
    {
        $this->load('logsheets');
        $this->touch();
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

    public function getLatestSequenceAttribute()
    {
        return $this->logsheets()->max('seq') ?? 0;
    }

    // Getter untuk ToDo (GT Revenue - Sum AR)
    public function getTodoAttribute()
    {
        return $this->gt_rev - ($this->sum_ar ?? 0);
    }

    // Getter untuk AR-AP (Sum AR - Sum AP)
    public function getArApAttribute()
    {
        return ($this->sum_ar ?? 0) - ($this->sum_ap ?? 0);
    }
}