<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = Ledger::query();
        
        // Apply filters
        if ($request->filled('filter_type')) {
            switch ($request->filter_type) {
                case 'day':
                    if ($request->filled('filter_date')) {
                        $query->whereDate('date', $request->filter_date);
                    }
                    break;
                case 'month':
                    if ($request->filled('filter_month') && $request->filled('filter_year')) {
                        $query->whereMonth('date', $request->filter_month)
                              ->whereYear('date', $request->filter_year);
                    }
                    break;
                case 'year':
                    if ($request->filled('filter_year')) {
                        $query->whereYear('date', $request->filter_year);
                    }
                    break;
                // 'all' doesn't need any filter
            }
        }
        
        $ledgers = $query->orderBy('date', 'desc')->get();
        $projects = Project::orderBy('coa')->get();
        
        // Calculate summary based on filtered data (only PAID entries)
        $paidLedgers = $ledgers->filter(function($ledger) {
            return strtoupper($ledger->status) === 'PAID';
        });
        
        $summary = [
            'sum_debit' => $paidLedgers->sum('debit'),
            'sum_credit' => $paidLedgers->sum('credit'),
            'saldo' => $paidLedgers->sum('debit') - $paidLedgers->sum('credit')
        ];
        
        return view('ledger', compact('ledgers', 'projects', 'summary'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:COST OPERATION,REVENUE PROJECT,COST PROJECT,KAS MARGIN',
            'budget' => 'required|string',
            'sub_budget' => 'required|string',
            'recipient' => 'required|string',
            'date' => 'required|date',
            'month' => 'required|string',
            'status' => 'required|string|in:LISTING,PAID',
            'credit' => 'required|numeric|min:0',
            'debit' => 'required|numeric|min:0'
        ]);

        // Auto-generate description for manual entries
        if (in_array($validated['category'], ['COST OPERATION', 'KAS MARGIN'])) {
            $validated['description'] = $validated['category'] . ' - ' . $validated['sub_budget'] . ' - ' . $validated['recipient'];
            // COST OPERATION and KAS MARGIN go to credit
            $validated['credit'] = $validated['credit'];
            $validated['debit'] = 0;
        } elseif ($validated['category'] === 'COST PROJECT') {
            $validated['description'] = $validated['category'] . ' - Manual Entry';
            // COST PROJECT goes to credit
            $validated['credit'] = $validated['credit'];
            $validated['debit'] = 0;
        } elseif ($validated['category'] === 'REVENUE PROJECT') {
            $validated['description'] = $validated['category'] . ' - Manual Entry';
            // REVENUE PROJECT goes to debit
            $validated['debit'] = $validated['credit'];
            $validated['credit'] = 0;
        }

        try {
            Ledger::create($validated);
            return redirect()->route('ledger.index')->with('success', 'Data ledger berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('ledger.index')->with('error', 'Gagal menambahkan data ledger: ' . $e->getMessage());
        }
    }

    public function edit(Ledger $ledger)
    {
        return response()->json($ledger);
    }

    public function update(Request $request, Ledger $ledger)
    {
        $isProjectCategory = in_array($ledger->category, ['COST PROJECT', 'REVENUE PROJECT']);
        
        if ($isProjectCategory) {
            // For project categories, only allow editing date and month
            $validated = $request->validate([
                'date' => 'required|date',
                'month' => 'required|string'
            ]);
            
            // Keep existing values for other fields
            $validated['category'] = $ledger->category;
            $validated['budget'] = $ledger->budget;
            $validated['sub_budget'] = $ledger->sub_budget;
            $validated['recipient'] = $ledger->recipient;
            $validated['status'] = $ledger->status;
            $validated['debit'] = $ledger->debit;
            $validated['credit'] = $ledger->credit;
            $validated['description'] = $ledger->description;
        } else {
            // For operation categories, allow editing all fields
        $validated = $request->validate([
            'category' => 'required|string',
            'budget' => 'required|string',
                'sub_budget' => 'required|string',
                'recipient' => 'required|string',
            'date' => 'required|date',
                'month' => 'required|string',
            'status' => 'required|string',
            'debit' => 'required|numeric',
                'credit' => 'required|numeric'
        ]);
            
            // Auto-generate description for operation entries
            $validated['description'] = $validated['category'] . ' - ' . $validated['sub_budget'] . ' - ' . $validated['recipient'];
        }

        try {
            $ledger->update($validated);
            return redirect()->route('ledger.index')->with('success', 'Data ledger berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('ledger.index')->with('error', 'Gagal memperbarui data ledger: ' . $e->getMessage());
        }
    }

    public function destroy(Ledger $ledger)
    {
        try {
            $ledger->delete();
            return redirect()->route('ledger.index')->with('success', 'Data ledger berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('ledger.index')->with('error', 'Gagal menghapus data ledger: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Ledger $ledger)
    {
        try {
            $ledger->update(['status' => Ledger::STATUS_PAID]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getBudgetOptions(Request $request)
    {
        $category = $request->get('category');
        
        if (in_array($category, ['COST OPERATION', 'KAS MARGIN'])) {
            // Generate COA options for Cost Operation and Kas Margin
            $year = date('Y');
            $options = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
                $coa = "PL.{$monthPadded}-{$year}";
                $options[] = [
                    'id' => $coa,
                    'coa' => $coa
                ];
            }
            return response()->json($options);
        } else {
            // For project categories, return project COAs
            $projects = Project::orderBy('coa')->get();
            return response()->json($projects->map(function($project) {
                return [
                    'id' => $project->coa,
                    'coa' => $project->coa
                ];
            }));
        }
    }

    public function getSummary(Request $request)
    {
        $query = Ledger::query();
        
        // Apply filters
        if ($request->filled('filter_type')) {
            switch ($request->filter_type) {
                case 'day':
                    if ($request->filled('filter_date')) {
                        $query->whereDate('date', $request->filter_date);
                    }
                    break;
                case 'month':
                    if ($request->filled('filter_month') && $request->filled('filter_year')) {
                        $query->whereMonth('date', $request->filter_month)
                              ->whereYear('date', $request->filter_year);
                    }
                    break;
                case 'year':
                    if ($request->filled('filter_year')) {
                        $query->whereYear('date', $request->filter_year);
                    }
                    break;
                // 'all' doesn't need any filter
            }
        }
        
        // Only get PAID entries for summary using case-insensitive comparison
        $query->whereRaw('UPPER(status) = ?', ['PAID']);
        $ledgers = $query->get();
        
        $summary = [
            'sum_debit' => $ledgers->sum('debit'),
            'sum_credit' => $ledgers->sum('credit'),
            'saldo' => $ledgers->sum('debit') - $ledgers->sum('credit')
        ];
        
        return response()->json($summary);
    }
} 