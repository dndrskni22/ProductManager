SweetGlaze & Brews - Product Management System
  Sistem informasi pengelolaan katalog produk berbasis web yang dirancang untuk toko SweetGlaze & Brews. Aplikasi ini dibangun menggunakan PHP native dan database MySQL untuk menangani operasional inventaris donat dan minuman, mencakup pengelolaan data, validasi input, serta manajemen berkas gambar.   
  
Fitur Utama dan Arsitektur
  Manajemen Data (CRUD)
    -Create: Penambahan produk baru dengan validasi data server-side serta penanganan unggah berkas gambar.   
    -Read: Penampilan daftar produk menggunakan tata letak berbasis CSS Grid.   
    -Update: Pembaruan data produk berdasarkan parameter ID yang tervalidasi.   
    -Delete: Penghapusan data produk menggunakan metode HTTP POST untuk mencegah eksekusi tindakan yang tidak disengaja.   
    
Penerapan Standar Keamanan
-Pencegahan SQL Injection: Seluruh query yang melibatkan parameter dinamis menggunakan PDO Prepared Statements (prepare dan execute).   
-Pencegahan Cross-Site Scripting (XSS): Seluruh data masukan pengguna yang di-render ke HTML diproses menggunakan fungsi htmlspecialchars.   
-Proteksi CSRF (Cross-Site Request Forgery): Operasi penghapusan data dan pembaruan penting menggunakan token acak ($_SESSION['csrf']) yang diverifikasi menggunakan hash_equals().   
-Validasi Server-Side: Memastikan nama produk minimal 3 karakter, harga bertipe numerik positif (price > 0), stok bertipe numerik non-negatif (stock >= 0), serta nama produk bersifat unik (UNIQUE).   

Pola Alur Kerja (Workflows)
-Pola Post-Redirect-Get (PRG): Digunakan pada pemrosesan formulir untuk mencegah pengiriman ulang data saat pengguna melakukan refresh halaman setelah operasi penambahan atau pembaruan.   
-Pencarian dan Filtrasi Data: Menggunakan metode HTTP GET dengan parameter SQL LIKE untuk memfilter nama atau kategori produk tanpa mengubah state pada server.   

Manajemen Berkas Media
-Pengunggahan gambar produk dilengkapi validasi tipe berkas (JPG, JPEG, PNG, WEBP) serta pembatasan ukuran maksimal 2 MB.   

Spesifikasi Teknis
-Bahasa Pemrograman: PHP 8.x   
-Database: MySQL / MariaDB (PDO Extension, ERRMODE_EXCEPTION, FETCH_ASSOC)   
-Frontend: HTML5, CSS3 (Box Model, Flexbox, CSS Grid)   
-Web Server: Apache (XAMPP Environment)   
-Tipografi: Google Fonts (Pacifico & Fredoka)Skema Database

Sistem menggunakan database bernama store_db dengan struktur tabel products sebagai berikut:   
  CREATE DATABASE IF NOT EXISTS store_db;
  USE store_db;
  
  CREATE TABLE IF NOT EXISTS products (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL UNIQUE,
      category VARCHAR(50) NOT NULL DEFAULT 'Umum',
      price INT NOT NULL,
      stock INT NOT NULL DEFAULT 0,
      image VARCHAR(255) DEFAULT 'default.png',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
      
Struktur Direktori Proyek
donuts_drinks_store/
├── config/
│   └── db.php
├── database/
│   └── store_db.sql
├── public/
│   ├── assets/
│   │   └── style.css
│   ├── uploads/
│   ├── create.php
│   ├── delete.php
│   ├── edit.php
│   └── index.php
└── README.md   

Panduan Instalasi dan Penggunaan
-Kloning Repositori
  Unduh berkas proyek ke direktori kerja Anda:git clone https://github.com/dndrskni22/donuts_drinks_store.git

Konfigurasi Web Server
-Pindahkan folder proyek ke dalam direktori publik web server XAMPP:
  C:\xampp\htdocs\donuts_drinks_store   

Pengaturan Database
-Jalankan layanan Apache dan MySQL pada XAMPP Control Panel.   
-Buka phpMyAdmin (http://localhost/phpmyadmin/).   
-Buat database baru dengan nama store_db.   
-Impor berkas database/store_db.sql atau jalankan skrip SQL di atas.   
-Sesuaikan kredensial koneksi database pada berkas config/db.php jika diperlukan.   

Menjalankan Aplikasi
-Akses aplikasi melalui peramban web dengan alamat:http://localhost/donuts_drinks_store/public/index.php.

Matriks Pengujian Fitur dan Keamanan
-Validasi Input: Input nama < 3 karakter atau harga <= 0 -> Form menolak masukan dan menampilkan pesan kesalahan.   
-Pencegahan PRG: Refresh halaman setelah tambah produk -> Tidak terjadi duplikasi data; status dikirim via query string.   
-Uji Keamanan XSS: Input nama produk mengandung tag HTML (contoh: Test) -> Teks ditampilkan sebagai karakter mentah tanpa dieksekusi oleh browser.   
-Uji Proteksi CSRF: Mengirimkan request hapus tanpa token valid -> Server menolak permintaan dengan status HTTP 403 Forbidden.   
-Responsivitas UI: Mengubah ukuran viewport browser -> Grid produk menyesuaikan jumlah kolom secara otomatis.   

PengembangAdinda Riskiani - https://github.com/dndrskni22
