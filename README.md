# Sistem Informasi Kuliner Wisata Lombok

Sistem Informasi Kuliner Wisata Lombok adalah sebuah platform direktori web yang mendata restoran bersejarah, legendaris, dan tradisional di Pulau Lombok. Platform ini memungkinkan pemilik restoran untuk mendaftarkan tempat usaha mereka, sementara pengunjung dapat menjelajahi, memfilter, dan memberikan ulasan pada setiap restoran.

## Fitur Utama

### 1. Panel Pengunjung (Visitor)
* **Eksplorasi & Pencarian:** Mencari restoran berdasarkan kata kunci, rentang harga, wilayah (kecamatan), dan kategori sejarah.
* **Detail Restoran:** Melihat informasi profil lengkap, fasilitas, sejarah, peta lokasi terintegrasi (Google Maps), daftar menu beserta harganya, dan galeri foto.
* **Ulasan & Penilaian (Rating):** Memberikan ulasan dan rating 1-5 bintang pada restoran yang dikunjungi.
* **Social Share:** Membagikan halaman restoran ke WhatsApp, Facebook, dan Twitter.

### 2. Panel Admin Restoran
* **Registrasi & Login:** Sistem autentikasi aman dengan *password hashing*.
* **Manajemen Profil & Peta:** Mengatur profil restoran, kategori, fasilitas, dan menandai lokasi presisi secara interaktif menggunakan peta (Leaflet.js).
* **Manajemen Menu:** Menambahkan, mengedit, dan menghapus daftar menu beserta fotonya.
* **Manajemen Ulasan:** Melihat dan menghapus/menyetujui ulasan yang masuk (moderasi ulasan).

### 3. Arsitektur Desain Modern (UI/UX)
* Menggunakan pendekatan desain *Glassmorphism* yang elegan.
* Palet warna eksklusif (Dark Emerald Green & Gold).
* Animasi mikro interaktif pada kartu dan tombol.
* Responsif di semua perangkat (Mobile, Tablet, Desktop).

## Teknologi yang Digunakan
* **Backend:** PHP (Native/Procedural) 
* **Database:** MySQL
* **Frontend:** HTML5, CSS3, JavaScript (ES6)
* **Framework CSS:** Bootstrap 5
* **Peta Interaktif:** Leaflet.js (Dashboard Admin) & Google Maps Embed (Detail Pengunjung)
* **Ikon:** Bootstrap Icons

## Panduan Instalasi (Setup)

Jika Anda ingin melanjutkan atau menjalankan project ini secara lokal, ikuti langkah-langkah berikut:

1. **Persyaratan Sistem:**
   - Web Server lokal (Laragon / XAMPP). disarankan menggunakan **Laragon**.
   - PHP versi 8.0 ke atas.
   - Ekstensi PHP `mysqli` aktif.

2. **Kloning & Ekstraksi:**
   - Salin atau *clone* seluruh *folder* project ini (`my_kuliner`) ke dalam direktori *document root* web server Anda (contoh di Laragon: `C:\laragon\www\my_kuliner`, atau di XAMPP: `C:\xampp\htdocs\my_kuliner`).

3. **Konfigurasi Database:**
   - Buka phpMyAdmin, HeidiSQL, atau DBeaver.
   - Buat *database* baru dengan nama: `my_kuliner_db`.
   - Lakukan *import* berkas SQL yang ada ke dalam *database* tersebut. *(Catatan: Pastikan Anda melakukan ekspor database terkini dari sistem dan melampirkannya bersama project ini).*

4. **Konfigurasi Koneksi:**
   - Buka *file* `config/database.php`.
   - Sesuaikan kredensial koneksi (Host, Username, Password, Database Name) dengan konfigurasi lokal Anda:
     ```php
     $dbHost = 'localhost';
     $dbUser = 'root';
     $dbPass = ''; // Kosongkan jika default
     $dbName = 'my_kuliner_db';
     ```

5. **Jalankan Aplikasi:**
   - Buka *browser* dan akses URL: `http://localhost/my_kuliner` atau `http://my_kuliner.test` (jika menggunakan Auto Virtual Hosts Laragon).

## Struktur Folder Utama
```text
my_kuliner/
├── assets/          # File CSS (style.css), JS kustom, dan font statis
├── config/          # Pengaturan database dan konstanta sistem
├── includes/        # Potongan file modular (header, footer, navbar, dll)
├── restaurant_admin/# Modul dashboard khusus admin restoran
├── uploads/         # Direktori penyimpanan unggahan gambar (menu, restoran)
├── index.php        # Halaman Beranda (Landing Page)
├── restaurants.php  # Halaman Katalog & Pencarian
└── restaurant_detail.php # Halaman Detail Restoran (Profil, Menu, Ulasan)
```

## Catatan Tambahan (Bagi Developer Selanjutnya)
- Seluruh data gambar (*upload*) akan tersimpan di dalam folder `uploads/`. Pastikan folder ini memiliki hak akses *write* (tulis) jika Anda melakukan *deploy* ke server *hosting/VPS*.
- Pembersihan *input* (*escaping*) menggunakan fungsi `htmlspecialchars` (dibungkus dalam `e()`) yang terletak di dalam *file* bantuan untuk mencegah kerentanan XSS.
- Autentikasi berjalan murni menggunakan `$_SESSION`. Pastikan fitur *Session* berjalan normal pada instalasi PHP Anda.

---
**Dikembangkan bersama AI Antigravity** - Selamat mengembangkan proyek ini!
