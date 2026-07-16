@extends('layouts.app')

@section('content')
<div class="mb-8 border-b pb-4">
    <h1 class="text-2xl font-bold text-gray-800">Profile / Paket Layanan</h1>
    <p class="text-sm text-gray-600">Kelola bandwidth, harga bulanan, profil normal, dan profil isolasi pelanggan Mikrotik.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 bg-amber-100 border-l-4 border-amber-500 text-amber-800 rounded-r-lg shadow-sm text-sm">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- FORM INPUT -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Buat Paket Baru</h2>
        <form action="{{ route('packages.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Nama Paket</label>
                <input type="text" name="name" required placeholder="Contoh: Paket Home 10 Mbps" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Harga Bulanan (Rp)</label>
                <input type="number" name="price" required placeholder="150000" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Speed Limit (Bandwidth)</label>
                <input type="text" name="speed_limit" required placeholder="Contoh: 10M/10M" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none font-mono">
                <span class="text-[10px] text-gray-400 mt-1 block">Format: Upload/Download (M = Mbps).</span>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Profile Mikrotik (Normal)</label>
                <input type="text" name="mikrotik_profile" required placeholder="Contoh: profile-10m" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Profile Mikrotik (Isolasi)</label>
                <input type="text" name="mikrotik_profile_isolated" required placeholder="Contoh: isolir-10m" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none font-mono">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow">
                Simpan Paket
            </button>
        </form>
    </div>

    <!-- TABEL DATA -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Daftar Paket Internet</h2>
        @if($packages->isEmpty())
            <div class="text-center py-12 text-gray-500 text-sm">Belum ada paket layanan terdaftar.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-500">
                            <th class="p-3">Nama Layanan</th>
                            <th class="p-3">Harga</th>
                            <th class="p-3">Speed Limit</th>
                            <th class="p-3">Mikrotik Profile (Normal / Isolasi)</th>
                            <th class="p-3 text-center">User Aktif</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($packages as $package)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-3 font-semibold text-gray-800">{{ $package->name }}</td>
                                <td class="p-3 font-semibold text-slate-700">
                                    Rp {{ number_format($package->price, 0, ',', '.') }}
                                </td>
                                <td class="p-3 font-mono text-xs text-slate-600">{{ $package->speed_limit }}</td>
                                <td class="p-3 space-y-1">
                                    <span class="block font-mono bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded w-fit text-[11px] font-bold">🟢 {{ $package->mikrotik_profile }}</span>
                                    <span class="block font-mono bg-red-50 text-red-700 border border-red-200 px-2 py-0.5 rounded w-fit text-[11px] font-bold">🔴 {{ $package->mikrotik_profile_isolated }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $package->customers_count }} User
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Hapus paket layanan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs px-2.5 py-1.5 rounded-lg font-medium shadow-sm transition">
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
@endsection
