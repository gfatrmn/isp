<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NetCore ISP') }}</title>

    <!-- Script Inisialisasi Mode Cepat (Mencegah Flash Warna Saat Reload) -->
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Library Grafik Realtime -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Menyembunyikan Scrollbar secara Global & Kustomisasi Gaya -->
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

<body
    class="font-sans antialiased bg-slate-100 dark:bg-[#0e0f11] text-slate-800 dark:text-slate-300 transition-colors duration-200">

    <div class="flex h-screen overflow-hidden relative">

        <!-- Backdrop Mobile -->
        <div id="sidebarBackdrop"
            class="fixed inset-0 bg-black/60 z-20 hidden md:hidden transition-opacity duration-200"></div>

        <!-- SIDEBAR MODERN TANPA TREE -->
        <aside id="mainSidebar"
            class="fixed md:relative inset-y-0 left-0 w-64 bg-white dark:bg-[#121316] text-slate-600 dark:text-slate-400 flex flex-col flex-shrink-0 -translate-x-full md:translate-x-0 z-30 md:z-auto border-r border-slate-200 dark:border-[#1a1c21] transition-colors duration-200 transition-transform ease-in-out">

            <!-- Branding Header Sidebar -->
            <div
                class="p-4 flex items-center justify-between bg-white dark:bg-[#121316] border-b border-slate-200 dark:border-[#1a1c21] flex-shrink-0">
                <span
                    class="text-md font-extrabold tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-[#a6ff00]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                        </path>
                    </svg>
                    <span class="text-emerald-600 dark:text-[#a6ff00]">Net</span>Core
                </span>

                <button id="closeSidebarBtn"
                    class="text-slate-400 hover:text-slate-900 dark:hover:text-white md:hidden focus:outline-none">✕</button>
            </div>

            <!-- Navigasi Menu Sidebar -->
            <nav class="flex-1 overflow-y-auto no-scrollbar px-3 py-4 space-y-4">

                <!-- Link Dashboard Utama -->
                <div>
                    <a href="/dashboard"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition duration-200 {{ Request::is('dashboard') ? 'bg-emerald-500/10 text-emerald-700 font-semibold border border-emerald-500/20 dark:bg-[#a6ff00] dark:text-black dark:border-transparent dark:font-bold dark:shadow-md dark:shadow-[#a6ff00]/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1a1c21] hover:text-emerald-700 dark:hover:text-[#a6ff00]' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- GRUP MENU 1: INFRASTRUKTUR -->
                <div class="space-y-1">
                    <div
                        class="px-3 py-1 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        Infrastruktur
                    </div>

                    <a href="{{ route('mikrotik.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 {{ Request::is('mikrotik') || (Request::is('mikrotik/*') && !Request::is('mikrotik/monitoring*')) ? 'bg-emerald-500/10 text-emerald-700 font-semibold border border-emerald-500/20 dark:bg-[#1a1c21] dark:text-[#a6ff00] dark:border-transparent' : 'text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#a6ff00] hover:bg-slate-100 dark:hover:bg-[#1a1c21]' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01">
                            </path>
                        </svg>
                        <span>Server Mikrotik</span>
                    </a>

                    <a href="{{ route('mikrotik.monitoring') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 {{ Request::is('mikrotik/monitoring*') ? 'bg-emerald-500/10 text-emerald-700 font-semibold border border-emerald-500/20 dark:bg-[#1a1c21] dark:text-[#a6ff00] dark:border-transparent' : 'text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#a6ff00] hover:bg-slate-100 dark:hover:bg-[#1a1c21]' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span>Monitoring</span>
                    </a>
                </div>

                <!-- GRUP MENU 2: PELANGGAN & PPPoE -->
                <div class="space-y-1">
                    <div
                        class="px-3 py-1 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        Pelanggan & PPPoE
                    </div>

                    <a href="{{ route('customers.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 {{ Request::is('customers*') ? 'bg-emerald-500/10 text-emerald-700 font-semibold border border-emerald-500/20 dark:bg-[#1a1c21] dark:text-[#a6ff00] dark:border-transparent' : 'text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#a6ff00] hover:bg-slate-100 dark:hover:bg-[#1a1c21]' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span>Data Pelanggan</span>
                    </a>

                    <a href="{{ route('pppoe.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 {{ Request::is('pppoe*') ? 'bg-emerald-500/10 text-emerald-700 font-semibold border border-emerald-500/20 dark:bg-[#1a1c21] dark:text-[#a6ff00] dark:border-transparent' : 'text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#a6ff00] hover:bg-slate-100 dark:hover:bg-[#1a1c21]' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                            </path>
                        </svg>
                        <span>Akun PPPoE</span>
                    </a>

                    <a href="{{ route('isolir.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 {{ Request::is('isolir*') ? 'bg-emerald-500/10 text-emerald-700 font-semibold border border-emerald-500/20 dark:bg-[#1a1c21] dark:text-[#a6ff00] dark:border-transparent' : 'text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#a6ff00] hover:bg-slate-100 dark:hover:bg-[#1a1c21]' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                            </path>
                        </svg>
                        <span>Data Isolir</span>
                    </a>

                    <a href="{{ route('packages.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 {{ Request::is('packages*') ? 'bg-emerald-500/10 text-emerald-700 font-semibold border border-emerald-500/20 dark:bg-[#1a1c21] dark:text-[#a6ff00] dark:border-transparent' : 'text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#a6ff00] hover:bg-slate-100 dark:hover:bg-[#1a1c21]' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <span>Profile / Paket</span>
                    </a>
                </div>

                <!-- GRUP MENU 3: BILLING & SUPPORT -->
                <div class="space-y-1">
                    <div
                        class="px-3 py-1 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        Billing & Support
                    </div>

                    <a href="{{ route('invoices.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 {{ Request::is('invoices*') ? 'bg-emerald-500/10 text-emerald-700 font-semibold border border-emerald-500/20 dark:bg-[#1a1c21] dark:text-[#a6ff00] dark:border-transparent' : 'text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#a6ff00] hover:bg-slate-100 dark:hover:bg-[#1a1c21]' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span>Tagihan / Invoice</span>
                    </a>

                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#a6ff00] hover:bg-slate-100 dark:hover:bg-[#1a1c21]">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span>Tiket Gangguan</span>
                    </a>
                </div>

            </nav>

            <!-- Footer Sidebar -->
            <div
                class="p-3 bg-white dark:bg-[#121316] border-t border-slate-200 dark:border-[#1a1c21] text-center text-[9px] text-slate-500 font-mono flex-shrink-0">
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
                    <h2 class="text-xs font-bold text-slate-600 dark:text-slate-400 tracking-wide uppercase">Core
                        Network Operations Center</h2>
                </div>

                <div class="flex items-center gap-5">

                    <!-- FITUR DARK / LIGHT MODE SWITCHER -->
                    <button id="themeToggleBtn" type="button"
                        class="p-2 rounded-lg text-slate-700 dark:text-slate-400 bg-slate-100 dark:bg-transparent hover:bg-slate-200 dark:hover:bg-[#1a1c21] focus:outline-none transition duration-150 flex items-center gap-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-800">
                        <!-- Icon Bulan (Tampil saat Dark Mode) -->
                        <span id="themeToggleDarkIcon" class="hidden dark:inline-flex items-center gap-1">
                            🌙 <span class="hidden sm:inline text-[11px]">Dark</span>
                        </span>
                        <!-- Icon Matahari (Tampil saat Light Mode) -->
                        <span id="themeToggleLightIcon" class="inline-flex dark:hidden items-center gap-1">
                            ☀️ <span class="hidden sm:inline text-[11px]">Light</span>
                        </span>
                    </button>

                    <!-- INFO ROLE OPERATOR -->
                    <div class="text-right hidden sm:block border-l border-slate-200 dark:border-slate-800 pl-4">
                        <span
                            class="block text-sm font-semibold text-slate-800 dark:text-slate-200">{{ Auth::user()->name ?? 'Operator NOC' }}</span>
                        <span
                            class="text-[9px] text-emerald-700 dark:text-[#a6ff00] font-bold bg-emerald-100 dark:bg-[#a6ff00]/10 px-2 py-0.5 rounded border border-emerald-300 dark:border-[#a6ff00]/20">Online</span>
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
            <main
                class="flex-1 overflow-y-auto bg-slate-50 dark:bg-[#0e0f11] p-6 sm:p-8 transition-colors duration-200">
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
            // 1. Mobile Sidebar Toggle
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

            // 2. Dark Mode / Light Mode Switcher
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
