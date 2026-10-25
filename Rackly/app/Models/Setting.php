<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value', 'label'])]
class Setting extends Model
{
    /**
     * Ambil nilai setting berdasarkan key.
     * Kembalikan $default jika key tidak ditemukan.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Simpan atau perbarui nilai setting berdasarkan key.
     */
    public static function set(string $key, mixed $value, ?string $label = null): static
    {
        $data = ['value' => (string) $value];
        if ($label !== null) {
            $data['label'] = $label;
        }

        return static::updateOrCreate(['key' => $key], $data);
    }
}
