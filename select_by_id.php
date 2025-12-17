<?php

header("Content-Type: application/json, charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(500);
    $res = [
        'status' => 'error',
        'msg' => 'Server Error !'
    ];
    echo json_encode($res);
    exit();
}


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$koneksi = new mysqli("localhost", "root", "", "uts_pbp");
$q = "SELECT id, name, category, price, stock, image FROM items WHERE id = $id";


$result = $koneksi->query($q);

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'msg' => 'Process success',
        'data' => $data
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
}