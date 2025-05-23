<?php

namespace App\Http\Controllers;

use App\Models\Logsheet;
use App\Models\Project;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LogsheetController extends Controller
{
    public function index()
    {
        $logsheets = Logsheet::with('project')->get();
        $projects = Project::all();
        return view('logsheet', compact('logsheets', 'projects'));
    }

    public function store(Request $request)
    {
        Log::info('Received logsheet creation request:', $request->all());

        try {
            DB::beginTransaction();

            $validated = $request->validate([
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

            // Tambahkan perhitungan revenue dan cost
            $validated['revenue'] = $validated['quantity_1'] * $validated['rate_1'];
            $validated['cost'] = $validated['quantity_2'] * $validated['rate_2'];

            Log::info('Validated data:', $validated);

            $logsheet = Logsheet::create($validated);

            // Update project financials
            $logsheet->project->updateFinancials();

            DB::commit();
            
            Log::info('Logsheet created successfully:', $logsheet->toArray());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logsheet entry created successfully'
                ]);
            }
            
            return redirect()->route('logsheet.index')
                ->with('success', 'Logsheet entry created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating logsheet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating logsheet entry: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('logsheet.index')
                ->with('error', 'Error creating logsheet entry: ' . $e->getMessage());
        }
    }

    public function edit(Logsheet $logsheet)
    {
        return response()->json($logsheet->load('project'));
    }

    public function update(Request $request, Logsheet $logsheet)
    {
        Log::info('Received logsheet update request:', $request->all());

        try {
            DB::beginTransaction();

            $validated = $request->validate([
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

            // Tambahkan perhitungan revenue dan cost
            $validated['revenue'] = $validated['quantity_1'] * $validated['rate_1'];
            $validated['cost'] = $validated['quantity_2'] * $validated['rate_2'];

            $oldProjectId = $logsheet->project_id;
            $logsheet->update($validated);
            
            // Update financials for both old and new projects if project changed
            if ($oldProjectId != $request->project_id) {
                Project::find($oldProjectId)->updateFinancials();
            }
            $logsheet->project->updateFinancials();

            DB::commit();

            Log::info('Logsheet updated successfully:', $logsheet->toArray());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logsheet entry updated successfully'
                ]);
            }

            return redirect()->route('logsheet.index')
                ->with('success', 'Logsheet entry updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating logsheet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating logsheet entry: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('logsheet.index')
                ->with('error', 'Error updating logsheet entry: ' . $e->getMessage());
        }
    }

    public function destroy(Logsheet $logsheet)
    {
        try {
            DB::beginTransaction();

            $projectId = $logsheet->project_id;
            $logsheet->delete();
            
            // Update project financials after deletion
            Project::find($projectId)->updateFinancials();

            DB::commit();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logsheet entry deleted successfully'
                ]);
            }

            return redirect()->route('logsheet.index')
                ->with('success', 'Logsheet entry deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting logsheet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting logsheet entry: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('logsheet.index')
                ->with('error', 'Error deleting logsheet entry: ' . $e->getMessage());
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