<?php

namespace App\Http\Controllers;

use App\Models\Odp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class OdpController extends Controller
{
    /**
     * Tampilkan daftar ODP dengan query ringan & ter-paginasi
     */
    public function index()
    {
        // Performa: Hanya select kolom yang dibutuhkan & gunakan withCount
        $odps = Odp::select([
                'id',
                'code',
                'name',
                'capacity',
                'address',
                'district',
                'village',
                'google_maps_url',
                'odp_image',
                'created_at'
            ])
            ->withCount('customers')
            ->latest()
            ->paginate(15);

        return view('admin.odp.index', compact('odps'));
    }

    /**
     * Simpan Data ODP Baru dengan Sanitasi Keamanan Ketat
     */
    public function store(Request $request)
    {
        // 1. Validasi Keamanan Data Input
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:50', 'alpha_dash', 'unique:odps,code'],
            'name'            => ['required', 'string', 'max:255'],
            'capacity'        => ['required', 'integer', 'in:8,16,24,32,64'],
            'address'         => ['required', 'string', 'max:1000'],
            'district'        => ['nullable', 'string', 'max:100'],
            'village'         => ['nullable', 'string', 'max:100'],
            'google_maps_url' => ['nullable', 'url', 'max:500'],
            'odp_image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maksimal 2MB
        ]);

        // 2. Sanitasi String dari XSS / Whitespace Berlebih
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['name'] = trim($validated['name']);
        $validated['address'] = trim($validated['address']);

        // 3. Handling Upload Gambar secara Aman (Randomized Filename)
        if ($request->hasFile('odp_image') && $request->file('odp_image')->isValid()) {
            $file = $request->file('odp_image');
            // Menghasilkan nama unik secara acak untuk keamanan web shell
            $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file->getClientOriginalExtension();
            $validated['odp_image'] = $file->storeAs('odps', $filename, 'public');
        }

        Odp::create($validated);

        return redirect()->back()->with('success', 'Terminal ODP baru berhasil didaftarkan.');
    }

    /**
     * Perbarui Data ODP
     */
    public function update(Request $request, Odp $odp)
    {
        // 1. Validasi Keamanan Data Input
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:50', 'alpha_dash', 'unique:odps,code,' . $odp->id],
            'name'            => ['required', 'string', 'max:255'],
            'capacity'        => ['required', 'integer', 'in:8,16,24,32,64'],
            'address'         => ['required', 'string', 'max:1000'],
            'district'        => ['nullable', 'string', 'max:100'],
            'village'         => ['nullable', 'string', 'max:100'],
            'google_maps_url' => ['nullable', 'url', 'max:500'],
            'odp_image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // 2. Sanitasi Input
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['name'] = trim($validated['name']);
        $validated['address'] = trim($validated['address']);

        // 3. Upload Gambar Baru & Hapus Berkas Lama
        if ($request->hasFile('odp_image') && $request->file('odp_image')->isValid()) {
            if ($odp->odp_image && Storage::disk('public')->exists($odp->odp_image)) {
                Storage::disk('public')->delete($odp->odp_image);
            }

            $file = $request->file('odp_image');
            $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file->getClientOriginalExtension();
            $validated['odp_image'] = $file->storeAs('odps', $filename, 'public');
        }

        $odp->update($validated);

        return redirect()->back()->with('success', 'Data ODP berhasil diperbarui.');
    }

    /**
     * Hapus Data ODP secara Aman (Atomic Transaction)
     */
    public function destroy(Odp $odp)
    {
        // Proteksi Keamanan: Mencegah penghapusan jika ada pelanggan terhubung
        if ($odp->customers()->count() > 0) {
            return redirect()->back()->with('error', "Gagal! ODP {$odp->name} masih digunakan oleh {$odp->customers()->count()} pelanggan.");
        }

        DB::beginTransaction();
        try {
            if ($odp->odp_image && Storage::disk('public')->exists($odp->odp_image)) {
                Storage::disk('public')->delete($odp->odp_image);
            }

            $odp->delete();
            DB::commit();

            return redirect()->back()->with('success', 'Data ODP berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menghapus data ODP.');
        }
    }
}
