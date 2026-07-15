@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Akun PPPoE Pelanggan</h1>
        <p class="text-sm text-gray-500">Sinkronisasi data Secret router Mikrotik dan petakan ke billing pelanggan.</p>
    </div>
    <div>
        @foreach($servers as $srv)
            <a href="{{ route('pppoe.sync', $srv->id) }}" class="inline-flex items-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow-sm mr-2">
                🔄 Tarik Data dari {{ $srv->name }}
            </a>
        @endforeach
    </div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- FORM BUAT -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
        <h2 class="text-md font-bold mb-4 text-gray-800 border-b pb-2">Buat Akun PPPoE</h2>
        <form action="{{ route('pppoe.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Pilih Pelanggan</label>
                <select name="customer_id" required class="w-full bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($availableCustomers as $cust)
                        <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Pilih Router</label>
                <select name="mikrotik_server_id" required class="w-full bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
                    <option value="">-- Pilih Router --</option>
                    @foreach($servers as $srv)
                        <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Username PPPoE</label>
                <input type="text" name="username" required placeholder="user_baru" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Password PPPoE</label>
                <input type="text" name="password" required placeholder="secret_pass" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow">
                Push Secret ke Mikrotik
            </button>
        </form>
    </div>

    <!-- TABEL PEMETAAN -->
    <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-md font-bold mb-4 text-gray-800 border-b pb-2">Pemetaan Akun PPPoE</h2>
        @if($customers->isEmpty() || !$customers->contains(fn($c) => $c->pppoeAccount !== null))
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada pemetaan akun terdaftar. Silakan klik "Tarik Data" untuk sinkronisasi awal.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-400">
                            <th class="p-3">Nama Pelanggan</th>
                            <th class="p-3">PPPoE Credentials</th>
                            <th class="p-3">Profile & Paket</th>
                            <th class="p-3">Server Target</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($customers as $customer)
                            @if($customer->pppoeAccount)
                                <tr>
                                    <td class="p-3">
                                        <span class="font-semibold text-gray-800 block">{{ $customer->name }}</span>
                                        <span class="text-xxs text-gray-400 font-mono block">{{ $customer->customer_number }}</span>
                                    </td>
                                    <td class="p-3">
                                        <span class="block font-mono bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded w-fit font-bold">👤 {{ $customer->pppoeAccount->username }}</span>
                                        <span class="block text-xxs text-gray-400 mt-1">🔑 Pass: {{ $customer->pppoeAccount->password }}</span>
                                    </td>
                                    <td class="p-3">
                                        <span class="block text-xs font-bold text-gray-700">{{ $customer->package->name ?? 'Default Paket' }}</span>
                                        <span class="block text-xxs text-blue-600 font-mono mt-0.5">Profile: {{ $customer->package->mikrotik_profile ?? 'default' }}</span>
                                    </td>
                                    <td class="p-3 text-xs text-gray-600">
                                        <span class="font-semibold block">🖥️ {{ $customer->pppoeAccount->mikrotikServer->name ?? 'Unknown' }}</span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
