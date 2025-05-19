<?php

namespace App\Http\Controllers;

use App\Models\Logsheet;
use App\Models\Project;
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
} 