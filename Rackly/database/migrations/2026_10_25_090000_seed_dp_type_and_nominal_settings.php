<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'dp_type'],
            [
                'value' => 'percentage',
                'label' => 'Metode Perhitungan DP (percentage / fixed)',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'dp_nominal'],
            [
                'value' => '50000',
                'label' => 'Nominal Tetap DP (Rp)',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'dp_expiry_minutes'],
            [
                'value' => '15',
                'label' => 'Batas Waktu Pembayaran DP (Menit)',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['dp_type', 'dp_nominal', 'dp_expiry_minutes'])->delete();
    }
};
