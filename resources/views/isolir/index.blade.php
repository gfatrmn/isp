@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#121316] p-6 rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
                Manajemen Isolir Pelanggan
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola pemblokiran akses internet via Firewall Address List MikroTik tanpa memutus sesi dial-up.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400 font-mono bg-slate-100 dark:bg-[#1a1c21] px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800">
                Firewall List: <strong class="text-slate-700 dark:text-slate-200">ISOLIR_PELANGGAN</strong>
            </span>
        </div>
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

    <!-- STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#121316] p-4 rounded-xl border border-slate-200 dark:border-[#1a1c21] shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading block">Total Pelanggan</span>
                <div class="text-xl font-extrabold text-slate-800 dark:text-slate-200 font-heading mt-0.5">{{ $customers->total() }}</div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-[#1a1c21] text-slate-600 dark:text-slate-400 flex items-center justify-center text-sm font-bold">👥</div>
        </div>

        <div class="bg-white dark:bg-[#121316] p-4 rounded-xl border border-slate-200 dark:border-[#1a1c21] shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading block">Pelanggan Terisolir</span>
                <div class="text-xl font-extrabold text-red-600 dark:text-red-400 font-heading mt-0.5">
                    {{ $customers->where('status', 'isolated')->count() }}
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center text-sm font-bold">🚫</div>
        </div>

        <div class="bg-white dark:bg-[#121316] p-4 rounded-xl border border-slate-200 dark:border-[#1a1c21] shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading block">Status Aktif</span>
                <div class="text-xl font-extrabold text-emerald-600 dark:text-[#a6ff00] font-heading mt-0.5">
                    {{ $customers->where('status', '!=', 'isolated')->count() }}
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-[#a6ff00] flex items-center justify-center text-sm font-bold">✅</div>
        </div>
    </div>

    <!-- TABEL PELANGGAN ISOLIR -->
    <div class="bg-white dark:bg-[#121316] rounded-2xl shadow-sm border border-slate-200 dark:border-[#1a1c21] overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-[#1a1c21] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white font-heading">
                Daftar Pelanggan & Status Firewall
            </h2>
        </div>

        @if($customers->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="w-12 h-12 bg-slate-100 dark:bg-[#1a1c21] text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum Ada Data Pelanggan</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Silakan tambahkan data pelanggan terlebih dahulu.</p>
            </div>
        @else
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#16181d] border-b border-slate-200 dark:border-[#1a1c21] text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-heading">
                            <th class="py-3.5 px-6">Pelanggan</th>
                            <th class="py-3.5 px-6">IP Address & Server Router</th>
                            <th class="py-3.5 px-6 text-center">Status Akses</th>
                            <th class="py-3.5 px-6 text-right">Aksi Firewall</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#1a1c21] text-xs">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#16181d]/50 transition duration-150">
                                <!-- NAMA PELANGGAN -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ $customer->name }}
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        Code: {{ $customer->code ?? '-' }}
                                    </span>
                                </td>

                                <!-- IP & ROUTER -->
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-1">
                                        @if($customer->ip_address)
                                            <span class="font-mono bg-slate-100 dark:bg-[#1a1c21] text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded-md text-[11px] border border-slate-200 dark:border-slate-800 w-fit">
                                                {{ $customer->ip_address }}
                                            </span>
                                        @else
                                            <span class="text-red-500 text-[10px] italic">Belum ada IP Static</span>
                                        @endif
                                        <span class="text-[10px] text-slate-400">
                                            Router: {{ $customer->mikrotikServer->name ?? 'Belum Diatur' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- STATUS ISOLIR -->
                                <td class="py-4 px-6 text-center">
                                    @if($customer->status == 'isolated')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-50 text-red-600 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                            Terisolir (Blocked)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-[#a6ff00]/10 dark:text-[#a6ff00] dark:border-[#a6ff00]/20">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 dark:bg-[#a6ff00] rounded-full mr-1.5"></span>
                                            Aktif (Normal)
                                        </span>
                                    @endif
                                </td>

                                <!-- AKSI BLOCK / UNBLOCK -->
                                <td class="py-4 px-6 text-right">
                                    @if($customer->status == 'isolated')
                                        <!-- BUKA ISOLIR -->
                                        <form action="{{ route('isolir.unblock', $customer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Buka isolir untuk {{ $customer->name }}?')">
                                            @csrf
                                            <button type="submit"
                                                class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-700 hover:bg-emerald-500/20 border border-emerald-500/20 dark:bg-[#a6ff00]/10 dark:text-[#a6ff00] dark:border-[#a6ff00]/20 transition shadow-sm">
                                                Buka Isolir 🟢
                                            </button>
                                        </form>
                                    @else
                                        <!-- ISOLIR -->
                                        <form action="{{ route('isolir.block', $customer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Isolir akses internet {{ $customer->name }}?')">
                                            @csrf
                                            <button type="submit"
                                                class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-red-500/10 text-red-600 hover:bg-red-500/20 border border-red-500/20 dark:text-red-400 transition shadow-sm"
                                                {{ empty($customer->ip_address) ? 'disabled' : '' }}>
                                                Isolir 🔴
                                            </button>
                                        </form>
                                    @endif
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
