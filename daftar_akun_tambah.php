<?php
    require 'sis25.php';
    $_SESSION['menu'] = "admin";
    
    $message = "";
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $wa = $_POST['wa'];
        $nama = $_POST['nama'];
        $password = $_POST['password'];
        $cabang = $_POST['cabang'];
        
        // Validate input
        if (empty($wa) || empty($nama) || empty($password) || empty($cabang)) {
            $message = '<div class="alert alert-danger">Semua field harus diisi!</div>';
        } else {
            // Hash the password using bcrypt
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert into database
            $stmt = $konsis25->prepare("INSERT INTO admin (wa, nama, password, cabang) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $wa, $nama, $hashed_password, $cabang);
            
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Akun admin berhasil ditambahkan!</div>';
                // Clear form data
                $wa = $nama = $password = $cabang = "";
            } else {
                $message = '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        }
    }
    
    // Get available branches
    $availableBranches = getAvailableBranches();
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
            
            <!-- Display Message -->
            <?php echo $message; ?>
            
            <div class="row g-4">
                <div class="col-sm-12 col-xl-8 mx-auto">
                    <div class="bg-white h-100 p-4" style="width: 100%; border: 2px solid #0804b0; box-shadow: 0 6px 6px rgba(131,131,131,0.6); border-radius: 10px;">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="wa" class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="wa" name="wa" placeholder="Contoh: 628123456789" value="<?php echo isset($wa) ? htmlspecialchars($wa) : ''; ?>" required>
                                <div class="form-text">Nomor WhatsApp akan digunakan sebagai primary key</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" value="<?php echo isset($nama) ? htmlspecialchars($nama) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" value="<?php echo isset($password) ? htmlspecialchars($password) : ''; ?>" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Password akan di-encrypt menggunakan bcrypt</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                                <select class="form-select" id="cabang" name="cabang" required>
                                    <option value="">Pilih Cabang</option>
                                    <?php foreach ($availableBranches as $branch): ?>
                                        <option value="<?php echo htmlspecialchars($branch); ?>" 
                                                <?php echo (isset($cabang) && $cabang === $branch) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($branch); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="daftar_akun.php" class="btn btn-secondary me-md-2">Kembali</a>
                                <button type="submit" class="btn btn-primary">Tambah Akun</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <br>
            
            <!-- Existing Accounts Table -->
            <div class="row g-4">
                <div class="col-sm-12 col-xl-12">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5>Daftar Akun Admin</h5>
                        <div class="d-flex align-items-center">
                            <label for="itemsPerPage" class="me-2 mb-0">Tampilkan:</label>
                            <select id="itemsPerPage" class="form-select" style="width:auto;">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="bg-white h-100 p-4" style="width: 100%; border: 2px solid #0804b0; box-shadow: 0 6px 6px rgba(131,131,131,0.6); border-radius: 10px;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>WA</th>
                                    <th>Nama</th>
                                    <th>Cabang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="data_tabel">
                                <?php
                                // Fetch existing admin accounts
                                $result = $konsis25->query("SELECT wa, nama, cabang FROM admin ORDER BY nama");
                                if ($result && $result->num_rows > 0) {
                                    $no = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $no++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['wa']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['cabang']) . "</td>";
                                        echo "<td>";
                                        echo '<button class="btn btn-sm btn-warning me-1" onclick="editAdmin(\'' . htmlspecialchars($row['wa']) . '\')">Edit</button>';
                                        echo '<button class="btn btn-sm btn-danger" onclick="deleteAdmin(\'' . htmlspecialchars($row['wa']) . '\')">Hapus</button>';
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>Belum ada data admin</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <br><br>
        <div id="pagination" class="d-flex justify-content-center mt-4"></div>
        <br>
        
        <!-- Footer Start -->
        <?php require "footer.php"?>
        <!-- Footer End -->
    </div>
    <!-- Content End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <?php require "script.php"?>
    
    <!-- Custom JavaScript for password toggle -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
        
        function editAdmin(wa) {
            // Implement edit functionality
            alert('Edit admin dengan WA: ' + wa);
        }
        
        function deleteAdmin(wa) {
            if (confirm('Apakah Anda yakin ingin menghapus admin dengan WA: ' + wa + '?')) {
                // Implement delete functionality via AJAX
                alert('Delete admin dengan WA: ' + wa);
            }
        }
    </script>
</body>

</html>