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
            // Set default status untuk logsheet
            $logsheet->ar_status = 'LISTING';
            $logsheet->ap_status = 'LISTING';
            $logsheet->save();

            // Hitung revenue (quantity_1 * rate_1)
            $revenue = $logsheet->quantity_1 * $logsheet->rate_1;
            
            // Hitung cost (quantity_2 * rate_2)
            $cost = $logsheet->quantity_2 * $logsheet->rate_2;

            // Buat ledger untuk Revenue Project
            Ledger::create([
                'category' => 'REVENUE PROJECT',
                'budget' => strval($logsheet->coa),
                'date' => Carbon::now(),
                'status' => 'LISTING',
                'debit' => $revenue,
                'credit' => 0,
                'description' => "Revenue dari logsheet {$logsheet->id}"
            ]);

            // Buat ledger untuk Cost Project
            Ledger::create([
                'category' => 'COST PROJECT',
                'budget' => strval($logsheet->coa),
                'date' => Carbon::now(),
                'status' => 'LISTING',
                'debit' => $cost,
                'credit' => 0,
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
            if ($logsheet->isDirty(['revenue', 'cost', 'coa', 'quantity_1', 'rate_1', 'quantity_2', 'rate_2'])) {
                // Hitung revenue dan cost yang baru
                $revenue = $logsheet->quantity_1 * $logsheet->rate_1;
                $cost = $logsheet->quantity_2 * $logsheet->rate_2;

                // Update Revenue Project ledger
                Ledger::where('category', 'REVENUE PROJECT')
                    ->where('description', "Revenue dari logsheet {$logsheet->id}")
                    ->update([
                        'budget' => strval($logsheet->coa),
                        'debit' => $revenue
                    ]);

                // Update Cost Project ledger
                Ledger::where('category', 'COST PROJECT')
                    ->where('description', "Cost dari logsheet {$logsheet->id}")
                    ->update([
                        'budget' => strval($logsheet->coa),
                        'debit' => $cost
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