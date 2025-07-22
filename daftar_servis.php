<?php
session_start();
$_SESSION['menu'] = 'service';
require "sisjkt25.php";

// Ambil semua data dari display_servis & simpan dalam array associative
$displayMap = [];

$sql_display = "SELECT id, no_rangka, tanggal_servis, jam_reservasi, id_pemilik, id_servis FROM display_servis";
$result_display = $konsisjkt25->query($sql_display);

while ($row = $result_display->fetch_assoc()) {
    $key = trim($row['no_rangka']) . '|' . $row['tanggal_servis'] . '|' . $row['jam_reservasi'] . '|' . $row['id_pemilik'] . '|' . $row['id_servis'];

    // Simpan semua id yang cocok dengan kombinasi key
    if (!isset($displayMap[$key])) {
        $displayMap[$key] = [];
    }

    $displayMap[$key][] = $row['id'];
}

$cari = isset($_POST['cari']) ? $_POST['cari'] : '';
$message = "";
$token = "sisjkt25"; // Sesuaikan dengan token yang digunakan

// Query dasar ambil dari riwayat_servis
$sql = "SELECT id, id_pemilik, no_rangka, tanggal_servis, jam_reservasi, odometer, catatan, total_biaya, selesai, id_servis FROM riwayat_servis";

