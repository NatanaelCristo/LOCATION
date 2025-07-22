<?php
session_start();
require "sis25.php";

header('Content-Type: application/json');

try {
    // Query untuk mengambil data admin
    $sql = "SELECT wa, nama, cabang FROM admin ORDER BY id DESC";
    $result = $konsis25->query($sql);
    
    $data = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'wa' => htmlspecialchars($row['wa']),
                'nama' => htmlspecialchars($row['nama']),
                'cabang' => htmlspecialchars($row['cabang'])
            ];
        }
    }
    
    echo json_encode($data);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}

if (isset($konsis25)) {
    $konsis25->close();
}
?>