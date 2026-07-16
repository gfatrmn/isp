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

    <!-- Menyembunyikan Batang Scrollbar secara Global di Sidebar -->
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

<body class="font-sans antialiased bg-slate-100 text-slate-900">

    <div class="flex h-screen overflow-hidden relative">

        <!-- Backdrop Mobile -->
        <div id="sidebarBackdrop"
            class="fixed inset-0 bg-slate-900/40 z-20 hidden md:hidden transition-opacity duration-200"></div>

        <!-- SIDEBAR -->
        <aside id="mainSidebar"
            class="fixed md:relative inset-y-0 left-0 w-64 bg-slate-950 text-slate-400 flex flex-col flex-shrink-0 -translate-x-full md:translate-x-0 z-30 md:z-auto border-r border-slate-900 transition-transform duration-300 ease-in-out">

            <!-- Branding Header -->
            <div class="p-5 flex items-center justify-between bg-slate-950 border-b border-slate-900 flex-shrink-0">
                <span class="text-lg font-extrabold tracking-wider text-white flex items-center gap-2">
                    🌐 <span class="text-blue-500">Net</span>Core
                </span>
                <span
                    class="text-xs font-mono px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20">v1.0</span>
                <button id="closeSidebarBtn"
                    class="text-slate-500 hover:text-white md:hidden focus:outline-none">✕</button>
            </div>

            <!-- Navigasi Menu -->
            <nav class="flex-1 overflow-y-auto no-scrollbar px-4 py-6 space-y-3">

                <!-- Menu Utama: Dashboard -->
                <a href="/dashboard"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition duration-200 {{ Request::is('dashboard') ? 'bg-blue-600 text-white shadow' : 'hover:bg-slate-900 hover:text-slate-200' }}">
                    <span>📊</span> Dashboard
                </a>

                <hr class="border-slate-900 my-2">

                <!-- GRUP MENU 1: INFRASTRUKTUR -->
                <!-- GRUP MENU 1: INFRASTRUKTUR -->
                <div>
                    <div
                        class="flex items-center gap-3 px-3 py-2 text-sm font-bold text-slate-300 uppercase tracking-wider select-none">
                        <span>🖥️</span> <span>Infrastruktur</span>
                    </div>
                    <!-- Area Tree Sub-menu -->
                    <div class="relative pl-6 mt-1 space-y-1">
                        <!-- Garis Indikator Vertikal Utama (Tebal 2px) -->
                        <div class="absolute left-3 top-0 bottom-4 w-0.5 bg-slate-700 rounded-full"></div>

                        <!-- Sub-menu 1: Server Mikrotik (CRUD Router) -->
                        <div class="relative">
                            <!-- Garis L Horizontal -->
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-700 rounded-full"></div>
                            <a href="{{ route('mikrotik.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-medium transition duration-150 {{ Request::is('mikrotik') || (Request::is('mikrotik/*') && !Request::is('mikrotik/monitoring*')) ? 'text-blue-400 bg-slate-900 font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                                Server Mikrotik
                            </a>
                        </div>

                        <!-- Sub-menu 2: Monitoring (Grafik, Resource & Interfaces) -->
                        <div class="relative">
                            <!-- Garis L Horizontal -->
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-700 rounded-full"></div>
                            <a href="{{ route('mikrotik.monitoring') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-medium transition duration-150 {{ Request::is('mikrotik/monitoring*') ? 'text-blue-400 bg-slate-900 font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                                Monitoring
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GRUP MENU 2: 📋 PELANGGAN & PPPoE (STRUKTUR POHON STRUKTURAL BARU) -->
                <div>
                    <div
                        class="flex items-center gap-3 px-3 py-2 text-sm font-bold text-slate-300 uppercase tracking-wider select-none">
                        <span>📋</span> <span>Pelanggan & PPPoE</span>
                    </div>
                    <div class="relative pl-6 mt-1 space-y-1">
                        <!-- Garis Indikator Vertikal Utama -->
                        <div class="absolute left-3 top-0 bottom-4 w-0.5 bg-slate-700 rounded-full"></div>

                        <!-- 1. Data Pelanggan -->
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-700 rounded-full"></div>
                            <a href="{{ route('customers.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-medium transition duration-150 {{ Request::is('customers*') ? 'text-blue-400 bg-slate-900 font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                                Data Pelanggan
                            </a>
                        </div>

                        <!-- 2. Akun PPPoE -->
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-700 rounded-full"></div>
                            <a href="{{ route('pppoe.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-medium transition duration-150 {{ Request::is('pppoe*') ? 'text-blue-400 bg-slate-900 font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                                Akun PPPoE
                            </a>
                        </div>

                        <!-- 3. Monitoring Aktif -->
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-700 rounded-full"></div>
                            <a href="#"
                                class="block pl-3 py-2 rounded-md text-xs font-medium transition duration-150 text-slate-400 hover:text-slate-200 hover:bg-slate-900">
                                Monitoring Aktif
                            </a>
                        </div>

                        <!-- 4. Profile / Paket -->
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-700 rounded-full"></div>
                            <a href="{{ route('packages.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-medium transition duration-150 {{ Request::is('packages*') ? 'text-blue-400 bg-slate-900 font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                                Profile / Paket
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GRUP MENU 3: BILLING & SUPPORT -->
                <div>
                    <div
                        class="flex items-center gap-3 px-3 py-2 text-sm font-bold text-slate-300 uppercase tracking-wider select-none">
                        <span>🧾</span> <span>Billing & Kas</span>
                    </div>
                    <div class="relative pl-6 mt-1 space-y-1">
                        <div class="absolute left-3 top-0 bottom-4 w-0.5 bg-slate-700 rounded-full"></div>
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-700 rounded-full"></div>
                            <a href="{{ route('invoices.index') }}"
                                class="block pl-3 py-2 rounded-md text-xs font-medium transition duration-150 {{ Request::is('invoices*') ? 'text-blue-400 bg-slate-900 font-bold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                                Tagihan / Invoice
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute -left-3 top-4 w-2.5 h-0.5 bg-slate-700 rounded-full"></div>
                            <a href="#"
                                class="block pl-3 py-2 rounded-md text-xs font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-900">
                                Tiket Gangguan
                            </a>
                        </div>
                    </div>
                </div>

            </nav>

            <!-- Keterangan Footer Sidebar -->
            <div
                class="p-4 bg-slate-950 border-t border-slate-900 text-center text-[10px] text-slate-600 font-mono flex-shrink-0">
                &copy; {{ date('Y') }} Ghufrofi Fathurrahman
            </div>
        </aside>

        <!-- KONTEN KANAN -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- TOPBAR -->
            <header
                class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 z-10 flex-shrink-0 shadow-sm">
                <div class="flex items-center gap-4">
                    <button id="openSidebarBtn"
                        class="text-slate-600 hover:text-slate-900 md:hidden focus:outline-none p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-xs font-bold text-slate-500 tracking-wide uppercase">Core Network Operations Center
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <span
                            class="block text-sm font-semibold text-slate-800">{{ Auth::user()->name ?? 'Operator NOC' }}</span>
                        <span
                            class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Online</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-xs font-bold bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-100 transition shadow-sm">
                            Keluar 🚪
                        </button>
                    </form>
                </div>
            </header>

            <!-- VIEW AREA UTAMA -->
            <main class="flex-1 overflow-y-auto bg-slate-50 p-6 sm:p-8">
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
