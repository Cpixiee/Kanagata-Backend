<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Logsheet;
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
} 