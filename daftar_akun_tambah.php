<?php
    require 'sis25.php';
    $_SESSION['menu'] = "admin";
    $token = TOKEN;
    
    $message = '';
    
    // Proses form submission
    if ($_POST) {
        $wa = trim($_POST['wa']);
        $nama = trim($_POST['nama']);
        $password = $_POST['password'];
        $cabang = trim($_POST['cabang']);
        
        // Validasi input
        if (empty($wa) || empty($nama) || empty($password) || empty($cabang)) {
            $message = '<div class="alert alert-danger">Semua field harus diisi!</div>';
        } else {
            try {
                // Cek apakah WA sudah ada
                $checkQuery = "SELECT wa FROM admin WHERE wa = ?";
                $checkStmt = $konsis25->prepare($checkQuery);
                $checkStmt->bind_param("s", $wa);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                
                if ($checkResult->num_rows > 0) {
                    $message = '<div class="alert alert-danger">Nomor WA sudah terdaftar!</div>';
                } else {
                    // Hash password menggunakan bcrypt
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    
                    // Insert data ke database
                    $insertQuery = "INSERT INTO admin (wa, nama, password, cabang) VALUES (?, ?, ?, ?)";
                    $insertStmt = $konsis25->prepare($insertQuery);
                    $insertStmt->bind_param("ssss", $wa, $nama, $hashedPassword, $cabang);
                    
                    if ($insertStmt->execute()) {
                        $message = '<div class="alert alert-success">Akun admin berhasil ditambahkan!</div>';
                        
                        // Redirect setelah 2 detik
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = 'daftar_akun.php';
                            }, 2000);
                        </script>";
                    } else {
                        $message = '<div class="alert alert-danger">Error: ' . $konsis25->error . '</div>';
                    }
                }
                
                $checkStmt->close();
                if (isset($insertStmt)) {
                    $insertStmt->close();
                }
                
            } catch(Exception $e) {
                $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    
<?php require 'head.php'; ?>
<?php require 'head_log.php'; ?>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <?php require 'spinner.php'; ?>
        <!-- Spinner End -->

        <?php
            require 'navigasi.php';
            require 'menu_top.php';
        ?>

        <!-- Form Start -->
        <div class="container-fluid pt-4 px-4">
            <div style="width: 100%; border: 2px solid #0804b0; box-shadow: 0 6px 6px rgba(131,131,131,0.6); border-radius: 10px;">
                <p align="justify" style="padding: 10px;">
                    <font color="#05319c"><strong>Tambah Akun Admin</strong></font><br>
                </p>
            </div>
            
            <br>
            
            <div class="row g-4">
                <div class="col-sm-12 col-xl-8 mx-auto">
                    <div class="bg-white h-100 p-4" style="width: 100%; border: 2px solid #0804b0; box-shadow: 0 6px 6px rgba(131,131,131,0.6); border-radius: 10px;">
                        
                        <!-- Display Message -->
                        <?php echo $message; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="wa" class="form-label">Nomor WhatsApp *</label>
                                <input type="text" class="form-control" id="wa" name="wa" 
                                       placeholder="Contoh: 628123456789" 
                                       value="<?php echo isset($_POST['wa']) ? htmlspecialchars($_POST['wa']) : ''; ?>" 
                                       required>
                                <small class="form-text text-muted">Format: 628xxxxxxxxxx (tanpa tanda +)</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       placeholder="Masukkan nama lengkap"
                                       value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Masukkan password" required>
                                <small class="form-text text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cabang" class="form-label">Cabang *</label>
                                <select class="form-select" id="cabang" name="cabang" required>
                                    <option value="">Pilih Cabang</option>
                                    <option value="Head Office" <?php echo (isset($_POST['cabang']) && $_POST['cabang'] == 'Head Office') ? 'selected' : ''; ?>>Head Office</option>
                                    <option value="Jakarta" <?php echo (isset($_POST['cabang']) && $_POST['cabang'] == 'Jakarta') ? 'selected' : ''; ?>>Jakarta</option>
                                    <option value="Tangerang" <?php echo (isset($_POST['cabang']) && $_POST['cabang'] == 'Tangerang') ? 'selected' : ''; ?>>Tangerang</option>
                                </select>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="daftar_akun.php" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <br><br>
        
        <!-- Footer Start -->
        <?php require "footer.php"?>
        <!-- Footer End -->
    </div>

    <?php require "script.php"?>
    
    <script>
        // Validasi form
        document.querySelector('form').addEventListener('submit', function(e) {
            const wa = document.getElementById('wa').value.trim();
            const password = document.getElementById('password').value;
            
            // Validasi nomor WA
            if (!/^628\d{9,12}$/.test(wa)) {
                alert('Format nomor WhatsApp tidak valid! Gunakan format 628xxxxxxxxxx');
                e.preventDefault();
                return false;
            }
            
            // Validasi password
            if (password.length < 6) {
                alert('Password minimal 6 karakter!');
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>

</html>