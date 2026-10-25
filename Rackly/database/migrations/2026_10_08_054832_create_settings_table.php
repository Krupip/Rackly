<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('label')->nullable(); // Label tampilan untuk admin
            $table->timestamps();
        });

        // Seed nilai default DP
        DB::table('settings')->insert([
            [
                'key'   => 'dp_percentage',
                'value' => '30',
                'label' => 'Persentase Down Payment (%)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'   => 'dp_type',
                'value' => 'percentage',
                'label' => 'Metode Perhitungan DP (percentage / fixed)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'   => 'dp_nominal',
                'value' => '50000',
                'label' => 'Nominal Tetap DP (Rp)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'   => 'dp_expiry_minutes',
                'value' => '15',
                'label' => 'Batas Waktu Pembayaran DP (Menit)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
