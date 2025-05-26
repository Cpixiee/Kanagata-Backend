<?php

namespace App\Observers;

use App\Models\Ledger;
use App\Models\Project;
use App\Models\Logsheet;
use Illuminate\Support\Facades\Log;

class LedgerObserver
{
    /**
     * Handle the Ledger "updated" event.
     */
    public function updated(Ledger $ledger): void
    {
        try {
            if ($ledger->isDirty('status') && $ledger->status === 'PAID') {
                // Ambil logsheet ID dari description
                preg_match('/logsheet (\d+)/', $ledger->description, $matches);
                if (!empty($matches[1])) {
                    $logsheetId = $matches[1];
                    $logsheet = Logsheet::find($logsheetId);
                    
                    if ($logsheet) {
                        // Update status logsheet berdasarkan kategori ledger
                        if ($ledger->category === 'REVENUE PROJECT') {
                            $logsheet->ar_status = 'PAID';
                        } elseif ($ledger->category === 'COST PROJECT') {
                            $logsheet->ap_status = 'PAID';
                        }
                        $logsheet->save();

                        // Update project statistics
                        $this->updateProjectStatistics($logsheet->coa);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error updating project statistics: ' . $e->getMessage());
        }
    }

    /**
     * Update project statistics based on ledger status changes
     */
    private function updateProjectStatistics(string $coa): void
    {
        $project = Project::where('coa', $coa)->first();
        
        if ($project) {
            // Hitung total AR dan AP dari ledger
            $arPaid = Ledger::where('budget', $coa)
                ->where('category', 'REVENUE PROJECT')
                ->where('status', 'PAID')
                ->sum('debit');

            $apPaid = Ledger::where('budget', $coa)
                ->where('category', 'COST PROJECT')
                ->where('status', 'PAID')
                ->sum('debit');

            $sumAr = Ledger::where('budget', $coa)
                ->where('category', 'REVENUE PROJECT')
                ->sum('debit');

            $sumAp = Ledger::where('budget', $coa)
                ->where('category', 'COST PROJECT')
                ->sum('debit');

            // Update project
            $project->update([
                'ar_paid' => $arPaid,
                'ap_paid' => $apPaid,
                'sum_ar' => $sumAr,
                'sum_ap' => $sumAp,
                'ar_os' => $sumAr - $arPaid,
                'ap_os' => $sumAp - $apPaid
            ]);
        }
    }
} 