<?php
// Contoh konfigurasi database untuk sis25.php
// Sesuaikan dengan konfigurasi database Anda

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_NAME', 'dstmobil_db'); // Ganti dengan nama database Anda
define('DB_USER', 'your_username'); // Ganti dengan username database Anda
define('DB_PASS', 'your_password'); // Ganti dengan password database Anda
define('DB_CHARSET', 'utf8mb4');

// Konfigurasi lainnya
define('TOKEN', 'your_secure_token_here');

// Mulai session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    // Membuat koneksi PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // Log error dan tampilkan pesan error yang user-friendly
    error_log("Database connection error: " . $e->getMessage());
    die("Koneksi database gagal. Silakan hubungi administrator.");
}

// Fungsi untuk verifikasi password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Fungsi untuk hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Fungsi untuk sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Fungsi untuk format nomor WhatsApp
function formatWhatsApp($wa) {
    // Hapus semua karakter non-digit
    $wa = preg_replace('/[^0-9]/', '', $wa);
    
    // Jika dimulai dengan 08, ganti dengan 628
    if (substr($wa, 0, 2) == '08') {
        $wa = '628' . substr($wa, 2);
    }
    
    // Jika dimulai dengan 8, tambahkan 62 di depan
    if (substr($wa, 0, 1) == '8') {
        $wa = '62' . $wa;
    }
    
    return $wa;
}

// Fungsi untuk cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['admin_wa']) && !empty($_SESSION['admin_wa']);
}

// Fungsi untuk logout
function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Fungsi untuk redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

?>