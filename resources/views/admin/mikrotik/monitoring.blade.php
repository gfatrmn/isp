@extends('layouts.app')

@section('content')
<div class="mb-6 border-b pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Monitoring Infrastruktur</h1>
        <p class="text-xs text-gray-500">Pantau fluktuasi trafik bandwidth secara realtime.</p>
    </div>

    <div class="w-full sm:w-72">
        <label class="block text-xxs font-bold uppercase text-gray-400 mb-0.5">Pilih Perangkat Router</label>
        <select id="routerSelect" class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs text-gray-700 font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach($routers as $router)
                <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->ip_address }})</option>
            @endforeach
            @if($routers->isEmpty())
                <option value="">-- Tidak ada router berstatus Online --</option>
            @endif
        </select>
    </div>
</div>

<!-- CONTAINER GRAFIK UTUH HANYA BANDWIDTH -->
<div class="w-full bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
    <h2 class="text-xs font-bold text-gray-800 mb-4 uppercase tracking-wider border-b pb-2 flex items-center justify-between">
        <span>📉 Grafik Bandwidth Realtime</span>
        <span id="interfaceBadge" class="text-xxs font-mono bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded border border-blue-200 hidden"></span>
    </h2>
    <div class="h-64 relative">
        <canvas id="bandwidthChart"></canvas>
    </div>
    <p id="debugStatus" class="text-xxs text-gray-400 mt-2 font-mono"></p>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('bandwidthChart').getContext('2d');
        const routerSelect = document.getElementById('routerSelect');
        const interfaceBadge = document.getElementById('interfaceBadge');
        const debugStatus = document.getElementById('debugStatus');

        const bandwidthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Download (RX) dalam kbps',
                        data: [],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 1
                    },
                    {
                        label: 'Upload (TX) dalam kbps',
                        data: [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, // biar update realtime gak nge-lag
                plugins: {
                    legend: { labels: { boxWidth: 12, font: { size: 10 } } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 9 },
                            callback: function(value) { return value + ' kbps'; }
                        }
                    },
                    x: { ticks: { font: { size: 9 } } }
                }
            }
        });

        function resetChart() {
            bandwidthChart.data.labels = [];
            bandwidthChart.data.datasets[0].data = [];
            bandwidthChart.data.datasets[1].data = [];
            bandwidthChart.update(); // FIX: dulunya bandwidthChart.data.update() (error, method gak ada)
        }

        function updateLiveMonitoring() {
            let id = routerSelect.value;
            if (!id && routerSelect.options.length > 0) {
                id = routerSelect.options[0].value;
            }

            if (!id || id === "") {
                debugStatus.innerText = "Tidak ada router aktif dipilih.";
                return;
            }

            fetch(`/api/mikrotik/${id}/status-realtime`)
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.success) {
                        debugStatus.innerText = "Gagal ambil data: " + (data.message || 'unknown error');
                        return;
                    }

                    // Update Label Nama Interface yang dimonitor
                    if (data.monitored_interface) {
                        interfaceBadge.innerText = `LIVE: ${data.monitored_interface.toUpperCase()}`;
                        interfaceBadge.classList.remove('hidden');
                    }

                    // Alirkan Titik Data Bandwidth ke Grafik
                    const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    if (bandwidthChart.data.labels.length > 12) {
                        bandwidthChart.data.labels.shift();
                        bandwidthChart.data.datasets[0].data.shift();
                        bandwidthChart.data.datasets[1].data.shift();
                    }

                    bandwidthChart.data.labels.push(now);

                    // Konversi aman raw bits ke kbps
                    let dlKbps = (parseFloat(data.download) || 0) / 1000;
                    let ulKbps = (parseFloat(data.upload) || 0) / 1000;

                    bandwidthChart.data.datasets[0].data.push(parseFloat(dlKbps.toFixed(1)));
                    bandwidthChart.data.datasets[1].data.push(parseFloat(ulKbps.toFixed(1)));

                    bandwidthChart.update(); // FIX: dulunya bandwidthChart.data.update() (error, silently gagal)

                    debugStatus.innerText = `Update terakhir: ${now} | RX: ${dlKbps.toFixed(1)} kbps | TX: ${ulKbps.toFixed(1)} kbps`;
                })
                .catch(err => {
                    console.error("Gagal menarik data trafik:", err);
                    debugStatus.innerText = "Error: " + err.message;
                });
        }

        // Loop interval otomatis per 2 detik
        setInterval(updateLiveMonitoring, 2000);

        routerSelect.addEventListener('change', () => {
            resetChart();
            updateLiveMonitoring();
        });

        updateLiveMonitoring();
    });
</script>
@endsection
