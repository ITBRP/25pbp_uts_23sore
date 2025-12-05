<?php
header("Content-Type: application/json; charset=UTF-8");
require "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server Error!"
    ]);
    exit;
}

parse_str(file_get_contents("php://input"), $_PUT);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "ID belum dikirim atau tidak valid"
    ]);
    exit;
}

$id = intval($_GET['id']);

$errors = [];

// name
if (!isset($_PUT['name']) || strlen($_PUT['name']) < 3) {
    $errors['name'] = "Minimal 3 karakter";
}

// category
$allowed = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];
if (!isset($_PUT['category']) || !in_array($_PUT['category'], $allowed)) {
    $errors['category'] = "Kategori tidak valid";
}

// price
if (!isset($_PUT['price']) || !is_numeric($_PUT['price']) || $_PUT['price'] <= 0) {
    $errors['price'] = "Harus angka, lebih dari 0";
}

// stock
if (isset($_PUT['stock']) && (!is_numeric($_PUT['stock']) || $_PUT['stock'] < 0)) {
    $errors['stock'] = "Harus angka minimal 0";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit;
}

$cek = $koneksi->prepare("SELECT * FROM buku WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$res = $cek->get_result();

if ($res->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data tidak ditemukan"
    ]);
    exit;
}

$name = $_PUT['name'];
$category = $_PUT['category'];
$price = intval($_PUT['price']);
$stock = isset($_PUT['stock']) ? intval($_PUT['stock']) : 0;

$update = $koneksi->prepare(
    "UPDATE buku SET name=?, category=?, price=?, stock=? WHERE id=?"
);
$update->bind_param("ssiii", $name, $category, $price, $stock, $id);
$update->execute();

$row = $res->fetch_assoc();
$imageName = $row['image'];

http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => $id,
        "name" => $name,
        "category" => $category,
        "price" => $price,
        "stock" => $stock,
        "image" => $imageName
    ]
], JSON_PRETTY_PRINT);

