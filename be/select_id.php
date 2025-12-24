<?php
header("Content-Type: application/json; charset=UTF-8");

// Check method harus GET
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Server Error!"
    ]);
    exit();
}

// Validasi parameter id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Parameter id tidak valid!"
    ]);
    exit();
}

$id = intval($_GET['id']);

// Koneksi database
$koneksi = new mysqli('localhost', 'root', '', 'buku_data');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error!"
    ]);
    exit();
}

// Query data
$q = "SELECT * FROM only WHERE id = $id LIMIT 1";
$result = $koneksi->query($q);

if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data tidak ditemukan!"
    ]);
    exit();
}

$data = $result->fetch_assoc();
$koneksi->close();

http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Data ditemukan",
    "data" => $data
]);
