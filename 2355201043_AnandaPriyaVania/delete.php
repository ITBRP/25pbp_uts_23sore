<?php
header("Content-Type: application/json; charset=UTF-8");

// methode
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "ID tidak dikirim"
    ]);
    exit();
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "ID harus berupa angka"
    ]);
    exit();
}

// koneksi database
$koneksi = new mysqli("localhost", "root", "", "2355201043");

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

// cek data
$cek = $koneksi->query("SELECT * FROM db_baru WHERE id='$id'");

if ($cek->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit();
}

// hapus data
$hapus = $koneksi->query("DELETE FROM db_baru WHERE id='$id'");

if (!$hapus) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

// respon sukses
http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Delete data success",
    "data" => [
        "id" => (int)$id
    ]
]);
