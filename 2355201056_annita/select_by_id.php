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

if(!isset($_GET['id'])){
    http_response_code(400);
    echo json_encode(["status"=>"error","msg"=>"ID belum dikirim"]);
    exit();
}

$id = $_GET['id'];

$stmt = $koneksi->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data not found"]);
    exit();
}

$data = $res->fetch_assoc();

http_response_code(200);
echo json_encode([
    "status"=>"success",
    "msg"=>"Process success",
    "data"=>$data
], JSON_PRETTY_PRINT);
