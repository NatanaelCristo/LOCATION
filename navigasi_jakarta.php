<style>
/* Hover dan aktif untuk link sidebar */
.sidebar .navbar-nav .nav-link:hover,
.sidebar .navbar-nav .nav-link.active {
    color: #05319c !important;
    border-color: #05319c;
    background: #ffffff;
}

/* Hover dan aktif untuk ikon di sidebar */
.sidebar .navbar-nav .nav-link:hover i,
.sidebar .navbar-nav .nav-link.active i {
    color: #05319c !important;
}

/* Hover untuk item dalam dropdown */
.sidebar .dropdown-menu .dropdown-item:hover {
    color: #05319c;
    background-color: #f0f2ff;
}

/* Warna panah dropdown toggle saat aktif atau hover */
.sidebar .navbar .dropdown-toggle::after {
    color: #05319c;
    transition: 0.3s ease;
}

/* Saat dropdown terbuka (aria-expanded=true) */
.sidebar .navbar .dropdown-toggle[aria-expanded="true"]::after {
    transform: rotate(-180deg);
    color: #05319c;
}

/* Transisi halus untuk semua elemen interaktif */
.sidebar .navbar-nav .nav-link,
.sidebar .navbar-nav .nav-link i,
.sidebar .dropdown-menu .dropdown-item,
.sidebar .navbar .dropdown-toggle::after {
    transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease, transform 0.3s ease;
}
</style>

<!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class=""></i>DST MOBIL</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                 
                   
                </div>
                <div class="navbar-nav w-100">
                    <a href="beranda" class="nav-link dropdown-toggle <?php echo (isset($_SESSION['menu']) && $_SESSION['menu'] == 'beranda') ? 'active' : ''; ?>"><i class="fa fa-tachometer-alt me-2"></i>Beranda</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle <?php echo (isset($_SESSION['menu']) && $_SESSION['menu'] == 'admin') ? 'active' : ''; ?>" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i>Admin
                        </a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="update_teknisi" class="dropdown-item">Update Jumlah Kapasitas</a>
                      
                            <!-- Link Deteksi Wajah disembunyikan -->
                            <!-- <a href="https://dstmobil.com/FACE/index?cabang=jakarta" target="_blank" class="dropdown-item">Deteksi Wajah</a> -->
                            <a href="antrian_1" class="dropdown-item">Antrian</a>
                        </div>
                    </div>
                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle <?php echo (isset($_SESSION['menu']) && $_SESSION['menu'] == 'service') ? 'active' : ''; ?>" data-bs-toggle="dropdown">
                            <i class="fas fa-tools me-2"></i>Service
                        </a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="daftar_servis" class="dropdown-item">Laporan Servis</a>
                            <!--<a href="catat_servis" class="dropdown-item">Pendaftaran Servis</a>-->
                            
                        </div>
                    </div>
                    
                   
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->