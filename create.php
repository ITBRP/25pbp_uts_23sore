<?php
header("Content-Type: application/json; charset=UTF-8");
require "db.php";

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Method salah!'
    ]);
    exit();
}

$errors = [];

// --- name
if(!isset($_POST['name'])){
    $errors['name'] = "Name belum dikirim";
}else{
    if(strlen($_POST['name']) < 3){
        $errors['name'] = "Minimal 3 karakter";
    }
}

// --- category
$allowedCategory = ['Elektronik','Fashion','Makanan','Lainnya'];

if(!isset($_POST['category'])){
    $errors['category'] = "Category belum dikirim";
}else{
    if(!in_array($_POST['category'], $allowedCategory)){
        $errors['category'] = "Kategori tidak valid";
    }
}

// --- price
if(!isset($_POST['price'])){
    $errors['price'] = "Price belum dikirim";
}else{
    if(!is_numeric($_POST['price']) || $_POST['price'] <= 0){
        $errors['price'] = "Harus berupa angka dan lebih dari 0";
    }
}

// --- stock
if(isset($_POST['stock']) && $_POST['stock'] !== ""){
    if(!is_numeric($_POST['stock']) || $_POST['stock'] < 0){
        $errors['stock'] = "Harus angka minimal 0";
    }
}

// --- image
$anyPhoto   = false;
$imageName  = null;

if(isset($_FILES['image'])){

    if($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){

        $allowedExt = ['jpg','jpeg','png'];
        $oriName    = $_FILES['image']['name'];
        $fileExt    = strtolower(pathinfo($oriName, PATHINFO_EXTENSION));
        $fileSize   = $_FILES['image']['size'];

        if(!in_array($fileExt, $allowedExt)){
            $errors['image'] = "Format file tidak valid (hanya jpg, jpeg, png)";
        }
        elseif($fileSize > 3000000){
            $errors['image'] = "Ukuran file maksimal 3MB";
        }
        else{
            $anyPhoto  = true;
            $imageName = md5(date('dmyhis')) . "." . $fileExt;
        }
    }
}

if(count($errors) > 0){
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg"    => "Data error",
        "errors" => $errors
    ]);
    exit();
}

if($anyPhoto){
    move_uploaded_file($_FILES['image']['tmp_name'], "img/" . $imageName);
}

if($imageName === null){
    $imageName = "";
}

$name     = $_POST['name'];
$category = $_POST['category'];
$price    = intval($_POST['price']);
$stock    = intval($_POST['stock']);

$stmt = $koneksi->prepare(
    "INSERT INTO buku (name, category, price, stock, image) VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param("ssiis", $name, $category, $price, $stock, $imageName);
$stmt->execute();
$id = $stmt->insert_id;

http_response_code(201);
echo json_encode([
    "status" => "success",
    "msg"    => "Process success",
    "data"   => [
        "id"       => $id,
        "name"     => $name,
        "category" => $category,
        "price"    => $price,
        "stock"    => $stock,
        "image"    => $imageName
    ]
], JSON_PRETTY_PRINT);
?>
