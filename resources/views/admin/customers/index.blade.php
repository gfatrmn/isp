@extends('layouts.app')

@section('content')
<div class="mb-8 border-b pb-4">
    <h1 class="text-2xl font-bold text-gray-800">Manajemen Pelanggan</h1>
    <p class="text-sm text-gray-600">Kelola master data fisik pelanggan dan status penagihan biling internet.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-lg text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- FORM INPUT -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
        <h2 class="text-md font-bold mb-4 text-gray-800 border-b pb-2">Registrasi Pelanggan</h2>
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Nama Lengkap" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">No. WhatsApp</label>
                <input type="text" name="phone" required placeholder="0812xxxxxxxx" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Pilih Paket Layanan</label>
                <select name="package_id" required class="w-full bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
                    <option value="">-- Pilih Paket Internet --</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}">{{ $package->name }} (Rp {{ number_format($package->price) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Tgl Jatuh Tempo (1-28)</label>
                <input type="number" name="billing_date" value="5" min="1" max="28" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Alamat Rumah</label>
                <textarea name="address" rows="3" required placeholder="Alamat pemasangan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm focus:outline-none"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow">
                Daftarkan Pelanggan
            </button>
        </form>
    </div>

    <!-- TABEL UTAMA -->
    <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-md font-bold mb-4 text-gray-800 border-b pb-2">Database Pelanggan</h2>
        @if($customers->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada pelanggan terdaftar.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-400">
                            <th class="p-3">ID & Nama</th>
                            <th class="p-3">Kontak & Alamat</th>
                            <th class="p-3">Paket Layanan</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($customers as $customer)
                            <tr>
                                <td class="p-3">
                                    <span class="block font-mono font-bold text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded w-fit mb-1">{{ $customer->customer_number }}</span>
                                    <span class="font-semibold text-gray-800 block">{{ $customer->name }}</span>
                                </td>
                                <td class="p-3 text-xs text-gray-600">
                                    <span class="block font-medium">📞 {{ $customer->phone }}</span>
                                    <span class="text-gray-400 block mt-0.5 max-w-xs truncate">{{ $customer->address }}</span>
                                </td>
                                <td class="p-3 text-xs">
                                    <span class="block font-semibold text-gray-700">{{ $customer->package->name ?? 'Tanpa Paket' }}</span>
                                    <span class="text-gray-400 block mt-0.5">Billing: Tiap Tanggal {{ $customer->billing_date }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-bold {{ $customer->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Hapus data pelanggan?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs px-2.5 py-1.5 rounded-lg font-medium shadow-sm">Hapus</button>
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
