<?php

header("Content-Type: application/json; charset=UTF-8");


if($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Metode request tidak sesuai'
    ]);
    exit();
}


$koneksi = new mysqli('localhost', 'root', '', 'pbp');


$query = "SELECT * FROM produk";
$result = $koneksi->query($query);


$data = mysqli_fetch_all($result, MYSQLI_ASSOC);


echo json_encode([
    'status' => 'success',
    'msg' => 'Data berhasil ditampilkan',
    'data' => $data
]);
