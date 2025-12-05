<?php
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] != 'DELETE'){
    http_response_code(500);
    $res = [
        'status' => 'error',
        'msg' => 'Server error'
    ];
    echo json_encode($res);
    exit();
}

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if(!isset($data['id']) || $data['id'] == ""){
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'ID tidak boleh kosong'
    ]);
    exit();
}

if(true){
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

$id = $data['id'];
$koneksi = mysqli_connect("localhost", "root", "", "uts_be");
$periksa = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE id = '$id'");
$cek = mysqli_fetch_assoc($periksa);

if(!$cek){
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit();
}


$Delete = mysqli_query($koneksi, "DELETE FROM mahasiswa WHERE id = '$id'");
if(!$Delete){
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}


http_response_code(200);
echo json_encode([
    'status' => 'success',
    'msg' => 'Delete data success',
    'data' => [
        'id' => $id
    ]
]);