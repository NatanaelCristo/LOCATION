<!-- Tambahkan kontrol pagination di atas tabel -->
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

<!-- Tabel dengan tbody yang dimodifikasi -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>No.</th>
            <th>WhatsApp</th>
            <th>Nama</th>
            <th>Cabang</th>
        </tr>
    </thead>
    <tbody id="data_tabel">
        <!-- Data akan dimuat via JavaScript -->
    </tbody>
</table>

<!-- Kontrol pagination di bawah tabel -->
<div id="pagination" class="d-flex justify-content-center mt-4"></div>

<!-- CSS untuk styling pagination -->
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

<!-- JavaScript untuk pagination -->
<script>
$(document).ready(function () {
    // Variabel utama
    let jumlahPerHalaman = parseInt($("#itemsPerPage").val()) || 10;
    let halamanAktif = 1;
    let semua_data = [];
    let dataDicari = [];

    load_data();

    // Fungsi untuk ambil semua data dari server
    function load_data() {
        // Tampilkan loading
        $("#data_tabel").html('<tr><td colspan="4" class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Memuat data...</td></tr>');
        $("#infoHalaman").text('Memuat data...');
        
        $.ajax({
            url: 'get_admin_data.php',
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    $("#data_tabel").html('<tr><td colspan="4" class="text-center text-danger">Error: ' + response.error + '</td></tr>');
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
                console.error("Gagal mengambil data admin:", error);
                $("#data_tabel").html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>');
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
            $("#data_tabel").append('<tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>');
            $("#infoHalaman").text('Tidak ada data');
            return;
        }

        let nomor = mulai + 1;
        dataDitampilkan.forEach((item) => {
            const baris = `
                <tr>
                    <td>${nomor++}.</td>
                    <td>${item.wa}</td>
                    <td>${item.nama}</td>
                    <td>${item.cabang}</td>
                </tr>`;
            $("#data_tabel").append(baris);
        });
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
});
</script>