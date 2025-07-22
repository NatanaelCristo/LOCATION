<?php
    require 'sis25.php';
    $_SESSION['menu'] = "admin";
    
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
            
            <!-- Alert Messages -->
            <div id="alertMessage" style="display: none;"></div>
            
            <div class="row g-4">
                <div class="col-sm-12 col-xl-8 mx-auto">
                    <div class="bg-white h-100 p-4" style="width: 100%; border: 2px solid #0804b0; box-shadow: 0 6px 6px rgba(131,131,131,0.6); border-radius: 10px;">
                        <form id="adminForm">
                            <div class="mb-3">
                                <label for="wa" class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="wa" name="wa" placeholder="Contoh: 628123456789" required>
                                <div class="form-text">Nomor WhatsApp akan digunakan sebagai primary key</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
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
                                        <option value="<?php echo htmlspecialchars($branch); ?>">
                                            <?php echo htmlspecialchars($branch); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="daftar_akun.php" class="btn btn-secondary me-md-2">Kembali</a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm me-2" id="submitSpinner" style="display: none;"></span>
                                    Tambah Akun
                                </button>
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
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <button class="btn btn-info btn-sm ms-2" onclick="refreshTable()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
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
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" id="editWa" name="wa">
                        
                        <div class="mb-3">
                            <label for="editNama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editNama" name="nama" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="editPassword" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditPassword">
                                    <i class="fas fa-eye" id="editEyeIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editCabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                            <select class="form-select" id="editCabang" name="cabang" required>
                                <option value="">Pilih Cabang</option>
                                <?php foreach ($availableBranches as $branch): ?>
                                    <option value="<?php echo htmlspecialchars($branch); ?>">
                                        <?php echo htmlspecialchars($branch); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                            <span class="spinner-border spinner-border-sm me-2" id="editSpinner" style="display: none;"></span>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <?php require "script.php"?>
    
    <!-- Custom JavaScript for AJAX functionality -->
    <script>
        let currentPage = 1;
        let itemsPerPage = 10;
        
        // Initialize
        $(document).ready(function() {
            loadAdminTable();
            
            // Items per page change
            $('#itemsPerPage').change(function() {
                itemsPerPage = parseInt($(this).val());
                currentPage = 1;
                loadAdminTable();
            });
        });
        
        // Password toggle for main form
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
        
        // Password toggle for edit form
        document.getElementById('toggleEditPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('editPassword');
            const eyeIcon = document.getElementById('editEyeIcon');
            
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
        
        // Handle main form submission
        $('#adminForm').submit(function(e) {
            e.preventDefault();
            
            const submitBtn = $('#submitBtn');
            const spinner = $('#submitSpinner');
            
            // Show loading state
            submitBtn.prop('disabled', true);
            spinner.show();
            
            const formData = new FormData();
            formData.append('action', 'add_admin');
            formData.append('wa', $('#wa').val());
            formData.append('nama', $('#nama').val());
            formData.append('password', $('#password').val());
            formData.append('cabang', $('#cabang').val());
            
            $.ajax({
                url: 'api/ho/admin_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $('#adminForm')[0].reset();
                        loadAdminTable();
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('danger', 'Terjadi kesalahan: ' + error);
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    spinner.hide();
                }
            });
        });
        
        // Handle edit form submission
        $('#editForm').submit(function(e) {
            e.preventDefault();
            
            const submitBtn = $('#editSubmitBtn');
            const spinner = $('#editSpinner');
            
            // Show loading state
            submitBtn.prop('disabled', true);
            spinner.show();
            
            const formData = new FormData();
            formData.append('action', 'edit_admin');
            formData.append('wa', $('#editWa').val());
            formData.append('nama', $('#editNama').val());
            formData.append('password', $('#editPassword').val());
            formData.append('cabang', $('#editCabang').val());
            
            $.ajax({
                url: 'api/ho/admin_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $('#editModal').modal('hide');
                        loadAdminTable();
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('danger', 'Terjadi kesalahan: ' + error);
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    spinner.hide();
                }
            });
        });
        
        // Load admin table
        function loadAdminTable() {
            $('#data_tabel').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
            
            $.ajax({
                url: 'get_admin_list.php',
                type: 'GET',
                data: {
                    page: currentPage,
                    limit: itemsPerPage
                },
                success: function(response) {
                    if (response.success) {
                        displayAdminTable(response.data);
                        updatePagination(response.pagination);
                    } else {
                        $('#data_tabel').html('<tr><td colspan="5" class="text-center text-danger">' + response.message + '</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#data_tabel').html('<tr><td colspan="5" class="text-center text-danger">Error loading data: ' + error + '</td></tr>');
                }
            });
        }
        
        // Display admin table
        function displayAdminTable(data) {
            let html = '';
            if (data.length > 0) {
                data.forEach(function(admin, index) {
                    const no = ((currentPage - 1) * itemsPerPage) + index + 1;
                    html += `
                        <tr>
                            <td>${no}</td>
                            <td>${admin.wa}</td>
                            <td>${admin.nama}</td>
                            <td>${admin.cabang}</td>
                            <td>
                                <button class="btn btn-sm btn-warning me-1" onclick="editAdmin('${admin.wa}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteAdmin('${admin.wa}')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center">Belum ada data admin</td></tr>';
            }
            $('#data_tabel').html(html);
        }
        
        // Update pagination
        function updatePagination(pagination) {
            let html = '';
            if (pagination.total_pages > 1) {
                // Previous button
                if (pagination.current_page > 1) {
                    html += `<button class="btn btn-outline-primary me-1" onclick="changePage(${pagination.current_page - 1})">Previous</button>`;
                }
                
                // Page numbers
                for (let i = 1; i <= pagination.total_pages; i++) {
                    if (i === pagination.current_page) {
                        html += `<button class="btn btn-primary me-1" onclick="changePage(${i})">${i}</button>`;
                    } else {
                        html += `<button class="btn btn-outline-primary me-1" onclick="changePage(${i})">${i}</button>`;
                    }
                }
                
                // Next button
                if (pagination.current_page < pagination.total_pages) {
                    html += `<button class="btn btn-outline-primary me-1" onclick="changePage(${pagination.current_page + 1})">Next</button>`;
                }
            }
            $('#pagination').html(html);
        }
        
        // Change page
        function changePage(page) {
            currentPage = page;
            loadAdminTable();
        }
        
        // Refresh table
        function refreshTable() {
            loadAdminTable();
        }
        
        // Edit admin
        function editAdmin(wa) {
            const formData = new FormData();
            formData.append('action', 'get_admin');
            formData.append('wa', wa);
            
            $.ajax({
                url: 'api/ho/admin_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        const admin = response.data;
                        $('#editWa').val(admin.wa);
                        $('#editNama').val(admin.nama);
                        $('#editPassword').val('');
                        $('#editCabang').val(admin.cabang);
                        $('#editModal').modal('show');
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('danger', 'Terjadi kesalahan: ' + error);
                }
            });
        }
        
        // Delete admin
        function deleteAdmin(wa) {
            if (confirm('Apakah Anda yakin ingin menghapus admin dengan WA: ' + wa + '?')) {
                const formData = new FormData();
                formData.append('action', 'delete_admin');
                formData.append('wa', wa);
                
                $.ajax({
                    url: 'api/ho/admin_handler.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            loadAdminTable();
                        } else {
                            showAlert('danger', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('danger', 'Terjadi kesalahan: ' + error);
                    }
                });
            }
        }
        
        // Show alert message
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#alertMessage').html(alertHtml).show();
            
            // Auto hide after 5 seconds
            setTimeout(function() {
                $('#alertMessage .alert').alert('close');
            }, 5000);
        }
    </script>
</body>

</html>