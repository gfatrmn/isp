<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NetCore ISP') }}</title>

    <!-- Script Inisialisasi Mode Cepat (Mencegah Flash Putih Saat Reload) -->
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Library Grafik Realtime -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Menyembunyikan Batang Scrollbar secara Global & Kustomisasi Theme Class -->
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

<body class="font-sans antialiased bg-slate-100 dark:bg-[#0e0f11] text-slate-800 dark:text-slate-300 transition-colors duration-200">

    <div class="flex h-screen overflow-hidden relative">

        <!-- Backdrop Mobile -->
        <div id="sidebarBackdrop"
            class="fixed inset-0 bg-black/60 z-20 hidden md:hidden transition-opacity duration-200"></div>

        <!-- SIDEBAR -->
        <aside id="mainSidebar"
            class="fixed md:relative inset-y-0 left-0 w-64 bg-slate-900 dark:bg-[#121316] text-slate-400 flex flex-col flex-shrink-0 -translate-x-full md:translate-x-0 z-30 md:z-auto border-r border-slate-800 dark:border-[#1a1c21] transition-transform duration-300 ease-in-out">

            <!-- Branding Header Sidebar -->
            <div class="p-4 flex items-center justify-between bg-slate-900 dark:bg-[#121316] border-b border-slate-800 dark:border-[#1a1c21] flex-shrink-0">
                <span class="text-md font-extrabold tracking-wider text-white flex items-center gap-2">
                    🌐 <span class="text-[#a6ff00]">Net</span>Core
                </span>
                <span
                    class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-[#a6ff00]/10 text-[#a6ff00] border border-[#a6ff00]/20">v1.0</span>
                <button id="closeSidebarBtn"
                    class="text-slate-500 hover:text-white md:hidden focus:outline-none">✕</button>
            </div>

            <!-- Navigasi Menu Sidebar -->
            <nav class="flex-1 overflow-y-auto no-scrollbar px-4 py-4 space-y-2">

                <!-- Link Dashboard Utama -->
                <a href="/dashboard"
                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-bold transition duration-200 {{ Request::is('dashboard') ? 'bg-[#a6ff00] text-black shadow-lg shadow-[#a6ff00]/20 font-bold' : 'text-white hover:bg-slate-800 dark:hover:bg-[#1a1c21] hover:text-[#a6ff00]' }}">
                    <span class="text-xs">📊</span> Dashboard
                </a>

                <hr class="border-slate-800 dark:border-[#1a1c21] my-2">

                <!-- GRUP MENU 1: INFRASTRUKTUR -->
                <div>
                    <div
                        class="flex items-center gap-2 px-3 py-1 text-[10px] font-bold text-white uppercase tracking-widest select-none">
                        <span>🖥️</span> <span>Infrastruktur</span>
                    </div>

                    <div class="relative pl-6 mt-1 space-y-0.5">
                        <div class="absolute left-3 top-0 bottom-3 w-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>

                        <!-- Sub-menu 1: Server Mikrotik -->
                        <div class="relative">
                            <div class="absolute -left-3 top-3.5 w-2.5 h-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>
                            <a href="{{ route('mikrotik.index') }}"
                                class="block pl-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 {{ Request::is('mikrotik') || (Request::is('mikrotik/*') && !Request::is('mikrotik/monitoring*')) ? 'text-[#a6ff00] bg-slate-800 dark:bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-[#a6ff00] hover:bg-slate-800 dark:hover:bg-[#1a1c21]' }}">
                                Server Mikrotik
                            </a>
                        </div>

                        <!-- Sub-menu 2: Monitoring -->
                        <div class="relative">
                            <div class="absolute -left-3 top-3.5 w-2.5 h-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>
                            <a href="{{ route('mikrotik.monitoring') }}"
                                class="block pl-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 {{ Request::is('mikrotik/monitoring*') ? 'text-[#a6ff00] bg-slate-800 dark:bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-[#a6ff00] hover:bg-slate-800 dark:hover:bg-[#1a1c21]' }}">
                                Monitoring
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GRUP MENU 2: PELANGGAN & PPPoE -->
                <div>
                    <div
                        class="flex items-center gap-2 px-3 py-1 text-[10px] font-bold text-white uppercase tracking-widest select-none">
                        <span>📋</span> <span>Pelanggan & PPPoE</span>
                    </div>

                    <div class="relative pl-6 mt-1 space-y-0.5">
                        <div class="absolute left-3 top-0 bottom-3 w-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>

                        <!-- 1. Data Pelanggan -->
                        <div class="relative">
                            <div class="absolute -left-3 top-3.5 w-2.5 h-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>
                            <a href="{{ route('customers.index') }}"
                                class="block pl-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 {{ Request::is('customers*') ? 'text-[#a6ff00] bg-slate-800 dark:bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-[#a6ff00] hover:bg-slate-800 dark:hover:bg-[#1a1c21]' }}">
                                Data Pelanggan
                            </a>
                        </div>

                        <!-- 2. Akun PPPoE -->
                        <div class="relative">
                            <div class="absolute -left-3 top-3.5 w-2.5 h-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>
                            <a href="{{ route('pppoe.index') }}"
                                class="block pl-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 {{ Request::is('pppoe*') ? 'text-[#a6ff00] bg-slate-800 dark:bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-[#a6ff00] hover:bg-slate-800 dark:hover:bg-[#1a1c21]' }}">
                                Akun PPPoE
                            </a>
                        </div>

                        <!-- 3. Monitoring Aktif -->
                        <div class="relative">
                            <div class="absolute -left-3 top-3.5 w-2.5 h-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>
                            <a href="#"
                                class="block pl-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 text-slate-400 hover:text-[#a6ff00] hover:bg-slate-800 dark:hover:bg-[#1a1c21]">
                                Monitoring Aktif
                            </a>
                        </div>

                        <!-- 4. Profile / Paket -->
                        <div class="relative">
                            <div class="absolute -left-3 top-3.5 w-2.5 h-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>
                            <a href="{{ route('packages.index') }}"
                                class="block pl-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 {{ Request::is('packages*') ? 'text-[#a6ff00] bg-slate-800 dark:bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-[#a6ff00] hover:bg-slate-800 dark:hover:bg-[#1a1c21]' }}">
                                Profile / Paket
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GRUP MENU 3: BILLING & SUPPORT -->
                <div>
                    <div
                        class="flex items-center gap-2 px-3 py-1 text-[10px] font-bold text-white uppercase tracking-widest select-none">
                        <span>🧾</span> <span>Billing & Kas</span>
                    </div>

                    <div class="relative pl-6 mt-1 space-y-0.5">
                        <div class="absolute left-3 top-0 bottom-3 w-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>

                        <div class="relative">
                            <div class="absolute -left-3 top-3.5 w-2.5 h-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>
                            <a href="{{ route('invoices.index') }}"
                                class="block pl-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 {{ Request::is('invoices*') ? 'text-[#a6ff00] bg-slate-800 dark:bg-[#1a1c21] font-bold shadow-inner' : 'text-slate-400 hover:text-[#a6ff00] hover:bg-slate-800 dark:hover:bg-[#1a1c21]' }}">
                                Tagihan / Invoice
                            </a>
                        </div>

                        <div class="relative">
                            <div class="absolute -left-3 top-3.5 w-2.5 h-0.5 bg-slate-700 dark:bg-slate-800 rounded-full"></div>
                            <a href="#"
                                class="block pl-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 text-slate-400 hover:text-[#a6ff00] hover:bg-slate-800 dark:hover:bg-[#1a1c21]">
                                Tiket Gangguan
                            </a>
                        </div>
                    </div>
                </div>

            </nav>

            <!-- Footer Sidebar -->
            <div
                class="p-3 bg-slate-900 dark:bg-[#121316] border-t border-slate-800 dark:border-[#1a1c21] text-center text-[9px] text-slate-500 font-mono flex-shrink-0">
                &copy; {{ date('Y') }} Ghufrofi Fathurrahman
            </div>
        </aside>

        <!-- KONTEN KANAN -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- NAVBAR / TOPBAR -->
            <header
                class="bg-white dark:bg-[#121316] border-b border-slate-200 dark:border-[#1a1c21] h-16 flex items-center justify-between px-6 z-10 flex-shrink-0 shadow-sm transition-colors duration-200">
                <div class="flex items-center gap-4">
                    <button id="openSidebarBtn"
                        class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white md:hidden focus:outline-none p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 tracking-wide uppercase">Core Network Operations Center</h2>
                </div>

                <div class="flex items-center gap-5">

                    <!-- FITUR DARK / LIGHT MODE SWITCHER (DI SEBELAH KIRI ROLE) -->
                    <button id="themeToggleBtn" type="button"
                        class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1a1c21] focus:outline-none transition duration-150 flex items-center gap-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-800">
                        <!-- Icon Bulan (Dark Mode) -->
                        <span id="themeToggleDarkIcon" class="hidden dark:inline flex items-center gap-1">
                            🌙 <span class="hidden sm:inline text-[11px]">Dark</span>
                        </span>
                        <!-- Icon Matahari (Light Mode) -->
                        <span id="themeToggleLightIcon" class="inline dark:hidden flex items-center gap-1">
                            ☀️ <span class="hidden sm:inline text-[11px]">Light</span>
                        </span>
                    </button>

                    <!-- INFO ROLE OPERATOR -->
                    <div class="text-right hidden sm:block border-l border-slate-200 dark:border-slate-800 pl-4">
                        <span class="block text-sm font-semibold text-slate-800 dark:text-slate-200">{{ Auth::user()->name ?? 'Operator NOC' }}</span>
                        <span
                            class="text-[9px] text-[#a6ff00] font-bold bg-[#a6ff00]/10 px-2 py-0.5 rounded border border-[#a6ff00]/20">Online</span>
                    </div>

                    <!-- TOMBOL LOGOUT -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-xs font-bold bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-3 py-1.5 rounded-lg hover:bg-red-500/20 transition shadow-sm">
                            Keluar 🚪
                        </button>
                    </form>
                </div>
            </header>

            <!-- VIEW AREA UTAMA -->
            <main class="flex-1 overflow-y-auto bg-slate-50 dark:bg-[#0e0f11] p-6 sm:p-8 transition-colors duration-200">
                @isset($slot)
                    {{ $slot }}
                @endisset

                @yield('content')
            </main>

        </div>
    </div>

    <!-- JAVASCRIPT RESPONSIVE MOBILE SIDEBAR & TOGGLE DARK/LIGHT MODE -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Logika Mobile Sidebar Toggle
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

            // 2. Logika Dark Mode / Light Mode Switcher
            const themeToggleBtn = document.getElementById('themeToggleBtn');

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                });
            }
        });
    </script>
</body>

</html>
