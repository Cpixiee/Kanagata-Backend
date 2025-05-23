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
        'budget_id',
        'sub_budget',
        'recipient',
        'date',
        'month',
        'status',
        'debit',
        'credit'
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

    // Konstanta untuk sub budget
    const SUB_BUDGET_PAYROLL = 'BY PAYROLL';
    const SUB_BUDGET_PROJECT = 'BY PROJECT';
    const SUB_BUDGET_PAJAK = 'BY PAJAK';
    const SUB_BUDGET_SHAREHOLDER = 'SHAREHOLDER';
    const SUB_BUDGET_INVENTARIS = 'BY INVENTARIS';
    const SUB_BUDGET_SEWA = 'BY SEWA';
    const SUB_BUDGET_TUTOR = 'BY TUTOR';
    const SUB_BUDGET_TAKIS = 'BY TAKIS';

    // Konstanta untuk status
    const STATUS_LISTING = 'listing';
    const STATUS_PAID = 'paid';

    // Relasi dengan Project (Budget)
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'budget_id');
    }

    // Mendapatkan opsi kategori
    public static function getCategoryOptions(): array
    {
        return [
            self::CATEGORY_COST_OPERATION,
            self::CATEGORY_REVENUE_PROJECT,
            self::CATEGORY_COST_PROJECT,
            self::CATEGORY_KAS_MARGIN
        ];
    }

    // Mendapatkan opsi sub budget
    public static function getSubBudgetOptions(): array
    {
        return [
            self::SUB_BUDGET_PAYROLL,
            self::SUB_BUDGET_PROJECT,
            self::SUB_BUDGET_PAJAK,
            self::SUB_BUDGET_SHAREHOLDER,
            self::SUB_BUDGET_INVENTARIS,
            self::SUB_BUDGET_SEWA,
            self::SUB_BUDGET_TUTOR,
            self::SUB_BUDGET_TAKIS
        ];
    }

    // Mendapatkan opsi penerima
    public static function getRecipientOptions(): array
    {
        return [
            'rizal ramdhanu',
            'andar rahman',
            'fariz dandy',
            'adam',
            'wirakusuma'
        ];
    }

    // Mendapatkan opsi status
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_LISTING,
            self::STATUS_PAID
        ];
    }
} 