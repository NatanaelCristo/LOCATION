# Sistem Manajemen Admin DST Mobil

Sistem ini mengelola akun admin untuk domain dstmobil.com dengan fitur keamanan password menggunakan bcrypt.

## Struktur Tabel Database

### Tabel `admin`
- `wa` (VARCHAR 15) - Primary Key, nomor WhatsApp
- `nama` (VARCHAR 100) - Nama lengkap admin
- `password` (VARCHAR 255) - Password yang di-hash dengan bcrypt
- `cabang` (VARCHAR 50) - Cabang tempat admin bertugas
- `created_at` (TIMESTAMP) - Waktu pembuatan akun
- `updated_at` (TIMESTAMP) - Waktu terakhir diupdate

## File yang Dibuat

### 1. `daftar_akun.php`
**Fungsi:** Menampilkan daftar semua akun admin
**Fitur:**
- Menampilkan data wa, nama, dan cabang
- Tombol tambah akun baru
- Tombol edit dan hapus untuk setiap akun
- Pagination dan filter tampilan
- Keamanan dengan htmlspecialchars untuk mencegah XSS

### 2. `daftar_akun_tambah.php`
**Fungsi:** Form untuk menambah akun admin baru
**Fitur:**
- Form input wa, nama, password, dan cabang
- Validasi format nomor WhatsApp (628xxxxxxxxxx)
- Password di-hash dengan bcrypt sebelum disimpan
- Validasi duplikasi nomor WA
- Redirect otomatis setelah berhasil menyimpan

### 3. `login_admin.php`
**Fungsi:** Halaman login untuk admin
**Fitur:**
- Form login dengan nomor WA dan password
- Verifikasi password menggunakan bcrypt
- Session management
- Auto-format nomor WhatsApp
- Toggle visibility password
- Responsive design dengan Bootstrap

### 4. `create_admin_table.sql`
**Fungsi:** Script SQL untuk membuat tabel admin
**Isi:**
- Struktur tabel lengkap dengan index
- Data contoh dengan password "admin123" (sudah di-hash)
- Komentar untuk setiap field

### 5. `config_database_example.php`
**Fungsi:** Contoh konfigurasi database dan fungsi helper
**Fitur:**
- Koneksi PDO dengan error handling
- Fungsi hash dan verifikasi password
- Fungsi format nomor WhatsApp
- Session management functions
- Input sanitization

## Cara Instalasi

### 1. Setup Database
```sql
-- Jalankan script SQL
source create_admin_table.sql;
```

### 2. Konfigurasi Database
1. Salin `config_database_example.php` menjadi `sis25.php`
2. Sesuaikan konfigurasi database:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nama_database_anda');
define('DB_USER', 'username_database');
define('DB_PASS', 'password_database');
```

### 3. Upload File
Upload semua file PHP ke server web Anda.

## Cara Penggunaan

### Login Admin
1. Akses `login_admin.php`
2. Masukkan nomor WA (format: 628xxxxxxxxxx)
3. Masukkan password
4. Klik "Masuk"

**Akun Default:**
- WA: 628123456789
- Password: admin123

### Mengelola Akun
1. Setelah login, akses `daftar_akun.php`
2. Untuk menambah akun baru, klik tombol "Tambah"
3. Isi form dengan data lengkap
4. Password akan otomatis di-hash dengan bcrypt

## Fitur Keamanan

### 1. Password Bcrypt
- Semua password di-hash menggunakan `PASSWORD_BCRYPT`
- Verifikasi menggunakan `password_verify()`
- Cost factor default PHP (10)

### 2. Input Validation
- Format nomor WhatsApp divalidasi
- Semua input di-sanitize dengan `htmlspecialchars()`
- Prepared statements untuk query database

### 3. Session Management
- Session untuk autentikasi admin
- Auto-redirect jika belum login
- Logout function tersedia

### 4. SQL Injection Prevention
- Menggunakan PDO prepared statements
- Parameter binding untuk semua query

## Format Nomor WhatsApp

Sistem otomatis memformat nomor WhatsApp:
- Input: `08123456789` → Output: `628123456789`
- Input: `8123456789` → Output: `628123456789`
- Input: `628123456789` → Output: `628123456789`

## Struktur File Dependencies

```
sis25.php (konfigurasi database dan fungsi)
├── daftar_akun.php
├── daftar_akun_tambah.php
├── login_admin.php
└── file template lainnya:
    ├── head.php
    ├── head_log.php
    ├── spinner.php
    ├── navigasi.php
    ├── menu_top.php
    ├── footer.php
    └── script.php
```

## Catatan Penting

1. **Backup Database:** Selalu backup database sebelum mengubah struktur tabel
2. **Password Default:** Ubah password default setelah instalasi pertama
3. **File Permissions:** Pastikan file PHP memiliki permission yang tepat
4. **HTTPS:** Gunakan HTTPS untuk keamanan login
5. **Error Logging:** Aktifkan error logging untuk debugging

## Troubleshooting

### Error Koneksi Database
- Periksa konfigurasi di `sis25.php`
- Pastikan database server berjalan
- Periksa permission user database

### Login Gagal
- Periksa format nomor WhatsApp (harus 628xxxxxxxxxx)
- Pastikan password sesuai dengan yang tersimpan
- Periksa session configuration

### Error Permission
- Pastikan file PHP readable oleh web server
- Periksa permission direktori upload

## Update dan Maintenance

- Regularly update password untuk akun admin
- Monitor log error untuk issue keamanan
- Backup database secara berkala
- Update dependency (Bootstrap, FontAwesome) sesuai kebutuhan