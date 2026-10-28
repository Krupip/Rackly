# Checklist Fitur MVP & Status Pengujian Aplikasi Rackly

Dokumen ini memuat daftar periksa (*checklist*) seluruh fitur inti Minimum Viable Product (MVP) aplikasi **Rackly (Sistem Reservasi Meja Biliar Online Terintegrasi Payment Gateway DP)** beserta status implementasi dan status hasil pengujian otomatisnya.

---

## Ringkasan Eksekutif Hasil Pengujian

- **Target Status:** MVP Lengkap & Siap Didemonstrasikan
- **Cakupan Fitur:** 100% Selesai Diimplementasikan
- **Hasil Pengujian Otomatis (Automated Tests):**
  - **Status:** **`PASSED`** (Semua Pengujian Lolos)
  - **Total Test Cases:** **80 Tests**
  - **Total Assertions:** **233 Assertions**
  - **Kegagalan (Failed / Error):** **0**
- **Framework Pengujian:** Pest PHP (v4.7) / PHPUnit 12 pada Laravel 13

---

## 1. Tabel Checklist Seluruh Fitur MVP

### A. Autentikasi, Profil, & Kontrol Akses (RBAC)
| No | Fitur / Komponen | Cakupan & Kriteria Penerimaan | Status Implementasi | Status Pengujian | Kasus Uji / File Test Terkait |
| :---: | :--- | :--- | :---: | :---: | :--- |
| 1.1 | **Registrasi Pengguna** | Pelanggan baru dapat mendaftar akun dengan validasi nama, email unik, dan konfirmasi password. | Selesai | **PASSED** | `RegistrationTest` (`new_users_can_register`) |
| 1.2 | **Login Pengguna & Admin** | Autentikasi berbasis sesi (*session-based*) dengan penolakan kredensial salah dan proteksi brute-force. | Selesai | **PASSED** | `AuthenticationTest` (`users_can_authenticate...`) |
| 1.3 | **Logout Pengguna** | Penghancuran sesi dan pembersihan cookie sesi browser. | Selesai | **PASSED** | `AuthenticationTest` (`users_can_logout`) |
| 1.4 | **Role-Based Access Control (RBAC)** | Pembagian peran `admin` dan `user`. Rute `/admin/*` hanya dapat diakses oleh admin, user biasa ditolak dengan 403 Forbidden. | Selesai | **PASSED** | `AdminManagementTest` (`regular_user_cannot_access_any_admin_routes`, `UserTest`) |
| 1.5 | **Manajemen Profil Pengguna** | Edit informasi nama, email, ubah kata sandi, dan opsi hapus akun dengan verifikasi password. | Selesai | **PASSED** | `ProfileTest` (5 test cases), `PasswordUpdateTest` |

---

### B. Katalog & Ketersediaan Meja Interaktif
| No | Fitur / Komponen | Cakupan & Kriteria Penerimaan | Status Implementasi | Status Pengujian | Kasus Uji / File Test Terkait |
| :---: | :--- | :--- | :---: | :---: | :--- |
| 2.1 | **Katalog Meja (Landing Page)** | Menampilkan seluruh meja biliar aktif beserta spesifikasi, harga sewa per jam, dan foto meja. | Selesai | **PASSED** | `ExampleTest` (`it_returns_a_successful_response_on_home_page...`) |
| 2.2 | **Status Meja Nonaktif / Pemeliharaan** | Meja dengan status `inactive` / pemeliharaan tidak dapat dipilih atau dibooking oleh pelanggan. | Selesai | **PASSED** | `BookingFlowTest` (`user_cannot_view_booking_form_for_inactive_maintenance_table`), `BilliardTableTest` |
| 2.3 | **Penetapan Harga Dinamis & Kebijakan DP** | Menampilkan besaran DP (persentase 40% atau nominal tetap) sesuai konfigurasi sistem di dashboard admin. | Selesai | **PASSED** | `ExampleTest` (`it_displays_fixed_dp_nominal_on_home_page...`), `SettingTest` |

---

