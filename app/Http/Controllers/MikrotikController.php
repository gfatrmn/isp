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
    public function index()
    {
        $servers = MikrotikServer::all();
        return view('admin.mikrotik.index', compact('servers'));
    }

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
            'status' => 'disconnect'
        ]);

        return redirect()->back()->with('success', 'Router berhasil ditambahkan!');
    }

    public function testConnect($id)
    {
        $server = MikrotikServer::findOrFail($id);

        try {
            $client = new Client((new Config())
                    ->set('host', $server->ip_address)
                    ->set('user', $server->username)
                    ->set('pass', $server->password)
                    ->set('port', (int) $server->api_port)
                    ->set('timeout', 3)
            );

            $query = new Query('/system/identity/print');
            $client->query($query)->read();

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

    public function monitor($id)
    {
        $server = MikrotikServer::findOrFail($id);
        if ($server->status !== 'connect') {
            return redirect()->route('mikrotik.index')->with('error', 'Router dalam status terputus.');
        }
        return view('admin.mikrotik.monitor', compact('server'));
    }

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

            $query = (new Query('/interface/monitor-traffic'))
                ->equal('interface', $interface)
                ->equal('once', '');

            $response = $client->query($query)->read();

            if (!empty($response) && isset($response[0])) {
                $data = $response[0];
                return response()->json([
                    'success' => true,
                    'upload'  => (int) ($data['tx-bits-per-second'] ?? 0),
                    'download' => (int) ($data['rx-bits-per-second'] ?? 0),
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Tidak ada data.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $server = MikrotikServer::findOrFail($id);
        $server->delete();
        return redirect()->back()->with('success', 'Router berhasil dihapus.');
    }

    public function monitoring()
    {
        $routers = MikrotikServer::where('status', 'connect')->get();
        return view('admin.mikrotik.monitoring', compact('routers'));
    }

    // ENDPOINT UTAMA KHUSUS TRAFIK RAW BITS PER INTERFACE ACTIVE
    public function getSystemStatusRealtime(Request $request, $id)
    {
        $server = MikrotikServer::findOrFail($id);

        try {
            $client = new Client((new Config())
                    ->set('host', $server->ip_address)
                    ->set('user', $server->username)
                    ->set('pass', $server->password)
                    ->set('port', (int) $server->api_port)
                    ->set('timeout', 2)
            );

            // Deteksi interface pertama yang running (UP) DAN BUKAN loopback/pppoe-binding
            $interfaceQuery = new Query('/interface/print');
            $interfaceResponse = $client->query($interfaceQuery)->read();

            $targetInterface = null;
            foreach ($interfaceResponse as $iface) {
                $isUp = isset($iface['running']) &&
                        ($iface['running'] === 'true' || $iface['running'] === true || $iface['running'] === 'yes');

                $type = $iface['type'] ?? '';
                $name = $iface['name'] ?? '';

                // Skip loopback & pppoe-server-binding, kita mau interface fisik/uplink asli
                $isExcluded = $type === 'loopback'
                            || $type === 'pppoe-in'
                            || str_contains($name, '<pppoe')
                            || $name === 'lo';

                if ($isUp && !$isExcluded) {
                    $targetInterface = $name;
                    break;
                }
            }

            // Fallback kalau semua interface ke-skip / gak ketemu
            if (!$targetInterface) {
                $targetInterface = 'ether1';
            }

            // Ambil monitor-traffic dari target interface aktif
            $trafficQuery = (new Query('/interface/monitor-traffic'))
                ->equal('interface', $targetInterface)
                ->equal('once', '');
            $trafficResponse = $client->query($trafficQuery)->read();

            $tx_bps = 0;
            $rx_bps = 0;

            if (!empty($trafficResponse) && isset($trafficResponse[0])) {
                $tx_bps = $trafficResponse[0]['tx-bits-per-second'] ?? $trafficResponse[0]['tx-bps'] ?? 0;
                $rx_bps = $trafficResponse[0]['rx-bits-per-second'] ?? $trafficResponse[0]['rx-bps'] ?? 0;
            }

            return response()->json([
                'success' => true,
                'upload' => (int) $tx_bps,
                'download' => (int) $rx_bps,
                'monitored_interface' => $targetInterface
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung: ' . $e->getMessage()
            ]);
        }
    }
}
