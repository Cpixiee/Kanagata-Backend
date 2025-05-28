<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('logsheets')->get();
        return view('projects', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'coa' => 'required|string',
            'customer' => 'required|string',
            'activity' => 'required|string',
            'prodi' => 'required|string',
            'grade' => 'required|string',
            'quantity_1' => 'required|numeric',
            'rate_1' => 'required|numeric',
            'quantity_2' => 'required|numeric',
            'rate_2' => 'required|numeric',
        ]);

        if (Auth::user()->role === 'admin') {
            $project = Project::create($data);
            return response()->json([
                'message' => 'Project created successfully',
                'project' => $project
            ]);
        } else {
            $reviewRequest = ReviewRequest::create([
                'user_id' => Auth::id(),
                'action_type' => 'create',
                'model_type' => 'Project',
                'data' => $data,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Your request has been submitted for review',
                'request' => $reviewRequest
            ]);
        }
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'coa' => 'required|string',
            'customer' => 'required|string',
            'activity' => 'required|string',
            'prodi' => 'required|string',
            'grade' => 'required|string',
            'quantity_1' => 'required|numeric',
            'rate_1' => 'required|numeric',
            'quantity_2' => 'required|numeric',
            'rate_2' => 'required|numeric',
        ]);

        if (Auth::user()->role === 'admin') {
            $project->update($data);
            return response()->json([
                'message' => 'Project updated successfully',
                'project' => $project
            ]);
        } else {
            $reviewRequest = ReviewRequest::create([
                'user_id' => Auth::id(),
                'action_type' => 'update',
                'model_type' => 'Project',
                'model_id' => $project->id,
                'data' => $data,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Your update request has been submitted for review',
                'request' => $reviewRequest
            ]);
        }
    }

    public function destroy(Project $project)
    {
        if (Auth::user()->role === 'admin') {
            $project->delete();
            return response()->json([
                'message' => 'Project deleted successfully'
            ]);
        } else {
            $reviewRequest = ReviewRequest::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Project',
                'model_id' => $project->id,
                'data' => $project->toArray(),
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Your deletion request has been submitted for review',
                'request' => $reviewRequest
            ]);
        }
    }
}