if (!empty($cari)) {
    $sql .= " WHERE tanggal_servis = ?";
    $stmsisjkt25 = $konsisjkt25->prepare($sql);
    $stmsisjkt25->bind_param("s", $cari);
    $stmsisjkt25->execute();
    $result = $stmsisjkt25->get_result();
} else {
    $result = $konsisjkt25->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require 'head.php'; ?>

<body>
<div class="container-xxl position-relative bg-white d-flex p-0">
    <?php require 'spinner.php'; ?>
    <?php require 'navigasi.php'; ?>

    <div class="content">
        <?php require 'navbar.php'; ?>

        <div class="container-fluid pt-4 px-4">
            <div style="width: 100%; border: 2px solid #0804b0; box-shadow: 0 6px 6px rgba(131,131,131,0.6); border-radius: 10px;">
                <p align="justify" style="padding: 10px;">
                    <font color="#05319c"><strong>Daftar Servis</strong></font><br>
                    <form action="" method="post" id="formCari">
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <input type="date" name="cari" id="inputCari" class="form-control" value="<?= isset($_POST['cari']) ? htmlspecialchars($_POST['cari']) : '' ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-secondary" id="resetCari">Reset</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-info" id="refreshData"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                            </div>
                        </div>
                    </form>
                </p>
            </div>

            <br>
            <div class="row g-4">
                <div class="col-sm-12 col-xl-12">
                    <!-- Baris control atas -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <!-- Kiri: label + select -->
                        <div class="d-flex align-items-center">
                            <label for="itemsPerPage" class="me-2 mb-0">Tampilkan:</label>
                            <select id="itemsPerPage" class="form-select" style="width:auto;">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="ms-2 mb-0" id="infoData">entri</span>
                        </div>
                        
                        <!-- Kanan: info halaman -->
                        <div id="infoHalaman" class="text-muted"></div>
                    </div>
                    
                    <div class="bg-white h-100 p-4" style="width: 100%; border: 2px solid #0804b0; box-shadow: 0 6px 6px rgba(131,131,131,0.6); border-radius: 10px;">

                        <?php echo $message; ?>

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>ID Riwayat</th>
                                <th>ID Display</th>
                                <th>ID Pemilik</th>
                                <th>No. Rangka</th>
                                <th>Tanggal Servis</th>
                                <th>Jarak Tempuh (km)</th>
                                <th>Catatan</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th>Aksi</th>
                                <th>Update</th>
                            </tr>
                            </thead>
                            <tbody id="data_tabel">
                            <?php
                            function formatTanggalIndonesia($tanggal) {
                                $bulanIndo = [
                                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ];
                                $date = new DateTime($tanggal);
                                $tgl = $date->format('j');
                                $bln = (int)$date->format('n');
                                $thn = $date->format('Y');
                                return "$tgl " . $bulanIndo[$bln] . " $thn";
                            }

                            // Data akan dimuat via JavaScript, jadi kosongkan ini
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <br>
            <div id="pagination" class="d-flex justify-content-center mt-4"></div>
            <br>
        </div>

        <?php require "footer.php"; ?>
    </div>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
</div>

<?php require "script.php"; ?>

<style>
.pagination .btn {
    min-width: 40px;
}
.pagination .btn.active {
    background-color: #0804b0;
    border-color: #0804b0;
    color: white;
}
.pagination .btn:hover:not(.active):not(:disabled) {
    background-color: #e9ecef;
    border-color: #0804b0;
}
</style>

<script>
$(document).ready(function () {
    // Variabel utama
    let jumlahPerHalaman = parseInt($("#itemsPerPage").val()) || 10;
    let halamanAktif = 1;
    let semua_data = [];
    let dataDicari = [];
    let displayMap = <?php echo json_encode($displayMap); ?>;
    let cariTanggal = "<?php echo $cari; ?>";

    load_data();

    // Fungsi untuk ambil semua data dari server
    function load_data() {
        // Tampilkan loading
        $("#data_tabel").html('<tr><td colspan="12" class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Memuat data...</td></tr>');
        $("#infoHalaman").text('Memuat data...');
        
        $.ajax({
            url: 'get_daftar_servis_data.php',
            type: 'POST',
            dataType: 'json',
            data: {
                cari: cariTanggal
            },
            success: function (response) {
                if (response.error) {
                    $("#data_tabel").html('<tr><td colspan="12" class="text-center text-danger">Error: ' + response.error + '</td></tr>');
                    $("#infoHalaman").text('Error memuat data');
                    return;
                }
                
                semua_data = response;
                dataDicari = [...semua_data];
                halamanAktif = 1;
                tampilkanTabel(dataDicari, halamanAktif);
                buatPagination(dataDicari);
            },
            error: function (xhr, status, error) {
                console.error("Gagal mengambil data servis:", error);
                $("#data_tabel").html('<tr><td colspan="12" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>');
                $("#infoHalaman").text('Error memuat data');
            }
        });
    }

    // Fungsi untuk menampilkan tabel berdasarkan halaman
    function tampilkanTabel(data, halaman) {
        $("#data_tabel").empty();
        const mulai = (halaman - 1) * jumlahPerHalaman;
        const akhir = mulai + jumlahPerHalaman;
        const dataDitampilkan = data.slice(mulai, akhir);

        // Update info data dan halaman
        updateInfoData(data, mulai, akhir);

        if (dataDitampilkan.length === 0) {
            $("#data_tabel").append('<tr><td colspan="12" class="text-center">Tidak ada data servis ditemukan</td></tr>');
            $("#infoHalaman").text('Tidak ada data');
            return;
        }

        let nomor = mulai + 1;
        dataDitampilkan.forEach((item) => {
            // Format tanggal Indonesia
            const tanggalFormatted = formatTanggalIndonesia(item.tanggal_servis);
            
            // Status badge
            let statusBadge = '';
            if (item.selesai == 0) {
                statusBadge = '<span class="badge bg-secondary">Dalam Antrian</span>';
            } else if (item.selesai == 1) {
                statusBadge = '<span class="badge bg-warning text-dark">Sedang Dikerjakan</span>';
            } else if (item.selesai == 2) {
                statusBadge = '<span class="badge bg-success">Selesai</span>';
            }

            // Tombol aksi
            let aksiButton = '';
            if (item.selesai == 1) {
                aksiButton = `
                    <form action='update_selesai.php' method='post' onsubmit="return confirm('Tandai servis ini selesai?');" style="display:inline;">
                        <input type='hidden' name='id_riwayat' value='${item.id}'>
                        <input type='hidden' name='id_display' value='${item.id_display}'>
                        <button type='submit' class='btn btn-success btn-sm'><i class='bi bi-check-lg'></i></button>
                    </form>`;
            } else if (item.selesai == 2) {
                aksiButton = '<button class="btn btn-secondary btn-sm" disabled><i class="bi bi-check2-all"></i></button>';
            } else {
                aksiButton = '<span class="text-muted">-</span>';
            }

            // Tombol update
            const updateButton = `
                <form action='catat_servis' method='post' style="display:inline;">
                    <input type='hidden' name='id_riwayat' value='${item.id}'>
                    <input type='hidden' name='id_display' value='${item.id_display}'>
                    <input type='hidden' name='mode' value='prefill'>
                    <input type='hidden' name='id_pemilik' value='${item.id_pemilik}'>
                    <input type='hidden' name='no_rangka' value='${item.no_rangka}'>
                    <input type='hidden' name='tanggal_servis' value='${item.tanggal_servis}'>
                    <input type='hidden' name='selesai' value='${item.selesai}'>
                    <button type='submit' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></button>
                </form>`;

            const baris = `
                <tr>
                    <td>${nomor++}</td>
                    <td>${item.id}</td>
                    <td>${item.id_display}</td>
                    <td>${item.id_pemilik}</td>
                    <td>${item.no_rangka}</td>
                    <td>${tanggalFormatted}</td>
                    <td>${item.odometer}</td>
                    <td>${item.catatan}</td>
                    <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.total_biaya)}</td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center">${aksiButton}</td>
                    <td class="text-center">${updateButton}</td>
                </tr>`;
            $("#data_tabel").append(baris);
        });
    }

    // Fungsi untuk format tanggal Indonesia
    function formatTanggalIndonesia(tanggal) {
        const bulanIndo = [
            '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        const date = new Date(tanggal);
        const tgl = date.getDate();
        const bln = date.getMonth() + 1;
        const thn = date.getFullYear();
        
        return `${tgl} ${bulanIndo[bln]} ${thn}`;
    }

    // Fungsi untuk update info data dan halaman
    function updateInfoData(data, mulai, akhir) {
        const totalData = data.length;
        const totalHalaman = Math.ceil(totalData / jumlahPerHalaman);
        const dataAkhir = Math.min(akhir, totalData);
        
        // Update info data
        $("#infoData").text(`entri (Total: ${totalData} data)`);
        
        // Update info halaman
        if (totalData > 0) {
            $("#infoHalaman").text(`Menampilkan ${mulai + 1} - ${dataAkhir} dari ${totalData} entri (Halaman ${halamanAktif} dari ${totalHalaman})`);
        } else {
            $("#infoHalaman").text('Tidak ada data');
        }
    }

    // Fungsi untuk membuat tombol pagination
    function buatPagination(data) {
        const totalHalaman = Math.ceil(data.length / jumlahPerHalaman);
        const kontainerPagination = $("#pagination");
        kontainerPagination.empty();
        kontainerPagination.addClass('pagination');

        if (totalHalaman <= 1) return;

        // Tombol Sebelumnya
        kontainerPagination.append(
            `<button class="btn btn-outline-primary me-1" id="tombolSebelumnya" ${halamanAktif === 1 ? 'disabled' : ''}>«</button>`
        );

        // Tombol Halaman
        for (let i = 1; i <= totalHalaman; i++) {
            kontainerPagination.append(
                `<button class="btn btn-outline-primary me-1 tombol-halaman ${i === halamanAktif ? 'active' : ''}" data-halaman="${i}">${i}</button>`
            );
        }

        // Tombol Selanjutnya
        kontainerPagination.append(
            `<button class="btn btn-outline-primary" id="tombolSelanjutnya" ${halamanAktif === totalHalaman ? 'disabled' : ''}>»</button>`
        );
    }

    // Ubah jumlah data per halaman
    $('#itemsPerPage').on('change', function () {
        jumlahPerHalaman = parseInt($(this).val());
        halamanAktif = 1;
        tampilkanTabel(dataDicari, halamanAktif);
        buatPagination(dataDicari);
    });

    // Klik tombol halaman
    $(document).on("click", ".tombol-halaman", function () {
        halamanAktif = parseInt($(this).data("halaman"));
        tampilkanTabel(dataDicari, halamanAktif);
        buatPagination(dataDicari);
    });

    // Tombol sebelumnya
    $(document).on("click", "#tombolSebelumnya", function () {
        if (halamanAktif > 1) {
            halamanAktif--;
            tampilkanTabel(dataDicari, halamanAktif);
            buatPagination(dataDicari);
        }
    });

    // Tombol selanjutnya
    $(document).on("click", "#tombolSelanjutnya", function () {
        const totalHalaman = Math.ceil(dataDicari.length / jumlahPerHalaman);
        if (halamanAktif < totalHalaman) {
            halamanAktif++;
            tampilkanTabel(dataDicari, halamanAktif);
            buatPagination(dataDicari);
        }
    });

    // Handle form pencarian
    $('#formCari').on('submit', function(e) {
        e.preventDefault();
        cariTanggal = $('#inputCari').val();
        load_data();
    });

    // Handle tombol reset
    $('#resetCari').on('click', function() {
        $('#inputCari').val('');
        cariTanggal = '';
        load_data();
    });

    // Handle tombol refresh
    $('#refreshData').on('click', function() {
        load_data();
    });
});
</script>
</body>
</html>