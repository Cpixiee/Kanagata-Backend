<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Logsheet;
use App\Models\Ledger;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Get latest projects
        $latestProjects = Project::latest()->take(5)->get();

        // Get latest logsheets
        $latestLogsheets = Logsheet::with('project')
            ->latest()
            ->take(3)
            ->get();

        // Get latest ledgers
        $latestLedgers = Ledger::latest()
            ->take(3)
            ->get();

        return view('dashboard', compact(
            'latestProjects',
            'latestLogsheets',
            'latestLedgers'
        ));
    }
} 