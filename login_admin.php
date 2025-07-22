<?php
require 'sis25.php';

$message = '';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header("Location: daftar_akun.php");
    exit();
}

// Proses login
if ($_POST) {
    $wa = formatWhatsApp($_POST['wa']);
    $password = $_POST['password'];
    
    if (empty($wa) || empty($password)) {
        $message = '<div class="alert alert-danger">Nomor WA dan password harus diisi!</div>';
    } else {
        try {
            // Cari admin berdasarkan nomor WA
            $query = "SELECT wa, nama, password, cabang FROM admin WHERE wa = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$wa]);
            $admin = $stmt->fetch();
            
            if ($admin && verifyPassword($password, $admin['password'])) {
                // Login berhasil
                $_SESSION['admin_wa'] = $admin['wa'];
                $_SESSION['admin_nama'] = $admin['nama'];
                $_SESSION['admin_cabang'] = $admin['cabang'];
                
                // Update last login (opsional)
                $updateQuery = "UPDATE admin SET updated_at = CURRENT_TIMESTAMP WHERE wa = ?";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([$admin['wa']]);
                
                header("Location: daftar_akun.php");
                exit();
            } else {
                $message = '<div class="alert alert-danger">Nomor WA atau password salah!</div>';
            }
        } catch(PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login Admin - DST Mobil</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Stylesheet -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Heebo', sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            background: linear-gradient(135deg, #0804b0 0%, #05319c 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0804b0;
            box-shadow: 0 0 0 0.2rem rgba(8, 4, 176, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #0804b0 0%, #05319c 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(8, 4, 176, 0.3);
            color: white;
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 2px solid #e9ecef;
            border-right: none;
            background: #f8f9fa;
        }
        
        .input-group .form-control {
            border-radius: 0 10px 10px 0;
            border-left: none;
        }
        
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #0804b0;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h3 class="mb-0">
                <i class="fas fa-user-shield me-2"></i>
                Login Admin
            </h3>
            <p class="mb-0 mt-2">DST Mobil Management</p>
        </div>
        
        <div class="login-body">
            <?php echo $message; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="wa" class="form-label">Nomor WhatsApp</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fab fa-whatsapp text-success"></i>
                        </span>
                        <input type="text" class="form-control" id="wa" name="wa" 
                               placeholder="628123456789" 
                               value="<?php echo isset($_POST['wa']) ? htmlspecialchars($_POST['wa']) : ''; ?>" 
                               required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock text-secondary"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Masukkan password" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Masuk
                </button>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Sistem keamanan terlindungi
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Format WhatsApp number
        document.getElementById('wa').addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, '');
            
            if (value.startsWith('08')) {
                value = '628' + value.substring(2);
            } else if (value.startsWith('8')) {
                value = '62' + value;
            }
            
            this.value = value;
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const wa = document.getElementById('wa').value.trim();
            const password = document.getElementById('password').value;
            
            if (!/^628\d{9,12}$/.test(wa)) {
                alert('Format nomor WhatsApp tidak valid! Gunakan format 628xxxxxxxxxx');
                e.preventDefault();
                return false;
            }
            
            if (password.length < 6) {
                alert('Password minimal 6 karakter!');
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>