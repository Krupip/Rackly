# LOGGING_GUIDE.md — Panduan Log Aman Rackly

> **Target Penilaian:** 03 — Contoh log aman, penyebab error, dan tindakan perbaikannya.

---

## 1. Lokasi & Format Log

| Item | Detail |
|------|--------|
| **Driver** | Laravel `daily` (file harian, default) |
| **Lokasi file** | `Rackly/storage/logs/laravel-YYYY-MM-DD.log` |
| **Channel aktif** | `LOG_CHANNEL=stack` (env), fallback ke `single` |
| **Format baris** | `[YYYY-MM-DD HH:MM:SS] production.LEVEL: EVENT context JSON` |

### Prinsip Log Aman (Sanitized Logging)

- ✅ Dicatat: `user_id`, `booking_id`, `table_id`, `order_id`, `ip_address`, `timestamp`, `status`
- ❌ TIDAK dicatat: password, `MIDTRANS_SERVER_KEY`, `snap_token` (nilai lengkap), nomor kartu kredit
- ❌ TIDAK dicatat: session token, cookie, atau data sensitif pengguna lainnya

---

## 2. Daftar Event Log Aplikasi

| Event Key | Level | Trigger |
|-----------|-------|---------|
| `booking.created` | `INFO` | Booking baru berhasil dibuat |
| `booking.past_time_attempt` | `WARNING` | User mencoba booking jam yang sudah lewat |
| `booking.denial_of_inventory_attempt` | `WARNING` | User mencoba membuat booking ke-2 saat ada pending aktif |
| `booking.max_duration_exceeded` | `WARNING` | User mencoba booking melebihi batas maksimal jam |
| `booking.overlap_conflict_detected` | `WARNING` | Race condition terdeteksi: slot sudah terisi saat transaksi DB |
| `webhook.midtrans_received` | `INFO` | Webhook Midtrans diterima dan signature valid |
| `webhook.booking_dp_lunas` | `INFO` | Status booking diubah ke `dp_lunas` via webhook |
| `webhook.booking_cancelled` | `INFO` | Booking dibatalkan via webhook expire/cancel |
| `webhook.signature_mismatch` | `WARNING` | Signature webhook tidak cocok (potensi tampering) |
| `webhook.order_not_found` | `WARNING` | Order ID webhook tidak ditemukan di database |

---

## 3. Contoh Baris Log Aman (Format Nyata)

### 3.1 Booking Berhasil Dibuat
```
[2026-11-05 10:15:32] production.INFO: booking.created
  {"booking_id":42,"user_id":7,"table_id":2,"start":"2026-11-06 14:00:00","end":"2026-11-06 16:00:00","dp_amount":48000}
```

### 3.2 Pembayaran DP Lunas via Webhook
```
[2026-11-05 10:16:00] production.INFO: webhook.midtrans_received
  {"order_id":"DP-42-1762307732","transaction_status":"settlement","booking_id":42}

[2026-11-05 10:16:00] production.INFO: webhook.booking_dp_lunas
  {"booking_id":42,"order_id":"DP-42-1762307732"}
```

### 3.3 Booking Dibatalkan via Webhook Expire
```
[2026-11-05 10:30:05] production.INFO: webhook.booking_cancelled
  {"booking_id":43,"reason":"expire","order_id":"DP-43-1762308000"}
```

### 3.4 Percobaan Booking Waktu Lampau
```
[2026-11-05 11:05:10] production.WARNING: booking.past_time_attempt
  {"user_id":7,"table_id":3,"requested_at":"2026-11-05 10:00:00","server_now":"2026-11-05 11:05:10"}
```

### 3.5 Percobaan Penimbunan Meja (Denial-of-Inventory)
```
[2026-11-05 11:08:22] production.WARNING: booking.denial_of_inventory_attempt
  {"user_id":7,"active_booking_id":42,"attempted_table_id":5}
```

### 3.6 Percobaan Booking Melebihi Durasi Maksimal
```
[2026-11-05 11:10:00] production.WARNING: booking.max_duration_exceeded
  {"user_id":7,"table_id":2,"requested_hours":7,"max_hours_allowed":5}
```

### 3.7 Webhook Signature Tidak Cocok (Potensi Serangan)
```
[2026-11-05 11:12:00] production.WARNING: webhook.signature_mismatch
  {"order_id":"DP-42-1762307732","ip_address":"203.0.113.55"}
```

---

## 4. Tabel Penyebab Error & Tindakan Perbaikan

### Error 1: Webhook Signature Mismatch / Tampering
| Field | Detail |
|-------|--------|
| **Log Event** | `webhook.signature_mismatch` (WARNING) |
| **Penyebab** | a) `MIDTRANS_SERVER_KEY` di `.env` salah/berbeda dengan dashboard Midtrans. b) Request palsu dari pihak ketiga yang bukan Midtrans. c) Payload webhook dimodifikasi di tengah jalan (man-in-the-middle). |
| **Tindakan** | 1. Periksa nilai `MIDTRANS_SERVER_KEY` di `.env` dan samakan dengan Midtrans Dashboard → Settings → Access Keys. 2. Aktifkan IP Whitelist di Midtrans Dashboard (hanya izinkan IP notifikasi resmi Midtrans). 3. Pastikan endpoint webhook menggunakan HTTPS. 4. Jika IP mencurigakan berulang, tambahkan ke firewall/blocklist. |

