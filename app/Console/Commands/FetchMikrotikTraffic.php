<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MikrotikServer;
use App\Events\MikrotikTrafficUpdated;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;

class FetchMikrotikTraffic extends Command
{
    protected $signature = 'mikrotik:fetch-traffic';
    protected $description = 'Fetch real-time traffic from connected Mikrotik servers and broadcast via Reverb';

    public function handle()
    {
        $this->info('Starting Mikrotik Traffic Fetcher daemon...');

        while (true) {
            $servers = MikrotikServer::where('status', 'connect')->get();

            foreach ($servers as $server) {
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

                        $isExcluded = $type === 'loopback'
                                    || $type === 'pppoe-in'
                                    || str_contains($name, '<pppoe')
                                    || $name === 'lo';

                        if ($isUp && !$isExcluded) {
                            $targetInterface = $name;
                            break;
                        }
                    }

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

                    // Dispatch Event Reverb ke WebSocket
                    MikrotikTrafficUpdated::dispatch(
                        $server->id,
                        $tx_bps,
                        $rx_bps,
                        $targetInterface
                    );

                } catch (\Exception $e) {
                    $this->error("Error fetching router {$server->name}: " . $e->getMessage());
                }
            }

            // Tahan delay 1 detik sebelum iterasi berikutnya
            sleep(1);
        }
    }
}
