<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'budget',
        'sub_budget',
        'recipient',
        'date',
        'month',
        'status',
        'debit',
        'credit',
        'description'
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2'
    ];

    // Konstanta untuk kategori
    const CATEGORY_COST_OPERATION = 'COST OPERATION';
    const CATEGORY_REVENUE_PROJECT = 'REVENUE PROJECT';
    const CATEGORY_COST_PROJECT = 'COST PROJECT';
    const CATEGORY_KAS_MARGIN = 'KAS MARGIN';

    // Konstanta untuk status
    const STATUS_LISTING = 'LISTING';
    const STATUS_PAID = 'PAID';

    // Relasi dengan Project (Budget)
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'budget_id')
            ->withDefault(function ($budget, $ledger) {
                // If budget_id is a COA string, create a virtual budget object
                if (preg_match('/^PL\.\d{2}-\d{4}$/', $ledger->budget_id)) {
                    $budget->coa = $ledger->budget_id;
                }
            });
    }

    // Get formatted budget/COA
    public function getBudgetCoaAttribute()
    {
        if (is_object($this->budget)) {
            return $this->budget->coa;
        }
        return $this->budget_id;
    }

    /**
     * Get the available category options.
     *
     * @return array
     */
    public static function getCategoryOptions(): array
    {
        return [
            self::CATEGORY_COST_OPERATION,
            self::CATEGORY_REVENUE_PROJECT,
            self::CATEGORY_COST_PROJECT,
            self::CATEGORY_KAS_MARGIN
        ];
    }

    /**
     * Get the available status options.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_LISTING,
            self::STATUS_PAID
        ];
    }

    /**
     * Get the available sub budget options.
     *
     * @return array
     */
    public static function getSubBudgetOptions(): array
    {
        return [
            'OPERASIONAL',
            'GAJI',
            'TRANSPORT',
            'AKOMODASI',
            'KONSUMSI',
            'ATK',
            'LAIN-LAIN'
        ];
    }

    /**
     * Get the available recipient options.
     *
     * @return array
     */
    public static function getRecipientOptions(): array
    {
        return [
            'MUHAMMAD ANDAR RAHMAN',
            'DANU STEVEN',
            'MICHALE SUDARSONO',
            'WIT URROHMAN',
            'AGENG PRASETYO',
            'KANAGATA',
            'LAIN-LAIN'
        ];
    }

    // Generate COA options for Cost Operation and Kas Margin
    public static function getCoaOptions(?int $year = null): array
    {
        if (!$year) {
            $year = date('Y');
        }

        $coas = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
            $coas[] = [
                'id' => "PL.{$monthPadded}-{$year}",
                'coa' => "{$year} - PL.{$monthPadded}-{$year}"
            ];
        }
        return $coas;
    }
} 