<?php

namespace App\Observers;

use App\Models\Logsheet;
use App\Models\Ledger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LogsheetObserver
{
    /**
     * Handle the Logsheet "created" event.
     */
    public function created(Logsheet $logsheet): void
    {
        try {
            // Pastikan status AR dan AP default "Listing" jika kosong
            if (empty($logsheet->ar_status)) {
                $logsheet->ar_status = 'Listing';
            }
            if (empty($logsheet->ap_status)) {
                $logsheet->ap_status = 'Listing';
            }

            // Hitung revenue (quantity_1 * rate_1)
            $revenue = $logsheet->quantity_1 * $logsheet->rate_1;
            
            // Hitung cost (quantity_2 * rate_2)
            $cost = $logsheet->quantity_2 * $logsheet->rate_2;

            // Buat ledger untuk Revenue Project
            Ledger::create([
                'category' => Ledger::CATEGORY_REVENUE_PROJECT,
                'budget' => strval($logsheet->coa),
                'sub_budget' => '-',
                'recipient' => '-',
                'date' => Carbon::now(),
                'month' => Carbon::now()->format('M Y'),
                'status' => Ledger::STATUS_LISTING,
                'debit' => $revenue,
                'credit' => 0,
                'description' => "Revenue dari logsheet {$logsheet->id}"
            ]);

            // Buat ledger untuk Cost Project dengan sub_budget "by tutor" dan recipient nama tutor
            Ledger::create([
                'category' => Ledger::CATEGORY_COST_PROJECT,
                'budget' => strval($logsheet->coa),
                'sub_budget' => 'by tutor', // Set sub_budget sebagai "by tutor"
                'recipient' => $logsheet->tutor,  // Set recipient sebagai nama tutor
                'date' => Carbon::now(),
                'month' => Carbon::now()->format('M Y'),
                'status' => Ledger::STATUS_LISTING,
                'debit' => 0,
                'credit' => $cost,
                'description' => "Cost dari logsheet {$logsheet->id}"
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating ledger entries: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle the Logsheet "updated" event.
     */
    public function updated(Logsheet $logsheet): void
    {
        try {
            if ($logsheet->isDirty(['revenue', 'cost', 'coa', 'quantity_1', 'rate_1', 'quantity_2', 'rate_2', 'tutor'])) {
                // Hitung revenue dan cost yang baru
                $revenue = $logsheet->quantity_1 * $logsheet->rate_1;
                $cost = $logsheet->quantity_2 * $logsheet->rate_2;

                // Update Revenue Project ledger
                Ledger::where('category', Ledger::CATEGORY_REVENUE_PROJECT)
                    ->where('description', "Revenue dari logsheet {$logsheet->id}")
                    ->update([
                        'budget' => strval($logsheet->coa),
                        'debit' => $revenue
                    ]);

                // Update Cost Project ledger dengan recipient yang baru (sub_budget tetap "by tutor")
                Ledger::where('category', Ledger::CATEGORY_COST_PROJECT)
                    ->where('description', "Cost dari logsheet {$logsheet->id}")
                    ->update([
                        'budget' => strval($logsheet->coa),
                        'recipient' => $logsheet->tutor,
                        'credit' => $cost
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating ledger entries: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle the Logsheet "deleted" event.
     */
    public function deleted(Logsheet $logsheet): void
    {
        try {
            // Delete related ledgers
            Ledger::where('description', "Revenue dari logsheet {$logsheet->id}")
                ->orWhere('description', "Cost dari logsheet {$logsheet->id}")
                ->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting ledger entries: ' . $e->getMessage());
            throw $e;
        }
    }
} 