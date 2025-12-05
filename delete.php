<?php
header("Content-Type: application/json; charset=UTF-8");
require "db.php";

if($_SERVER['REQUEST_METHOD'] != 'DELETE'){
    http_response_code(405);
    echo json_encode(["status"=>"error","msg"=>"Method salah!"]);
    exit();
}

if(!isset($_GET['id'])){
    http_response_code(400);
    echo json_encode(["status"=>"error","msg"=>"ID belum dikirim"]);
    exit();
}

$id = $_GET['id'];
$cek = $koneksi->prepare("SELECT id FROM buku WHERE id=?");
$cek->bind_param("i",$id);
$cek->execute();
$res = $cek->get_result();

if($res->num_rows == 0){
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data not found"]);
    exit();
}

$del = $koneksi->prepare("DELETE FROM buku WHERE id=?");
$del->bind_param("i",$id);
$del->execute();

http_response_code(200);
echo json_encode([
    "status"=>"success",
    "msg"=>"Delete data success",
    "data"=>["id"=>$id]
], JSON_PRETTY_PRINT);
?>
