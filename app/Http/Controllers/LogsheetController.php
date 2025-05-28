<?php

namespace App\Http\Controllers;

use App\Models\Logsheet;
use App\Models\Project;
use App\Models\Ledger;
use App\Models\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LogsheetController extends Controller
{
    public function index()
    {
        $logsheets = Logsheet::with('project')->latest()->get();
        $projects = Project::all();
        return view('logsheet', compact('logsheets', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'coa' => 'required|string',
            'customer' => 'required|string',
            'activity' => 'required|string',
            'prodi' => 'required|string',
            'grade' => 'required|string',
            'seq' => 'required|integer',
            'quantity_1' => 'required|integer',
            'rate_1' => 'required|numeric',
            'ar_status' => 'required|in:Listing,Paid,Pending',
            'tutor' => 'required|string',
            'quantity_2' => 'required|integer',
            'rate_2' => 'required|numeric',
            'ap_status' => 'required|in:Listing,Paid,Pending',
        ]);

        try {
            DB::beginTransaction();

            // Tambahkan perhitungan revenue dan cost
            $data['revenue'] = $data['quantity_1'] * $data['rate_1'];
            $data['cost'] = $data['quantity_2'] * $data['rate_2'];

            if (Auth::user()->role === 'admin') {
                $logsheet = Logsheet::create($data);
                $logsheet->project->updateFinancials();
                DB::commit();

                return response()->json([
                    'message' => 'Logsheet entry created successfully',
                    'logsheet' => $logsheet
                ]);
            } else {
                $reviewRequest = ReviewRequest::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'create',
                    'model_type' => 'Logsheet',
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
                'message' => 'Error creating logsheet entry: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit(Logsheet $logsheet)
    {
        return response()->json($logsheet->load('project'));
    }

    public function update(Request $request, Logsheet $logsheet)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'coa' => 'required|string',
            'customer' => 'required|string',
            'activity' => 'required|string',
            'prodi' => 'required|string',
            'grade' => 'required|string',
            'seq' => 'required|integer|min:1|max:100',
            'quantity_1' => 'required|numeric|min:1',
            'rate_1' => 'required|numeric|min:0',
            'ar_status' => 'required|string|in:Listing,Paid,Pending',
            'tutor' => 'required|string',
            'quantity_2' => 'required|numeric|min:1',
            'rate_2' => 'required|numeric|min:0',
            'ap_status' => 'required|string|in:Listing,Paid,Pending'
        ]);

        try {
            DB::beginTransaction();

            if (Auth::user()->role === 'admin') {
                // Calculate revenue and cost
                $data['revenue'] = $data['quantity_1'] * $data['rate_1'];
                $data['cost'] = $data['quantity_2'] * $data['rate_2'];
                
                $logsheet->update($data);
                
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Logsheet entry updated successfully',
                    'logsheet' => $logsheet
                ]);
            } else {
                $reviewRequest = ReviewRequest::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'update',
                    'model_type' => 'Logsheet',
                    'model_id' => $logsheet->id,
                    'data' => $data,
                    'status' => 'pending'
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Your update request has been submitted for review',
                    'request' => $reviewRequest
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating logsheet: ' . $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Logsheet $logsheet)
    {
        if (Auth::user()->role === 'admin') {
            $logsheet->delete();
            return response()->json([
                'message' => 'Logsheet entry deleted successfully'
            ]);
        } else {
            $reviewRequest = ReviewRequest::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Logsheet',
                'model_id' => $logsheet->id,
                'data' => $logsheet->toArray(),
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Your deletion request has been submitted for review',
                'request' => $reviewRequest
            ]);
        }
    }

    public function getChartData()
    {
        try {
            $year = request('year', date('Y'));
            $month = request('month', date('m'));
            
            // Create array for all months
            $monthsArray = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthsArray[sprintf('%02d', $m)] = [
                    'x' => sprintf('%04d-%02d-01', $year, $m),
                    'y' => 0
                ];
            }

            // Get monthly totals from logsheets
            $monthlyTotals = Logsheet::whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as month')
                ->selectRaw('SUM(revenue) as total_revenue')
                ->selectRaw('SUM(cost) as total_cost')
                ->groupBy('month')
                ->get();

            // Get selected month's data
            $selectedMonthData = Logsheet::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->selectRaw('SUM(revenue) as revenue')
                ->selectRaw('SUM(cost) as cost_project')
                ->first();

            // Get Cost Operation from ledger (total credit)
            $selectedMonthCostOperation = Ledger::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('credit');

            // Get yearly summary
            $yearlyData = Logsheet::whereYear('created_at', $year)
                ->selectRaw('SUM(revenue) as revenue')
                ->selectRaw('SUM(cost) as cost_project')
                ->first();

            // Get yearly Cost Operation
            $yearlyCostOperation = Ledger::whereYear('date', $year)
                ->sum('credit');

            // Calculate monthly averages
            $monthsWithData = $monthlyTotals->count() ?: 1; // Prevent division by zero
            $averageData = [
                'revenue' => $yearlyData->revenue / $monthsWithData,
                'cost_project' => $yearlyData->cost_project / $monthsWithData,
                'cost_operation' => $yearlyCostOperation / $monthsWithData,
            ];

            // Initialize result arrays
            $revenue = $monthsArray;
            $cost = $monthsArray;
            $profit = $monthsArray;

            // Fill in the actual values
            foreach ($monthlyTotals as $data) {
                $m = sprintf('%02d', $data->month);
                $revenue[$m]['y'] = round((float)$data->total_revenue, 2);
                $cost[$m]['y'] = round((float)$data->total_cost, 2);
                $profit[$m]['y'] = round((float)($data->total_revenue - $data->total_cost), 2);
            }

            // Calculate gross margin for this month and summary
            $thisMonthGrossMargin = ($selectedMonthData->revenue ?? 0) - ($selectedMonthData->cost_project ?? 0);
            $yearlyGrossMargin = ($yearlyData->revenue ?? 0) - ($yearlyData->cost_project ?? 0);
            $averageGrossMargin = $averageData['revenue'] - $averageData['cost_project'];

            return response()->json([
                'success' => true,
                'revenue' => array_values($revenue),
                'cost' => array_values($cost),
                'profit' => array_values($profit),
                'this_month' => [
                    'revenue' => round((float)($selectedMonthData->revenue ?? 0), 2),
                    'cost_project' => round((float)($selectedMonthData->cost_project ?? 0), 2),
                    'cost_operation' => round((float)$selectedMonthCostOperation, 2),
                    'gross_margin' => round((float)$thisMonthGrossMargin, 2),
                    'profit_loss' => round((float)($thisMonthGrossMargin - $selectedMonthCostOperation), 2)
                ],
                'summary' => [
                    'revenue' => round((float)($yearlyData->revenue ?? 0), 2),
                    'cost_project' => round((float)($yearlyData->cost_project ?? 0), 2),
                    'cost_operation' => round((float)$yearlyCostOperation, 2),
                    'gross_margin' => round((float)$yearlyGrossMargin, 2),
                    'profit_loss' => round((float)($yearlyGrossMargin - $yearlyCostOperation), 2)
                ],
                'average' => [
                    'revenue' => round((float)$averageData['revenue'], 2),
                    'cost_project' => round((float)$averageData['cost_project'], 2),
                    'cost_operation' => round((float)$averageData['cost_operation'], 2),
                    'gross_margin' => round((float)$averageGrossMargin, 2),
                    'profit_loss' => round((float)($averageGrossMargin - $averageData['cost_operation']), 2)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getChartData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting chart data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function insight()
    {
        return view('insight');
    }
} 