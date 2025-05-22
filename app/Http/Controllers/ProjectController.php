<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        try {
            $validatedData = $request->validate([
                'coa' => 'required|string',
                'customer' => 'required|in:' . implode(',', Project::getCustomerOptions()),
                'activity' => 'required|in:' . implode(',', Project::getActivityOptions()),
                'prodi' => 'required|in:' . implode(',', Project::getProdiOptions()),
                'grade' => 'required|in:' . implode(',', Project::getGradeOptions()),
                'quantity_1' => 'required|numeric|min:1',
                'rate_1' => 'required|numeric|min:0',
                'quantity_2' => 'required|numeric|min:1',
                'rate_2' => 'required|numeric|min:0'
            ]);

            // Calculate derived values
            $validatedData['gt_rev'] = $validatedData['quantity_1'] * $validatedData['rate_1'];
            $validatedData['gt_cost'] = $validatedData['quantity_2'] * $validatedData['rate_2'];
            $validatedData['gt_margin'] = $validatedData['gt_rev'] - $validatedData['gt_cost'];

            // Initialize AR/AP values
            $validatedData['sum_ar'] = 0;
            $validatedData['ar_paid'] = 0;
            $validatedData['ar_os'] = 0;
            $validatedData['sum_ap'] = 0;
            $validatedData['ap_paid'] = 0;
            $validatedData['ap_os'] = 0;

            $project = Project::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Proyek berhasil dibuat',
                'data' => $project
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
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
        try {
            $validatedData = $request->validate([
                'coa' => 'required|string',
                'customer' => 'required|string',
                'activity' => 'required|string',
                'prodi' => 'required|string',
                'grade' => 'required|string',
                'quantity_1' => 'required|numeric',
                'rate_1' => 'required|numeric',
                'quantity_2' => 'required|numeric',
                'rate_2' => 'required|numeric'
            ]);

            // Hitung nilai turunan
            $validatedData['gt_rev'] = $validatedData['quantity_1'] * $validatedData['rate_1'];
            $validatedData['gt_cost'] = $validatedData['quantity_2'] * $validatedData['rate_2'];
            $validatedData['gt_margin'] = $validatedData['gt_rev'] - $validatedData['gt_cost'];

            $project->update($validatedData);
            $project->updateFinancials();

            return response()->json([
                'success' => true,
                'message' => 'Proyek berhasil diperbarui',
                'data' => $project
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('projects.index')->with('error', 'Gagal menghapus proyek');
        }
    }
}