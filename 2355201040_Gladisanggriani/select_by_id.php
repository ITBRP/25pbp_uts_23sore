<?php 
header("Content-Type: application/json; charset=UTF-8");
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(500);
    $res = [
        'status' => 'error',
        'msg' => 'Server error !'
    ];
    echo json_encode($res);
    exit();
}

if(!isset($_GET['id'])){
    http_response_code(400);
    $res = [
        'status' => 'error',
        'msg' => "ID tidak boleh kosong"
    ];
    echo json_encode($res);
    exit();
}else{
    if($_GET['id'] == ''){
        http_response_code(400);
        $res = [
            'status' => 'error',
            'msg' => "ID tidak boleh kosong"
        ];
        echo json_encode($res);
        exit();
    }
}

$id = $_GET['id'];
$koneksi = mysqli_connect("localhost", "root", "", "uts_be");
$q = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE id = '$id'");
$data = mysqli_fetch_assoc($q);

if(!$data){
    http_response_code(404);
    $res = [
        'status' => 'error',
        'msg' => 'Data not found'
    ];
    echo json_encode($res);
    exit();
}

http_response_code(200);
$res = [
    'status' => 'success',
    'msg' => 'Process success',
    'data' => $data
];
echo json_encode($res);