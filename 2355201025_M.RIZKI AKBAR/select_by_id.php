<?php 
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method salah !'
    ]);
    exit();
}

error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli('localhost', 'root', '', 'uts_backkend');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

$id = $_GET['id'];
$q = "SELECT * FROM products WHERE id = $id";
$dataQuery = $koneksi->query($q);

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

?>