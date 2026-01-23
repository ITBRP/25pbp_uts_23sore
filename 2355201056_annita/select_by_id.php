<?php 

header("Content-Type: application/json; charset=UTF-8");
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(405);
    $res = [
        'status' => 'Error',
        'msg' => 'Method salah !'
    ];
    echo json_encode($res);
    exit();
}

$koneksi = new mysqli('localhost', 'root', '', 'db_be_uts');
$id = $_GET['id'];
$q = "SELECT * FROM buku WHERE id=$id";
$dataQuery = $koneksi->query($q);
$data = mysqli_fetch_assoc($dataQuery);

if (!$data) {
    http_response_code(404);
    echo json_encode([
        "status" => "Error",
        "msg" => "Data not found!"
    ]);
    exit();
}

echo json_encode([
    'status' => 'Success',
    'msg' => 'Proses berhasil',
    'data' => $data
]);