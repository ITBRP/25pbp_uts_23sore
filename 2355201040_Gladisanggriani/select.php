<?php
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(500);
    $res = [
        'status' => 'error',
        'msg' => 'Server Salah'
    ];
    echo json_encode($res);
    exit();
}

// if(isset($_GET['force500'])){
//     http_response_code(500);
//     echo json_encode([
//         'status' => 'error',
//         'msg' => 'Server error'
//     ]);
//     exit();
// }

$koneksi = mysqli_connect("localhost","root","","uts_be");
$q = mysqli_query($koneksi, "SELECT * FROM mahasiswa");
$data = [];
while($row = mysqli_fetch_assoc($q)){
    $data[] = $row;
}

if(count($data) == 0){
    http_response_code(404);
    $res = [
        'status' => 'error',
        'msg' => 'Data not found'
    ];
    echo json_encode($res);
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

http_response_code(200);
$res = [
    'status' => 'success',
    'msg' => 'Process success',
    'data' => $data
];
echo json_encode($res);