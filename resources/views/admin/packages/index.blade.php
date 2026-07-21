@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#121316] p-6 rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600 dark:text-[#a6ff00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Paket Layanan / Profile
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola batasan bandwidth, tarif bulanan, dan profil MikroTik untuk pelanggan PPPoE.
            </p>
        </div>

        <!-- TOMBOL BUKA MODAL -->
        <button onclick="openAddModal()"
            class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-[#a6ff00] dark:text-black dark:hover:bg-[#92eb00] font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Paket Baru</span>
        </button>
    </div>

    <!-- NOTIFIKASI FLASH & ERROR VALIDASI -->
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

    @if ($errors->any())
        <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 text-amber-800 dark:text-amber-300 rounded-xl text-xs shadow-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TABEL DAFTAR PAKET -->
    <div class="bg-white dark:bg-[#121316] rounded-2xl shadow-sm border border-slate-200 dark:border-[#1a1c21] overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-[#1a1c21] flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white font-heading">
                Daftar Paket Terdaftar ({{ $packages->count() }})
            </h2>
        </div>

        @if($packages->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="w-12 h-12 bg-slate-100 dark:bg-[#1a1c21] text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum Ada Paket Layanan</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Klik tombol "Tambah Paket Baru" di atas untuk menambahkan profil baru.</p>
            </div>
        @else
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#16181d] border-b border-slate-200 dark:border-[#1a1c21] text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading">
                            <th class="py-3.5 px-6">Nama Layanan</th>
                            <th class="py-3.5 px-6">Harga Bulanan</th>
                            <th class="py-3.5 px-6">Speed Limit</th>
                            <th class="py-3.5 px-6">Profile MikroTik</th>
                            <th class="py-3.5 px-6 text-center">User Aktif</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#1a1c21] text-xs">
                        @foreach($packages as $package)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#16181d]/50 transition duration-150">
                                <td class="py-4 px-6 font-bold text-slate-800 dark:text-slate-200 font-heading">
                                    {{ $package->name }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300 font-mono">
                                    Rp {{ number_format($package->price, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-mono bg-slate-100 dark:bg-[#1a1c21] text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded-md text-[11px] border border-slate-200 dark:border-slate-800">
                                        ⚡ {{ $package->speed_limit }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center gap-1.5 font-mono bg-emerald-50 text-emerald-700 dark:bg-[#a6ff00]/10 dark:text-[#a6ff00] border border-emerald-200 dark:border-[#a6ff00]/20 px-2.5 py-1 rounded-lg text-[11px] font-bold">
                                        <span>🟢</span> {{ $package->mikrotik_profile }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20">
                                        {{ $package->customers_count ?? 0 }} User
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <form action="{{ route('packages.destroy', $package->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket {{ $package->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-500/10 text-red-600 hover:bg-red-500/20 border border-red-500/20 dark:text-red-400 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<!-- MODAL POP-UP TAMBAH PAKET -->
<div id="addPackageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-200">
    <div class="bg-white dark:bg-[#121316] w-full max-w-lg rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-200" id="modalContainer">

        <!-- Header Modal -->
        <div class="p-5 border-b border-slate-200 dark:border-[#1a1c21] flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <span>📦</span> Buat Paket Layanan Baru
            </h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 focus:outline-none">
                ✕
            </button>
        </div>

        <!-- Form Tambah Paket -->
        <form action="{{ route('packages.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Nama Paket</label>
                <input type="text" name="name" required placeholder="Contoh: Home Super 20 Mbps"
                    class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Harga Bulanan (Rp)</label>
                <input type="number" name="price" required placeholder="150000"
                    class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Speed Limit (Bandwidth)</label>
                <input type="text" name="speed_limit" required placeholder="Contoh: 10M/20M"
                    class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                <p class="text-[10px] text-slate-400 mt-1">Format MikroTik: Upload/Download (M = Mbps, k = kbps).</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Nama Profile MikroTik (Normal)</label>
                <input type="text" name="mikrotik_profile" required placeholder="Contoh: profile-20m"
                    class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                <p class="text-[10px] text-slate-400 mt-1">Pastikan nama profil cocok dengan yang terdaftar di MikroTik PPP Profile.</p>
            </div>

            <!-- Footer Modal Buttons -->
            <div class="pt-4 border-t border-slate-200 dark:border-[#1a1c21] flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddModal()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1a1c21] transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-[#a6ff00] dark:text-black dark:hover:bg-[#92eb00] transition shadow-md">
                    Simpan Paket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT CONTROLLER MODAL -->
<script>
    function openAddModal() {
        const modal = document.getElementById('addPackageModal');
        const container = document.getElementById('modalContainer');
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeAddModal() {
        const modal = document.getElementById('addPackageModal');
        const container = document.getElementById('modalContainer');
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Menutup modal saat klik di luar area dialog
    document.getElementById('addPackageModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddModal();
        }
    });
</script>
@endsection
