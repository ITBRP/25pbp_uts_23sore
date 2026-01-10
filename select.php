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

$koneksi = new mysqli("localhost", "root", "", "uts_pbp");
$q = "SELECT * FROM items";
$result = $koneksi->query($q);
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$res = [
    'status' => 'success',
    'data' => $items
];

echo json_encode($res);
exit();


?>