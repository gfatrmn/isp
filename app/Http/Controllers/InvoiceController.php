<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    // 1. Menampilkan Daftar Invoice Pelanggan
    public function index()
    {
        $invoices = Invoice::with('customer.package')->latest()->get();
        return view('admin.invoices.index', compact('invoices'));
    }

    // 2. Memicu Pembuatan Invoice Bulanan Otomatis
    public function generateMonthlyInvoices()
    {
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        // Ambil semua pelanggan aktif
        $customers = Customer::where('status', '!=', 'inactive')->get();
        $generatedCount = 0;

        foreach ($customers as $customer) {
            // Cek apakah bulan ini pelanggan sudah di-generate tagihannya
            $existingInvoice = Invoice::where('customer_id', $customer->id)
                ->whereMonth('due_date', $currentMonth)
                ->whereYear('due_date', $currentYear)
                ->first();

            if (!$existingInvoice) {
                $dueDate = Carbon::create($currentYear, $currentMonth, $customer->billing_date);

                Invoice::create([
                    'customer_id' => $customer->id,
                    'invoice_number' => 'INV-' . $currentYear . $currentMonth . '-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
                    'amount' => $customer->package->price ?? 0,
                    'due_date' => $dueDate,
                    'status' => 'unpaid'
                ]);
                $generatedCount++;
            }
        }

        return redirect()->back()->with('success', "Berhasil membuat {$generatedCount} invoice tagihan baru untuk bulan ini.");
    }
}