### C. Sistem Reservasi Cerdas & Validasi Jadwal
| No | Fitur / Komponen | Cakupan & Kriteria Penerimaan | Status Implementasi | Status Pengujian | Kasus Uji / File Test Terkait |
| :---: | :--- | :--- | :---: | :---: | :--- |
| 3.1 | **Pemilihan Tanggal & Slot Jam** | Kalender interaktif dan pemilihan jam mulai serta durasi bermain (minimal 1 jam). | Selesai | **PASSED** | `BookingFlowTest` (`user_can_view_booking_form_for_active_table`) |
| 3.2 | **Pencegahan Double-Booking (Anti-Overlap)** | Sistem menolak jika ada jadwal yang bertabrakan (*overlapping*) pada meja dan tanggal yang sama. | Selesai | **PASSED** | `BookingConflictTest` (`prevents_booking_overlapping_time_slots_on_the_same_table`) |
| 3.3 | **Dukungan Reservasi Estafet (Back-to-Back)** | Memperbolehkan pemesanan baru yang dimulai tepat pada jam selesainya pemesanan sebelumnya tanpa jeda waktu. | Selesai | **PASSED** | `BookingConflictTest` (`allows_back_to_back_estafet_bookings...`) |
| 3.4 | **Reservasi Paralel Antar Meja** | Memperbolehkan pemesanan di jam yang sama asalkan pada nomor meja yang berbeda. | Selesai | **PASSED** | `BookingConflictTest` (`allows_booking_at_the_same_time_on_different_tables`) |
| 3.5 | **Pencegahan Booking Waktu Lampau (Past Time Prevention)** | Validasi ketat di sisi backend dan frontend: menolak tanggal kemarin serta menolak jam yang sudah lewat untuk hari ini berbasis timezone `Asia/Jakarta`. | Selesai | **PASSED** | `BookingConflictTest` (`user_cannot_book_a_date_in_the_past`, `user_cannot_book_a_past_hour_for_today`) |
| 3.6 | **Perhitungan Otomatis Biaya & Uang Muka (DP)** | Kalkulasi matematis durasi bermain × tarif/jam, lalu nominal DP dihitung secara akurat dan sisa tagihan dicatat. | Selesai | **PASSED** | `BookingTest` (`calculates_correct_duration_and_total_price...`, `calculates_correct_DP_amount...`) |

---

### D. Integrasi Pembayaran Midtrans Snap & Webhook
| No | Fitur / Komponen | Cakupan & Kriteria Penerimaan | Status Implementasi | Status Pengujian | Kasus Uji / File Test Terkait |
| :---: | :--- | :--- | :---: | :---: | :--- |
| 4.1 | **Halaman Checkout & Ringkasan Pesanan** | Menampilkan rincian meja, jadwal, subtotal, DP yang harus dibayar, dan sisa pelunasan di lokasi. Dilindungi dari IDOR (hanya pemilik pesanan yang bisa akses). | Selesai | **PASSED** | `BookingFlowTest` (`user_can_view_checkout_page...`, `user_cannot_view_checkout_page_for_another_users_booking`) |
| 4.2 | **Generasi Midtrans Snap Token** | Pembuatan token transaksi Midtrans otomatis berbasis Order ID unik untuk menampilkan pop-up pembayaran di browser. | Selesai | **PASSED** | `PaymentTest` (`can_create_a_payment_record_with_midtrans_order_id`) |
| 4.3 | **Sinkronisasi Webhook Otomatis (Settlement)** | Endpoint HTTP POST `/webhook/midtrans` menerima notifikasi pembayaran sukses, memvalidasi signature key, lalu otomatis mengubah status booking menjadi `dp_lunas`. | Selesai | **PASSED** | `PaymentConfirmationTest` (`midtrans_webhook_updates_booking_to_dp_lunas_on_settlement`) |
| 4.4 | **Penanganan Pembayaran Kedaluwarsa / Dibatalkan** | Jika transaksi Midtrans berstatus `expire` atau `cancel`, webhook otomatis mengubah status booking menjadi `cancelled` sehingga slot meja kembali tersedia. | Selesai | **PASSED** | `PaymentConfirmationTest` (`midtrans_webhook_cancels_booking_on_expire_or_cancel`), `BookingConflictTest` (`ignores_cancelled_bookings...`) |

---

