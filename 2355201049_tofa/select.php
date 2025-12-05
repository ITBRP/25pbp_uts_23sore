<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

$koneksi = new mysqli('localhost', 'root', '', 'uts_pbp_sore');

$q = "SELECT * FROM mahasiswa";
$res = $koneksi->query($q);

$data = [];

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'status' => 'success',
    'msg' => 'Data ditemukan',
    'data' => $data
]);
