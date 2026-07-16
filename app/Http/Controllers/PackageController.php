<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        // Menghitung jumlah pelanggan aktif per paket
        $packages = Package::withCount(['customers' => function($query) {
            $query->where('status', 'active');
        }])->get();

        return view('admin.packages.index', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'speed_limit' => 'required|string|regex:/^\d+[M|k]\/\d+[M|k]$/',
            'mikrotik_profile' => 'required|string|max:255',
            'mikrotik_profile_isolated' => 'required|string|max:255',
        ], [
            'speed_limit.regex' => 'Format speed limit harus Rx/Tx (Contoh: 10M/10M atau 512k/512k).'
        ]);

        Package::create($request->all());

        return redirect()->back()->with('success', 'Paket layanan berhasil didaftarkan.');
    }

    public function destroy(Package $package)
    {
        if ($package->customers()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal! Paket ini masih digunakan oleh pelanggan.');
        }

        $package->delete();
        return redirect()->back()->with('success', 'Paket layanan berhasil dihapus.');
    }
}
