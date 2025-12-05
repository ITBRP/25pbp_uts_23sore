<?php
mysqli_report(MYSQLI_REPORT_OFF);
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["status" => "error", "msg" => "Method salah!"]);
    exit();
}

if (!isset($_GET['id']) || $_GET['id'] === "") {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "ID belum dikirim"]);
    exit();
}
$id = $_GET['id'];


$input = json_decode(file_get_contents("php://input"), true);
$_PUT = $input ? $input : [];


$name     = $_PUT['name'] ?? "";
$category = $_PUT['category'] ?? "";
$price    = $_PUT['price'] ?? "";
$stock    = $_PUT['stock'] ?? "";


$errors = [];

if ($name === "" || strlen($name) < 3) {
    $errors['name'] = "Minimal 3 karakter";
}

$validCat = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];
if ($category === "" || !in_array($category, $validCat)) {
    $errors['category'] = "Kategori tidak valid";
}

if ($price === "" || !is_numeric($price) || $price <= 0) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Data error", "errors" => $errors]);
    exit();
}

$koneksi = new mysqli("localhost", "root", "", "uts_be");


$check = $koneksi->query("SELECT * FROM data_barang WHERE id='$id'");


if ($check === false) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Server error"]);
    exit();
}


if ($check->num_rows == 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "msg" => "Data not found"]);
    exit();
}


$q = "UPDATE data_barang SET 
        name='$name', 
        category='$category', 
        price='$price',
        stock='$stock'
      WHERE id='$id'";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Server error"]);
    exit();
}

http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => $id,
        "name" => $name,
        "category" => $category,
        "price" => (int)$price,
        "stock" => (int)$stock
    ]
]);
