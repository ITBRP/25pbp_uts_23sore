<?php
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status"=>"error","msg"=>"ID belum dikirim"]);
    exit();
}

$id = $_GET['id'];

$koneksi = new mysqli("localhost","root","","uts_be");

// cek data ada atau tidak
$cek = $koneksi->query("SELECT * FROM data_barang WHERE id='$id'");
if ($cek->num_rows == 0) {
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data not found"]);
    exit();
}

$q = "DELETE FROM data_barang WHERE id='$id'";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Server error"]);
    exit();
}

http_response_code(200);
echo json_encode([
    "status"=>"success",
    "msg"=>"Delete data success",
    "data"=>["id"=>$id]
]);