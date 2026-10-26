<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'open_hour'],
            [
                'value' => '10:00',
                'label' => 'Jam Buka Operasional (HH:MM)',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'close_hour'],
            [
                'value' => '23:00',
                'label' => 'Jam Tutup Operasional (HH:MM)',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['open_hour', 'close_hour'])->delete();
    }
};
