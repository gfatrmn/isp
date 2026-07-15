<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['package', 'pppoeAccount'])->get();
        $packages = Package::all();
        return view('admin.customers.index', compact('customers', 'packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'billing_date' => 'required|integer|between:1|28',
        ]);

        $yearMonth = Carbon::now()->format('Ym');
        $lastCustomer = Customer::where('customer_number', 'LIKE', "CUST-{$yearMonth}-%")->latest()->first();
        $nextIncrement = $lastCustomer ? str_pad(((int) substr($lastCustomer->customer_number, -4)) + 1, 4, '0', STR_PAD_LEFT) : '0001';
        $customerNumber = "CUST-{$yearMonth}-{$nextIncrement}";

        Customer::create([
            'customer_number' => $customerNumber,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'package_id' => $request->package_id,
            'billing_date' => $request->billing_date,
            'status' => 'active'
        ]);

        return redirect()->back()->with('success', 'Pelanggan baru berhasil didaftarkan.');
    }

    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data pelanggan dihapus.');
    }
}
