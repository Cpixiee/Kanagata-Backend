<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logsheet;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('invoice');
    }

    public function getData(Request $request)
    {
        try {
            // Ambil parameter filter
            $year = $request->get('year', date('Y'));
            $month = $request->get('month');
            
            // Query dasar untuk logsheet dengan filter tahun
            $query = Logsheet::with('project')->whereYear('created_at', $year);
            
            // Tambahkan filter bulan jika ada
            if ($month) {
                $query->whereMonth('created_at', $month);
            }
            
            $logsheets = $query->get();
            
            // 1. AR REV - Total revenue dari semua logsheet (quantity_1 * rate_1)
            $arRevenue = $logsheets->sum(function($logsheet) {
                return ($logsheet->quantity_1 ?? 0) * ($logsheet->rate_1 ?? 0);
            });
            
            // 2. AR Paid - Ambil dari project table field ar_paid
            $projectIds = $logsheets->pluck('project_id')->unique()->filter();
            $arPaid = 0;
            if ($projectIds->count() > 0) {
                $arPaid = Project::whereIn('id', $projectIds)->sum('ar_paid') ?? 0;
            }
            
            // 3. OS-AR - Outstanding AR (AR REV - AR PAID)
            $osAr = $arRevenue - $arPaid;
            
            // 4. AP COST - Total cost dari semua logsheet (quantity_2 * rate_2)
            $apCost = $logsheets->sum(function($logsheet) {
                return ($logsheet->quantity_2 ?? 0) * ($logsheet->rate_2 ?? 0);
            });
            
            // 5. AP PAID - Ambil dari project table field ap_paid
            $apPaid = 0;
            if ($projectIds->count() > 0) {
                $apPaid = Project::whereIn('id', $projectIds)->sum('ap_paid') ?? 0;
            }
            
            // 6. OS-AP - Outstanding AP (AP COST - AP PAID)
            $osAp = $apCost - $apPaid;
            
            // 7. MARGIN - Hitung dari logsheet (total revenue - total cost)
            $totalLogsheetRevenue = $logsheets->sum(function($logsheet) {
                return ($logsheet->quantity_1 ?? 0) * ($logsheet->rate_1 ?? 0);
            });
            
            $totalLogsheetCost = $logsheets->sum(function($logsheet) {
                return ($logsheet->quantity_2 ?? 0) * ($logsheet->rate_2 ?? 0);
            });
            
            $margin = $totalLogsheetRevenue - $totalLogsheetCost;
            
            // 8. AR-AP Balance
            $arApBalance = $osAr - $osAp;
            
            // Get AR Details
            $arDetails = $this->getARDetails($year, $month);
            
            // Get AP Details  
            $apDetails = $this->getAPDetails($year, $month);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'ar_revenue' => $arRevenue,
                    'ar_paid' => $arPaid,
                    'os_ar' => $osAr,
                    'ap_cost' => $apCost,
                    'ap_paid' => $apPaid,
                    'os_ap' => $osAp,
                    'margin' => $margin, // Now calculated from logsheet data
                    'ar_ap_balance' => $arApBalance,
                    'ar_details' => $arDetails,
                    'ap_details' => $apDetails
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in InvoiceController@getData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data invoice'
            ], 500);
        }
    }

    private function getARDetails($year, $month = null)
    {
        try {
            // Query untuk mendapatkan detail AR per project
            $query = DB::table('logsheets')
                ->join('projects', 'logsheets.project_id', '=', 'projects.id')
                ->select(
                    'projects.coa as project_name',
                    DB::raw('SUM(COALESCE(logsheets.quantity_1, 0) * COALESCE(logsheets.rate_1, 0)) as total_revenue'),
                    'projects.ar_paid as paid_amount',
                    DB::raw('SUM(COALESCE(logsheets.quantity_1, 0) * COALESCE(logsheets.rate_1, 0)) - COALESCE(projects.ar_paid, 0) as outstanding_amount')
                )
                ->whereYear('logsheets.created_at', $year);
                
            if ($month) {
                $query->whereMonth('logsheets.created_at', $month);
            }
            
            $results = $query->groupBy('projects.id', 'projects.coa', 'projects.ar_paid')
                        ->having('total_revenue', '>', 0)
                        ->orderBy('total_revenue', 'desc')
                        ->get();
                        
            return $results->map(function($item) {
                return [
                    'project_name' => $item->project_name,
                    'total_revenue' => round($item->total_revenue, 0),
                    'paid_amount' => round($item->paid_amount ?? 0, 0),
                    'outstanding_amount' => round($item->outstanding_amount ?? 0, 0)
                ];
            })->toArray();
                        
        } catch (\Exception $e) {
            Log::error('Error in getARDetails: ' . $e->getMessage());
            return [];
        }
    }

    private function getAPDetails($year, $month = null)
    {
        try {
            // Query untuk mendapatkan detail AP per project
            $query = DB::table('logsheets')
                ->join('projects', 'logsheets.project_id', '=', 'projects.id')
                ->select(
                    'projects.coa as project_name',
                    DB::raw('SUM(COALESCE(logsheets.quantity_2, 0) * COALESCE(logsheets.rate_2, 0)) as total_cost'),
                    'projects.ap_paid as paid_amount',
                    DB::raw('SUM(COALESCE(logsheets.quantity_2, 0) * COALESCE(logsheets.rate_2, 0)) - COALESCE(projects.ap_paid, 0) as outstanding_amount')
                )
                ->whereYear('logsheets.created_at', $year);
                
            if ($month) {
                $query->whereMonth('logsheets.created_at', $month);
            }
            
            $results = $query->groupBy('projects.id', 'projects.coa', 'projects.ap_paid')
                        ->having('total_cost', '>', 0)
                        ->orderBy('total_cost', 'desc')
                        ->get();
                        
            return $results->map(function($item) {
                return [
                    'project_name' => $item->project_name,
                    'total_cost' => round($item->total_cost, 0),
                    'paid_amount' => round($item->paid_amount ?? 0, 0),
                    'outstanding_amount' => round($item->outstanding_amount ?? 0, 0)
                ];
            })->toArray();
                        
        } catch (\Exception $e) {
            Log::error('Error in getAPDetails: ' . $e->getMessage());
            return [];
        }
    }

    public function getProjectSummary(Request $request, $projectId = null)
    {
        try {
            $year = $request->get('year', date('Y'));
            $month = $request->get('month');
            
            $query = Logsheet::with('project');
            
            if ($projectId) {
                $query->where('project_id', $projectId);
            }
            
            $query->whereYear('created_at', $year);
            
            if ($month) {
                $query->whereMonth('created_at', $month);
            }
            
            $logsheets = $query->get();
            
            $summary = $logsheets->groupBy('project_id')->map(function ($projectLogsheets) {
                $project = $projectLogsheets->first()->project;
                
                $totalRevenue = $projectLogsheets->sum(function($l) { 
                    return ($l->quantity_1 ?? 0) * ($l->rate_1 ?? 0); 
                });
                
                $totalCost = $projectLogsheets->sum(function($l) { 
                    return ($l->quantity_2 ?? 0) * ($l->rate_2 ?? 0); 
                });
                
                // Calculate margin from logsheet data (revenue - cost)
                $margin = $totalRevenue - $totalCost;
                
                return [
                    'project_name' => $project ? $project->coa : 'Unknown',
                    'total_revenue' => $totalRevenue,
                    'total_cost' => $totalCost,
                    'margin' => $margin, // Now calculated from logsheet data
                    'entry_count' => $projectLogsheets->count()
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $summary->values()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getProjectSummary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil ringkasan project'
            ], 500);
        }
    }

    public function getMonthlyData(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            $monthlyData = [];
            
            for ($month = 1; $month <= 12; $month++) {
                $logsheets = Logsheet::whereYear('created_at', $year)
                                   ->whereMonth('created_at', $month)
                                   ->get();
                
                $monthlyData[] = [
                    'month' => $month,
                    'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                    'ar_revenue' => $logsheets->sum(function($l) { 
                        return ($l->quantity_1 ?? 0) * ($l->rate_1 ?? 0); 
                    }),
                    'ap_cost' => $logsheets->sum(function($l) { 
                        return ($l->quantity_2 ?? 0) * ($l->rate_2 ?? 0); 
                    }),
                    'entry_count' => $logsheets->count()
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $monthlyData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data bulanan'
            ], 500);
        }
    }
} 