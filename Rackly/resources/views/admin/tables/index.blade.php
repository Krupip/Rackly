@extends('layouts.admin')

@section('title', 'Kelola Meja — Admin Rackly Billiard')
@section('breadcrumb', 'Operasional / Kelola meja')

@section('content')
<div x-data="{
    search: '',
    typeFilter: '',
    selectedTable: {{ $tables->first() ? json_encode([
        'id' => $tables->first()->id,
        'table_number' => $tables->first()->table_number,
        'type' => $tables->first()->type,
        'price_per_hour' => $tables->first()->price_per_hour,
        'is_active' => (int) $tables->first()->is_active,
        'update_url' => route('tables.update', $tables->first()->id)
    ]) : 'null' }},
    deleteModal: {
        show: false,
        tableNumber: '',
        actionUrl: ''
    },
    confirmDelete(number, url) {
        this.deleteModal = {
            show: true,
            tableNumber: number,
            actionUrl: url
        };
    }
}" class="space-y-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">OPERASIONAL / INVENTARIS</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Kelola meja</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Atur inventaris, tarif, dan kesiapan setiap meja di klub.</p>
        </div>

        <div>
            <a href="{{ route('tables.create') }}" class="inline-flex items-center gap-2 bg-[#1B5E43] hover:bg-[#154b35] text-white px-5 py-3 rounded-xl font-bold text-sm shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah meja
            </a>
        </div>
    </div>

    @php
        $totalTables = $tables->count();
        $activeTables = $tables->where('is_active', 1)->count();
        $maintenanceTables = $tables->where('is_active', 0)->count();
    @endphp

    <!-- Metrics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Total meja</span>
            <div class="text-3xl font-extrabold text-gray-900">{{ $totalTables }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Tersedia saat ini</span>
            <div class="text-3xl font-extrabold text-[#1B5E43]">{{ $activeTables }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Dalam perawatan</span>
            <div class="text-3xl font-extrabold text-amber-600">{{ $maintenanceTables }}</div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" x-model="search" placeholder="Cari nama atau nomor meja..."
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
        </div>

        <div class="w-full md:w-auto">
            <select x-model="typeFilter" class="w-full md:w-auto px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                <option value="">Semua jenis meja</option>
                <option value="Reguler">Reguler</option>
                <option value="VIP">VIP</option>
            </select>
        </div>
    </div>

    <!-- Main Two-Column Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Table List (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">NAMA MEJA</span>
                <div class="flex items-center gap-8 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <span>TARIF / JAM</span>
                    <span>STATUS</span>
                    <span>AKSI</span>
                </div>
            </div>

            <div class="overflow-y-auto" style="max-height: 320px; scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
                <div class="divide-y divide-gray-100">
                    @forelse($tables as $table)
                        @php
                            $tableData = json_encode([
                                'id' => $table->id,
                                'table_number' => $table->table_number,
                                'type' => $table->type,
                                'price_per_hour' => $table->price_per_hour,
                                'is_active' => (int) $table->is_active,
                                'update_url' => route('tables.update', $table->id)
                            ]);
                        @endphp
                        <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-emerald-50/30 transition cursor-pointer"
                             @click="selectedTable = {{ $tableData }}"
                             x-show="(
                                 (search === '' || '{{ strtolower($table->table_number) }}'.includes(search.toLowerCase()) || '{{ strtolower($table->type) }}'.includes(search.toLowerCase())) &&
                                 (typeFilter === '' || typeFilter === '{{ $table->type }}')
                             )">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-[#1B5E43] text-white font-extrabold text-sm flex items-center justify-center shrink-0 shadow-sm">
                                    {{ preg_replace('/[^0-9]/', '', $table->table_number) ?: '01' }}
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-gray-900 text-sm">Meja {{ $table->table_number }}</h3>
                                    <p class="text-xs text-gray-400">{{ $table->type }} · 9 kaki</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6">
                                <span class="font-extrabold text-gray-900 text-xs sm:text-sm">
                                    Rp{{ number_format($table->price_per_hour, 0, ',', '.') }}
                                </span>

                                <div>
                                    @if($table->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                            Perawatan
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5" @click.stop>
                                    <a href="{{ route('tables.edit', $table->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 transition" title="Edit meja">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>

                                    <button type="button" 
                                            @click.stop="confirmDelete('{{ $table->table_number }}', '{{ route('tables.destroy', $table->id) }}')" 
                                            class="p-1.5 text-gray-400 hover:text-red-600 transition" 
                                            title="Hapus meja">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-xs">
                            Belum ada meja. Klik "+ Tambah meja" untuk menambahkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Inline Edit / Detail Form (5 cols) -->
        <div class="lg:col-span-5 space-y-5">
            <template x-if="selectedTable">
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-7 space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900">Edit meja</h2>
                        <p class="text-xs text-gray-400 mt-0.5" x-text="'Meja ' + selectedTable.table_number + ' · ' + selectedTable.type"></p>
                    </div>

                    <form :action="selectedTable.update_url" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama / Nomor Meja</label>
                            <input type="text" name="table_number" x-model="selectedTable.table_number" required
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jenis Meja</label>
                            <select name="type" x-model="selectedTable.type" required
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <option value="Reguler">Reguler</option>
                                <option value="VIP">VIP</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tarif Per Jam (Rp)</label>
                            <input type="number" name="price_per_hour" x-model="selectedTable.price_per_hour" required
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status Operasional</label>
                            <select name="is_active" x-model="selectedTable.is_active" required
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <option :value="1">Aktif (Tersedia untuk Booking)</option>
                                <option :value="0">Perawatan (Maintenance / Ditutup)</option>
                            </select>
                        </div>

                        <div class="pt-3 space-y-2">
                            <button type="submit" class="w-full py-3 px-4 bg-[#1B5E43] hover:bg-[#154b35] text-white font-bold rounded-xl text-xs transition shadow-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </template>

            <!-- Green Info Box -->
            <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-4 text-xs text-emerald-900 flex gap-2.5 leading-relaxed">
                <svg class="w-4 h-4 text-[#1B5E43] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Status Terisi atau Dipesan mengikuti jadwal otomatis. Ubah status menjadi <strong>Perawatan</strong> jika meja sedang dibersihkan atau diganti lakennya.</span>
            </div>
        </div>

    </div>

    <!-- Custom Delete Confirmation Modal -->
    <div x-show="deleteModal.show" 
         x-transition.opacity 
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div @click.outside="deleteModal.show = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Hapus Meja</h3>
                    <p class="text-xs text-gray-500">Konfirmasi tindakan penghapusan meja.</p>
                </div>
            </div>

            <p class="text-xs text-gray-600 leading-relaxed">
                Apakah Anda yakin ingin menghapus <strong>Meja <span x-text="deleteModal.tableNumber"></span></strong>? Meja yang dihapus tidak dapat dipulihkan kembali.
            </p>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" @click="deleteModal.show = false" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <form :action="deleteModal.actionUrl" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition cursor-pointer shadow-sm">
                        Ya, Hapus Meja
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection