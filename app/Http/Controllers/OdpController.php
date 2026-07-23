<?php

namespace App\Http\Controllers;

use App\Models\Odp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OdpController extends Controller
{
    /**
     * Tampilkan daftar ODP beserta form pendaftaran
     */
    public function index()
    {
        // Menghitung jumlah port terpakai langsung dari relasi customers
        $odps = Odp::withCount('customers')->latest()->paginate(15);

        return view('admin.odp.index', compact('odps'));
    }

    /**
     * Simpan Data ODP Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'            => 'required|string|max:50|unique:odps,code',
            'name'            => 'required|string|max:255',
            'capacity'        => 'required|integer|min:1|max:64',
            'address'         => 'required|string',
            'district'        => 'nullable|string|max:100',
            'village'         => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|string|max:500',
            'odp_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Upload Foto ODP jika ada
        if ($request->hasFile('odp_image')) {
            $validated['odp_image'] = $request->file('odp_image')->store('odps', 'public');
        }

        Odp::create($validated);

        return redirect()->back()->with('success', 'Terminal ODP baru berhasil didaftarkan.');
    }

    /**
     * Hapus Data ODP
     */
    public function destroy(Odp $odp)
    {
        // Proteksi: Jangan hapus jika ada pelanggan yang terhubung ke ODP ini
        if ($odp->customers()->count() > 0) {
            return redirect()->back()->with('error', "Gagal! ODP {$odp->name} masih digunakan oleh {$odp->customers()->count()} pelanggan.");
        }

        if ($odp->odp_image) {
            Storage::disk('public')->delete($odp->odp_image);
        }

        $odp->delete();

        return redirect()->back()->with('success', 'Data ODP berhasil dihapus.');
    }
}
