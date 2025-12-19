<?php
header("Content-Type: application/json; charset=UTF-8");


//Validasi Method
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}


//Koneksi Database
$koneksi = new mysqli('localhost', 'root', '', 'UTS_pbp');


$id = $_GET['id'];
$q = "SELECT * FROM mahasiswa WHERE id=$id";
$checked = mysqli_query($koneksi, $q);

if (mysqli_num_rows($checked) == 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit;
}

$q = "DELETE FROM mahasiswa WHERE id=$id";
mysqli_query($koneksi, $q);
echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
]);