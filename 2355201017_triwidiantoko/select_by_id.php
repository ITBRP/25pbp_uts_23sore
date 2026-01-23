<?php
header("Content-Type: application/json; charset=UTF-8");

// Hanya izinkan GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Parameter ID tidak valid'
    ]);
    exit();
}

$id = intval($_GET['id']);

// Koneksi database
$koneksi = new mysqli('localhost', 'root', '', 'uts_pbp');

// Query
$q = "SELECT * FROM data_buku WHERE id = $id";
$res = $koneksi->query($q);

// Cek data ditemukan
if ($res->num_rows == 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit();
}

// Ambil data
$data = $res->fetch_assoc();

// Response sukses
echo json_encode([
    'status' => 'success',
    'msg' => 'Process success',
    'data' => $data
]);
?>
