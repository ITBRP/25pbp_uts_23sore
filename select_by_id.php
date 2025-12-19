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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit();
}

$id = intval($_GET['id']);

$koneksi = new mysqli('localhost', 'root', '', 'UTS_pbp');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

$q = "SELECT id, name, category, price, stock, image 
      FROM mahasiswa WHERE id = $id";

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

if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    $koneksi->close();
    exit();
}

$row = $result->fetch_assoc();
$koneksi->close();

http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id"       => intval($row['id']),
        "name"     => $row['name'],
        "category" => $row['category'],
        "price"    => intval($row['price']),
        "stock"    => intval($row['stock']),
        "image"    => $row['image']
    ]
]);