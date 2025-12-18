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
$koneksi = new mysqli('localhost', 'root', '', 'uts_be');
$id = $_GET['id'];
$q = "SELECT * FROM mahasiswa WHERE id=$id";
$dataQuery = $koneksi->query($q);
$data = mysqli_fetch_assoc($dataQuery);

if(!$data){
    http_response_code(404);
    $res = [
        'status' => 'error',
        'msg' => 'Data not found'
    ];
    echo json_encode($res);
    exit();
}

echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => $data
]);