<?php
header("Content-Type: application/json; charset=UTF-8");
require "db.php";

if($_SERVER['REQUEST_METHOD'] != 'PUT'){
    http_response_code(405);
    echo json_encode(["status"=>"error","msg"=>"Method salah!"]);
    exit();
}

parse_str(file_get_contents("php://input"), $_PUT);

if(!isset($_GET['id'])){
    http_response_code(400);
    echo json_encode(["status"=>"error","msg"=>"ID belum dikirim"]);
    exit();
}

$id = $_GET['id'];

$errors = [];

if(!isset($_PUT['name']) || strlen($_PUT['name']) < 3){
    $errors['name'] = "Minimal 3 karakter";
}

$allowed = ['Elektronik','Fashion','Makanan','Lainnya'];
if(!isset($_PUT['category']) || !in_array($_PUT['category'], $allowed)){
    $errors['category'] = "Kategori tidak valid";
}

if(!isset($_PUT['price']) || !is_numeric($_PUT['price']) || $_PUT['price'] <= 0){
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}

if(isset($_PUT['stock']) && (!is_numeric($_PUT['stock']) || $_PUT['stock'] < 0)){
    $errors['stock'] = "Harus angka minimal 0";
}

if(count($errors) > 0){
    http_response_code(400);
    echo json_encode([
        "status"=>"error",
        "msg"=>"Data error",
        "errors"=>$errors
    ]);
    exit();
}

$cek = $koneksi->prepare("SELECT id FROM buku WHERE id=?");
$cek->bind_param("i",$id);
$cek->execute();
$ada = $cek->get_result();

if($ada->num_rows == 0){
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data not found"]);
    exit();
}

$name = $_PUT['name'];
$category = $_PUT['category'];
$price = intval($_PUT['price']);
$stock = intval($_PUT['stock']);

$update = $koneksi->prepare("UPDATE buku SET name=?, category=?, price=?, stock=? WHERE id=?");
$update->bind_param("ssiii", $name, $category, $price, $stock, $id);
$update->execute();

http_response_code(200);
echo json_encode([
    "status"=>"success",
    "msg"=>"Process success",
    "data"=>[
        "id"=>$id,
        "name"=>$name,
        "category"=>$category,
        "price"=>$price,
        "stock"=>$stock
    ]
], JSON_PRETTY_PRINT);
?>
