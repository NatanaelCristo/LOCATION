<?php
require_once 'sis25.php';

header('Content-Type: application/json');

// Check if request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Validate parameters
if ($page < 1) $page = 1;
if ($limit < 1 || $limit > 100) $limit = 10;

try {
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM admin";
    $countResult = $konsis25->query($countQuery);
    $totalRecords = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    // Get admin data with pagination
    $query = "SELECT wa, nama, cabang, created_at FROM admin ORDER BY nama LIMIT ? OFFSET ?";
    $stmt = $konsis25->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'wa' => $row['wa'],
            'nama' => $row['nama'],
            'cabang' => $row['cabang'],
            'created_at' => $row['created_at']
        ];
    }
    
    $stmt->close();
    
    // Prepare response
    $response = [
        'success' => true,
        'data' => $data,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'limit' => $limit,
            'offset' => $offset
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>