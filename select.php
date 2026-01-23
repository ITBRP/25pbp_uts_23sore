<?php
header("Content-Type: application/json; charset=UTF-8");

// Validasi method
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error!'
    ]);
    exit();
}

// Koneksi database
$koneksi = new mysqli('localhost', 'root', '', 'mahasiswa');

// Query data
$q = "SELECT * FROM products ORDER BY id ASC";
$result = $koneksi->query($q);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

http_response_code(200);

echo json_encode([
    'status' => 'success',
    'msg' => 'Process success',
    'data' => $data
]);
