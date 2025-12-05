<?php
require "db.php";

// Cek metode
if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
    apiResponse(405, "Method salah!");
}

// Ambil ID
$id = $_GET['id'] ?? 0;

if (!$id) {
    apiResponse(400, "ID tidak valid");
}

// Cek apakah data ada
$cek = mysqli_prepare($koneksi, "SELECT * FROM buku WHERE id=?");
mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);
$res = mysqli_stmt_get_result($cek);

if (mysqli_num_rows($res) == 0) {
    apiResponse(404, "Data tidak ditemukan");
}

// Ambil input PUT
parse_str(file_get_contents("php://input"), $_PUT);

$nama     = trim($_PUT['nama'] ?? "");
$category = trim($_PUT['category'] ?? "");
$price    = $_PUT['price'] ?? "";
$stock    = $_PUT['stock'] ?? 0;

$errors = [];

// Validasi
if (strlen($nama) < 3) {
    $errors['nama'] = "Minimal 3 karakter";
}

$allowed = ['Elektronik','Fashion','Makanan','Lainnya'];
if (!in_array($category, $allowed)) {
    $errors['category'] = "Kategori tidak valid";
}

if (!is_numeric($price) || $price <= 0) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}

if (!is_numeric($stock) || $stock < 0) {
    $errors['stock'] = "Harus angka minimal 0";
}

// Jika error
if (!empty($errors)) {
    apiResponse(400, "Error data", $errors);
}

// Update ke DB
$update = mysqli_prepare($koneksi, 
    "UPDATE buku SET nama=?, category=?, price=?, stock=? WHERE id=?"
);

mysqli_stmt_bind_param($update, "ssiii", $nama, $category, $price, $stock, $id);

if (mysqli_stmt_execute($update)) {
    apiResponse(200, "Process success", [
        "id"       => $id,
        "nama"     => $nama,
        "category" => $category,
        "price"    => (int)$price,
        "stock"    => (int)$stock
    ]);
}

apiResponse(500, "Server error");
?>
