# Rackly — Sistem Reservasi Meja Biliar dengan Payment Gateway DP

**Rackly** adalah aplikasi web reservasi meja biliar yang mengintegrasikan payment gateway (DP 40%) untuk mengikat komitmen pelanggan sejak awal. Sistem ini menjembatani kebutuhan pelanggan yang ingin memesan meja secara online dengan kebutuhan pengelola untuk mengelola jadwal, meja, dan transaksi dalam satu antarmuka terpadu.

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.3+), MySQL
- **Frontend:** Laravel Blade + Alpine.js + Tailwind CSS
- **Payment Gateway:** Midtrans Snap (QRIS & Virtual Account)
- **Autentikasi:** Laravel Breeze (session-based, RBAC 2 role)
- **ORM:** Eloquent ORM
- **Testing:** Pest PHP

---

## Prerequisites

Sebelum mulai, pastikan hal berikut sudah terpenuhi:

| Kebutuhan              | Keterangan                                                                 |
| :--------------------- | :------------------------------------------------------------------------- |
| **PHP 8.3+**           | Dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`    |
| **Composer 2**         | [https://getcomposer.org/](https://getcomposer.org/)                       |
| **Node.js 18+**        | Untuk build Tailwind CSS & Alpine.js via Vite                              |
| **MySQL 8+**           | Database utama aplikasi                                                    |
| **Ngrok / tunnel**     | Diperlukan untuk menerima webhook Midtrans saat development lokal          |
| **Akun Midtrans**      | Sandbox mode — daftar gratis di [https://midtrans.com](https://midtrans.com) |

> **Catatan:** seluruh perintah di panduan ini dijalankan di **PowerShell** atau **Git Bash** (Windows), atau terminal biasa (Linux/macOS).

---

## Quick Start (Local)

### 1. Clone Repository

```bash
git clone https://github.com/YOUR_USERNAME/Project-RPL.git
cd Project-RPL/Rackly
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Buat File `.env`

```bash
# Linux / macOS / Git Bash
cp .env.example .env

# Windows PowerShell
Copy-Item .env.example .env
```

