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
}
