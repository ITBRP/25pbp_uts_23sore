<?php
// ini code untuk proses request GET
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error !'
    ]);
    exit();
}

//KONEKSI DATABASE
$koneksi = new mysqli('localhost', 'root', '', '2355201043');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

//AMBIL SEMUA DATA
$q = "SELECT id, name, category, price, stock, image FROM db_baru ORDER BY id ASC";
$result = $koneksi->query($q);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => (int) $row['id'],
        'name' => $row['name'],
        'category' => $row['category'],
        'price' => (int) $row['price'],
        'stock' => (int) $row['stock'],
        'image' => $row['image']
    ];
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'msg' => 'Process success',
    'data' => $data
], JSON_PRETTY_PRINT);
