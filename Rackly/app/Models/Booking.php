<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'guest_name',
    'is_walkin',
    'billiard_table_id',
    'start_time',
    'end_time',
    'total_price',
    'dp_amount',
    'paid_amount',
    'status',
])]
class Booking extends Model
{
    use HasFactory;

    // Mengubah tipe kolom menjadi datetime agar mudah difilter jamnya
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    // Relasi: Booking ini milik satu User tertentu
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Booking ini menggunakan satu Meja tertentu
    public function billiardTable(): BelongsTo
    {
        return $this->belongsTo(BilliardTable::class);
    }

    // Relasi: Satu Booking memiliki satu data Pembayaran Midtrans
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}