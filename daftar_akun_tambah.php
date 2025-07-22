<?php
    require 'sis25.php';
    $_SESSION['menu'] = "admin";
    
    $message = "";
    $success = false;
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $wa = $_POST['wa'] ?? '';
        $nama = $_POST['nama'] ?? '';
        $password = $_POST['password'] ?? '';
        $cabang = $_POST['cabang'] ?? '';
        
        // Validate input
        if (empty($wa) || empty($nama) || empty($password) || empty($cabang)) {
            $message = '<div class="alert alert-danger">Semua field harus diisi!</div>';
        } else {
            // Hash password using bcrypt
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Check if WA already exists
            $check_stmt = $konsis25->prepare("SELECT wa FROM admin WHERE wa = ?");
            $check_stmt->bind_param("s", $wa);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $message = '<div class="alert alert-danger">Nomor WA sudah terdaftar!</div>';
            } else {
                // Insert new admin
                $insert_stmt = $konsis25->prepare("INSERT INTO admin (wa, nama, password, cabang) VALUES (?, ?, ?, ?)");
                $insert_stmt->bind_param("ssss", $wa, $nama, $hashed_password, $cabang);
                
                if ($insert_stmt->execute()) {
                    $message = '<div class="alert alert-success">Admin berhasil ditambahkan!</div>';
                    $success = true;
                    // Clear form data on success
                    $wa = $nama = $password = $cabang = '';
                } else {
                    $message = '<div class="alert alert-danger">Error: ' . $insert_stmt->error . '</div>';
                }
                $insert_stmt->close();
            }
            $check_stmt->close();
        }
    }
    
    // Get available branches from sis25.php
    $cabang_options = array_values($apiCabangs);
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
                    <font color="#05319c"><strong>Tambah Admin Baru</strong></font><br>
                </p>
            </div>
            
            <br>
            
            <div class="row g-4">
                <div class="col-sm-12 col-xl-8 mx-auto">
                    <div class="bg-white h-100 p-4" style="width: 100%; border: 2px solid #0804b0; box-shadow: 0 6px 6px rgba(131,131,131,0.6); border-radius: 10px;">
                        
                        <!-- Display Message -->
                        <?php echo $message; ?>
                        
                        <?php if ($success): ?>
                            <div class="text-center mb-3">
                                <a href="daftar_akun.php" class="btn btn-primary">Kembali ke Daftar Admin</a>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="wa" class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="wa" name="wa" 
                                       value="<?php echo htmlspecialchars($wa ?? ''); ?>" 
                                       placeholder="Contoh: 081234567890" required>
                                <div class="form-text">Masukkan nomor WhatsApp tanpa tanda + atau 0 di depan</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?php echo htmlspecialchars($nama ?? ''); ?>" 
                                       placeholder="Masukkan nama lengkap" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Masukkan password" required>
                                <div class="form-text">Password minimal 6 karakter</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                                <select class="form-select" id="cabang" name="cabang" required>
                                    <option value="">Pilih Cabang</option>
                                    <?php foreach ($cabang_options as $cabang_name): ?>
                                        <option value="<?php echo htmlspecialchars($cabang_name); ?>" 
                                                <?php echo (isset($cabang) && $cabang == $cabang_name) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cabang_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-success me-2">
                                    <i class="fa-solid fa-save me-1"></i> Simpan
                                </button>
                                <a href="daftar_akun.php" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                                </a>
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
    <!-- Content End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <?php require "script.php"?>
    
    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const wa = document.getElementById('wa').value;
            
            // Validate password length
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
            
            // Validate WA number format (should be numbers only)
            if (!/^\d+$/.test(wa)) {
                e.preventDefault();
                alert('Nomor WhatsApp hanya boleh berisi angka!');
                return false;
            }
            
            // Confirm before submit
            if (!confirm('Apakah Anda yakin ingin menambahkan admin ini?')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Auto-format WA number (remove non-digits)
        document.getElementById('wa').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
    </script>
</body>

</html>