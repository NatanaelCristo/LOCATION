<?php
require "sis25.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'insert_admin') {
    
    $wa = trim($_POST['wa'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $password = $_POST['password'] ?? '';
    $cabang = $_POST['cabang'] ?? '';
    
    // Validate input
    if (empty($wa) || empty($nama) || empty($password) || empty($cabang)) {
        echo json_encode([
            'success' => false,
            'message' => 'Semua field harus diisi!'
        ]);
        exit;
    }
    
    // Validate password length
    if (strlen($password) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'Password minimal 6 karakter!'
        ]);
        exit;
    }
    
    // Validate WA number (should be numbers only)
    if (!preg_match('/^\d+$/', $wa)) {
        echo json_encode([
            'success' => false,
            'message' => 'Nomor WA hanya boleh berisi angka!'
        ]);
        exit;
    }
    
    try {
        // Check if WA already exists
        $check_stmt = $konsis25->prepare("SELECT wa FROM admin WHERE wa = ?");
        $check_stmt->bind_param("s", $wa);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Nomor WA sudah terdaftar!'
            ]);
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();
        
        // Hash password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert new admin
        $insert_stmt = $konsis25->prepare("INSERT INTO admin (wa, nama, password, cabang) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $wa, $nama, $hashed_password, $cabang);
        
        if ($insert_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Admin berhasil ditambahkan!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $insert_stmt->error
            ]);
        }
        $insert_stmt->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method or action!'
    ]);
}

$konsis25->close();
?>