### E. Riwayat Pemesanan & Pembatalan Mandiri
| No | Fitur / Komponen | Cakupan & Kriteria Penerimaan | Status Implementasi | Status Pengujian | Kasus Uji / File Test Terkait |
| :---: | :--- | :--- | :---: | :---: | :--- |
| 5.1 | **Daftar Riwayat Booking Pelanggan** | Halaman khusus pelanggan untuk melihat daftar pemesanan aktif maupun lampau beserta statusnya (`pending`, `dp_lunas`, `completed`, `cancelled`). | Selesai | **PASSED** | `BookingFlowTest` (`user_can_view_their_own_booking_history`) |
| 5.2 | **Pembatalan Mandiri Pelanggan** | Pelanggan dapat membatalkan pesanan yang masih berstatus `pending` secara mandiri. Dilindungi dari pembatalan pesanan milik orang lain. | Selesai | **PASSED** | `BookingConflictTest` (`user_can_cancel_their_own_pending_booking`, `user_cannot_cancel_someone_elses_booking`) |

---

### F. Fitur Operasional Kasir & Admin (Walk-In & Pelunasan)
| No | Fitur / Komponen | Cakupan & Kriteria Penerimaan | Status Implementasi | Status Pengujian | Kasus Uji / File Test Terkait |
| :---: | :--- | :--- | :---: | :---: | :--- |
| 6.1 | **Input Booking Walk-In di Kasir** | Staf admin dapat mendaftarkan pelanggan yang datang langsung (*walk-in*) tanpa akun dan tanpa kewajiban DP melalui form khusus. | Selesai | **PASSED** | `WalkInBookingTest` (`admin_can_create_a_walk_in_booking_without_paying_dp`) |
| 6.2 | **Validasi Bentrok Booking Walk-In** | Form booking walk-in memiliki validasi jadwal yang sama ketatnya dengan booking online untuk mencegah double-booking. | Selesai | **PASSED** | `WalkInBookingTest` (`walk_in_booking_fails_when_timeslot_conflicts...`, `...validation_fails_when_end_time_is_not_after_start_time`) |
| 6.3 | **Konfirmasi Pelunasan Sisa Tagihan** | Kasir dapat mengonfirmasi pelunasan sisa tagihan saat pelanggan tiba di lokasi, mengubah status pesanan dari `dp_lunas` menjadi `completed`. | Selesai | **PASSED** | `PaymentConfirmationTest` (`admin_can_confirm_full_payment_for_dp_lunas_booking`) |
| 6.4 | **Pencegahan Pelunasan Invalid** | Sistem menolak konfirmasi pelunasan jika booking belum berstatus `dp_lunas` (misal masih `pending`). | Selesai | **PASSED** | `PaymentConfirmationTest` (`admin_cannot_confirm_payment_for_booking_that_is_not_dp_lunas`) |

---

### G. Dashboard Admin & Konfigurasi Sistem
| No | Fitur / Komponen | Cakupan & Kriteria Penerimaan | Status Implementasi | Status Pengujian | Kasus Uji / File Test Terkait |
| :---: | :--- | :--- | :---: | :---: | :--- |
| 7.1 | **Metrik & Ringkasan Dashboard** | Menampilkan statistik jumlah booking hari ini, meja aktif, pendapatan DP, dan daftar pemesanan terbaru. | Selesai | **PASSED** | `AdminManagementTest` (`admin_can_view_admin_dashboard_with_correct_metrics`) |
| 7.2 | **Manajemen Meja Biliar (CRUD)** | Tambah meja baru, edit nama/harga/foto/status meja, serta hapus meja dari sistem. | Selesai | **PASSED** | `AdminManagementTest` (`admin_can_create_a_new_billiard_table`, `...update...`, `...delete...`) |
| 7.3 | **Pengaturan Kebijakan DP Sistem** | Admin dapat mengatur mode DP (persentase atau nominal tetap) dengan validasi input yang ketat. | Selesai | **PASSED** | `AdminManagementTest` (`admin_can_update_DP_setting...`, `...validation_fails_with_invalid_percentage`, `...missing_or_invalid`) |

---

