<?php
header("Content-Type: application/json; charset=UTF-8");

//Validasi Method
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}

//Koneksi Database
$koneksi = new mysqli('localhost', 'root', '', 'be');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}
// insert ke db
$koneksi = new mysqli('localhost', 'root', '', 'be');
$q = "SELECT * FROM products";
$dataQuery = $koneksi->query($q);
$data = mysqli_fetch_all($dataQuery, MYSQLI_ASSOC);

echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => $data
]);