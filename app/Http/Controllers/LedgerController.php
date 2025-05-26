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
        $ledgers = Ledger::orderBy('date', 'desc')->get();
        $projects = Project::orderBy('coa')->get();
        
        return view('ledger', compact('ledgers', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'budget' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|string',
            'debit' => 'required|numeric',
            'credit' => 'required|numeric',
            'description' => 'required|string'
        ]);

        try {
            Ledger::create($validated);
            return redirect()->route('ledger.index')->with('success', 'Data ledger berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('ledger.index')->with('error', 'Gagal menambahkan data ledger: ' . $e->getMessage());
        }
    }

    public function edit(Ledger $ledger)
    {
        return response()->json($ledger);
    }

    public function update(Request $request, Ledger $ledger)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'budget' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|string',
            'debit' => 'required|numeric',
            'credit' => 'required|numeric',
            'description' => 'required|string'
        ]);

        try {
            $ledger->update($validated);
            return redirect()->route('ledger.index')->with('success', 'Data ledger berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('ledger.index')->with('error', 'Gagal memperbarui data ledger: ' . $e->getMessage());
        }
    }

    public function destroy(Ledger $ledger)
    {
        try {
            $ledger->delete();
            return redirect()->route('ledger.index')->with('success', 'Data ledger berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('ledger.index')->with('error', 'Gagal menghapus data ledger: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Ledger $ledger)
    {
        try {
            if (!in_array($ledger->category, ['COST PROJECT', 'REVENUE PROJECT'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya ledger dengan kategori COST PROJECT dan REVENUE PROJECT yang dapat diubah statusnya'
                ], 400);
            }

            $ledger->status = 'PAID';
            $ledger->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah menjadi PAID'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
} 