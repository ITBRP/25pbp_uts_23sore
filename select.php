<?php
header("Content-Type: application/json; charset=UTF-8");
require "db.php";

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
?>
