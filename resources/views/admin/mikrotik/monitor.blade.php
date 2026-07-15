@extends('layouts.app')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-4 mb-6 gap-4">
    <div>
        <a href="{{ route('mikrotik.index') }}" class="text-xs text-blue-600 hover:underline font-bold">← Kembali ke Server</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">Live Bandwidth Monitor</h1>
        <p class="text-xs text-gray-400 font-mono">Router: {{ $server->name }} | IP: {{ $server->ip_address }}</p>
    </div>
    <div class="w-full sm:w-auto">
        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Pilih Interface</label>
        <select id="interfaceSelect" class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-lg text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
            <option value="ether1" selected>ether1</option>
            <option value="ether2">ether2</option>
            <option value="pppoe-out1">pppoe-out1</option>
        </select>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-l-4 border-l-blue-600 flex justify-between items-center">
        <div>
            <span class="text-xs uppercase font-bold text-gray-400">Download Speed (RX)</span>
            <h2 id="downloadText" class="text-3xl font-extrabold text-blue-600 mt-1">0.00 Mbps</h2>
        </div>
        <div class="text-xl font-bold text-blue-200">↓</div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-l-4 border-l-emerald-500 flex justify-between items-center">
        <div>
            <span class="text-xs uppercase font-bold text-gray-400">Upload Speed (TX)</span>
            <h2 id="uploadText" class="text-3xl font-extrabold text-emerald-600 mt-1">0.00 Mbps</h2>
        </div>
        <div class="text-2xl font-bold text-emerald-200">↑</div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
    <div class="h-96 w-full relative">
        <canvas id="trafficChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('trafficChart');
        if (!ctx) return;

        const maxDataLength = 15;
        const trafficChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: Array(maxDataLength).fill(''),
                datasets: [
                    { label: 'Download (RX)', borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, 0.02)', data: Array(maxDataLength).fill(0), fill: true, tension: 0.3, borderWidth: 2 },
                    { label: 'Upload (TX)', borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.02)', data: Array(maxDataLength).fill(0), fill: true, tension: 0.3, borderWidth: 2 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        function loadLiveTraffic() {
            const intf = document.getElementById('interfaceSelect').value;
            fetch(`/mikrotik/{{ $server->id }}/traffic?interface=${intf}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const rx = (data.download / 1000000).toFixed(2);
                        const tx = (data.upload / 1000000).toFixed(2);

                        document.getElementById('downloadText').innerText = rx + " Mbps";
                        document.getElementById('uploadText').innerText = tx + " Mbps";

                        const timeLabel = new Date().toTimeString().split(' ')[0];
                        trafficChart.data.labels.push(timeLabel); trafficChart.data.labels.shift();
                        trafficChart.data.datasets[0].data.push(parseFloat(rx)); trafficChart.data.datasets[0].data.shift();
                        trafficChart.data.datasets[1].data.push(parseFloat(tx)); trafficChart.data.datasets[1].data.shift();
                        trafficChart.update('none');
                    }
                });
        }
        setInterval(loadLiveTraffic, 2000);
        loadLiveTraffic();
    });
</script>
@endsection
