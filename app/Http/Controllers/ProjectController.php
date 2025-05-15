<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all(); // Mengambil semua data tanpa pagination
        return view('projects', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
    
            // Debug data yang dikirim
            // dd($request->all());
            
            $validated = $request->validate([
                'customer_name' => 'required|in:'.implode(',', Project::getCustomerNames()),
                'tutor_name' => 'required|in:'.implode(',', Project::getTutorNames()),
                'tahun_ajaran' => 'required|string|max:7',
                'activity' => 'required|in:'.implode(',', Project::getActivity()),
                'prodi' => 'required|in:'.implode(',', Project::getProdi()),
                'grade' => 'required|in:'.implode(',', Project::getGrade()),
                'quantity' => 'required|integer|min:1',
                'rate_tutor' => 'required|integer|min:0',
                'gt_rev' => 'required|integer|min:0',
                'jam_pertemuan' => 'required|integer|min:1',
                'sum_ip' => 'required|integer|min:0',
                'gt_cost' => 'required|integer|min:0',
                'gt_margin' => 'required|integer',
                'ar' => 'required|integer|min:0',
                'ar_outstanding' => 'required|integer|min:0',
                'sum_ar' => 'required|integer|min:0',
                'sum_ar_paid' => 'required|integer|min:0',
                'todo' => 'required|integer|min:0',
                'arus_kas' => 'nullable|integer'
            ]);

            Project::create($validated);
            
            if ($request->ajax()) {
                return response()->json(['message' => 'Project created successfully']);
            }
            return redirect()->route('projects.index')->with('success', 'Project created successfully.');
        // } catch (\Exception $e) {
        //     return back()
        //         ->withErrors(['msg' => 'Terjadi kesalahan: ' . $e->getMessage()])
        //         ->withInput();
        // }
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        if (request()->ajax()) {
            return response()->json($project);
        }
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'customer_name' => 'required|in:'.implode(',', Project::getCustomerNames()),
            'tutor_name' => 'required|in:'.implode(',', Project::getTutorNames()),
            'tahun_ajaran' => 'required|string|max:7',
            'activity' => 'required|in:'.implode(',', Project::getActivity()),
            'prodi' => 'required|in:'.implode(',', Project::getProdi()),
            'grade' => 'required|in:'.implode(',', Project::getGrade()),
            'quantity' => 'required|integer|min:1',
            'rate_tutor' => 'required|integer|min:0',
            'gt_rev' => 'required|integer|min:0',
            'jam_pertemuan' => 'required|integer|min:1',
            'sum_ip' => 'required|integer|min:0',
            'gt_cost' => 'required|integer|min:0',
            'gt_margin' => 'required|integer',
            'ar' => 'required|integer|min:0',
            'ar_outstanding' => 'required|integer|min:0',
            'sum_ar' => 'required|integer|min:0',
            'sum_ar_paid' => 'required|integer|min:0',
            'todo' => 'required|integer|min:0',
            'arus_kas' => 'nullable|integer'
        ]);

        $project->update($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Project updated successfully']);
        }
        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        
        if (request()->ajax()) {
            return response()->json(['message' => 'Project deleted successfully']);
        }
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}