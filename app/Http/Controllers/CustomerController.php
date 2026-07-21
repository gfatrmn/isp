<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Odp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar pelanggan dengan Pagination & Eager Loading teroptimasi.
     */
    public function index()
    {
        // PERFORMA:
        // 1. hanya select kolom yang dibutuhkan dari relasi untuk menghemat memory.
        // 2. paginate(15) agar query ringan.
        $customers = Customer::with([
            'package:id,name,price',
            'odp:id,name'
        ])
        ->select([
            'id', 'customer_number', 'name', 'nik', 'phone', 'email',
            'package_id', 'odp_id', 'billing_date', 'address',
            'district', 'village', 'google_maps_url', 'ktp_image',
            'house_image', 'status', 'created_at'
        ])
        ->latest()
        ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Menampilkan halaman form pendaftaran pelanggan baru.
     */
    public function create()
    {
        // PERFORMA: Ambil hanya ID & Nama dari paket dan ODP
        $packages = Package::select('id', 'name', 'price')->get();
        $odps = class_exists(Odp::class) ? Odp::select('id', 'name')->get() : collect();

        return view('admin.customers.create', compact('packages', 'odps'));
    }

    /**
     * Menyimpan data pelanggan baru ke database.
     */
    public function store(Request $request)
    {
        // KEAMANAN & VALIDASI STRIKTIF:
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'nik'             => 'nullable|string|numeric|digits:16', // Memastikan NIK 16 digit angka
            'phone'           => 'required|string|max:15|regex:/^[0-9+\s-]+$/', // Validasi format telepon/WA
            'email'           => 'nullable|email:dns|max:255', // Check ketersediaan domain email
            'package_id'      => 'required|exists:packages,id',
            'odp_id'          => 'nullable|exists:odps,id',
            'billing_date'    => 'required|integer|between:1,28',
            'address'         => 'required|string|max:500',
            'district'        => 'nullable|string|max:100',
            'village'         => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|url|max:500',
            // KEAMANAN FILE: Validasi ekstensi & batasan ukuran (Max 2MB)
            'ktp_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'house_image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $ktpPath = null;
        $housePath = null;

        // DAN KONTROL TRANSAKSI (DB Transaction):
        // Memastikan jika error di tengah jalan, berkas/data tidak menggantung.
        try {
            DB::beginTransaction();

            // 1. Upload Gambar KTP (Aman dari overwrite nama file)
            if ($request->hasFile('ktp_image')) {
                $ktpPath = $request->file('ktp_image')->store('customers/ktp', 'public');
                $validated['ktp_image'] = $ktpPath;
            }

            // 2. Upload Gambar Rumah
            if ($request->hasFile('house_image')) {
                $housePath = $request->file('house_image')->store('customers/house', 'public');
                $validated['house_image'] = $housePath;
            }

            // 3. Autogenerate Nomor Pelanggan (Lock row untuk cegah duplicate number saat high-concurrency)
            $yearMonth = Carbon::now()->format('Ym');
            $lastCustomer = Customer::where('customer_number', 'LIKE', "CUST-{$yearMonth}-%")
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $nextIncrement = $lastCustomer ? str_pad(((int) substr($lastCustomer->customer_number, -4)) + 1, 4, '0', STR_PAD_LEFT) : '0001';

            $validated['customer_number'] = "CUST-{$yearMonth}-{$nextIncrement}";
            $validated['status'] = 'active';

            // 4. Simpan ke Database
            Customer::create($validated);

            DB::commit();

            return redirect()->route('admin.customers.index')->with('success', 'Pelanggan baru berhasil didaftarkan.');

        } catch (\Exception $e) {
            DB::rollBack();

            // KEAMANAN & PEMBERSIHAN FILE: Hapus file terupload jika DB insert gagal
            if ($ktpPath && Storage::disk('public')->exists($ktpPath)) {
                Storage::disk('public')->delete($ktpPath);
            }
            if ($housePath && Storage::disk('public')->exists($housePath)) {
                Storage::disk('public')->delete($housePath);
            }

            Log::error('Error Store Customer: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan data pelanggan: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus data pelanggan dan file gambar terkait.
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        try {
            DB::transaction(function () use ($customer) {
                // Hapus berkas gambar dari storage
                if ($customer->ktp_image && Storage::disk('public')->exists($customer->ktp_image)) {
                    Storage::disk('public')->delete($customer->ktp_image);
                }

                if ($customer->house_image && Storage::disk('public')->exists($customer->house_image)) {
                    Storage::disk('public')->delete($customer->house_image);
                }

                $customer->delete();
            });

            return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error Delete Customer: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus data pelanggan.']);
        }
    }
}
