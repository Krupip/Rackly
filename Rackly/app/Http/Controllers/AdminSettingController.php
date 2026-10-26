<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    // Tampilkan halaman pengaturan
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings', compact('settings'));
    }

    // Simpan perubahan pengaturan
    public function update(Request $request)
    {
        $dpType = $request->input('dp_type', 'percentage');

        $rules = [
            'dp_type'           => 'nullable|in:percentage,fixed',
            'dp_expiry_minutes' => 'nullable|integer|in:15,30,60',
            'open_hour'         => 'nullable|regex:/^\d{2}:\d{2}$/',
            'close_hour'        => 'nullable|regex:/^\d{2}:\d{2}$/',
        ];

        if ($dpType === 'fixed') {
            $rules['dp_nominal'] = 'required|numeric|min:1000';
            $rules['dp_percentage'] = 'nullable|integer|min:1|max:100';
        } else {
            $rules['dp_percentage'] = 'required|integer|min:1|max:100';
            $rules['dp_nominal'] = 'nullable|numeric|min:1000';
        }

        $messages = [
            'dp_type.in'             => 'Metode perhitungan DP tidak valid.',
            'dp_percentage.required' => 'Persentase DP wajib diisi.',
            'dp_percentage.integer'  => 'Persentase DP harus berupa angka bulat.',
            'dp_percentage.min'      => 'Persentase DP minimal 1%.',
            'dp_percentage.max'      => 'Persentase DP maksimal 100%.',
            'dp_nominal.required'    => 'Nominal DP wajib diisi jika metode nominal tetap dipilih.',
            'dp_nominal.numeric'     => 'Nominal DP harus berupa angka.',
            'dp_nominal.min'         => 'Nominal DP minimal Rp1.000.',
            'open_hour.regex'        => 'Format jam buka tidak valid (gunakan HH:MM, contoh: 10:00).',
            'close_hour.regex'       => 'Format jam tutup tidak valid (gunakan HH:MM, contoh: 23:00).',
        ];

        $request->validate($rules, $messages);

        Setting::set('dp_type', $dpType, 'Metode Perhitungan DP (percentage / fixed)');

        if ($request->filled('dp_percentage')) {
            Setting::set('dp_percentage', $request->dp_percentage, 'Persentase Down Payment (%)');
        }

        if ($request->filled('dp_nominal')) {
            Setting::set('dp_nominal', $request->dp_nominal, 'Nominal Tetap DP (Rp)');
        }

        if ($request->filled('dp_expiry_minutes')) {
            Setting::set('dp_expiry_minutes', $request->dp_expiry_minutes, 'Batas Waktu Pembayaran DP (Menit)');
        }

        if ($request->filled('open_hour')) {
            Setting::set('open_hour', $request->open_hour, 'Jam Buka Operasional (HH:MM)');
        }

        if ($request->filled('close_hour')) {
            Setting::set('close_hour', $request->close_hour, 'Jam Tutup Operasional (HH:MM)');
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil disimpan!');
    }
}