Lalu buka `.env` dan isi nilainya (lihat bagian [Konfigurasi `.env`](#konfigurasi-env) di bawah).

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Buat Database & Jalankan Migrasi

Buat database kosong bernama `cue_reserve` di MySQL, lalu:

```bash
php artisan migrate --seed
```

Perintah `--seed` otomatis membuat akun admin dan data awal meja.

### 6. Build Asset Frontend

```bash
npm run build
```

Atau untuk mode development dengan hot-reload:

```bash
npm run dev
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi berjalan di `http://localhost:8000`.

### 8. (Opsional) Jalankan Tunnel untuk Webhook Midtrans

Midtrans membutuhkan URL publik untuk mengirim notifikasi pembayaran. Gunakan Ngrok:

```bash
ngrok http 8000
```

Salin URL HTTPS yang diberikan Ngrok (contoh: `https://xxxx.ngrok.io`), lalu set di dashboard Midtrans sebagai **Payment Notification URL**:

```
https://xxxx.ngrok.io/webhook/midtrans
```

---

## Konfigurasi `.env`

```dotenv
APP_NAME=Rackly
APP_ENV=local
APP_KEY=           # diisi otomatis oleh php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cue_reserve
DB_USERNAME=root
DB_PASSWORD=

# Midtrans — ambil dari https://dashboard.midtrans.com (sandbox)
MIDTRANS_SERVER_KEY=SB-Mid-server-XXXXXXXXXXXXXXXXXXXXXXXX
MIDTRANS_CLIENT_KEY=SB-Mid-client-XXXXXXXXXXXXXXXXXXXXXXXX
MIDTRANS_MERCHANT_ID=GXXXXXXXX
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

> **Penting:** jangan commit file `.env` ke repository. File `.env` sudah ada di `.gitignore`.

---

## Akun Demo

Setelah `php artisan migrate --seed`, akun berikut tersedia — password semuanya `password`:

| Email                    | Role    | Keterangan                          |
| :----------------------- | :------ | :---------------------------------- |
| `admin@rackly.com`       | `admin` | Akses penuh ke dashboard admin      |
| `user@example.com`       | `user`  | Akun pelanggan untuk booking online |

> Gunakan akun `admin` untuk mengelola meja, melihat semua booking, dan konfigurasi pengaturan aplikasi.

---

## Menjalankan Test

```bash
php artisan test
```

**Yang diharapkan saat suite lulus:**

```
PASS  Tests\Feature\...
...

Tests:    80 passed
```

> Kegagalan ditandai dengan `FAIL` dan exit code bukan nol.

---

## Troubleshooting

| Masalah                                        | Penyebab                                          | Solusi                                                                  |
| :--------------------------------------------- | :------------------------------------------------ | :---------------------------------------------------------------------- |
| `No application encryption key has been specified` | `APP_KEY` kosong                              | Jalankan `php artisan key:generate`                                     |
| `SQLSTATE[HY000]: No such host`                | Konfigurasi DB salah                              | Periksa `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` di `.env`    |
| `Class "Midtrans\..." not found`               | Dependency belum terinstall                       | Jalankan `composer install`                                             |
| Webhook Midtrans tidak diterima                | URL tidak publik / tunnel tidak aktif             | Pastikan Ngrok aktif dan URL sudah di-set di dashboard Midtrans         |
| Vite error / asset tidak muncul                | Node modules belum terinstall atau belum di-build | Jalankan `npm install && npm run build`                                 |
| `Timezone` tidak sesuai                        | `APP_TIMEZONE` belum di-set                       | Pastikan `APP_TIMEZONE=Asia/Jakarta` ada di `.env`                      |

---

## Struktur Project

```
Project-RPL/
├── Rackly/                      # Aplikasi Laravel utama
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/     # BookingController, AdminController, dst.
│   │   │   └── Middleware/      # IsAdmin, dll.
│   │   └── Models/              # User, BilliardTable, Booking, Payment, Setting
│   ├── config/
│   │   ├── app.php              # Timezone Asia/Jakarta
│   │   └── midtrans.php         # Konfigurasi Midtrans
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── resources/
│   │   └── views/               # Blade templates (booking, admin, auth, dst.)
│   ├── routes/
│   │   ├── web.php              # Route utama & webhook
│   │   └── auth.php             # Route autentikasi
│   ├── tests/
│   │   └── Feature/             # 80 test Pest PHP
│   ├── .env.example
│   └── README.md
└── README.md                    # Dokumen ini
```

---

## Fitur Inti

Status: **[Ada]** = sudah terimplementasi, **[Rencana]** = direncanakan.

1. **[Ada] Katalog Meja Interaktif** — tampilan daftar meja, tipe, harga/jam, dan status ketersediaan
2. **[Ada] Pemesanan Online dengan Validasi Jadwal** — cegah konflik (*overlap*) dan pemesanan di tanggal/jam lampau
3. **[Ada] Pembayaran DP via Midtrans** — Snap.js popup (QRIS & Virtual Account), 40% dari total harga
4. **[Ada] Webhook Otomatis** — sinkronisasi status booking dari Midtrans secara real-time
5. **[Ada] Walk-in Booking (Admin)** — kasir bisa input booking langsung tanpa payment gateway
6. **[Ada] Riwayat Booking & Pembatalan** — pelanggan bisa melihat histori dan membatalkan booking
7. **[Ada] Dashboard Admin** — manajemen meja, semua booking, dan pengaturan aplikasi
8. **[Ada] RBAC 2 Role** — `user` (pelanggan) dan `admin` (pengelola), dilindungi middleware `IsAdmin`
9. **[Ada] Custom Popup Modal** — tidak menggunakan `alert()` / `confirm()` bawaan browser
10. **[Rencana]** Notifikasi email konfirmasi booking

---

## Keamanan

- Session-based authentication via Laravel Breeze
- Middleware `IsAdmin` melindungi seluruh rute `/admin/*`
- Webhook Midtrans divalidasi dengan *signature key* (SHA-512)
- CSRF dinonaktifkan hanya untuk endpoint `/webhook/midtrans`
- Password di-hash dengan `bcrypt`
- `.env` tidak di-commit (ada di `.gitignore`), `.env.example` hanya template

---

## Masalah yang Diselesaikan

1. **No-Show & Kerugian Pendapatan** — kewajiban DP 40% mengikat komitmen pelanggan secara finansial sejak awal
2. **Risiko Double Booking** — validasi otomatis di backend mencegah konflik jadwal pada meja yang sama
3. **Verifikasi Pembayaran Manual** — integrasi Midtrans + webhook mengonfirmasi pembayaran secara otomatis tanpa intervensi staf
4. **Visibilitas Meja Rendah** — ketersediaan meja ditampilkan secara real-time berdasarkan jadwal yang sudah dibooking

---

## Dokumen Lengkap

| File | Deskripsi |
| :--- | :-------- |
| [`README.md`](README.md) | Dokumen ini — panduan setup dan gambaran umum proyek |
| [`HowToRun.md`](HowToRun.md) | Panduan langkah demi langkah menjalankan aplikasi dan pengujian |
| [`checklist_mvp.md`](checklist_mvp.md) | Daftar periksa seluruh fitur MVP beserta status pengujian unit & feature test |
| [`Rackly/.env.example`](Rackly/.env.example) | Template konfigurasi environment |
| [`Rackly/AGENTS.md`](Rackly/AGENTS.md) | Panduan perilaku AI coding agent untuk proyek ini |

## Video Demonstrasi
Link Google Drive: https://drive.google.com/file/d/1menb4NaDfTpGp8S3TrfDnkEJnAf76MOB/view?usp=sharing