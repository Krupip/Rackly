<?php

namespace App\Http\Controllers;

use App\Models\BilliardTable;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Hanya tampilkan meja yang statusnya aktif/tersedia
        $tables = BilliardTable::where('is_active', 1)->get();
        $dpPercentage = (int) Setting::get('dp_percentage', 30);
        $openHour = Setting::get('open_hour', '12:00');
        $closeHour = Setting::get('close_hour', '02:00');
        return view('home', compact('tables', 'dpPercentage', 'openHour', 'closeHour'));
    }
}
