<?php 
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}

$koneksi = new mysqli('localhost', 'root', '', 'UTS_pbp');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

$q = "SELECT id, name, category, price, stock, image FROM mahasiswa ORDER BY id DESC";
$result = $koneksi->query($q);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    $koneksi->close();
    exit();
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id"       => intval($row['id']),
        "name"     => $row['name'],
        "category" => $row['category'],
        "price"    => intval($row['price']),
        "stock"    => intval($row['stock']),
        "image"    => $row['image']
    ];
}

$koneksi->close();

http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg"    => "Process success",
    "data"   => $data
]);