---

### Error 2: Overlapping Schedule Conflict / Race Condition
| Field | Detail |
|-------|--------|
| **Log Event** | `booking.overlap_conflict_detected` (WARNING) |
| **Penyebab** | Dua user membuka form booking yang sama secara bersamaan dan menekan tombol submit hampir serentak sebelum salah satu sempat menyimpan ke database (classic race condition pada tabel `bookings`). |
| **Tindakan** | 1. ✅ Sudah dimitigasi: `BookingController::store` menggunakan `DB::transaction()` dengan `lockForUpdate()` pada query cek konflik, memastikan hanya satu yang lolos secara atomik. 2. Jika konflik tetap terdeteksi, kembalikan error "Maaf, jadwal ini sudah dipesan" ke user. 3. Pastikan migrasi DB menggunakan engine **InnoDB** (mendukung row-level locking), bukan MyISAM. |

---

### Error 3: Past-Time Booking Attempt
| Field | Detail |
|-------|--------|
| **Log Event** | `booking.past_time_attempt` (WARNING) |
| **Penyebab** | a) User memanipulasi form HTML secara manual (misal via browser DevTools) untuk mengirimkan jam yang sudah lewat. b) Perbedaan timezone antara browser user dan server (timezone server harus `Asia/Jakarta`). |
| **Tindakan** | 1. ✅ Sudah dimitigasi: validasi `$start->isPast()` di backend `BookingController::store` tidak bergantung pada frontend. 2. Pastikan `config/app.php`: `'timezone' => 'Asia/Jakarta'` dan environment server OS juga WIB. 3. Frontend juga memfilter jam lampau sebagai lapisan pertahanan UX (bukan satu-satunya). |

---

### Error 4: Database Connection Timeout / Failure
| Field | Detail |
|-------|--------|
| **Log Event** | Laravel default: `production.ERROR: ... SQLSTATE[HY000] [2002] ...` |
| **Penyebab** | a) Server database MySQL mati atau crash. b) Credential database di `.env` salah (`DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`). c) Database `DB_DATABASE` belum ada / belum di-migrate. |
| **Tindakan** | 1. Jalankan `php artisan migrate:status` untuk cek koneksi. 2. Periksa `.env`: `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=rackly`, `DB_USERNAME`, `DB_PASSWORD`. 3. Restart MySQL: `net start MySQL` (Windows) atau `sudo systemctl restart mysql` (Linux). 4. Jalankan `php artisan migrate` jika tabel belum ada. |

---

### Error 5: Denial-of-Inventory Attempt
| Field | Detail |
|-------|--------|
| **Log Event** | `booking.denial_of_inventory_attempt` (WARNING) |
| **Penyebab** | User (baik disengaja maupun tidak sengaja) mencoba membuat pesanan baru padahal masih memiliki pesanan berstatus `pending` yang belum dibayar atau dibatalkan. Ini berpotensi memblokir slot meja milik user lain. |
| **Tindakan** | 1. ✅ Sudah dimitigasi: redirect otomatis ke halaman checkout pesanan aktif disertai pesan informatif dan tombol batalkan mandiri. 2. Pesanan pending yang melebihi `dp_expiry_minutes` di-auto-cancel setiap kali `create` atau `store` dipanggil. 3. Jika user tertentu terus-menerus muncul di log ini dengan frekuensi tinggi, admin dapat memantau via dashboard dan menonaktifkan akun jika diperlukan. |

---

## 5. Cara Membaca File Log

```bash
# Buka log hari ini
cat storage/logs/laravel-$(date +%Y-%m-%d).log

# Filter hanya WARNING dan ERROR
grep -E "WARNING|ERROR" storage/logs/laravel-$(date +%Y-%m-%d).log

# Filter event tertentu
grep "webhook.signature_mismatch" storage/logs/laravel-$(date +%Y-%m-%d).log

# Di Windows (PowerShell):
Select-String -Path "storage\logs\laravel-$(Get-Date -Format yyyy-MM-dd).log" -Pattern "WARNING"
```

---

## 6. Konfigurasi Log di `.env`

```env
# Pilihan: single | daily | stack | null
LOG_CHANNEL=daily

# Level minimum yang direkam (debug < info < notice < warning < error < critical)
LOG_LEVEL=warning
```

> **Rekomendasi produksi:** Gunakan `LOG_CHANNEL=daily` agar log terpecah per hari dan tidak membengkak. Untuk staging/development, gunakan `LOG_LEVEL=debug`.

