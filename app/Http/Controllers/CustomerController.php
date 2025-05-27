<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Logsheet;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        
        // Get unique customers from both AR and AP status
        $validCustomers = Logsheet::where(function($query) {
                $query->where('ar_status', 'Paid')
                      ->orWhere('ar_status', 'Listing')
                      ->orWhere('ap_status', 'Paid')
                      ->orWhere('ap_status', 'Listing');
            })
            ->distinct()
            ->pluck('customer')
            ->toArray();

        return view('customer', compact('customers', 'validCustomers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('customer-images', 'public');
            $data['image'] = $imagePath;
        }

        $customer = Customer::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => $customer
        ]);
    }

    public function getProjectSummary($customerName)
    {
        // Ambil semua project yang terkait dengan customer ini
        $projects = Project::where('customer', $customerName)->get();
        
        if ($projects->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No projects found for this customer',
                'data' => null
            ]);
        }

        // Hitung agregasi data langsung dari database
        $summary = [
            'customer_name' => $customerName,
            'total_projects' => $projects->count(),
            'total_gt_rev' => $projects->sum('gt_rev'),
            'total_gt_cost' => $projects->sum('gt_cost'),
            'total_gt_margin' => $projects->sum('gt_margin'),
            'total_sum_ar' => $projects->sum('sum_ar'),
            'total_ar_paid' => $projects->sum('ar_paid'),
            'total_ar_os' => $projects->sum('ar_os'),
            'total_sum_ap' => $projects->sum('sum_ap'),
            'total_ap_paid' => $projects->sum('ap_paid'),
            'total_ap_os' => $projects->sum('ap_os'),
            'total_todo' => $projects->sum('todo'),
            'total_ar_ap' => $projects->sum('ar_ap'),
            'projects_detail' => $projects->map(function($project) {
                return [
                    'id' => $project->id,
                    'coa' => $project->coa,
                    'activity' => $project->activity,
                    'prodi' => $project->prodi,
                    'grade' => $project->grade,
                    'gt_rev' => $project->gt_rev,
                    'gt_cost' => $project->gt_cost,
                    'gt_margin' => $project->gt_margin
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
} 