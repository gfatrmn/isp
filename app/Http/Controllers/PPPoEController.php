<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\PppoeAccount;
use App\Models\MikrotikServer;
use App\Models\Package;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;

class PPPoEController extends Controller
{
    // 1. Menampilkan Halaman Utama PPPoE & Daftar Akun Terhubung
    public function index()
    {
        $customers = Customer::with(['pppoeAccount.mikrotikServer', 'package'])->get();

        // Hanya mengambil server Mikrotik yang statusnya terkoneksi (Connected)
        $servers = MikrotikServer::where('status', 'connect')->get();

        // Mengambil semua pelanggan yang BELUM mempunyai akun PPPoE untuk opsi pendaftaran
        $availableCustomers = Customer::doesntHave('pppoeAccount')->get();

        $packages = Package::all();

        return view('admin.pppoe.index', compact('customers', 'servers', 'availableCustomers', 'packages'));
    }

    // 2. Mendaftarkan Akun PPPoE Baru (Laravel Lokal -> Push ke Mikrotik)
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'mikrotik_server_id' => 'required|exists:mikrotik_servers,id',
            'username' => 'required|string|unique:pppoe_accounts,username',
            'password' => 'required|string',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $server = MikrotikServer::findOrFail($request->mikrotik_server_id);
        $package = $customer->package;

        if (!$package) {
            return redirect()->back()->with('error', 'Pelanggan ini belum memilih paket layanan internet.');
        }

        try {
            $client = new Client((new Config())
                ->set('host', $server->ip_address)
                ->set('user', $server->username)
                ->set('pass', $server->password)
                ->set('port', (int) $server->api_port)
                ->set('timeout', 3)
            );

            // Push data Secret Baru ke Mikrotik
            $query = (new Query('/ppp/secret/add'))
                ->equal('name', $request->username)
                ->equal('password', $request->password)
                ->equal('service', 'pppoe')
                ->equal('profile', $package->mikrotik_profile)
                ->equal('comment', 'Pelanggan: ' . $customer->name);

            $client->query($query)->read();

            // Simpan ke database lokal Laravel jika sukses push ke router
            PppoeAccount::create([
                'customer_id' => $customer->id,
                'mikrotik_server_id' => $server->id,
                'username' => $request->username,
                'password' => $request->password,
            ]);

            return redirect()->back()->with('success', "Akun PPPoE untuk {$customer->name} berhasil dibuat secara realtime di Mikrotik!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal push ke Mikrotik: " . $e->getMessage());
        }
    }

    // 3. Menarik Data Akun Lama dari Mikrotik (Tanpa Proteksi Pembatalan Paket)
    public function syncFromMikrotik($serverId)
    {
        $server = MikrotikServer::findOrFail($serverId);

        try {
            $client = new Client((new Config())
                ->set('host', $server->ip_address)
                ->set('user', $server->username)
                ->set('pass', $server->password)
                ->set('port', (int) $server->api_port)
                ->set('timeout', 5)
            );

            // Query mengambil seluruh database PPP Secret dari Mikrotik
            $query = new Query('/ppp/secret/print');
            $secrets = $client->query($query)->read();

            $importedCount = 0;

            foreach ($secrets as $secret) {
                // Filter hanya service pppoe atau all
                if (isset($secret['name']) && isset($secret['service']) && ($secret['service'] === 'pppoe' || $secret['service'] === 'all')) {

                    $username = $secret['name'];
                    $password = $secret['password'] ?? '123456';
                    $profileName = $secret['profile'] ?? 'default';

                    // Cek apakah username PPPoE ini sudah ada di database lokal agar tidak duplikat
                    $existingAccount = PppoeAccount::where('username', $username)->first();

                    if (!$existingAccount) {
                        // Cari paket lokal yang cocok
                        $package = Package::where('mikrotik_profile', $profileName)->first();

                        // JIKA BELUM ADA PAKET SAMA SEKALI, BUAT OTOMATIS DI SINI (PROTEKSI DIHILANGKAN)
                        if (!$package) {
                            $package = Package::firstOrCreate(
                                ['mikrotik_profile' => $profileName],
                                [
                                    'name' => 'Paket ' . $profileName,
                                    'price' => 0, // Set 0 dulu, bisa diedit nanti
                                    'speed_limit' => '10M/10M',
                                    'mikrotik_profile_isolated' => 'ISOLIR'
                                ]
                            );
                        }

                        // Buat data fisik Pelanggan otomatis
                        $customer = Customer::create([
                            'customer_number' => 'CUST-SYNC-' . strtoupper(uniqid()),
                            'name' => 'Pelanggan ' . $username,
                            'phone' => '081200000000',
                            'address' => 'Diimport otomatis dari Mikrotik: ' . $server->name,
                            'package_id' => $package->id,
                            'billing_date' => 5,
                            'status' => 'active'
                        ]);

                        // Simpan akun PPPoE ke database lokal
                        PppoeAccount::create([
                            'customer_id' => $customer->id,
                            'mikrotik_server_id' => $server->id,
                            'username' => $username,
                            'password' => $password,
                        ]);

                        $importedCount++;
                    }
                }
            }

            return redirect()->back()->with('success', "Sinkronisasi Berhasil! {$importedCount} akun PPPoE dari Mikrotik sukses ditarik ke dalam sistem web.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal sinkronisasi data: " . $e->getMessage());
        }
    }
}
