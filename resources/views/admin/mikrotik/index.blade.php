@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#121316] p-6 rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600 dark:text-[#a6ff00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                </svg>
                Server Mikrotik
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola infrastruktur router untuk monitoring bandwidth realtime dan manajemen pelanggan PPPoE.
            </p>
        </div>

        <!-- TOMBOL BUKA MODAL -->
        <button onclick="openAddModal()"
            class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-[#a6ff00] dark:text-black dark:hover:bg-[#92eb00] font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Router</span>
        </button>
    </div>

    <!-- NOTIFIKASI FLASH -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 rounded-xl text-xs flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 flex-shrink-0 text-emerald-600 dark:text-[#a6ff00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 text-red-800 dark:text-red-300 rounded-xl text-xs flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- TABEL DAFTAR ROUTER -->
    <div class="bg-white dark:bg-[#121316] rounded-2xl shadow-sm border border-slate-200 dark:border-[#1a1c21] overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-[#1a1c21] flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white font-heading">
                Koneksi Router Terdaftar ({{ $servers->count() }})
            </h2>
        </div>

        @if($servers->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="w-12 h-12 bg-slate-100 dark:bg-[#1a1c21] text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum Ada Router Terdaftar</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Klik tombol "Tambah Router" di atas untuk menghubungkan perangkat Mikrotik Anda.</p>
            </div>
        @else
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#16181d] border-b border-slate-200 dark:border-[#1a1c21] text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading">
                            <th class="py-3.5 px-6">Nama Router</th>
                            <th class="py-3.5 px-6">IP Address & Port</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#1a1c21] text-xs">
                        @foreach($servers as $server)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#16181d]/50 transition duration-150">
                                <td class="py-4 px-6 font-bold text-slate-800 dark:text-slate-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-2 h-2 rounded-full {{ $server->status == 'connect' ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-slate-300 dark:bg-slate-700' }}"></div>
                                        <span>{{ $server->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono bg-slate-100 dark:bg-[#1a1c21] text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded-md text-[11px] border border-slate-200 dark:border-slate-800">
                                            {{ $server->ip_address }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">Port: {{ $server->api_port }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($server->status == 'connect')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-[#a6ff00]/10 dark:text-[#a6ff00] dark:border-[#a6ff00]/20">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 dark:bg-[#a6ff00] rounded-full mr-1.5 animate-pulse"></span>
                                            Connected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-50 text-red-600 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                            Disconnected
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- CEK KONEKSI -->
                                        <a href="{{ route('mikrotik.test', $server->id) }}"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-[#1a1c21] dark:text-slate-300 dark:hover:bg-slate-800 transition">
                                            Cek Koneksi
                                        </a>

                                        <!-- HAPUS ROUTER -->
                                        <form action="{{ route('mikrotik.destroy', $server->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus router {{ $server->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-500/10 text-red-600 hover:bg-red-500/20 border border-red-500/20 dark:text-red-400 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<!-- MODAL POP-UP TAMBAH ROUTER -->
<div id="addRouterModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-200">
    <div class="bg-white dark:bg-[#121316] w-full max-w-lg rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-200" id="modalContainer">

        <!-- Header Modal -->
        <div class="p-5 border-b border-slate-200 dark:border-[#1a1c21] flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <span>⚡</span> Tambah Router Mikrotik Baru
            </h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 focus:outline-none">
                ✕
            </button>
        </div>

        <!-- Form Tambah Router -->
        <form action="{{ route('mikrotik.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Nama Router / Identitas</label>
                <input type="text" name="name" required placeholder="Contoh: Router Core Utama"
                    class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">IP Address Router</label>
                <input type="text" name="ip_address" required placeholder="192.168.88.1 atau IP Publik"
                    class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Username API</label>
                    <input type="text" name="username" required placeholder="admin"
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Password API</label>
                    <input type="password" name="password" placeholder="••••••••"
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Port API Mikrotik</label>
                <input type="number" name="api_port" value="8728" required
                    class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                <p class="text-[10px] text-slate-400 mt-1">Default API: 8728 | API-SSL: 8729</p>
            </div>

            <!-- Footer Modal Buttons -->
            <div class="pt-4 border-t border-slate-200 dark:border-[#1a1c21] flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddModal()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1a1c21] transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-[#a6ff00] dark:text-black dark:hover:bg-[#92eb00] transition shadow-md">
                    Simpan & Hubungkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT CONTROLLER MODAL -->
<script>
    function openAddModal() {
        const modal = document.getElementById('addRouterModal');
        const container = document.getElementById('modalContainer');
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeAddModal() {
        const modal = document.getElementById('addRouterModal');
        const container = document.getElementById('modalContainer');
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Menutup modal saat klik di luar area dialog
    document.getElementById('addRouterModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddModal();
        }
    });
</script>
@endsection
