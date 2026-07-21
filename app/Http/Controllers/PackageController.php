<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\MikrotikServer;
use Illuminate\Http\Request;
use Exception;

class PackageController extends Controller
{
    /**
     * Tampilkan halaman daftar paket layanan
     */
    public function index()
    {
        // Query ringan menggunakan withCount untuk menghitung pelanggan aktif
        $packages = Package::withCount(['customers' => function ($query) {
            $query->where('status', 'active');
        }])->latest()->get();

        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Simpan Paket Baru ke Database Lokal & Sinkronkan ke PPP Profile MikroTik
     */
    public function store(Request $request)
    {
        // 1. Validasi Keamanan Data Input
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'price'            => ['required', 'numeric', 'min:0'],
            'speed_limit'      => ['required', 'regex:/^[0-9]+[kKMmGg]?\/[0-9]+[kKMmGg]?$/'],
            'mikrotik_profile' => ['required', 'string', 'max:255', 'alpha_dash'],
        ]);

        // Fallback nilai nullable untuk database
        $validated['mikrotik_profile_isolated'] = $validated['mikrotik_profile'];

        // 2. Simpan Data ke DB Lokal
        $package = Package::create($validated);

        // 3. Sinkronkan ke PPP Profile di Router MikroTik yang Terhubung
        $servers = MikrotikServer::where('status', 'connect')->get();
        $syncedServers = 0;
        $failedServers = 0;

        foreach ($servers as $server) {
            try {
                $client = $this->connectMikrotik($server);

                // Cek apakah PPP Profile sudah ada di MikroTik
                $checkQuery = new \RouterOS\Query('/ppp/profile/print');
                $checkQuery->where('name', $package->mikrotik_profile);
                $existing = $client->query($checkQuery)->read();

                // Jika belum ada, buat PPP Profile baru di MikroTik
                if (empty($existing)) {
                    $addQuery = new \RouterOS\Query('/ppp/profile/add');
                    $addQuery->equal('name', $package->mikrotik_profile);
                    $addQuery->equal('rate-limit', $package->speed_limit); // Format e.g. 10M/10M

                    $client->query($addQuery)->read();
                }

                $syncedServers++;
            } catch (Exception $e) {
                $failedServers++;
                // Log error jika diperlukan: \Log::error("Gagal sync profile ke router {$server->name}: " . $e->getMessage());
            }
        }

        $message = "Paket {$package->name} berhasil disimpan.";
        if ($syncedServers > 0) {
            $message .= " Dan disinkronkan ke {$syncedServers} router MikroTik.";
        }
        if ($failedServers > 0) {
            $message .= " (Gagal terhubung ke {$failedServers} router).";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Hapus Paket dari Database & Hapus PPP Profile dari MikroTik
     */
    public function destroy(Package $package)
    {
        // Proteksi Keamanan: Jangan hapus paket jika masih digunakan pelanggan
        if ($package->customers()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal! Paket ini masih digunakan oleh pelanggan aktif.');
        }

        $profileName = $package->mikrotik_profile;

        // 1. Hapus dari MikroTik (Jika Ada)
        $servers = MikrotikServer::where('status', 'connect')->get();
        foreach ($servers as $server) {
            try {
                $client = $this->connectMikrotik($server);

                $findQuery = new \RouterOS\Query('/ppp/profile/print');
                $findQuery->where('name', $profileName);
                $existing = $client->query($findQuery)->read();

                if (!empty($existing)) {
                    foreach ($existing as $item) {
                        $removeQuery = new \RouterOS\Query('/ppp/profile/remove');
                        $removeQuery->equal('.id', $item['.id']);
                        $client->query($removeQuery)->read();
                    }
                }
            } catch (Exception $e) {
                // Lanjutkan penghapusan lokal meskipun API router offline
            }
        }

        // 2. Hapus dari DB Lokal
        $package->delete();

        return redirect()->back()->with('success', "Paket layanan {$package->name} berhasil dihapus.");
    }

    /**
     * Helper Private untuk Koneksi API MikroTik (Ringan & Cepat)
     */
    private function connectMikrotik(MikrotikServer $server)
    {
        $config = new \RouterOS\Config([
            'host'    => $server->ip_address,
            'user'    => $server->username,
            'pass'    => $server->password,
            'port'    => (int) $server->api_port,
            'timeout' => 3, // Timeout pendek 3 detik agar web tidak hang/lemot
        ]);

        return new \RouterOS\Client($config);
    }
}
