<?php

header("Content-Type: application/json; charset=UTF-8");


if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data Not Found'
    ]);
    exit();
}

$db = new mysqli('localhost', 'root', '', 'pbp');


$produkId = $_GET['id'];


$cekQuery = "SELECT image FROM produk WHERE id = $produkId";
$hasilCek = $db->query($cekQuery);



$data = $hasilCek->fetch_assoc();
$gambarLama = $data['image'];


if ($gambarLama && file_exists('img/' . $gambarLama)) {
    unlink('img/' . $gambarLama);
}

$hapusQuery = "DELETE FROM produk WHERE id = $produkId";
$db->query($hapusQuery);


echo json_encode([
    'status' => 'success',
    'msg' => 'Data produk berhasil dihapus'
]);
