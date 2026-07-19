<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NetCore ISP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Library Grafik Realtime -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Menyembunyikan Batang Scrollbar secara Global di Sidebar & Kustomisasi Gaya -->
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="font-sans antialiased bg-[#0e0f11] text-slate-300">

    <div class="flex h-screen overflow-hidden relative">

        <!-- Backdrop Mobile -->
        <div id="sidebarBackdrop"
            class="fixed inset-0 bg-black/60 z-20 hidden md:hidden transition-opacity duration-200"></div>

        <!-- SIDEBAR (TEMA GELAP DENGAN AKSEN TRUE NEON LIME GREEN) -->
        <aside id="mainSidebar"
            class="fixed md:relative inset-y-0 left-0 w-64 bg-[#121316] text-slate-400 flex flex-col flex-shrink-0 -translate-x-full md:translate-x-0 z-30 md:z-auto border-r border-[#1a1c21] transition-transform duration-300 ease-in-out">

            <!-- Branding Header -->
            <div class="p-5 flex items-center justify-between bg-[#121316] border-b border-[#1a1c21] flex-shrink-0">
                <span class="text-lg font-extrabold tracking-wider text-white flex items-center gap-2">
                    🌐 <span class="text-[#a6ff00]">Net</span>Core
                </span>
                <span
                    class="text-[10px] font-mono px-2 py-0.5 rounded bg-[#a6ff00]/10 text-[#a6ff00] border border-[#a6ff00]/20">v1.0</span>
                <button id="closeSidebarBtn"
                    class="text-slate-500 hover:text-white md:hidden focus:outline-none">✕</button>
            </div>

            <!-- Navigasi Menu -->
            <nav class="flex-1 overflow-y-auto no-scrollbar px-4 py-6 space-y-3">

                <!-- Menu Utama: Dashboard (Active Style True Neon Lime Green) -->
                <a href="/dashboard"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-bold transition duration-200 {{ Request::is('dashboard') ? 'bg-[#a6ff00] text-black shadow-lg shadow-[#a6ff00]/20 font-bold' : 'hover:bg-[#1a1c21] hover:text-slate-200' }}">
                    <span>📊</span> Dashboard
                </a>

                <hr class="border-[#1a1c21] my-2">

                <!-- GRUP MENU 1: INFRASTRUKTUR -->
                <div>
                    <div
                        class="flex items-center gap-3 px-3 py-2 text-xxs font-bold text-slate-500 uppercase tracking-widest select-none">
                        <span>🖥️</span> <span>Infrastruktur</span>
                    </div>
                    <!-- Area Tree Sub-menu -->
                    <div class="relative pl-6 mt-1 space-y-1">
                        <!-- Garis Indikator Vertikal Utama -->
                        <div class="absolute left-3 top-0 bottom-4 w-0.5 bg-slate-800 rounded-full"></div>

                        <!-- Sub-menu 1: Server Mikrotik -->
                        <div class="relative">
                            <!-- Garis L Horizontal -->
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-800 rounded-full"></div>
                            <a href="{{ route('mikrotik.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-bold transition duration-150 {{ Request::is('mikrotik') || (Request::is('mikrotik/*') && !Request::is('mikrotik/monitoring*')) ? 'text-[#a6ff00] bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1a1c21]' }}">
                                Server Mikrotik
                            </a>
                        </div>

                        <!-- Sub-menu 2: Monitoring -->
                        <div class="relative">
                            <!-- Garis L Horizontal -->
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-800 rounded-full"></div>
                            <a href="{{ route('mikrotik.monitoring') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-bold transition duration-150 {{ Request::is('mikrotik/monitoring*') ? 'text-[#a6ff00] bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1a1c21]' }}">
                                Monitoring
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GRUP MENU 2: PELANGGAN & PPPoE -->
                <div>
                    <div
                        class="flex items-center gap-3 px-3 py-2 text-xxs font-bold text-slate-500 uppercase tracking-widest select-none">
                        <span>📋</span> <span>Pelanggan & PPPoE</span>
                    </div>
                    <div class="relative pl-6 mt-1 space-y-1">
                        <!-- Garis Indikator Vertikal Utama -->
                        <div class="absolute left-3 top-0 bottom-4 w-0.5 bg-slate-800 rounded-full"></div>

                        <!-- 1. Data Pelanggan -->
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-800 rounded-full"></div>
                            <a href="{{ route('customers.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-bold transition duration-150 {{ Request::is('customers*') ? 'text-[#a6ff00] bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1a1c21]' }}">
                                Data Pelanggan
                            </a>
                        </div>

                        <!-- 2. Akun PPPoE -->
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-800 rounded-full"></div>
                            <a href="{{ route('pppoe.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-bold transition duration-150 {{ Request::is('pppoe*') ? 'text-[#a6ff00] bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1a1c21]' }}">
                                Akun PPPoE
                            </a>
                        </div>

                        <!-- 3. Monitoring Aktif -->
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-800 rounded-full"></div>
                            <a href="#"
                                class="block pl-3 py-2 rounded-md text-xs font-bold transition duration-150 text-slate-400 hover:text-slate-200 hover:bg-[#1a1c21]">
                                Monitoring Aktif
                            </a>
                        </div>

                        <!-- 4. Profile / Paket -->
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-800 rounded-full"></div>
                            <a href="{{ route('packages.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-bold transition duration-150 {{ Request::is('packages*') ? 'text-[#a6ff00] bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1a1c21]' }}">
                                Profile / Paket
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GRUP MENU 3: BILLING & SUPPORT -->
                <div>
                    <div
                        class="flex items-center gap-3 px-3 py-2 text-xxs font-bold text-slate-500 uppercase tracking-widest select-none">
                        <span>🧾</span> <span>Billing & Kas</span>
                    </div>
                    <div class="relative pl-6 mt-1 space-y-1">
                        <div class="absolute left-3 top-0 bottom-4 w-0.5 bg-slate-800 rounded-full"></div>
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-800 rounded-full"></div>
                            <a href="{{ route('invoices.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-bold transition duration-150 {{ Request::is('invoices*') ? 'text-[#a6ff00] bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1a1c21]' }}">
                                Tagihan / Invoice
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-800 rounded-full"></div>
                            <a href="#"
                                class="block pl-3 py-2 rounded-md text-xs font-bold text-slate-400 hover:text-slate-200 hover:bg-[#1a1c21]">
                                Tiket Gangguan
                            </a>
                        </div>
                    </div>
                </div>

            </nav>

            <!-- Keterangan Footer Sidebar -->
            <div
                class="p-4 bg-[#121316] border-t border-[#1a1c21] text-center text-[10px] text-slate-600 font-mono flex-shrink-0">
                &copy; {{ date('Y') }} Ghufrofi Fathurrahman
            </div>
        </aside>

        <!-- KONTEN KANAN -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- TOPBAR -->
            <header
                class="bg-[#121316] border-b border-[#1a1c21] h-16 flex items-center justify-between px-6 z-10 flex-shrink-0 shadow-sm">
                <div class="flex items-center gap-4">
                    <button id="openSidebarBtn"
                        class="text-slate-400 hover:text-white md:hidden focus:outline-none p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-xs font-bold text-slate-400 tracking-wide uppercase">Core Network Operations Center</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <span class="block text-sm font-semibold text-slate-200">{{ Auth::user()->name ?? 'Operator NOC' }}</span>
                        <span
                            class="text-[9px] text-[#a6ff00] font-bold bg-[#a6ff00]/10 px-2 py-0.5 rounded border border-[#a6ff00]/20">Online</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20 px-3 py-1.5 rounded-lg hover:bg-red-500/20 transition shadow-sm">
                            Keluar 🚪
                        </button>
                    </form>
                </div>
            </header>

            <!-- VIEW AREA UTAMA -->
            <main class="flex-1 overflow-y-auto bg-[#0e0f11] p-6 sm:p-8">
                @isset($slot)
                    {{ $slot }}
                @endisset

                @yield('content')
            </main>

        </div>
    </div>

    <!-- JAVASCRIPT RESPONSIVE MOBILE SIDEBAR -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById('mainSidebar');
            const openBtn = document.getElementById('openSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const backdrop = document.getElementById('sidebarBackdrop');

            function handleToggle() {
                if (sidebar && backdrop) {
                    sidebar.classList.toggle('-translate-x-full');
                    backdrop.classList.toggle('hidden');
                }
            }

            if (openBtn && closeBtn && backdrop) {
                openBtn.addEventListener('click', handleToggle);
                closeBtn.addEventListener('click', handleToggle);
                backdrop.addEventListener('click', handleToggle);
            }
        });
    </script>
</body>

</html>
