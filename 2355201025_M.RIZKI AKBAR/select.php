<?php 
// ini code untuk proses request yang formatnya formdata
header("Content-Type: application/json; charset=UTF-8");
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(405);
    $res = [
        'status' => 'error',
        'msg' => 'Method salah !'
    ];
    echo json_encode($res);
    exit();
}

// insert ke db
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli('localhost', 'root', '', 'uts_backend');

if ($koneksi->connect_error) {
    http_response_code(500);
    $res = [
        'status' => 'error',
        'msg' => 'Server error'
    ];
    echo json_encode($res);
    exit();
}

$q = "SELECT * FROM products";
$dataQuery = $koneksi->query($q);
$data = mysqli_fetch_all($dataQuery, MYSQLI_ASSOC);

echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => $data
]);

