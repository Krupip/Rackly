<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['table_number', 'type', 'price_per_hour', 'is_active'])]
class BilliardTable extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relasi: Satu Meja bisa memiliki banyak Booking (riwayat jadwal)
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}