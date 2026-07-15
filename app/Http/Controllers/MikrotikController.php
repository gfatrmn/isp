<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MikrotikServer;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;
use RouterOS\Exceptions\ClientException;

class MikrotikController extends Controller
{
    // 1. Menampilkan Halaman Daftar Router & Form Tambah
    public function index()
    {
        $servers = MikrotikServer::all();
        return view('admin.mikrotik.index', compact('servers'));
    }

    // 2. Menyimpan Data Router Baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'api_port' => 'required|integer',
        ]);

        MikrotikServer::create([
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'username' => $request->username,
            'password' => $request->password ?? '',
            'api_port' => $request->api_port,
            'status' => 'disconnect' // Default awal
        ]);

        return redirect()->back()->with('success', 'Router berhasil ditambahkan!');
    }

    // 3. Mengetes Koneksi API ke Mikrotik (System Identity)
    public function testConnect($id)
    {
        $server = MikrotikServer::findOrFail($id);

        try {
            // Konfigurasi koneksi menggunakan package RouterOS\Client
            $client = new Client((new Config())
                ->set('host', $server->ip_address)
                ->set('user', $server->username)
                ->set('pass', $server->password)
                ->set('port', (int) $server->api_port)
                ->set('timeout', 3)
            );

            // Cek identity mikrotik sebagai test read
            $query = new Query('/system/identity/print');
            $client->query($query)->read();

            // Update status jika sukses
            $server->update(['status' => 'connect']);
            return redirect()->back()->with('success', "Koneksi ke {$server->name} Berhasil (Connected)!");

        } catch (ClientException $e) {
            $server->update(['status' => 'disconnect']);
            return redirect()->back()->with('error', "Koneksi Gagal (Client Error): " . $e->getMessage());
        } catch (\Exception $e) {
            $server->update(['status' => 'disconnect']);
            return redirect()->back()->with('error', "Koneksi Gagal: " . $e->getMessage());
        }
    }

    // 4. Menampilkan Halaman Grafik Live Monitor Bandwidth
    public function monitor($id)
    {
        $server = MikrotikServer::findOrFail($id);

        // Proteksi jika dicoba buka manual saat statusnya sedang terputus
        if ($server->status !== 'connect') {
            return redirect()->route('mikrotik.index')->with('error', 'Router dalam status terputus. Silakan cek koneksi terlebih dahulu.');
        }

        return view('admin.mikrotik.monitor', compact('server'));
    }

    // 5. API Endpoint JSON untuk Menyuplai Data ke Chart.js (Realtime 2 Detik)
    public function getTrafficRealtime(Request $request, $id)
    {
        $interface = $request->get('interface', 'ether1');
        $server = MikrotikServer::findOrFail($id);

        try {
            $client = new Client((new Config())
                ->set('host', $server->ip_address)
                ->set('user', $server->username)
                ->set('pass', $server->password)
                ->set('port', (int) $server->api_port)
                ->set('timeout', 2)
            );

            // Menjalankan command monitor-traffic Mikrotik
            $query = (new Query('/interface/monitor-traffic'))
                ->equal('interface', $interface)
                ->equal('once', ''); // Mengambil 1 snapshot data saja

            $response = $client->query($query)->read();

            if (!empty($response) && isset($response[0])) {
                $data = $response[0];
                return response()->json([
                    'success' => true,
                    'upload'  => (int) ($data['tx-bits-per-second'] ?? 0),
                    'download'=> (int) ($data['rx-bits-per-second'] ?? 0),
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dikembalikan oleh router.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 6. Menghapus Data Router dari Sistem
    public function destroy($id)
    {
        $server = MikrotikServer::findOrFail($id);
        $server->delete();
        return redirect()->back()->with('success', 'Router berhasil dihapus dari sistem.');
    }
}
