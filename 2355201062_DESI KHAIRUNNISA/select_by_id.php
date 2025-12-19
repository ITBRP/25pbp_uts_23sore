<?php 
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
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
$koneksi = new mysqli('localhost', 'root', '', 'data_buku');

if ($dataQuery->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
} else {
    $data = mysqli_fetch_assoc($dataQuery);
    echo json_encode([
        'status' => 'success',
        'msg' => 'Proses berhasil',
        'data' => $data
    ]);
}

$id = $_GET['id'];
$q = "SELECT * FROM products WHERE id=$id";
$dataQuery = $koneksi->query($q);
$data = mysqli_fetch_assoc($dataQuery);

