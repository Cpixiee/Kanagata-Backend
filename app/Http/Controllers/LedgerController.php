<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Project;
use App\Models\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $data = $request->validate([
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

        try {
            DB::beginTransaction();

            // Auto-generate description for manual entries
            if (in_array($data['category'], ['COST OPERATION', 'KAS MARGIN'])) {
                $data['description'] = $data['category'] . ' - ' . $data['sub_budget'] . ' - ' . $data['recipient'];
                // COST OPERATION and KAS MARGIN go to credit
                $data['credit'] = $data['credit'];
                $data['debit'] = 0;
            } elseif ($data['category'] === 'COST PROJECT') {
                $data['description'] = $data['category'] . ' - Manual Entry';
                // COST PROJECT goes to credit
                $data['credit'] = $data['credit'];
                $data['debit'] = 0;
            } elseif ($data['category'] === 'REVENUE PROJECT') {
                $data['description'] = $data['category'] . ' - Manual Entry';
                // REVENUE PROJECT goes to debit
                $data['debit'] = $data['credit'];
                $data['credit'] = 0;
            }

            if (Auth::user()->role === 'admin') {
                $ledger = Ledger::create($data);
                DB::commit();
                return response()->json([
                    'message' => 'Ledger entry created successfully',
                    'ledger' => $ledger
                ]);
            } else {
                $reviewRequest = ReviewRequest::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'create',
                    'model_type' => 'Ledger',
                    'data' => $data,
                    'status' => 'pending'
                ]);
                DB::commit();
                return response()->json([
                    'message' => 'Your request has been submitted for review',
                    'request' => $reviewRequest
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating ledger entry: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit(Ledger $ledger)
    {
        return response()->json($ledger);
    }

    public function update(Request $request, Ledger $ledger)
    {
        $data = $request->validate([
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

        try {
            DB::beginTransaction();

            // Auto-generate description for all entries
            if (in_array($data['category'], ['COST OPERATION', 'KAS MARGIN'])) {
                $data['description'] = $data['category'] . ' - ' . $data['sub_budget'] . ' - ' . $data['recipient'];
                // COST OPERATION and KAS MARGIN: ensure debit is 0
                $data['debit'] = 0;
            } elseif ($data['category'] === 'COST PROJECT') {
                $data['description'] = $data['category'] . ' - Manual Entry';
                // COST PROJECT: ensure debit is 0
                $data['debit'] = 0;
            } elseif ($data['category'] === 'REVENUE PROJECT') {
                $data['description'] = $data['category'] . ' - Manual Entry';
                // REVENUE PROJECT: ensure credit is 0
                $data['credit'] = 0;
            }

            if (Auth::user()->role === 'admin') {
                $ledger->update($data);
                DB::commit();
                return response()->json([
                    'message' => 'Ledger entry updated successfully',
                    'ledger' => $ledger
                ]);
            } else {
                $reviewRequest = ReviewRequest::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'update',
                    'model_type' => 'Ledger',
                    'model_id' => $ledger->id,
                    'data' => $data,
                    'status' => 'pending'
                ]);
                DB::commit();
                return response()->json([
                    'message' => 'Your update request has been submitted for review',
                    'request' => $reviewRequest
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating ledger entry: ' . $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Ledger $ledger)
    {
        if (Auth::user()->role === 'admin') {
            $ledger->delete();
            return response()->json([
                'message' => 'Ledger entry deleted successfully'
            ]);
        } else {
            $reviewRequest = ReviewRequest::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Ledger',
                'model_id' => $ledger->id,
                'data' => $ledger->toArray(),
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Your deletion request has been submitted for review',
                'request' => $reviewRequest
            ]);
        }
    }

    public function markAsPaid(Ledger $ledger)
    {
        // Hanya admin yang bisa langsung mark as paid
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false, 
                'message' => 'Unauthorized. Only admin can directly mark as paid.'
            ], 403);
        }

        try {
            $ledger->update(['status' => Ledger::STATUS_PAID]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function requestMarkAsPaid(Request $request, Ledger $ledger)
    {
        $request->validate([
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120' // 5MB max
        ]);

        try {
            DB::beginTransaction();

            // Upload file
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments/ledger', $fileName, 'public');

            // Create review request untuk mark as paid
            $reviewRequest = ReviewRequest::create([
                'user_id' => Auth::id(),
                'action_type' => 'update',
                'model_type' => 'Ledger',
                'model_id' => $ledger->id,
                'data' => array_merge($ledger->toArray(), ['status' => 'PAID']),
                'attachment' => $filePath,
                'status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan perubahan status ke PAID telah dikirim untuk review beserta bukti transaksi',
                'request' => $reviewRequest
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
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