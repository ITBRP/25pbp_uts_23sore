<?php
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status"=>"error","msg"=>"ID belum dikirim"]);
    exit();
}

$id = $_GET['id'];

$koneksi = new mysqli("localhost","root","","uts_be");

$q = "SELECT * FROM data_barang WHERE id = '$id'";
$res = $koneksi->query($q);

if ($res->num_rows == 0) {
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data not found"]);
    exit();
}

$row = $res->fetch_assoc();

http_response_code(200);
echo json_encode([
    "status"=>"success",
    "msg"=>"Process success",
    "data"=>$row
]);