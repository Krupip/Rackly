@extends('layouts.admin')

@section('title', 'Edit Meja — Admin Rackly Billiard')
@section('breadcrumb', 'Operasional / Kelola meja / Edit')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('tables.index') }}" class="p-2 rounded-xl bg-white border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Edit Meja {{ $table->table_number }}</h1>
            <p class="text-xs text-gray-500">Perbarui spesifikasi, tarif sewa, atau status operasional meja ini.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('tables.update', $table->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama / Nomor Meja -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="table_number">
                    Nama / Nomor Meja <span class="text-red-500">*</span>
                </label>
                <input type="text" name="table_number" id="table_number" required value="{{ old('table_number', $table->table_number) }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                @error('table_number')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipe Meja -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="type">
                    Jenis Meja <span class="text-red-500">*</span>
                </label>
                <select name="type" id="type" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                    <option value="Reguler" {{ old('type', $table->type) == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                    <option value="VIP" {{ old('type', $table->type) == 'VIP' ? 'selected' : '' }}>VIP</option>
                    <option value="Pool" {{ old('type', $table->type) == 'Pool' ? 'selected' : '' }}>Pool</option>
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Per Jam -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="price_per_hour">
                    Harga Sewa Per Jam (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="price_per_hour" id="price_per_hour" required min="0" value="{{ old('price_per_hour', $table->price_per_hour) }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                @error('price_per_hour')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="is_active">
                    Status Operasional <span class="text-red-500">*</span>
                </label>
                <select name="is_active" id="is_active" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                    <option value="1" {{ old('is_active', $table->is_active) == 1 ? 'selected' : '' }}>Aktif (Tersedia untuk Booking)</option>
                    <option value="0" {{ old('is_active', $table->is_active) == 0 ? 'selected' : '' }}>Perawatan (Maintenance)</option>
                </select>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('tables.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:text-gray-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#1B5E43] hover:bg-[#154b35] text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection