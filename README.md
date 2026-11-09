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

## Panduan Instalasi Ulang & Akses Aplikasi (Fresh Clone Repo, Build, Deploy Lokal)

Bagian ini memandu proses instalasi ulang dari repositori bersih (*fresh clone*), melakukan build aset, hingga deployment lokal serta mengakses paket demo.

### 1. Fresh Clone Repository

Buka terminal (Git Bash, PowerShell, atau Command Prompt), lalu clone repositori dan masuk ke direktori aplikasi Laravel `Rackly`:

```bash
git clone https://github.com/Krupip/Project-RPL.git
cd Project-RPL/Rackly
```

### 2. Instalasi Dependensi (Backend & Frontend)

Unduh paket library PHP dan JavaScript yang dibutuhkan:

```bash
# Dependensi PHP (Laravel 13, Midtrans SDK, Breeze, Pest PHP)
composer install

# Dependensi Frontend (Tailwind CSS, Alpine.js, Vite)
npm install
```

### 3. Konfigurasi Environment (`.env`)

Salin template konfigurasi `.env.example` menjadi `.env`:

```bash
# Linux / macOS / Git Bash
cp .env.example .env

# Windows PowerShell
Copy-Item .env.example .env
```

Lalu buat Application Encryption Key:

```bash
php artisan key:generate
```

> Pastikan kredensial database di `.env` sudah sesuai (default: `DB_DATABASE=cue_reserve`, `DB_USERNAME=root`, `DB_PASSWORD=`). Kredensial Midtrans Sandbox juga sudah tersedia di `.env.example`.

### 4. Database Preparation & Seeding Data Awal

Pastikan service MySQL aktif (misalnya melalui XAMPP atau Laragon), buat database kosong `cue_reserve`, lalu jalankan migrasi dan seeder:

```bash
# Perintah migrasi bersih dan pengisian data demo awal
php artisan migrate:fresh --seed
```

Perintah di atas otomatis membuat seluruh tabel, data awal meja biliar, pengaturan tarif DP, serta akun demo admin dan pelanggan.

### 5. Build Aset Frontend

Kompilasi aset CSS dan JavaScript agar siap disajikan:

```bash
npm run build
```

> **Catatan:** Jika Anda sedang melakukan pengembangan aktif dengan hot-reload, Anda dapat menjalankan `npm run dev` pada terminal terpisah.

### 6. Deploy Lokal (Production-Ready Local Deployment)

Untuk mensimulasikan deployment lokal yang optimal, cepat, dan stabil:

```bash
# Buat symlink storage publik jika diperlukan
php artisan storage:link

# Optimasi caching rute dan template view
php artisan route:cache
php artisan view:cache

# (Opsional) Caching konfigurasi lingkungan lokal
# php artisan config:cache
```

> **Tips:** Jika ingin kembali ke mode pengembangan murni atau setelah mengubah file konfigurasi / `.env`, bersihkan cache dengan:
> ```bash
> php artisan optimize:clear
> ```

### 7. Menjalankan Server Aplikasi

Jalankan server aplikasi lokal bawaan Laravel:

```bash
php artisan serve
```

Aplikasi sekarang aktif dan dapat diakses di browser melalui:
👉 **`http://localhost:8000`** (atau `http://127.0.0.1:8000`)

### 8. (Opsional) Tunnel Webhook Midtrans Real-Time

Untuk menguji respon pembayaran otomatis dari Midtrans Simulator ke server lokal Anda:

```bash
ngrok http 8000
```

Salin URL HTTPS Ngrok (misal: `https://xxxx.ngrok-free.app`), lalu masukkan pada dashboard Midtrans Sandbox (**Settings** -> **Configuration** -> **Payment Notification URL**):
```
https://xxxx.ngrok-free.app/webhook/midtrans
```

---

## Akses Paket Demo (Demo Package & Scenarios)

Setelah menjalankan `php artisan migrate:fresh --seed`, Anda dapat langsung mendemonstrasikan sistem dengan akun demo berikut:

| Peran (Role) | Email | Password | Hak Akses & Skenario Pengujian |
| :--- | :--- | :--- | :--- |
| **Admin / Kasir** | `admin@rackly.com` | `password` | Akses penuh dashboard operasional (`/admin/dashboard`), manajemen meja biliar (CRUD status & harga), pemesanan langsung kasir (*Walk-in Booking*), konfirmasi pelunasan tagihan sisa, dan konfigurasi persentase/nominal DP. |
| **Pelanggan (User)** | `user@example.com` | `password` | Eksplorasi katalog meja real-time, reservasi online dengan pencegahan tabrakan jadwal (*anti-overlap*), pembayaran DP via pop-up Midtrans Snap, riwayat pesanan, serta pembatalan mandiri. |

### Skenario Alur Pengujian Demo Cepat:
1. **Pemesanan Pelanggan:** Login sebagai `user@example.com`, pilih meja yang aktif, pilih tanggal & jam sewa (tersedia proteksi jam lampau dan batas maks. 5 jam).
2. **Checkout & DP:** Lihat kalkulasi DP 40% dan rincian sisa pelunasan di lokasi.
3. **Pembayaran Midtrans:** Klik **Bayar DP Sekarang**, pop-up Midtrans Snap akan terbuka. Selesaikan simulasi pembayaran di Midtrans Simulator.
4. **Proteksi Anti Penimbunan:** Coba lakukan pemesanan meja kedua sebelum pesanan pertama dibayar/dibatalkan — sistem akan otomatis mengarahkan kembali ke tagihan pending aktif (*Denial-of-Inventory protection*).
5. **Pelunasan di Kasir:** Logout dan login sebagai `admin@rackly.com`, buka dashboard, cari pesanan pelanggan tadi yang berstatus `dp_lunas`, lalu klik tombol **Pelunasan** untuk menyelesaikan transaksi.
6. **Video Demonstrasi:** Dokumentasi dan tautan rekaman video demo alur lengkap dapat dilihat pada file [`demo.md`](demo.md).

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

