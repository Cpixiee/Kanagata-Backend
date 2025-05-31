<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Ledger;
use App\Models\Logsheet;
use App\Models\ReviewRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        try {
            Log::info('Accessing review index', [
                'user' => Auth::user(),
                'role' => Auth::user()->role
            ]);

            $query = ReviewRequest::with('user');

            // Apply category filter
            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('model_type', ucfirst($request->category));
            }

            // Apply search filter
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->whereHas('user', function($userQuery) use ($searchTerm) {
                        $userQuery->where('name', 'like', "%{$searchTerm}%")
                                 ->orWhere('email', 'like', "%{$searchTerm}%");
                    })
                    ->orWhere('action_type', 'like', "%{$searchTerm}%")
                    ->orWhere('model_type', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%")
                    ->orWhereRaw("JSON_EXTRACT(data, '$.coa') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(data, '$.customer') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(data, '$.activity') LIKE ?", ["%{$searchTerm}%"]);
                });
            }

            $reviewRequests = $query->latest()->get();

            if ($request->ajax()) {
                return response()->json([
                    'requests' => $reviewRequests,
                    'html' => view('partials.review-list', compact('reviewRequests'))->render()
                ]);
            }

            return view('review', compact('reviewRequests'));
        } catch (\Exception $e) {
            Log::error('Error in review index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->ajax()) {
                return response()->json(['error' => 'An error occurred while loading review requests'], 500);
            }
            
            return back()->with('error', 'An error occurred while loading review requests');
        }
    }

    public function show(ReviewRequest $request)
    {
        return response()->json(['request' => $request->load('user')]);
    }

    public function approve(ReviewRequest $request)
    {
        try {
            DB::beginTransaction();

            $modelName = $this->getModelName($request);

            switch ($request->model_type) {
                case 'Project':
                    if ($request->action_type === 'create') {
                        Project::create($request->data);
                    } elseif ($request->action_type === 'update') {
                        $project = Project::findOrFail($request->model_id);
                        $project->update($request->data);
                    } elseif ($request->action_type === 'delete') {
                        $project = Project::findOrFail($request->model_id);
                        $project->delete();
                    }
                    break;

                case 'Ledger':
                    if ($request->action_type === 'create') {
                        Ledger::create($request->data);
                    } elseif ($request->action_type === 'update') {
                        $ledger = Ledger::findOrFail($request->model_id);
                        $ledger->update($request->data);
                    } elseif ($request->action_type === 'delete') {
                        $ledger = Ledger::findOrFail($request->model_id);
                        $ledger->delete();
                    }
                    break;

                case 'Logsheet':
                    if ($request->action_type === 'create') {
                        $logsheet = Logsheet::create($request->data);
                        $logsheet->project->updateFinancials();
                    } elseif ($request->action_type === 'update') {
                        $logsheet = Logsheet::findOrFail($request->model_id);
                        $oldProjectId = $logsheet->project_id;
                        $logsheet->update($request->data);
                        
                        // Update financials for both old and new projects if project changed
                        if ($oldProjectId != $request->data['project_id']) {
                            Project::find($oldProjectId)->updateFinancials();
                        }
                        $logsheet->project->updateFinancials();
                    } elseif ($request->action_type === 'delete') {
                        $logsheet = Logsheet::findOrFail($request->model_id);
                        $projectId = $logsheet->project_id;
                        $logsheet->delete();
                        Project::find($projectId)->updateFinancials();
                    }
                    break;
            }

            $request->update(['status' => 'approved']);
            
            // Log approval activity
            NotificationService::logApprove(
                $request->model_type,
                $modelName,
                $request->user->name
            );

            DB::commit();

            return response()->json(['message' => 'Request approved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error approving request: ' . $e->getMessage()], 500);
        }
    }

    public function reject(ReviewRequest $request)
    {
        $modelName = $this->getModelName($request);
        
        $request->update(['status' => 'rejected']);
        
        // Log rejection activity
        NotificationService::logReject(
            $request->model_type,
            $modelName,
            $request->user->name
        );
        
        return response()->json(['message' => 'Request rejected successfully']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'action_type' => 'required|in:create,update,delete',
            'model_type' => 'required|string',
            'model_id' => 'nullable|integer',
            'data' => 'required|array'
        ]);

        $reviewRequest = ReviewRequest::create([
            'user_id' => Auth::id(),
            'action_type' => $data['action_type'],
            'model_type' => $data['model_type'],
            'model_id' => $data['model_id'],
            'data' => $data['data'],
            'status' => 'pending'
        ]);

        // Log review request submission
        $modelName = $this->getModelNameFromData($data);
        NotificationService::logReviewRequest(
            $data['model_type'],
            $data['action_type'],
            $modelName
        );

        return response()->json([
            'message' => 'Review request created successfully',
            'request' => $reviewRequest
        ]);
    }

    private function getModelName(ReviewRequest $request)
    {
        $data = $request->data;
        
        switch ($request->model_type) {
            case 'Project':
                return $data['coa'] ?? 'Unknown Project';
            case 'Ledger':
                return ($data['category'] ?? 'Unknown') . ' - ' . ($data['budget'] ?? 'Unknown');
            case 'Logsheet':
                return ($data['coa'] ?? 'Unknown') . ' - ' . ($data['activity'] ?? 'Unknown');
            default:
                return 'Unknown';
        }
    }

    private function getModelNameFromData(array $data)
    {
        $requestData = $data['data'];
        
        switch ($data['model_type']) {
            case 'Project':
                return $requestData['coa'] ?? 'Unknown Project';
            case 'Ledger':
                return ($requestData['category'] ?? 'Unknown') . ' - ' . ($requestData['budget'] ?? 'Unknown');
            case 'Logsheet':
                return ($requestData['coa'] ?? 'Unknown') . ' - ' . ($requestData['activity'] ?? 'Unknown');
            default:
                return 'Unknown';
        }
    }
} 