@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Billing & Invoice</h1>
        <p class="text-sm text-gray-500">Kelola lembar tagihan bulanan internet dan pencatatan kas masuk pelanggan.</p>
    </div>
    <div>
        <form action="{{ route('invoices.generate') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow-sm">
                ⚙️ Generate Invoice Bulan Ini
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-lg text-sm shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
    <h2 class="text-md font-bold mb-4 text-gray-800 border-b pb-2">Daftar Invoice Terbit</h2>

    @if($invoices->isEmpty())
        <div class="text-center py-12 text-gray-400 text-sm">
            Belum ada invoice yang diterbitkan. Klik tombol <strong>"Generate Invoice"</strong> di atas untuk membuat tagihan perdana.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-400">
                        <th class="p-3">No. Invoice</th>
                        <th class="p-3">Pelanggan</th>
                        <th class="p-3">Total Tagihan</th>
                        <th class="p-3">Jatuh Tempo</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @foreach($invoices as $invoice)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-3 font-mono font-bold text-xs text-gray-700">{{ $invoice->invoice_number }}</td>
                            <td class="p-3">
                                <span class="font-semibold text-gray-800 block">{{ $invoice->customer->name ?? 'Unknown' }}</span>
                                <span class="text-xxs text-gray-400 block mt-0.5">{{ $invoice->customer->customer_number ?? '' }}</span>
                            </td>
                            <td class="p-3 font-semibold text-gray-700">Rp {{ number_format($invoice->amount) }}</td>
                            <td class="p-3 text-xs text-gray-600">{{ \Carbon\Carbon::parse($invoice->due_date)->translatedFormat('d F Y') }}</td>
                            <td class="p-3 text-center">
                                @if($invoice->status == 'paid')
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-bold bg-green-100 text-green-800 rounded-full">Lunas</span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-bold bg-amber-100 text-amber-800 rounded-full">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
