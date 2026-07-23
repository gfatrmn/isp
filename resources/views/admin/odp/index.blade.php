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
                Manajemen Terminal ODP
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola pendataan titik sebaran Optical Distribution Point (ODP), kapasitas splitter port, dan lokasi titik koordinat.
            </p>
        </div>

        <!-- TOMBOL BUKA MODAL TAMBAH ODP -->
        <button onclick="openAddOdpModal()"
            class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-[#a6ff00] dark:text-black dark:hover:bg-[#92eb00] font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah ODP Baru</span>
        </button>
    </div>

    <!-- NOTIFIKASI FLASH -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 rounded-xl text-xs flex items-center gap-2 shadow-sm">
            <span>✅ {{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 text-red-800 dark:text-red-300 rounded-xl text-xs flex items-center gap-2 shadow-sm">
            <span>⚠️ {{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 text-red-800 dark:text-red-300 rounded-xl text-xs shadow-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TABEL DATA ODP -->
    <div class="bg-white dark:bg-[#121316] rounded-2xl shadow-sm border border-slate-200 dark:border-[#1a1c21] overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-[#1a1c21] flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white font-heading">
                Daftar Terminal ODP Terdaftar ({{ $odps->total() }})
            </h2>
        </div>

        @if($odps->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="w-12 h-12 bg-slate-100 dark:bg-[#1a1c21] text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum Ada Terminal ODP</p>
                <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah ODP Baru" di atas untuk mendaftarkan ODP pertama Anda.</p>
            </div>
        @else
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#16181d] border-b border-slate-200 dark:border-[#1a1c21] text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading">
                            <th class="py-3.5 px-6">Kode & Nama ODP</th>
                            <th class="py-3.5 px-6">Alamat & Wilayah</th>
                            <th class="py-3.5 px-6 text-center">Penggunaan Port</th>
                            <th class="py-3.5 px-6 text-center">Media (Foto/Peta)</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#1a1c21] text-xs">
                        @foreach($odps as $odp)
                            @php
                                $used = $odp->customers_count ?? 0;
                                $percentage = $odp->capacity > 0 ? round(($used / $odp->capacity) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#16181d]/50 transition duration-150">
                                <!-- KODE & NAMA -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $odp->name }}</div>
                                    <span class="inline-block mt-0.5 font-mono bg-slate-100 dark:bg-[#1a1c21] text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded text-[10px] border border-slate-200 dark:border-slate-800">
                                        {{ $odp->code }}
                                    </span>
                                </td>

                                <!-- ALAMAT & WILAYAH -->
                                <td class="py-4 px-6">
                                    <div class="text-slate-700 dark:text-slate-300 font-medium">
                                        {{ Str::limit($odp->address, 35) }}
                                    </div>
                                    @if($odp->district || $odp->village)
                                        <div class="text-[10px] text-emerald-600 dark:text-[#a6ff00] font-semibold mt-0.5">
                                            Kec. {{ $odp->district ?? '-' }}, Desa {{ $odp->village ?? '-' }}
                                        </div>
                                    @endif
                                </td>

                                <!-- PENGGUNAAN PORT -->
                                <td class="py-4 px-6 text-center">
                                    <div class="max-w-[120px] mx-auto">
                                        <div class="flex justify-between text-[10px] font-mono font-bold mb-1">
                                            <span>{{ $used }}/{{ $odp->capacity }} Port</span>
                                            <span>{{ $percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300 {{ $percentage >= 90 ? 'bg-red-500' : ($percentage >= 70 ? 'bg-amber-500' : 'bg-emerald-500 dark:bg-[#a6ff00]') }}"
                                                style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- ICON MEDIA (FOTO & MAPS) -->
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- ICON FOTO -->
                                        @if($odp->odp_image)
                                            <a href="{{ asset('storage/' . $odp->odp_image) }}" target="_blank" rel="noopener noreferrer" title="Lihat Foto ODP"
                                                class="p-2 rounded-xl bg-slate-100 dark:bg-[#1a1c21] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:text-emerald-500 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="p-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] text-slate-300 dark:text-slate-700 border border-slate-100 dark:border-[#1a1c21] cursor-not-allowed" title="Foto Tidak Tersedia">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </span>
                                        @endif

                                        <!-- ICON MAPS -->
                                        @if($odp->google_maps_url)
                                            <a href="{{ $odp->google_maps_url }}" target="_blank" rel="noopener noreferrer" title="Buka Lokasi Google Maps"
                                                class="p-2 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 hover:bg-blue-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="p-2 rounded-xl bg-slate-50 dark:bg-[#0e0f11] text-slate-300 dark:text-slate-700 border border-slate-100 dark:border-[#1a1c21] cursor-not-allowed" title="Lokasi Maps Tidak Tersedia">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- ICON AKSI (EDIT & HAPUS) -->
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- ICON EDIT -->
                                        <button type="button" onclick="openEditOdpModal({{ json_encode($odp) }})" title="Edit ODP"
                                            class="p-2 rounded-xl bg-amber-500/10 text-amber-600 hover:bg-amber-500/20 border border-amber-500/20 dark:text-amber-400 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>

                                        <!-- ICON HAPUS -->
                                        <form action="{{ route('odp.destroy', $odp->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ODP {{ e($odp->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus ODP"
                                                class="p-2 rounded-xl bg-red-500/10 text-red-600 hover:bg-red-500/20 border border-red-500/20 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="p-4 border-t border-slate-200 dark:border-[#1a1c21]">
                {{ $odps->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL POP-UP TAMBAH ODP -->
<div id="addOdpModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-200">
    <div class="bg-white dark:bg-[#121316] w-full max-w-xl rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-200 max-h-[90vh] flex flex-col" id="modalOdpContainer">

        <div class="p-5 border-b border-slate-200 dark:border-[#1a1c21] flex items-center justify-between flex-shrink-0">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <span>📍</span> Tambah Terminal ODP Baru
            </h3>
            <button onclick="closeAddOdpModal()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 focus:outline-none">✕</button>
        </div>

        <form action="{{ route('odp.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto no-scrollbar">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Kode ODP</label>
                    <input type="text" name="code" required placeholder="ODP-PSN-001" value="{{ old('code') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Nama ODP</label>
                    <input type="text" name="name" required placeholder="ODP Banaran 01" value="{{ old('name') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Kapasitas</label>
                    <select name="capacity" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                        <option value="8">8 Port (1:8)</option>
                        <option value="16">16 Port (1:16)</option>
                        <option value="24">24 Port (1:24)</option>
                        <option value="32">32 Port (1:32)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Kecamatan</label>
                    <input type="text" name="district" placeholder="Pesantren" value="{{ old('district') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Desa / Kelurahan</label>
                    <input type="text" name="village" placeholder="Banaran" value="{{ old('village') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Alamat Tiang / Lokasi ODP</label>
                <textarea name="address" required rows="2" placeholder="Tiang PLN depan toko serba ada..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">{{ old('address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Link Google Maps Location</label>
                    <input type="url" name="google_maps_url" placeholder="https://maps.google.com/?q=..." value="{{ old('google_maps_url') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Upload Foto Fisik ODP</label>
                    <input type="file" name="odp_image" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#a6ff00]/10 dark:file:text-[#a6ff00] bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] rounded-xl cursor-pointer">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-[#1a1c21] flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddOdpModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1a1c21] transition">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-[#a6ff00] dark:text-black dark:hover:bg-[#92eb00] transition shadow-md">Simpan ODP</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL POP-UP EDIT ODP -->
<div id="editOdpModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-200">
    <div class="bg-white dark:bg-[#121316] w-full max-w-xl rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-200 max-h-[90vh] flex flex-col" id="modalEditOdpContainer">

        <div class="p-5 border-b border-slate-200 dark:border-[#1a1c21] flex items-center justify-between flex-shrink-0">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <span>✏️</span> Edit Data Terminal ODP
            </h3>
            <button onclick="closeEditOdpModal()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 focus:outline-none">✕</button>
        </div>

        <form id="editOdpForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto no-scrollbar">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Kode ODP</label>
                    <input type="text" name="code" id="edit_code" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Nama ODP</label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Kapasitas</label>
                    <select name="capacity" id="edit_capacity" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                        <option value="8">8 Port (1:8)</option>
                        <option value="16">16 Port (1:16)</option>
                        <option value="24">24 Port (1:24)</option>
                        <option value="32">32 Port (1:32)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Kecamatan</label>
                    <input type="text" name="district" id="edit_district"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Desa / Kelurahan</label>
                    <input type="text" name="village" id="edit_village"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Alamat Tiang / Lokasi ODP</label>
                <textarea name="address" id="edit_address" required rows="2"
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Link Google Maps Location</label>
                    <input type="url" name="google_maps_url" id="edit_google_maps_url"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Ganti Foto Fisik ODP</label>
                    <input type="file" name="odp_image" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#a6ff00]/10 dark:file:text-[#a6ff00] bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] rounded-xl cursor-pointer">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-[#1a1c21] flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditOdpModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1a1c21] transition">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white transition shadow-md">Update ODP</button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT CONTROLLER MODAL (ADD & EDIT) -->
<script>
    function openAddOdpModal() {
        const modal = document.getElementById('addOdpModal');
        const container = document.getElementById('modalOdpContainer');
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeAddOdpModal() {
        const modal = document.getElementById('addOdpModal');
        const container = document.getElementById('modalOdpContainer');
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.add('hidden'); }, 200);
    }

    function openEditOdpModal(odp) {
        const modal = document.getElementById('editOdpModal');
        const container = document.getElementById('modalEditOdpContainer');
        const form = document.getElementById('editOdpForm');

        form.action = `/odp/${odp.id}`;
        document.getElementById('edit_code').value = odp.code || '';
        document.getElementById('edit_name').value = odp.name || '';
        document.getElementById('edit_capacity').value = odp.capacity || 8;
        document.getElementById('edit_district').value = odp.district || '';
        document.getElementById('edit_village').value = odp.village || '';
        document.getElementById('edit_address').value = odp.address || '';
        document.getElementById('edit_google_maps_url').value = odp.google_maps_url || '';

        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEditOdpModal() {
        const modal = document.getElementById('editOdpModal');
        const container = document.getElementById('modalEditOdpContainer');
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.add('hidden'); }, 200);
    }

    document.getElementById('addOdpModal')?.addEventListener('click', function(e) { if (e.target === this) closeAddOdpModal(); });
    document.getElementById('editOdpModal')?.addEventListener('click', function(e) { if (e.target === this) closeEditOdpModal(); });
</script>
@endsection
