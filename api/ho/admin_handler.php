<?php
require_once '../../sis25.php';

header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get the action from POST data
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_admin':
        addAdmin();
        break;
    case 'edit_admin':
        editAdmin();
        break;
    case 'delete_admin':
        deleteAdmin();
        break;
    case 'get_admin':
        getAdmin();
        break;
    case 'get_branches':
        getBranches();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function addAdmin() {
    global $konsis25;
    
    $wa = $_POST['wa'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $password = $_POST['password'] ?? '';
    $cabang = $_POST['cabang'] ?? '';
    
    // Validate input
    if (empty($wa) || empty($nama) || empty($password) || empty($cabang)) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi!']);
        return;
    }
    
    // Check if WA already exists
    $checkStmt = $konsis25->prepare("SELECT wa FROM admin WHERE wa = ?");
    $checkStmt->bind_param("s", $wa);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Nomor WhatsApp sudah terdaftar!']);
        $checkStmt->close();
        return;
    }
    $checkStmt->close();
    
    // Hash the password using bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert into database
    $stmt = $konsis25->prepare("INSERT INTO admin (wa, nama, password, cabang) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $wa, $nama, $hashed_password, $cabang);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Akun admin berhasil ditambahkan!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
    $stmt->close();
}

function editAdmin() {
    global $konsis25;
    
    $wa = $_POST['wa'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $password = $_POST['password'] ?? '';
    $cabang = $_POST['cabang'] ?? '';
    
    // Validate input
    if (empty($wa) || empty($nama) || empty($cabang)) {
        echo json_encode(['success' => false, 'message' => 'WA, Nama, dan Cabang harus diisi!']);
        return;
    }
    
    // Prepare update query
    if (!empty($password)) {
        // Update with new password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $konsis25->prepare("UPDATE admin SET nama = ?, password = ?, cabang = ? WHERE wa = ?");
        $stmt->bind_param("ssss", $nama, $hashed_password, $cabang, $wa);
    } else {
        // Update without password change
        $stmt = $konsis25->prepare("UPDATE admin SET nama = ?, cabang = ? WHERE wa = ?");
        $stmt->bind_param("sss", $nama, $cabang, $wa);
    }
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Data admin berhasil diupdate!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ada perubahan data atau admin tidak ditemukan!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
    $stmt->close();
}

function deleteAdmin() {
    global $konsis25;
    
    $wa = $_POST['wa'] ?? '';
    
    if (empty($wa)) {
        echo json_encode(['success' => false, 'message' => 'Nomor WhatsApp harus diisi!']);
        return;
    }
    
    $stmt = $konsis25->prepare("DELETE FROM admin WHERE wa = ?");
    $stmt->bind_param("s", $wa);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Admin berhasil dihapus!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Admin tidak ditemukan!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
    $stmt->close();
}

function getAdmin() {
    global $konsis25;
    
    $wa = $_POST['wa'] ?? '';
    
    if (empty($wa)) {
        echo json_encode(['success' => false, 'message' => 'Nomor WhatsApp harus diisi!']);
        return;
    }
    
    $stmt = $konsis25->prepare("SELECT wa, nama, cabang FROM admin WHERE wa = ?");
    $stmt->bind_param("s", $wa);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $admin]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Admin tidak ditemukan!']);
    }
    $stmt->close();
}

function getBranches() {
    $branches = getAvailableBranches();
    echo json_encode(['success' => true, 'data' => $branches]);
}
?>