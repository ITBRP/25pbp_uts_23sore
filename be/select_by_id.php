<?php

header("Content-Type: application/json; charset=UTF-8");


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method tidak sesuai'
    ]);
    exit();
}


$koneksiDB = new mysqli('localhost', 'root', '', 'pbp');


$produkId = $_GET['id'];


$query = "SELECT * FROM produk WHERE id = $produkId";
$hasil = $koneksiDB->query($query);


$dataproduk = mysqli_fetch_assoc($hasil);


echo json_encode([
    'status' => 'success',
    'msg' => 'Data mahasiswa berhasil ditampilkan',
    'data' => $dataproduk
]);
