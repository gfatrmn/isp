@extends('layouts.app')

@section('content')
<div class="space-y-4">

    <!-- HEADER SECTION & SELECT ROUTER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#121316] p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-sm">
        <div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 dark:bg-[#a6ff00] rounded-full animate-pulse shadow-sm"></span>
                Monitoring Infrastruktur
            </h1>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Pantau trafik bandwidth router Mikrotik secara realtime.</p>
        </div>

        <!-- SELECT ROUTER -->
        <div class="w-full sm:w-72">
            <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 font-heading">
                Pilih Perangkat Router
            </label>
            <div class="relative">
                <select id="routerSelect"
                    class="w-full px-3 py-1.5 bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] rounded-xl text-xs text-slate-800 dark:text-slate-200 font-semibold shadow-sm focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition cursor-pointer appearance-none">
                    @foreach($routers as $router)
                        <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->ip_address }})</option>
                    @endforeach
                    @if($routers->isEmpty())
                        <option value="">-- Tidak ada router berstatus Online --</option>
                    @endif
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- STAT CARDS BANDWIDTH SAAT INI (COMPACT SIZE) -->
    <div class="grid grid-cols-2 gap-3">
        <!-- DOWNLOAD CARD -->
        <div class="bg-white dark:bg-[#121316] px-4 py-2.5 rounded-xl border border-slate-200 dark:border-[#1a1c21] shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading block">Download (RX)</span>
                <div class="text-base font-extrabold text-blue-600 dark:text-blue-400 font-heading mt-0.5 leading-tight" id="liveDownloadText">0 kbps</div>
            </div>
            <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold flex-shrink-0">
                ⬇️
            </div>
        </div>

        <!-- UPLOAD CARD -->
        <div class="bg-white dark:bg-[#121316] px-4 py-2.5 rounded-xl border border-slate-200 dark:border-[#1a1c21] shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading block">Upload (TX)</span>
                <div class="text-base font-extrabold text-emerald-600 dark:text-[#a6ff00] font-heading mt-0.5 leading-tight" id="liveUploadText">0 kbps</div>
            </div>
            <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-[#a6ff00] flex items-center justify-center text-xs font-bold flex-shrink-0">
                ⬆️
            </div>
        </div>
    </div>

    <!-- CONTAINER GRAFIK UTUH -->
    <div class="w-full bg-white dark:bg-[#121316] p-5 rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-sm">
        <div class="mb-3 pb-2.5 border-b border-slate-200 dark:border-[#1a1c21] flex items-center justify-between gap-2">
            <h2 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider font-heading flex items-center gap-2">
                <span>📉 Grafik Bandwidth Realtime (Reverb Stream)</span>
            </h2>
            <span id="interfaceBadge" class="text-[9px] font-mono bg-emerald-50 text-emerald-700 dark:bg-[#a6ff00]/10 dark:text-[#a6ff00] px-2 py-0.5 rounded-md border border-emerald-200 dark:border-[#a6ff00]/20 font-bold hidden">
                LIVE: -
            </span>
        </div>

        <!-- AREA CHART CANVAS -->
        <div class="h-80 relative">
            <canvas id="bandwidthChart"></canvas>
        </div>

        <!-- FOOTER STATUS WEBSOCKET -->
        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-[#16181d] flex items-center justify-between text-[9px] font-mono text-slate-400 dark:text-slate-500">
            <span id="debugStatus">Menyiapkan koneksi WebSocket...</span>
            <span id="wsIndicator" class="flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span>
                <span>Reverb Connected</span>
            </span>
        </div>
    </div>

</div>

<!-- SCRIPT MONITORING WEBSOCKET (REVERB) -->
@vite(['resources/js/app.js'])
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const canvasEl = document.getElementById('bandwidthChart');
        const ctx = canvasEl.getContext('2d');
        const routerSelect = document.getElementById('routerSelect');
        const interfaceBadge = document.getElementById('interfaceBadge');
        const debugStatus = document.getElementById('debugStatus');
        const liveDownloadText = document.getElementById('liveDownloadText');
        const liveUploadText = document.getElementById('liveUploadText');

        let activeChannel = null;
        const MAX_POINTS = 20;
        const SMOOTHING_FACTOR = 0.35;

        let smoothDl = null;
        let smoothUl = null;

        function makeGradient(ctx, colorTop) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, colorTop);
            gradient.addColorStop(1, 'rgba(0,0,0,0)');
            return gradient;
        }

        const gradientDl = makeGradient(ctx, 'rgba(59, 130, 246, 0.35)');
        const gradientUl = makeGradient(ctx, 'rgba(16, 185, 129, 0.35)');

        // Inisialisasi Chart.js
        const bandwidthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Download (RX)',
                        data: [],
                        borderColor: '#3b82f6',
                        backgroundColor: gradientDl,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        cubicInterpolationMode: 'monotone',
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointHitRadius: 10,
                        pointBackgroundColor: '#3b82f6',
                    },
                    {
                        label: 'Upload (TX)',
                        data: [],
                        borderColor: '#10b981',
                        backgroundColor: gradientUl,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        cubicInterpolationMode: 'monotone',
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointHitRadius: 10,
                        pointBackgroundColor: '#10b981',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 600,
                    easing: 'easeOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 8,
                            font: { size: 10, family: 'Space Grotesk' },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: '#121316',
                        titleFont: { size: 10 },
                        bodyFont: { size: 10 },
                        padding: 8,
                        cornerRadius: 8,
                        displayColors: true,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grace: '10%',
                        grid: { color: 'rgba(150, 150, 150, 0.08)' },
                        ticks: {
                            font: { size: 9, family: 'Space Grotesk' },
                            callback: function(value) { return value + ' kbps'; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 8.5, family: 'Space Grotesk' },
                            maxRotation: 0, // KUNCI 1: Paksa teks waktu tetap horizontal tegak (tidak miring)
                            minRotation: 0, // KUNCI 2: Paksa minimal rotasi 0 derajat
                            autoSkip: true, // Otomatis lewati teks jika terlalu banyak/padat
                            maxTicksLimit: 7 // Membatasi jumlah teks jam pada sumbu X agar selalu luas & rapi
                        }
                    }
                }
            }
        });

        function formatTraffic(bits) {
            let num = parseFloat(bits) || 0;
            let kbps = num / 1000;
            if (kbps >= 1000) {
                return (kbps / 1000).toFixed(2) + ' Mbps';
            }
            return kbps.toFixed(1) + ' kbps';
        }

        function smooth(prev, current, factor) {
            if (prev === null) return current;
            return prev + factor * (current - prev);
        }

        function resetChart() {
            bandwidthChart.data.labels = [];
            bandwidthChart.data.datasets[0].data = [];
            bandwidthChart.data.datasets[1].data = [];
            bandwidthChart.update();
            liveDownloadText.innerText = "0 kbps";
            liveUploadText.innerText = "0 kbps";
            smoothDl = null;
            smoothUl = null;
        }

        function subscribeToRouterTraffic() {
            let routerId = routerSelect.value;

            if (activeChannel) {
                window.Echo.leave(`mikrotik-traffic.${activeChannel}`);
                activeChannel = null;
            }

            if (!routerId || routerId === "") {
                debugStatus.innerText = "Status: Tidak ada router aktif yang dipilih.";
                return;
            }

            activeChannel = routerId;
            debugStatus.innerText = `Menghubungkan ke Reverb Channel: mikrotik-traffic.${routerId}...`;

            window.Echo.channel(`mikrotik-traffic.${routerId}`)
                .listen('.TrafficUpdated', (e) => {
                    const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                    if (e.interface) {
                        interfaceBadge.innerText = `INTERFACE: ${e.interface.toUpperCase()}`;
                        interfaceBadge.classList.remove('hidden');
                    }

                    if (bandwidthChart.data.labels.length >= MAX_POINTS) {
                        bandwidthChart.data.labels.shift();
                        bandwidthChart.data.datasets[0].data.shift();
                        bandwidthChart.data.datasets[1].data.shift();
                    }

                    bandwidthChart.data.labels.push(now);

                    let rawDl = parseFloat(e.download) || 0;
                    let rawUl = parseFloat(e.upload) || 0;

                    smoothDl = smooth(smoothDl, rawDl, SMOOTHING_FACTOR);
                    smoothUl = smooth(smoothUl, rawUl, SMOOTHING_FACTOR);

                    let dlKbps = parseFloat((smoothDl / 1000).toFixed(1));
                    let ulKbps = parseFloat((smoothUl / 1000).toFixed(1));

                    bandwidthChart.data.datasets[0].data.push(dlKbps);
                    bandwidthChart.data.datasets[1].data.push(ulKbps);

                    bandwidthChart.update();

                    liveDownloadText.innerText = formatTraffic(rawDl);
                    liveUploadText.innerText = formatTraffic(rawUl);

                    debugStatus.innerText = `Stream Aktif | Update: ${now} | Channel: mikrotik-traffic.${routerId}`;
                });
        }

        routerSelect.addEventListener('change', () => {
            resetChart();
            subscribeToRouterTraffic();
        });

        subscribeToRouterTraffic();
    });
</script>
@endsection
