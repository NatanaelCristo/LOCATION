<?php
    require 'sis25.php';
    $_SESSION['menu'] = "admin";
    $token = TOKEN;
    
    $message = "";
    
    // Handle delete action
    if (isset($_GET['delete'])) {
        $wa_to_delete = $_GET['delete'];
        $delete_stmt = $konsis25->prepare("DELETE FROM admin WHERE wa = ?");
        $delete_stmt->bind_param("s", $wa_to_delete);
        
        if ($delete_stmt->execute()) {
            $message = '<div class="alert alert-success">Admin berhasil dihapus!</div>';
        } else {
            $message = '<div class="alert alert-danger">Error: ' . $delete_stmt->error . '</div>';
        }
        $delete_stmt->close();
    }
    
    // Fetch all admin data
    $query = "SELECT wa, nama, cabang, created_at FROM admin ORDER BY created_at DESC";
    $result = $konsis25->query($query);
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
                    <font color="#05319c"><strong>Daftar Admin</strong></font><br>
                </p>
            </div>
            
            <br>
            
            <div class="row g-4">
                <div class="col-sm-12 col-xl-12">
                                            <!--Tambahkan Tombol Tambah berwarna hijau-->
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
                                        
                                    </tr>
                                </thead>
                                <tbody id="data_tabel">
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php $no = 1; ?>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($row['wa']); ?></td>
                                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                                <td>
                                                    <span class="badge bg-primary"><?php echo htmlspecialchars($row['cabang']); ?></span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada data admin</td>
                                        </tr>
                                    <?php endif; ?>
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
</body>

</html>