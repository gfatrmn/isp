@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- HEADER SECTION -->
    <div class="flex items-center justify-between bg-white dark:bg-[#121316] p-6 rounded-2xl border border-slate-200 dark:border-[#1a1c21] shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                📝 Pendaftaran Pelanggan Baru
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Isi formulir berikut untuk menambahkan data pelanggan ke sistem.
            </p>
        </div>
        <a href="{{ route('customers.index') }}"
           class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-[#1a1c21] dark:hover:bg-[#22252c] text-slate-700 dark:text-slate-300 transition">
            ← Kembali
        </a>
    </div>

    <!-- ERROR VALIDATION -->
    @if($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 text-red-800 dark:text-red-300 rounded-xl text-xs shadow-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM UTAMA -->
    <div class="bg-white dark:bg-[#121316] p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-[#1a1c21]">
        <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- BARIS 1: IDENTITAS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso" value="{{ old('name') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Nomor KTP / NIK</label>
                    <input type="text" name="nik" placeholder="351601xxxxxxxxxx" value="{{ old('nik') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
            </div>

            <!-- BARIS 2: KONTAK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Nomor WhatsApp / Telepon</label>
                    <input type="text" name="phone" required placeholder="081234567890" value="{{ old('phone') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Email (Opsional)</label>
                    <input type="email" name="email" placeholder="pelanggan@gmail.com" value="{{ old('email') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
            </div>

            <!-- BARIS 3: PAKET, ODP, TANGGAL BILLING -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Paket Layanan</label>
                    <select name="package_id" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }} - Rp {{ number_format($package->price) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Terminal ODP</label>
                    <select name="odp_id"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                        <option value="">-- Tanpa ODP --</option>
                        @foreach($odps as $odp)
                            <option value="{{ $odp->id }}">{{ $odp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Tgl Jatuh Tempo (1-28)</label>
                    <input type="number" name="billing_date" value="5" min="1" max="28" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
            </div>

            <!-- BARIS 4: WILAYAH KECAMATAN & DESA -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Kecamatan / Daerah</label>
                    <input type="text" name="district" placeholder="Contoh: Pesantren" value="{{ old('district') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Desa / Kelurahan</label>
                    <input type="text" name="village" placeholder="Contoh: Banaran" value="{{ old('village') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
                </div>
            </div>

            <!-- BARIS 5: ALAMAT LENGKAP & MAPS -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Alamat Lengkap</label>
                <textarea name="address" required rows="2" placeholder="Jl. Raya No. 123, RT 01 / RW 02..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">{{ old('address') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Link Google Maps Location</label>
                <input type="url" name="google_maps_url" placeholder="https://maps.google.com/?q=-7.8123,112.0123" value="{{ old('google_maps_url') }}"
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] text-xs font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 dark:focus:border-[#a6ff00] transition">
            </div>

            <!-- BARIS 6: UPLOAD FOTO KTP & FOTO RUMAH -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-200 dark:border-[#1a1c21]">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Upload Foto KTP</label>
                    <input type="file" name="ktp_image" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#a6ff00]/10 dark:file:text-[#a6ff00] bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] rounded-xl cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-heading">Upload Foto Rumah</label>
                    <input type="file" name="house_image" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#a6ff00]/10 dark:file:text-[#a6ff00] bg-slate-50 dark:bg-[#0e0f11] border border-slate-200 dark:border-[#1a1c21] rounded-xl cursor-pointer">
                </div>
            </div>

            <!-- BUTTON SIMPAN -->
            <div class="pt-4 border-t border-slate-200 dark:border-[#1a1c21] flex justify-end gap-3">
                <a href="{{ route('customers.index') }}"
                   class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-[#1a1c21] dark:hover:bg-[#22252c] text-slate-700 dark:text-slate-300 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-[#a6ff00] dark:text-black dark:hover:bg-[#92eb00] transition shadow-md">
                    Simpan Pelanggan Baru 🚀
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
