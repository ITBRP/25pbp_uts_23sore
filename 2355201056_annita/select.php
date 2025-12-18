<?php 

header("Content-Type: application/json; charset=UTF-8");
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(405);
    $res = [
        'status' => 'Error',
        'msg' => 'Method salah!'
    ];
    echo json_encode($res);
    exit();
}

$koneksi = new mysqli('localhost', 'root', '', 'db_be_uts');
$q = "SELECT * FROM buku";
$dataQuery = $koneksi->query($q);
$data = mysqli_fetch_all($dataQuery, MYSQLI_ASSOC);

echo json_encode([
    'status' => 'Success',
    'msg' => 'Proses berhasil',
    'data' => $data
]);