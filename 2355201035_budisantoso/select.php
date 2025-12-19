<?php
// ini code untuk proses request yang formatnya GET
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method salah !'
    ]);
    exit();
}

// select ke db
$koneksi = new mysqli('localhost', 'root', '', 'uts_be');
$q = "SELECT * FROM data_barang";
$dataQuery = $koneksi->query($q);
$data = mysqli_fetch_all($dataQuery, MYSQLI_ASSOC);



echo json_encode([
    'status' => 'success',
    'msg' => 'Process success',
    'data' => $data
]);
