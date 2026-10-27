<?php

use Illuminate\Support\Facades\Schedule;

// Jalankan auto-cancel booking kadaluarsa setiap menit
Schedule::command('booking:cancel-expired')->everyMinute();
