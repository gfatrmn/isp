<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MikrotikServer;
use Illuminate\Http\Request;
use Exception;

class MikrotikIsolirController extends Controller
{
    /**
     * Tampilkan halaman status isolir pelanggan
     */
    public function index()
    {
        $customers = Customer::with('mikrotikServer')->latest()->paginate(15);
        $servers = MikrotikServer::where('status', 'connect')->get();

        // PERBAIKAN DI SINI: panggil isolir.index
        return view('isolir.index', compact('customers', 'servers'));
    }

    /**
     * Eksekusi Isolir Pelanggan (Tambah ke Address List Mikrotik)
     */
    public function isolir(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);

        if (empty($customer->ip_address)) {
            return redirect()->back()->with('error', "Gagal isolir: Pelanggan {$customer->name} belum memiliki IP Address static!");
        }

        $server = MikrotikServer::find($customer->mikrotik_server_id);
        if (!$server) {
            return redirect()->back()->with('error', "Gagal isolir: Server Mikrotik tidak ditemukan.");
        }

        try {
            $client = $this->connectMikrotik($server);

            // Cek apakah IP sudah ada di address-list
            $printRequest = new \RouterOS\Query('/ip/firewall/address-list/print');
            $printRequest->where('list', 'ISOLIR_PELANGGAN');
            $printRequest->where('address', $customer->ip_address);
            $existing = $client->query($printRequest)->read();

            if (empty($existing)) {
                $addQuery = new \RouterOS\Query('/ip/firewall/address-list/add');
                $addQuery->equal('list', 'ISOLIR_PELANGGAN');
                $addQuery->equal('address', $customer->ip_address);
                $addQuery->equal('comment', "ISOLIR - {$customer->name}");

                $client->query($addQuery)->read();
            } else {
                return redirect()->back()->with('error', "Gagal isolir: IP Address {$customer->ip_address} sudah ada di address-list.");
            }

            $customer->update(['status' => 'isolated']);

            return redirect()->back()->with('success', "Pelanggan {$customer->name} ({$customer->ip_address}) berhasil DIISOLIR!");
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Error API Mikrotik: " . $e->getMessage());
        }
    }

    /**
     * Eksekusi Buka Isolir Pelanggan (Hapus dari Address List Mikrotik)
     */
    public function bukaIsolir(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);

        if (empty($customer->ip_address)) {
            return redirect()->back()->with('error', "Gagal buka isolir: IP Address pelanggan tidak terdaftar!");
        }

        $server = MikrotikServer::find($customer->mikrotik_server_id);
        if (!$server) {
            return redirect()->back()->with('error', "Gagal buka isolir: Server Mikrotik tidak terhubung.");
        }

        try {
            $client = $this->connectMikrotik($server);

            $printRequest = new \RouterOS\Query('/ip/firewall/address-list/print');
            $printRequest->where('list', 'ISOLIR_PELANGGAN');
            $printRequest->where('address', $customer->ip_address);
            $existing = $client->query($printRequest)->read();

            if (!empty($existing)) {
                foreach ($existing as $item) {
                    // PERBAIKAN DI SINI: Gunakan Query dan equal()
                    $removeQuery = new \RouterOS\Query('/ip/firewall/address-list/remove');
                    $removeQuery->equal('.id', $item['.id']);

                    $client->query($removeQuery)->read();
                }
            } else {
                return redirect()->back()->with('error', "Gagal buka isolir: IP Address {$customer->ip_address} tidak ditemukan di address-list.");
            }

            $customer->update(['status' => 'active']);

            return redirect()->back()->with('success', "Isolir pelanggan {$customer->name} ({$customer->ip_address}) berhasil DIBUKA!");
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Error API Mikrotik: " . $e->getMessage());
        }
    }

    private function connectMikrotik(MikrotikServer $server)
    {
        $config = new \RouterOS\Config([
            'host' => $server->ip_address,
            'user' => $server->username,
            'pass' => $server->password,
            'port' => (int) $server->api_port,
            'timeout' => 5,
        ]);

        return new \RouterOS\Client($config);
    }
}
