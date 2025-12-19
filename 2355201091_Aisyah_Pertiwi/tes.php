<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method salah'
    ]);
    exit();
}

if (!isset($_GET['id']) || $_GET['id'] == '') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'id tidak boleh kosong'
    ]);
    exit();
}

$koneksi = new mysqli('localhost', 'root', '', 'uts_pbb');

$id = $_GET['id'];
$q = "SELECT * FROM buku WHERE id=$id";
$dataQuery = $koneksi->query($q);

if (!$dataQuery) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Query gagal'
    ]);
    exit();
}

$data = mysqli_fetch_assoc($dataQuery);

if (!$data) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data tidak ditemukan'
    ]);
    exit();
}

echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => $data
]);
