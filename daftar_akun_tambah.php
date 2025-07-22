<?php
require "sis25.php";
$_SESSION['menu'] = "admin";
$token = TOKEN;

// Get available branches from sis25.php
$cabang_options = array_values($apiCabangs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require 'head.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="container-fluid bg-white min-vh-100 d-flex flex-column">
    <?php require 'spinner.php'; ?>
    <?php require 'navigasi.php'; ?>
    <?php require 'menu_top.php'; ?>

    <div class="container py-4">
        <div class="card shadow border-primary">
            <div class="card-header bg-primary text-white fw-bold">
                Form Pendaftaran Akun Admin Cabang
            </div>
            <div class="card-body">
                <form id="formReservasi">

                    <div class="mb-3">
                        <label for="id_pemilik" class="form-label">WA</label>
                        <input type="number" class="form-control" name="id_pemilik" id="id_pemilik" required>
                    </div>

                    <div class="mb-3">
                        <label for="nama_pemilik" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama_pemilik" id="nama_pemilik" required>
                    </div>

                    <div class="mb-3">
                        <label for="no_rangka" class="form-label">Password</label>
                        <input type="password" class="form-control" name="no_rangka" id="no_rangka" required>
                    </div>

                    <div class="mb-3">
                        <label for="model_mobil" class="form-label">Cabang</label>
                        <select class="form-control" name="model_mobil" id="model_mobil" required>
                            <option value="">Pilih Cabang</option>
                            <?php foreach ($cabang_options as $cabang_name): ?>
                                <option value="<?php echo htmlspecialchars($cabang_name); ?>">
                                    <?php echo htmlspecialchars($cabang_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="hasilSubmit" class="my-2 text-center fw-bold"></div>

                    <button type="submit" class="btn btn-primary w-100">Simpan Akun</button>
                    <a href="daftar_akun" class="btn btn-secondary w-100 mt-2">Kembali</a>
                </form>
            </div>
        </div>
    </div>

    <?php require 'footer.php'; ?>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
</div>

<?php require 'script.php'; ?>

<script>
document.getElementById('formReservasi').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const wa = document.getElementById('id_pemilik').value;
    const nama = document.getElementById('nama_pemilik').value;
    const password = document.getElementById('no_rangka').value;
    const cabang = document.getElementById('model_mobil').value;
    const hasilDiv = document.getElementById('hasilSubmit');
    
    // Validate input
    if (!wa || !nama || !password || !cabang) {
        hasilDiv.innerHTML = '<div class="text-danger">Semua field harus diisi!</div>';
        return;
    }
    
    if (password.length < 6) {
        hasilDiv.innerHTML = '<div class="text-danger">Password minimal 6 karakter!</div>';
        return;
    }
    
    // Show loading
    hasilDiv.innerHTML = '<div class="text-info">Menyimpan data...</div>';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('wa', wa);
    formData.append('nama', nama);
    formData.append('password', password);
    formData.append('cabang', cabang);
    formData.append('action', 'insert_admin');
    
    // Submit via AJAX
    fetch('process_admin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hasilDiv.innerHTML = '<div class="text-success">' + data.message + '</div>';
            // Clear form
            document.getElementById('formReservasi').reset();
            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = 'daftar_akun';
            }, 2000);
        } else {
            hasilDiv.innerHTML = '<div class="text-danger">' + data.message + '</div>';
        }
    })
    .catch(error => {
        hasilDiv.innerHTML = '<div class="text-danger">Error: ' + error.message + '</div>';
    });
});
</script>

</body>
</html>