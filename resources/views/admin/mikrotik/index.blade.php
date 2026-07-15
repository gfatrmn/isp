@extends('layouts.app')

@section('content')
<div class="mb-8 border-b pb-4">
    <h1 class="text-2xl font-bold text-gray-800">Server Mikrotik</h1>
    <p class="text-sm text-gray-600">Hubungkan router Anda untuk monitoring bandwidth realtime dan manajemen PPPoE.</p>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- FORM TAMBAH ROUTER -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Tambah Router Baru</h2>
        <form action="{{ route('mikrotik.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Nama Router</label>
                <input type="text" name="name" required placeholder="Contoh: Router Core" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">IP Address</label>
                <input type="text" name="ip_address" required placeholder="192.168.1.1" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Username API</label>
                    <input type="text" name="username" required placeholder="admin" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Password</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Port API Mikrotik</label>
                <input type="number" name="api_port" value="8728" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow">
                Simpan Router
            </button>
        </form>
    </div>

    <!-- TABEL DAFTAR ROUTER -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Koneksi Router</h2>
        @if($servers->isEmpty())
            <div class="text-center py-12 text-gray-500 text-sm">Belum ada data router terdaftar.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-500">
                            <th class="p-3">Nama Router</th>
                            <th class="p-3">IP & Port</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($servers as $server)
                            <tr>
                                <td class="p-3 font-semibold text-gray-800">{{ $server->name }}</td>
                                <td class="p-3">
                                    <span class="block font-mono bg-gray-100 px-2 py-0.5 rounded w-fit text-xs">{{ $server->ip_address }}</span>
                                    <span class="text-xxs text-gray-400 mt-1 block">Port: {{ $server->api_port }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    @if($server->status == 'connect')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span> Connected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span> Disconnected
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center space-x-1">
                                    <a href="{{ route('mikrotik.test', $server->id) }}" class="bg-slate-600 hover:bg-slate-700 text-white text-xs px-2.5 py-1.5 rounded-lg font-medium shadow-sm transition">Cek Koneksi</a>
                                    @if($server->status == 'connect')
                                        <a href="{{ route('mikrotik.monitor', $server->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-2.5 py-1.5 rounded-lg font-medium shadow-sm transition">Monitor Live 📊</a>
                                    @endif
                                    <form action="{{ route('mikrotik.destroy', $server->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus router?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs px-2.5 py-1.5 rounded-lg font-medium shadow-sm transition">Hapus</button>
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
