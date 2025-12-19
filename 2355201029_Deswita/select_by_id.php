<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'ID tidak dikirim'
    ]);
    exit;
}

$id = intval($_GET['id']);

$koneksi = new mysqli("localhost", "root", "", "pbp_remedial_5sore");

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

$q = "SELECT * FROM data_buku WHERE id = $id LIMIT 1";
$result = $koneksi->query($q);

if ($result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit;
}

$row = $result->fetch_assoc();

echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => (int)$row['id'],
        "name" => $row['name'],
        "category" => $row['category'],
        "price" =>(int)$row['price'],
        "stock" => (int)$row['stock'],
        "image" => $row['image'],
    ]
]);
?>