<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Jumlah total yang sudah dibayar user (DP + pelunasan)
            // Diisi oleh DP saat webhook Midtrans sukses,
            // lalu ditambah sisa ketika admin konfirmasi pelunasan.
            $table->integer('paid_amount')->default(0)->after('dp_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
    }
};