## Alur Kontribusi & Pengujian Pull Request (PR Workflow & Testing)

Untuk menjaga kualitas kode, mencegah regresi, dan memastikan aplikasi selalu dalam kondisi siap deploy (*deployable*), setiap penambahan fitur atau perbaikan bug wajib melalui alur Pull Request (PR) berikut:

### 1. Buat Branch Baru dari `main`
Sebelum menulis kode baru, pastikan branch `main` berada pada versi teranyar, lalu buat branch fitur terisolasi:

```bash
git checkout main
git pull origin main
git checkout -b feature/nama-fitur-baru
```

### 2. Kembangkan Fitur & Tulis Unit / Feature Test
Terapkan logika fitur baru beserta pengujian otomatisnya di dalam folder `Rackly/tests/Feature/` atau `Rackly/tests/Unit/`.

### 3. Eksekusi Seluruh Suite Pengujian (Verifikasi All Passed)
Pastikan seluruh 91 pengujian lulus tanpa ada satu pun kegagalan (*zero failure*):

```bash
cd Rackly
php artisan test
```

> **Kriteria Kelulusan PR:** Seluruh **91 test passed (281 assertions)** harus berstatus hijau (`PASSED`).

### 4. Build Aset Frontend
Pastikan aset Tailwind CSS dan JavaScript terkompilasi dengan bersih tanpa error sintaks:

```bash
npm run build
```

### 5. Commit dan Push Branch ke Remote Repository
Gunakan format pesan commit konvensional:

```bash
git add .
git commit -m "feat: implementasi fitur baru dan penambahan kasus uji"
git push origin feature/nama-fitur-baru
```

### 6. Buka Pull Request (PR) di GitHub
1. Kunjungi repositori GitHub: [https://github.com/Krupip/Project-RPL/pulls](https://github.com/Krupip/Project-RPL/pulls)
2. Klik tombol **New Pull Request**, pilih base `main` dan compare `feature/nama-fitur-baru`.
3. Cantumkan ringkasan perubahan, hasil pengujian lokal (`php artisan test: 91 passed`), dan screenshot/demo jika ada perubahan UI.
4. Tunggu peninjauan kode (*code review*) dan persetujuan (*approval*).

### 7. Penggabungan (Merge) & Deploy Lokal Ulang
Setelah PR di-merge ke branch `main`, lakukan pembaruan dan deploy lokal pada lingkungan pengujian/produksi:

```bash
git checkout main
git pull origin main
composer install
npm install
npm run build
php artisan migrate
php artisan optimize:clear
php artisan route:cache
php artisan view:cache
php artisan serve
```

---

## Menjalankan Test

```bash
php artisan test
```

**Yang diharapkan saat suite lulus:**

```
PASS  Tests\Feature\...
...

Tests:    91 passed (281 assertions)
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
│   │   └── Feature/             # 91 test Pest PHP (281 assertions)
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
4. **[Ada] Webhook Otomatis & Idempotent** — sinkronisasi status booking dari Midtrans secara real-time dan kebal duplikasi
5. **[Ada] Walk-in Booking (Admin)** — kasir bisa input booking langsung tanpa payment gateway
6. **[Ada] Riwayat Booking & Pembatalan** — pelanggan bisa melihat histori dan membatalkan booking secara mandiri
7. **[Ada] Dashboard Admin** — manajemen meja, semua booking, dan pengaturan aplikasi
8. **[Ada] RBAC 2 Role** — `user` (pelanggan) dan `admin` (pengelola), dilindungi middleware `IsAdmin`
9. **[Ada] Custom Popup Modal** — tidak menggunakan `alert()` / `confirm()` bawaan browser
10. **[Ada] Idempotensi Operasional** — proteksi klik ganda form booking, pelunasan berulang kasir, dan penolakan webhook kadaluarsa terlambat
11. **[Ada] Anti Denial-of-Inventory (Slot Hoarding)** — pembatasan 1 pesanan pending aktif per akun dan auto-expiry untuk mencegah penimbunan meja fiktif
12. **[Rencana]** Notifikasi email konfirmasi booking

---

## Keamanan

- Session-based authentication via Laravel Breeze
- Middleware `IsAdmin` melindungi seluruh rute `/admin/*`
- Webhook Midtrans divalidasi dengan *signature key* (SHA-512)
- Idempotent webhook handler: mencegah rollback status transaksi dan penolakan notifikasi duplikat
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
| [`SECURITY_REPORT.md`](SECURITY_REPORT.md) | Laporan hasil pengujian keamanan (OWASP Top 10) & catatan perubahan yang relevan |
| [`LOGGING_GUIDE.md`](LOGGING_GUIDE.md) | Panduan log aman, format audit event, penyebab error & tindakan perbaikan |
| [`demo.md`](demo.md) | Tautan video demonstrasi alur lengkap aplikasi |
| [`Rackly/.env.example`](Rackly/.env.example) | Template konfigurasi environment |
| [`Rackly/AGENTS.md`](Rackly/AGENTS.md) | Panduan perilaku AI coding agent untuk proyek ini |