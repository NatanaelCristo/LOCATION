# Admin Account Management System

Sistem manajemen akun admin untuk domain dstmobil.com dengan tabel admin yang memiliki struktur:
- `wa` (primary key) - Nomor WhatsApp
- `nama` - Nama lengkap admin
- `password` - Password yang di-encrypt menggunakan bcrypt
- `cabang` - Cabang dari pilihan yang tersedia

## Files Structure

```
/
├── sis25.php                    # Database configuration & branch definitions
├── daftar_akun_tambah.php       # Form tambah admin (PHP standard)
├── daftar_akun_tambah_ajax.php  # Form tambah admin (AJAX version)
├── get_admin_list.php           # Helper untuk pagination data admin
├── create_admin_table.sql       # SQL script untuk membuat tabel
└── api/
    └── ho/
        └── admin_handler.php    # AJAX handler untuk operasi admin
```

## Installation

1. **Create Database Table**
   ```sql
   -- Run the SQL script
   source create_admin_table.sql
   ```

2. **Configure Database**
   - Update database credentials in `sis25.php` if needed
   - Make sure database connection is working

3. **Set Available Branches**
   - Branches are defined in `sis25.php` in the `$apiCabangs` array
   - Current branches: Jakarta, Tangerang
   - Add more branches by updating the array

## Features

### 1. Add Admin Account (`daftar_akun_tambah.php`)
- Form untuk menambah akun admin baru
- Password dengan toggle show/hide (eye icon)
- Dropdown cabang berdasarkan data di `sis25.php`
- Password di-encrypt menggunakan bcrypt
- Validasi input dan duplicate check

### 2. AJAX Version (`daftar_akun_tambah_ajax.php`)
- Semua fitur dari versi standard
- Real-time form submission tanpa reload halaman
- Live table update setelah add/edit/delete
- Pagination dengan AJAX
- Modal untuk edit admin
- Loading states dan feedback messages

### 3. API Endpoints (`api/ho/admin_handler.php`)
- `add_admin` - Tambah admin baru
- `edit_admin` - Edit data admin
- `delete_admin` - Hapus admin
- `get_admin` - Get data admin by WA
- `get_branches` - Get available branches

### 4. Admin List (`get_admin_list.php`)
- Paginated list of admin accounts
- JSON response untuk AJAX requests

## Usage

### Basic Form (PHP)
```php
// Use daftar_akun_tambah.php
// Standard PHP form with page reload
```

### AJAX Form (Recommended)
```php
// Use daftar_akun_tambah_ajax.php
// Better user experience with AJAX
```

### API Usage
```javascript
// Add admin via AJAX
const formData = new FormData();
formData.append('action', 'add_admin');
formData.append('wa', '628123456789');
formData.append('nama', 'Admin Name');
formData.append('password', 'password123');
formData.append('cabang', 'Jakarta');

fetch('api/ho/admin_handler.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

## Security Features

1. **Password Encryption**: Menggunakan PHP's `password_hash()` dengan bcrypt
2. **Prepared Statements**: Semua query menggunakan prepared statements
3. **Input Validation**: Validasi di client-side dan server-side
4. **XSS Protection**: HTML escaping dengan `htmlspecialchars()`
5. **Duplicate Check**: Cek nomor WA yang sudah terdaftar

## Customization

### Add New Branch
```php
// In sis25.php
$apiCabangs = [
    "https://jakarta.dstmobil.com/api/tabel_servis" => "Jakarta",
    "https://tangerang.dstmobil.com/api/tabel_servis" => "Tangerang",
    "https://bandung.dstmobil.com/api/tabel_servis" => "Bandung" // New branch
];
```

### Modify Table Structure
```sql
-- Add new column to admin table
ALTER TABLE admin ADD COLUMN role VARCHAR(50) DEFAULT 'admin';
```

## Dependencies

- PHP 7.4+
- MySQL/MariaDB
- Bootstrap 5 (for UI)
- FontAwesome (for icons)
- jQuery (for AJAX version)

## Browser Support

- Chrome/Edge (recommended)
- Firefox
- Safari
- Mobile browsers

## Notes

- Gunakan versi AJAX (`daftar_akun_tambah_ajax.php`) untuk user experience yang lebih baik
- Password tidak pernah ditampilkan dalam plaintext setelah di-hash
- Nomor WA harus unik karena digunakan sebagai primary key
- Semua operasi database menggunakan prepared statements untuk keamanan