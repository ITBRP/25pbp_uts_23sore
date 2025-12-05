<?php
header("Content-Type: application/json; charset=UTF-8");

mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli("localhost", "root", "", "db_be_uts");

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Server error"]);
    exit();
}

if($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Server Error!"]);
    exit();
}

$q = $koneksi->query("SELECT * FROM buku");
$data = [];

while($row = $q->fetch_assoc()){
    $data[] = $row;
}

http_response_code(200);
echo json_encode([
    "status"=>"success",
    "msg"=>"Process success",
    "data"=>$data
], JSON_PRETTY_PRINT);
