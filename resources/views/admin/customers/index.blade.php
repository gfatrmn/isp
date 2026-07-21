@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#121316] p-6 rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600 dark:text-[#a6ff00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Data Pelanggan
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola identitas, peta lokasi, foto berkas, serta profil layanan pelanggan.
            </p>
        </div>

        <!-- TOMBOL TAMBAH PELANGGAN (PINDAH HALAMAN) -->
        <div>
            <a href="{{ route('customers.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-[#a6ff00] dark:text-black dark:hover:bg-[#92eb00] transition shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pelanggan Baru
            </a>
        </div>
    </div>

    <!-- FLASH NOTIFICATION -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 rounded-xl text-xs flex items-center gap-2 shadow-sm">
            <span>✅ {{ session('success') }}</span>
        </div>
    @endif

    <!-- TABEL DATA PELANGGAN -->
    <div class="bg-white dark:bg-[#121316] rounded-2xl shadow-sm border border-slate-200 dark:border-[#1a1c21] overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-[#1a1c21] flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white font-heading">
                Daftar Pelanggan Terdaftar ({{ $customers->total() }})
            </h2>
        </div>

        @if($customers->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="w-12 h-12 bg-slate-100 dark:bg-[#1a1c21] text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum Ada Pelanggan</p>
                <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Pelanggan Baru" di atas untuk menambahkan data baru.</p>
            </div>
        @else
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#16181d] border-b border-slate-200 dark:border-[#1a1c21] text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading">
                            <th class="py-3.5 px-6">ID & Nama Pelanggan</th>
                            <th class="py-3.5 px-6">Kontak & Wilayah</th>
                            <th class="py-3.5 px-6">Paket & ODP</th>
                            <th class="py-3.5 px-6 text-center">Berkas & Peta</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#1a1c21] text-xs">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#16181d]/50 transition duration-150">
                                <!-- ID & NAMA -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $customer->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $customer->customer_number }}</div>
                                    @if($customer->nik)
                                        <div class="text-[10px] text-slate-500 font-mono">NIK: {{ $customer->nik }}</div>
                                    @endif
                                </td>

                                <!-- KONTAK & WILAYAH -->
                                <td class="py-4 px-6">
                                    <div class="text-slate-700 dark:text-slate-300 font-medium">📞 {{ $customer->phone }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ Str::limit($customer->address, 30) }}
                                    </div>
                                    @if($customer->district || $customer->village)
                                        <div class="text-[10px] text-emerald-600 dark:text-[#a6ff00] font-semibold">
                                            Kec. {{ $customer->district ?? '-' }}, Desa {{ $customer->village ?? '-' }}
                                        </div>
                                    @endif
                                </td>

                                <!-- PAKET & ODP -->
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center gap-1 font-mono bg-emerald-50 text-emerald-700 dark:bg-[#a6ff00]/10 dark:text-[#a6ff00] border border-emerald-200 dark:border-[#a6ff00]/20 px-2.5 py-1 rounded-lg text-[11px] font-bold">
                                        📦 {{ $customer->package->name ?? 'Tanpa Paket' }}
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-1 font-mono">
                                        ODP: {{ $customer->odp->name ?? 'Belum Diatur' }} | Billing: Tgl {{ $customer->billing_date }}
                                    </div>
                                </td>

                                <!-- BERKAS & PETA -->
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($customer->ktp_image)
                                            <a href="{{ asset('storage/' . $customer->ktp_image) }}" target="_blank"
                                                class="px-2 py-1 rounded bg-slate-100 dark:bg-[#1a1c21] text-[10px] font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:border-emerald-500">
                                                🪪 KTP
                                            </a>
                                        @else
                                            <span class="text-[9px] text-slate-400 italic">No KTP</span>
                                        @endif

                                        @if($customer->house_image)
                                            <a href="{{ asset('storage/' . $customer->house_image) }}" target="_blank"
                                                class="px-2 py-1 rounded bg-slate-100 dark:bg-[#1a1c21] text-[10px] font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:border-emerald-500">
                                                🏠 Rumah
                                            </a>
                                        @endif

                                        @if($customer->google_maps_url)
                                            <a href="{{ $customer->google_maps_url }}" target="_blank"
                                                class="px-2 py-1 rounded bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 text-[10px] font-bold">
                                                📍 Maps
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <!-- STATUS -->
                                <td class="py-4 px-6 text-center">
                                    @if($customer->status == 'active')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-[#a6ff00]/10 dark:text-[#a6ff00] border border-emerald-200 dark:border-[#a6ff00]/20">Aktif</span>
                                    @elseif($customer->status == 'isolated')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-500/20">Isolir</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">Nonaktif</span>
                                    @endif
                                </td>

                                <!-- AKSI -->
                                <td class="py-4 px-6 text-right">
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data pelanggan {{ $customer->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-500/10 text-red-600 hover:bg-red-500/20 border border-red-500/20 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="p-4 border-t border-slate-200 dark:border-[#1a1c21]">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