### H. Desain Antarmuka (UI/UX) & Penanganan Kegagalan
| No | Fitur / Komponen | Cakupan & Kriteria Penerimaan | Status Implementasi | Status Pengujian | Catatan Implementasi |
| :---: | :--- | :--- | :---: | :---: | :--- |
| 8.1 | **Modal Pop-up Kustom (Bebas `alert()` bawaan)** | Seluruh notifikasi sukses, peringatan error, dan konfirmasi pembatalan menggunakan komponen pop-up modal kustom berbasis Alpine.js yang selaras dengan tema UI. | Selesai | **PASSED** | Diuji manual pada 4 tampilan (Home, Booking, History, Admin) |
| 8.2 | **Penanganan Data Kosong (Empty States)** | Tampilan informatif ketika belum ada meja terdaftar, belum ada riwayat booking, atau tidak ada jadwal masuk hari ini. | Selesai | **PASSED** | Diuji manual pada Blade views |
| 8.3 | **Pencegahan Dobel Klik & State Loading** | Tombol checkout dan simpan dilengkapi feedback visual (*disabled state*) untuk mencegah pengiriman ganda. | Selesai | **PASSED** | Diuji manual |

---

## 2. Rincian Eksekusi Test Suite Otomatis

Berikut rincian distribusi 80 test cases yang telah dijalankan dan lulus 100%:

```
================================================================================
Test Suite Summary: Pest PHP / PHPUnit
================================================================================
[Unit Tests]
  - BilliardTableTest             : 3 passed
  - BookingTest                   : 8 passed
  - ExampleTest                   : 1 passed
  - PaymentTest                   : 3 passed
  - SettingTest                   : 5 passed
  - UserTest                      : 4 passed
  Subtotal Unit Tests             : 24 passed

[Feature Tests]
  - AdminManagementTest           : 10 passed
  - AuthenticationTest            : 4 passed
  - EmailVerificationTest         : 3 passed
  - PasswordConfirmationTest      : 3 passed
  - PasswordUpdateTest            : 2 passed
  - RegistrationTest              : 2 passed
  - BookingConflictTest           : 8 passed
  - BookingFlowTest               : 7 passed
  - ExampleTest                   : 2 passed
  - PaymentConfirmationTest       : 5 passed
  - ProfileTest                   : 5 passed
  - WalkInBookingTest             : 5 passed
  Subtotal Feature Tests          : 56 passed
--------------------------------------------------------------------------------
TOTAL TEST CASES                  : 80 PASSED (233 assertions)
STATUS AKHIR                      : PASSED
================================================================================
```

---

## 3. Panduan Skenario Rekaman Video Demo (`demo.mp4`)

Untuk memenuhi pengumpulan file `demo.mp4`, disarankan merekam alur lengkap aplikasi dengan skenario berikut (durasi ideal: 3–5 menit):

### Skenario 1: Alur Pemesanan Online oleh Pelanggan (User)
1. **Buka Aplikasi:** Tampilkan halaman utama (`http://localhost:8000`), perlihatkan katalog meja biliar dan info tarif/DP.
2. **Login / Daftar:** Masuk menggunakan akun pelanggan (`user@example.com` / `password`).
3. **Pilih Meja & Jadwal:** Klik **Pesan Meja**, pilih tanggal (hari ini/besok), pilih jam mulai dan durasi. Perlihatkan bahwa jam lampau tidak dapat dipilih.
4. **Halaman Checkout:** Tampilkan kalkulasi total harga, DP 40%, dan sisa pelunasan.
5. **Pembayaran DP (Midtrans Simulator):** Klik tombol bayar, muncul pop-up Snap Midtrans. Pilih metode pembayaran (misal QRIS / Virtual Account) dan selesaikan simulasi pembayaran.
6. **Riwayat Booking:** Tampilkan status pesanan di halaman riwayat berubah menjadi **DP Lunas**.

### Skenario 2: Alur Operasional Kasir & Admin
1. **Login Admin:** Masuk menggunakan akun `admin@rackly.com` / `password`.
2. **Dashboard Operasional:** Tampilkan statistik meja, booking masuk hari ini, dan daftar transaksi.
3. **Konfirmasi Pelunasan:** Temukan booking pelanggan dari Skenario 1, klik **Konfirmasi Pelunasan** saat pelanggan dianggap sudah datang dan melunasi sisa tagihan di kasir. Status berubah menjadi **Selesai**.
4. **Input Booking Walk-In:** Buat pemesanan langsung dari meja kasir untuk pelanggan yang datang tanpa aplikasi (tanpa DP).
5. **Uji Anti Double-Booking:** Coba pilih slot meja dan jam yang sama dengan booking yang sudah ada, tunjukkan sistem mendeteksi konflik jadwal.
6. **Kelola Meja / Pengaturan:** Tampilkan sekilas menu manajemen meja (tambah/edit meja) dan pengaturan DP.

