<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LedgerController extends Controller
{
    public function index()
    {
        $ledgers = Ledger::with('budget')->get();
        $projects = Project::all();
        $coaOptions = Ledger::getCoaOptions();
        return view('ledger', compact('ledgers', 'projects', 'coaOptions'));
    }

    public function store(Request $request)
    {
        try {
            $category = $request->input('category');
            
            $validationRules = [
                'category' => 'required|in:' . implode(',', Ledger::getCategoryOptions()),
                'sub_budget' => 'required|in:' . implode(',', Ledger::getSubBudgetOptions()),
                'recipient' => 'required|in:' . implode(',', Ledger::getRecipientOptions()),
                'date' => 'required|date',
                'month' => 'required|string',
                'status' => 'required|in:' . implode(',', Ledger::getStatusOptions()),
                'debit' => 'required|numeric|min:0',
                'credit' => 'required|numeric|min:0'
            ];

            // Different validation for budget_id based on category
            if (in_array($category, [Ledger::CATEGORY_COST_PROJECT, Ledger::CATEGORY_REVENUE_PROJECT])) {
                $validationRules['budget_id'] = 'required|exists:projects,id';
            } else {
                $validationRules['budget_id'] = 'required|string|regex:/^PL\.\d{2}-\d{4}$/';
            }

            $validatedData = $request->validate($validationRules);

            $ledger = Ledger::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data ledger berhasil ditambahkan',
                'data' => $ledger
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

    public function edit(Ledger $ledger)
    {
        return response()->json($ledger->load('budget'));
    }

    public function update(Request $request, Ledger $ledger)
    {
        try {
            $category = $request->input('category');
            
            $validationRules = [
                'category' => 'required|in:' . implode(',', Ledger::getCategoryOptions()),
                'sub_budget' => 'required|in:' . implode(',', Ledger::getSubBudgetOptions()),
                'recipient' => 'required|in:' . implode(',', Ledger::getRecipientOptions()),
                'date' => 'required|date',
                'month' => 'required|string',
                'status' => 'required|in:' . implode(',', Ledger::getStatusOptions()),
                'debit' => 'required|numeric|min:0',
                'credit' => 'required|numeric|min:0'
            ];

            // Different validation for budget_id based on category
            if (in_array($category, [Ledger::CATEGORY_COST_PROJECT, Ledger::CATEGORY_REVENUE_PROJECT])) {
                $validationRules['budget_id'] = 'required|exists:projects,id';
            } else {
                $validationRules['budget_id'] = 'required|string|regex:/^PL\.\d{2}-\d{4}$/';
            }

            $validatedData = $request->validate($validationRules);

            $ledger->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data ledger berhasil diperbarui',
                'data' => $ledger
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

    public function destroy(Ledger $ledger)
    {
        try {
            $ledger->delete();
            return redirect()->route('ledger.index')->with('success', 'Data ledger berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('ledger.index')->with('error', 'Gagal menghapus data ledger');
        }
    }

    // New method to get budget options based on category
    public function getBudgetOptions(Request $request)
    {
        $category = $request->input('category');
        
        if (in_array($category, [Ledger::CATEGORY_COST_PROJECT, Ledger::CATEGORY_REVENUE_PROJECT])) {
            $options = Project::all()->map(function($project) {
                return [
                    'id' => $project->id,
                    'coa' => $project->coa
                ];
            });
        } else {
            $options = Ledger::getCoaOptions();
        }

        return response()->json($options);
    }
} 