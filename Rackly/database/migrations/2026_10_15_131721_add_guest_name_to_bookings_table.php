<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Buat user_id nullable agar booking walk-in bisa tanpa akun
            $table->foreignId('user_id')->nullable()->change();

            // Nama tamu untuk booking walk-in langsung di lokasi
            $table->string('guest_name')->nullable()->after('user_id');

            // Tandai apakah booking ini walk-in (langsung di kasir)
            $table->boolean('is_walkin')->default(false)->after('guest_name');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn(['guest_name', 'is_walkin']);
        });
    }
};
