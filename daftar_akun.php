<?php
    require 'sis25.php';
    $_SESSION['menu'] = "admin";
    $token = TOKEN;
    
    // Inisialisasi variabel message
    $message = '';
    
    // Koneksi database
    try {
        // Asumsikan koneksi database sudah ada di sis25.php
        // Jika tidak, uncomment baris berikut dan sesuaikan dengan konfigurasi database Anda
        /*
        $host = 'localhost';
        $dbname = 'dstmobil_db';
        $username = 'your_username';
        $password = 'your_password';
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        */
        
        // Query untuk mengambil data admin
        $query = "SELECT wa, nama, cabang FROM admin ORDER BY nama";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        $admins = [];
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
                        <font color="#05319c"><strong>Daftar Akun</strong></font><br>
                        <form action="" method="get">
                            <div style="text-align: center">
                                <div class="row">
                                    <div class="col">
                                        <div class="row justify-content-center">
                                         
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </p>
                </div>
                
              
                
                <br>
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <!--Tambahkan Tombol Tambah berwanra hijau-->
                           <div class="text-sm-end text-center">
                                <a href="daftar_akun_tambah" class="btn btn-success">
                                    <i class="fa-solid fa-plus me-1" style="color:white;"></i> Tambah
                                </a>
                            </div>
                            
                        <!-- Baris control atas -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <!-- Kiri: label + select -->
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

                            <!-- Display Message -->
                            <?php echo $message; ?>
                    
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
                                   if (!empty($admins)) {
                                       $no = 1;
                                       foreach ($admins as $admin) {
                                           echo "<tr>";
                                           echo "<td>" . $no . "</td>";
                                           echo "<td>" . htmlspecialchars($admin['wa']) . "</td>";
                                           echo "<td>" . htmlspecialchars($admin['nama']) . "</td>";
                                           echo "<td>" . htmlspecialchars($admin['cabang']) . "</td>";
                                           echo "<td>";
                                           echo "<a href='daftar_akun_edit.php?wa=" . urlencode($admin['wa']) . "' class='btn btn-warning btn-sm me-1'>";
                                           echo "<i class='fa-solid fa-edit'></i> Edit";
                                           echo "</a>";
                                           echo "<a href='daftar_akun_hapus.php?wa=" . urlencode($admin['wa']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus akun ini?\")'>";
                                           echo "<i class='fa-solid fa-trash'></i> Hapus";
                                           echo "</a>";
                                           echo "</td>";
                                           echo "</tr>";
                                           $no++;
                                       }
                                   } else {
                                       echo "<tr><td colspan='5' class='text-center'>Tidak ada data admin</td></tr>";
                                   }
                                   ?>
                                </tbody>
                            </table>
                       
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <div id="pagination" class="d-flex justify-content-center mt-4"></div>
            <br>
            <!-- Footer Start -->
            <?php require "footer.php"?>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <?php require "script.php"?>
    
    <script>
        // JavaScript untuk pagination (jika diperlukan)
        document.addEventListener('DOMContentLoaded', function() {
            const itemsPerPageSelect = document.getElementById('itemsPerPage');
            const tableRows = document.querySelectorAll('#data_tabel tr');
            
            itemsPerPageSelect.addEventListener('change', function() {
                const itemsPerPage = parseInt(this.value);
                // Implementasi pagination logic jika diperlukan
                console.log('Items per page:', itemsPerPage);
            });
        });
    </script>
</body>

</html>