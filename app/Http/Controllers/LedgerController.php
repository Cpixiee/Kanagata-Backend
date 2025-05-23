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
        return view('ledger', compact('ledgers', 'projects'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'category' => 'required|in:' . implode(',', Ledger::getCategoryOptions()),
                'budget_id' => 'required|exists:projects,id',
                'sub_budget' => 'required|in:' . implode(',', Ledger::getSubBudgetOptions()),
                'recipient' => 'required|in:' . implode(',', Ledger::getRecipientOptions()),
                'date' => 'required|date',
                'month' => 'required|string',
                'status' => 'required|in:' . implode(',', Ledger::getStatusOptions()),
                'debit' => 'required|numeric|min:0',
                'credit' => 'required|numeric|min:0'
            ]);

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
            $validatedData = $request->validate([
                'category' => 'required|in:' . implode(',', Ledger::getCategoryOptions()),
                'budget_id' => 'required|exists:projects,id',
                'sub_budget' => 'required|in:' . implode(',', Ledger::getSubBudgetOptions()),
                'recipient' => 'required|in:' . implode(',', Ledger::getRecipientOptions()),
                'date' => 'required|date',
                'month' => 'required|string',
                'status' => 'required|in:' . implode(',', Ledger::getStatusOptions()),
                'debit' => 'required|numeric|min:0',
                'credit' => 'required|numeric|min:0'
            ]);

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
} 