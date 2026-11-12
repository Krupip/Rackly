# Rackly Billiard Club — Link Web & Cara Simulasi Pemesanan

## 🔗 Link Aplikasi
**https://rackly-bl.vercel.app**

---

## 👤 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin / Kasir | `admin@rackly.com` | `password` |
| Pelanggan | `user@example.com` | `password` |

---

## 🎱 Simulasi Alur Pelanggan (Booking & Bayar DP)

### Langkah-langkah:

1. **Buka aplikasi** → https://rackly-bl.vercel.app
2. **Login sebagai pelanggan** → Klik **"Masuk"** di pojok kanan atas
   - Email: `user@example.com`
   - Password: `password`
3. **Pilih meja** → Di halaman beranda, lihat daftar meja yang tersedia (status: **Aktif**)
4. **Klik "Booking Sekarang"** pada meja yang diinginkan
5. **Isi form pemesanan:**
   - Pilih **tanggal** pemesanan
   - Pilih **jam mulai** dan **jam selesai**
   - Klik **"Lanjut ke Pembayaran"**
6. **Halaman Checkout** akan menampilkan detail booking dan nominal DP
7. **Klik "Bayar Sekarang"** → Pop-up Midtrans Snap akan terbuka
8. **Pilih metode pembayaran** (contoh: Virtual Account BCA, QRIS, dll.)
9. **Simulasikan pembayaran** menggunakan akun/kartu test sandbox Midtrans
10. Setelah pembayaran berhasil, **status booking berubah menjadi "DP Lunas"**
11. **Lihat riwayat booking** → klik menu **"Riwayat Booking"** di navbar

---

## 💳 Cara Bayar di Midtrans Sandbox (Simulasi Pembayaran)

> Aplikasi menggunakan mode **Sandbox** (simulasi), bukan uang sungguhan.

### Opsi A — Virtual Account BCA:
1. Pilih metode **"BCA Virtual Account"** di pop-up Midtrans
2. Salin nomor Virtual Account yang diberikan
3. Buka **Midtrans Payment Simulator**: https://simulator.sandbox.midtrans.com
4. Pilih **"BCA VA"** → masukkan nomor VA → klik **"Inquiry"** → klik **"Pay"**
5. Kembali ke aplikasi → status booking akan otomatis terupdate

### Opsi B — Kartu Kredit (Test Card):
1. Pilih metode **"Kartu Kredit"** di pop-up Midtrans
2. Gunakan data kartu test berikut:
   - Nomor kartu: `4811 1111 1111 1114`
   - Expired: `01/25` (bulan/tahun mendatang)
   - CVV: `123`
   - OTP/3DS: `112233`
3. Klik **"Bayar"**

---

## 🛠️ Simulasi Alur Admin / Kasir

1. **Login sebagai Admin** → https://rackly-bl.vercel.app/login
   - Email: `admin@rackly.com`
   - Password: `admin123`
2. **Dashboard Admin** → Lihat statistik:
   - Total booking hari ini
   - Total pendapatan DP hari ini
   - Daftar booking terbaru
3. **Manajemen Pesanan** → Menu **"Kelola Pesanan"**
   - Lihat semua booking dengan status (Pending DP, DP Lunas, Lunas, dll.)
   - Konfirmasi pelunasan booking yang sudah berstatus **"DP Lunas"**
   - Batalkan atau hapus pesanan jika diperlukan
4. **Manajemen Meja** → Menu **"Kelola Meja"**
   - Lihat semua meja biliar
   - Ubah status meja (Aktif / Nonaktif)
   - Tambah meja baru
5. **Pengaturan** → Menu **"Pengaturan"**
   - Ubah persentase DP (default: 30%)
   - Ubah jam operasional

---

## 📋 Catatan Teknis

- **Platform:** Vercel (serverless PHP) + TiDB Cloud (database MySQL)
- **Payment Gateway:** Midtrans Sandbox (simulasi, bukan transaksi nyata)
- **Region Server:** Singapore (sin1) untuk latensi rendah
- **Webhook Midtrans:** `https://rackly-bl.vercel.app/webhook/midtrans`
- **Status update** booking berjalan otomatis via webhook Midtrans setelah pembayaran selesai
