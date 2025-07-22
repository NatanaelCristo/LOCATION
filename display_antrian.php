<?php
session_start();
$_SESSION['menu'] = 'admin';
require "sisjkt25.php";

// --- JIKA DIPANGGIL VIA AJAX: HANYA KIRIM TABEL ---
if (isset($_GET['action']) && $_GET['action'] == 'get_table') {
    header('Content-Type: application/json');
    
    $cari = isset($_GET['cari']) ? $_GET['cari'] : '';
    
    // Query dasar
    $sql = "SELECT * FROM display_servis";
    
    // Tambahkan kondisi WHERE
    if (!empty($cari)) {
        $sql .= " WHERE tanggal_servis = ?";
        $stmt = $konsisjkt25->prepare($sql);
        $stmt->bind_param("s", $cari);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql .= " WHERE tanggal_servis = CURDATE()";
        $result = $konsisjkt25->query($sql);
    }

    ob_start(); // Tangkap output HTML sementara

    function formatTanggalIndonesia($tanggal) {
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $date = new DateTime($tanggal);
        return $date->format('j') . ' ' . $bulanIndo[(int)$date->format('n')] . ' ' . $date->format('Y');
    }

    if ($result && $result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td class='px-6 py-4 text-lg'>" . $no++ . "</td>";
            echo "<td class='px-6 py-4 text-lg'>" . htmlspecialchars($row['jam_reservasi']) . "</td>";
            echo "<td class='px-6 py-4 text-lg'>" . htmlspecialchars($row['nama_pemilik']) . "</td>";
            echo "<td class='px-6 py-4 text-lg font-mono'>" . htmlspecialchars(strtoupper($row['plat_mobil'])) . "</td>";
            echo "<td class='px-6 py-4 text-lg'>" . htmlspecialchars($row['model_mobil']) . "</td>";
            echo "<td class='px-6 py-4 text-lg'>" . htmlspecialchars($row['nama_servis']) . "</td>";
            echo "<td class='px-6 py-4'>";

            if ($row['selesai'] == 0) {
                echo "<span class='inline-block px-4 py-2 text-sm font-semibold rounded-full bg-gray-600 text-gray-100'>Dalam Antrian</span>";
            } elseif ($row['selesai'] == 1) {
                echo "<span class='inline-block px-4 py-2 text-sm font-semibold rounded-full bg-yellow-500 text-black blink'>Sedang Dikerjakan</span>";
            } elseif ($row['selesai'] == 2) {
                echo "<span class='inline-block px-4 py-2 text-sm font-semibold rounded-full bg-green-600 text-white'>Selesai</span>";
            }
            echo "</td></tr>";
        }
    } else {
        echo "<tr><td colspan='7' class='text-center px-6 py-4 text-lg text-gray-400'>Tidak ada data servis hari ini.</td></tr>";
    }

    $tableBody = ob_get_clean();
    echo json_encode([
        'status' => 'success', 
        'html' => $tableBody,
        'timestamp' => date('Y-m-d H:i:s'),
        'total_rows' => $result ? $result->num_rows : 0
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Daftar Servis</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #000;
      color: #fff;
      font-size: 2rem;
      margin: 0;
      padding: 0;
      overflow: hidden;
    }

    .text-suzuki-blue {
      color: #0066cc;
    }

    .bg-suzuki-blue {
      background-color: #0066cc;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    .blink {
      animation: blink 3s infinite;
    }

    table th, table td {
      text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    }

    @media (min-width: 1920px) {
      body {
        font-size: 2.5rem;
      }
      .text-4xl {
        font-size: 4rem;
      }
      table th, table td {
        padding: 2rem 3rem;
      }
    }

    tbody tr:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    /* Indikator refresh */
    .refresh-indicator {
      position: fixed;
      top: 10px;
      right: 10px;
      background: rgba(0, 102, 204, 0.8);
      color: white;
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 0.8rem;
      z-index: 1000;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .refresh-indicator.show {
      opacity: 1;
    }
  </style>
</head>
<body class="w-full h-screen flex flex-col p-0 m-0">

<!-- Indikator Refresh -->
<div id="refresh-indicator" class="refresh-indicator">
  <i class="fas fa-sync-alt"></i> Memperbarui data...
</div>

<!-- Header -->
<div class="w-full text-center mb-5 px-6">
  <div class="mt-4 flex justify-center gap-8 text-lg sm:text-xl text-gray-200">
    <p id="date">Tanggal: --</p>
    <p id="time">Jam: -- WIB</p>
    <p id="last-update" class="text-sm opacity-75">Update terakhir: --</p>
  </div>
</div>

<!-- Tabel Daftar Servis -->
<div class="w-full overflow-x-auto flex-grow px-6">
  <table class="min-w-full table-auto bg-gray-900 rounded-lg shadow-lg border border-gray-700">
    <thead class="bg-suzuki-blue text-white">
      <tr>
        <th class="px-6 py-4 text-left text-lg font-semibold">No.</th>
        <th class="px-6 py-4 text-left text-lg font-semibold">Jam</th>
        <th class="px-6 py-4 text-left text-lg font-semibold">Nama Pemilik</th>
        <th class="px-6 py-4 text-left text-lg font-semibold">Plat Nomor</th>
        <th class="px-6 py-4 text-left text-lg font-semibold">Tipe Mobil</th>
        <th class="px-6 py-4 text-left text-lg font-semibold">Layanan Servis</th>
        <th class="px-6 py-4 text-left text-lg font-semibold">Status</th>
      </tr>
    </thead>
    <tbody id="table-body" class="divide-y divide-gray-700 text-gray-100">
      <!-- Isi akan dimuat oleh PHP saat pertama kali, atau JS nanti -->
      <?php
      // Ulangi query untuk tampilan awal
      $sql = "SELECT * FROM display_servis WHERE tanggal_servis = CURDATE()";
      $result = $konsisjkt25->query($sql);

      if ($result && $result->num_rows > 0) {
          $no = 1;
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td class='px-6 py-4 text-lg'>" . $no++ . "</td>";
              echo "<td class='px-6 py-4 text-lg'>" . htmlspecialchars($row['jam_reservasi']) . "</td>";
              echo "<td class='px-6 py-4 text-lg'>" . htmlspecialchars($row['nama_pemilik']) . "</td>";
              echo "<td class='px-6 py-4 text-lg font-mono'>" . htmlspecialchars(strtoupper($row['plat_mobil'])) . "</td>";
              echo "<td class='px-6 py-4 text-lg'>" . htmlspecialchars($row['model_mobil']) . "</td>";
              echo "<td class='px-6 py-4 text-lg'>" . htmlspecialchars($row['nama_servis']) . "</td>";
              echo "<td class='px-6 py-4'>";
              if ($row['selesai'] == 0) {
                  echo "<span class='inline-block px-4 py-2 text-sm font-semibold rounded-full bg-gray-600 text-gray-100'>Dalam Antrian</span>";
              } elseif ($row['selesai'] == 1) {
                  echo "<span class='inline-block px-4 py-2 text-sm font-semibold rounded-full bg-yellow-500 text-black blink'>Sedang Dikerjakan</span>";
              } elseif ($row['selesai'] == 2) {
                  echo "<span class='inline-block px-4 py-2 text-sm font-semibold rounded-full bg-green-600 text-white'>Selesai</span>";
              }
              echo "</td></tr>";
          }
      } else {
          echo "<tr><td colspan='7' class='text-center px-6 py-4 text-lg text-gray-400'>Tidak ada data servis hari ini.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<script>
  let refreshInterval;
  let isRefreshing = false;

  // Update Tanggal & Jam Real-Time
  function updateDateTime() {
    const dateEl = document.getElementById('date');
    const timeEl = document.getElementById('time');
    const now = new Date();

    const optionsDate = { year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = now.toLocaleDateString('id-ID', optionsDate);
    dateEl.textContent = `Tanggal: ${formattedDate}`;

    const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
    const formattedTime = now.toLocaleTimeString('id-ID', optionsTime);
    timeEl.textContent = `Jam: ${formattedTime} WIB`;
  }

  // Show/Hide refresh indicator
  function showRefreshIndicator() {
    const indicator = document.getElementById('refresh-indicator');
    indicator.classList.add('show');
  }

  function hideRefreshIndicator() {
    const indicator = document.getElementById('refresh-indicator');
    indicator.classList.remove('show');
  }

  // Update last update time
  function updateLastUpdateTime() {
    const lastUpdateEl = document.getElementById('last-update');
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', { 
      hour: '2-digit', 
      minute: '2-digit', 
      second: '2-digit' 
    });
    lastUpdateEl.textContent = `Update terakhir: ${timeString}`;
  }

  // Ambil data tabel baru tanpa reload halaman
  async function refreshTable() {
    if (isRefreshing) {
      console.log('Refresh sedang berlangsung, skip...');
      return;
    }

    isRefreshing = true;
    showRefreshIndicator();

    try {
      // Gunakan nama file yang sama dengan file ini
      const currentFileName = window.location.pathname.split('/').pop() || 'display_antrian.php';
      const today = new Date().toISOString().split('T')[0]; // Format YYYY-MM-DD
      
      const response = await fetch(`${currentFileName}?action=get_table&cari=${encodeURIComponent(today)}&t=${Date.now()}`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();

      if (result.status === 'success') {
        document.getElementById('table-body').innerHTML = result.html;
        updateLastUpdateTime();
        console.log(`Data berhasil diperbarui. Total baris: ${result.total_rows}`);
      } else {
        console.warn('Response tidak success:', result);
      }
    } catch (error) {
      console.error("Gagal ambil data terbaru:", error);
      
      // Tampilkan pesan error di tabel jika perlu
      const tableBody = document.getElementById('table-body');
      if (tableBody.children.length === 0) {
        tableBody.innerHTML = "<tr><td colspan='7' class='text-center px-6 py-4 text-lg text-red-400'>Gagal memuat data. Mencoba lagi...</td></tr>";
      }
    } finally {
      isRefreshing = false;
      hideRefreshIndicator();
    }
  }

  // Inisialisasi
  function init() {
    // Update jam setiap detik
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Set waktu update terakhir
    updateLastUpdateTime();

    // Refresh tabel pertama kali setelah 2 detik
    setTimeout(() => {
      refreshTable();
    }, 2000);

    // Refresh tiap 5 detik
    refreshInterval = setInterval(refreshTable, 5000);

    console.log('Auto refresh diaktifkan (interval: 5 detik)');
  }

  // Cleanup saat halaman ditutup
  window.addEventListener('beforeunload', () => {
    if (refreshInterval) {
      clearInterval(refreshInterval);
    }
  });

  // Handle visibility change (pause saat tab tidak aktif)
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      if (refreshInterval) {
        clearInterval(refreshInterval);
        console.log('Tab tidak aktif, auto refresh dihentikan');
      }
    } else {
      if (!refreshInterval) {
        refreshInterval = setInterval(refreshTable, 5000);
        refreshTable(); // Refresh langsung saat tab aktif kembali
        console.log('Tab aktif kembali, auto refresh dimulai');
      }
    }
  });

  // Mulai saat DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
</script>

</body>
</html>