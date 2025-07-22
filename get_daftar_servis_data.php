<?php
session_start();
require "sisjkt25.php";

header('Content-Type: application/json');

try {
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
    
    // Query dasar ambil dari riwayat_servis
    $sql = "SELECT id, id_pemilik, no_rangka, tanggal_servis, jam_reservasi, odometer, catatan, total_biaya, selesai, id_servis FROM riwayat_servis";
    
    if (!empty($cari)) {
        $sql .= " WHERE tanggal_servis = ?";
        $stmt = $konsisjkt25->prepare($sql);
        $stmt->bind_param("s", $cari);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $konsisjkt25->query($sql);
    }
    
    $data = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Cari ID Display yang sesuai
            $key = trim($row['no_rangka']) . '|' . $row['tanggal_servis'] . '|' . $row['jam_reservasi'] . '|' . $row['id_pemilik'] . '|' . $row['id_servis'];
            $id_display = isset($displayMap[$key]) ? array_shift($displayMap[$key]) : 0;
            
            // Tambahkan id_display ke data
            $row['id_display'] = $id_display;
            
            $data[] = $row;
        }
    }
    
    echo json_encode($data);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}

$konsisjkt25->close();
?>