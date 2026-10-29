# Panduan Menjalankan Aplikasi (HowToRun.md)

Dokumen ini berisi panduan lengkap langkah demi langkah untuk menginstalasi, mengonfigurasi, dan menjalankan aplikasi **Rackly (Sistem Reservasi Meja Biliar Online)** di lingkungan lokal (*development*).

---

## 1. Kebutuhan Sistem (Prerequisites)

Pastikan perangkat Anda telah terinstal perangkat lunak berikut:

| Perangkat Lunak | Versi Minimal | Keterangan |
| :--- | :--- | :--- |
| **PHP** | `^8.3` | Ekstensi aktif: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `curl` |
| **Composer** | `2.x` | Manajemen paket dependensi PHP |
| **Node.js & npm** | `v18+` & `npm v9+` | Kompilasi aset frontend (Tailwind CSS, Alpine.js via Vite) |
| **MySQL / MariaDB** | `8.0+` / `10.4+` | Database server (contoh via XAMPP, Laragon, atau MySQL Server langsung) |
| **Git** | Versi terbaru | Kontrol versi |
| **Ngrok** *(Opsional)* | Versi terbaru | Tunneling publik jika ingin menguji webhook notifikasi Midtrans secara real-time |

---

## 2. Langkah-Langkah Menjalankan Aplikasi

### Langkah 1: Masuk ke Direktori Aplikasi
Buka terminal (PowerShell / Git Bash / Command Prompt), lalu arahkan ke direktori proyek Laravel (`Rackly`):

```bash
cd Rackly
```

---

### Langkah 2: Install Dependensi Backend & Frontend

Jalankan perintah berikut untuk mengunduh seluruh library PHP dan Node.js:

```bash
# Install dependensi PHP (Laravel 13, Midtrans SDK, Pest, Breeze)
composer install

# Install dependensi Frontend (Tailwind CSS, Alpine.js, Vite)
npm install
```

---

### Langkah 3: Konfigurasi File Environment (`.env`)

Salin file template `.env.example` menjadi `.env`:

```bash
# Di Windows PowerShell:
Copy-Item .env.example .env

# Di Linux / macOS / Git Bash:
cp .env.example .env
```

Buka file `.env` dengan teks editor (VS Code, Notepad, dll.), lalu pastikan pengaturan berikut sesuai:

```dotenv
APP_NAME=Rackly
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

# Konfigurasi Database MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cue_reserve
DB_USERNAME=root
DB_PASSWORD=

# Kredensial Midtrans Sandbox (Gunakan kredensial sandbox akun Anda atau gunakan default berikut)
MIDTRANS_SERVER_KEY=SB-Mid-server-XXXXXXXXXXXXXXXXXXXXXXXX
MIDTRANS_CLIENT_KEY=SB-Mid-client-XXXXXXXXXXXXXXXXXXXXXXXX
MIDTRANS_MERCHANT_ID=GXXXXXXXX
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

> **Catatan:** Pastikan database dengan nama `cue_reserve` sudah dibuat di MySQL Anda (misal via phpMyAdmin atau MySQL CLI: `CREATE DATABASE cue_reserve;`).

---

### Langkah 4: Generate Application Encryption Key

Buat kunci enkripsi aplikasi Laravel:

```bash
php artisan key:generate
```

---

### Langkah 5: Migrasi Database & Seeding Data Awal

Jalankan migrasi untuk membuat seluruh tabel database beserta seeder bawaan (akun demo admin, akun user, master meja, dan pengaturan sistem DP):

```bash
php artisan migrate --seed
```

> **Jika ingin me-reset ulang database dari nol kapan saja:**
> ```bash
> php artisan migrate:fresh --seed
> ```

---

### Langkah 6: Kompilasi Aset Frontend

Pilih salah satu dari dua cara berikut:

- **Opsi A (Produksi / Siap Pakai - Disarankan):**
  ```bash
  npm run build
  ```

- **Opsi B (Development dengan Hot Reload):**
  Buka tab terminal terpisah dan jalankan:
  ```bash
  npm run dev
  ```

---

### Langkah 7: Menjalankan Web Server Lokal

Jalankan server bawaan Laravel:

```bash
php artisan serve
```

Aplikasi sekarang dapat diakses di browser melalui:
👉 **`http://localhost:8000`** (atau `http://127.0.0.1:8000`)

---

### Langkah 8 (Opsional): Menjalankan Webhook Tunnel (Midtrans Simulator)

Jika Anda ingin menguji respon pembayaran otomatis dari Midtrans Simulator ke aplikasi lokal Anda:

1. Buka tab terminal baru, jalankan:
   ```bash
   ngrok http 8000
   ```
2. Salin URL HTTPS yang dihasilkan (misal: `https://abcd-1234.ngrok-free.app`).
3. Masuk ke [Midtrans MAP Dashboard Sandbox](https://dashboard.sandbox.midtrans.com) -> **Settings** -> **Configuration**.
4. Masukkan URL notifikasi:
   ```
   https://abcd-1234.ngrok-free.app/webhook/midtrans
   ```
5. Simpan pengaturan. Kini pembayaran di simulator akan otomatis mengupdate status booking menjadi `dp_lunas`.

---

## 3. Kredensial Akun Pengujian (Demo Accounts)

Data akun berikut otomatis dibuat saat menjalankan perintah `--seed`:

| Role Pengguna | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin / Kasir** | `admin@rackly.com` | `password` | Dashboard operasional, kelola meja (CRUD), input booking walk-in kasir, konfirmasi pelunasan, pengaturan DP sistem. |
| **Pelanggan (User)** | `user@example.com` | `password` | Eksplorasi meja, booking meja online, bayar DP via Midtrans Snap, cek riwayat booking, batalkan booking mandiri. |

> Anda juga dapat mendaftarkan akun pelanggan baru secara bebas melalui tombol **Daftar** di halaman awal aplikasi.

---

## 4. Menjalankan Pengujian Otomatis (Automated Testing)

Aplikasi telah dilengkapi pengujian otomatis menyeluruh (*Feature & Unit Tests*) berbasis **Pest PHP**:

```bash
# Jalankan seluruh 86 pengujian:
php artisan test
```

### Hasil yang Diharapkan:
```
Tests:    86 passed (268 assertions)
Duration: ~3.80s
Status:   PASSED
```

---

## 5. Troubleshooting Masalah Umum

| Gejala Kendala | Kemungkinan Penyebab | Langkah Solusi |
| :--- | :--- | :--- |
| `Unknown database 'cue_reserve'` | Database belum dibuat di MySQL | Buka phpMyAdmin / MySQL CLI, jalankan: `CREATE DATABASE cue_reserve;` lalu jalankan kembali `php artisan migrate --seed`. |
| `No application encryption key has been specified` | `APP_KEY` di `.env` masih kosong | Jalankan `php artisan key:generate`. |
| Tampilan halaman polos / CSS tidak rapi | Aset frontend belum di-build | Jalankan perintah `npm install && npm run build`. |
| Jam booking tidak sinkron dengan waktu lokal | Timezone aplikasi belum tersetel | Pastikan baris `APP_TIMEZONE=Asia/Jakarta` ada di file `.env`. |
| Pop-up pembayaran Midtrans tidak muncul di checkout | Client Key Midtrans belum diisi atau tidak valid | Periksa variabel `MIDTRANS_CLIENT_KEY` di file `.env`. Pastikan diawali dengan `SB-Mid-client-`. |
| 403 Forbidden saat mengakses rute `/admin/*` | Login menggunakan akun non-admin | Logout, kemudian login kembali menggunakan akun admin: `admin@rackly.com`. |

