<?php
//2355201025 : code benar
header("Content-Type: application/json; charset=UTF-8");

// methode put
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method Salah !"
    ]);
    exit;
}

// cek id url
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "ID tidak dikirim"
    ]);
    exit;
}

$id = intval($_GET["id"]);

// baca raw inout json
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Format JSON tidak valid"
    ]);
    exit;
}

$errors = [];

//=========================================== validasi 2355201025

// name
if (!isset($data["name"]) || trim($data["name"]) === "") {
    $errors["name"] = "Name tidak boleh kosong";
} else {
    $name = trim($data["name"]);
    if (strlen($name) < 3) $errors["name"] = "Minimal 3 karakter";
}

// price
if (!isset($data["price"])) {
    $errors["price"] = "Harus berupa angka dan lebih dari 0";
} else {
    $price = intval($data["price"]);
    if ($price <= 0) $errors["price"] = "Harus berupa angka dan lebih dari 0";
}

// kategori
$allowed = ["Elektronik", "Pakaian", "Makanan"];

if (!isset($data["category"]) || trim($data["category"]) === "") {
    $errors["category"] = "Kategori tidak boleh kosong";
} else {
    $category = trim($data["category"]);
    if (!in_array($category, $allowed)) {
        $errors["category"] = "Kategori tidak valid";
    }
}

// stock
if (!isset($data["stock"])) {
    $errors["stock"] = "Stock tidak dikirim";
} else {
    $stock = intval($data["stock"]);
    if ($stock < 0) $errors["stock"] = "Stock tidak boleh minus";
}

// Jika error → respond 400
if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit;
}

//=========================================== databases 2355201025

error_reporting(0);                                                   // wajib yang lain sunah
mysqli_report(MYSQLI_REPORT_OFF);                                     // wajib yang lain sunah
$koneksi = new mysqli("localhost", "root", "", "uts_backend");
if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// cek data lama
$qCheck = $koneksi->query("SELECT * FROM products WHERE id=$id LIMIT 1");

if ($qCheck->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit;
}

$old = $qCheck->fetch_assoc();
$oldImage = $old["image"]; // Kembalikan gambar lama pada respon


//=========================================== update data 2355201025

$q = "UPDATE products SET 
        name='$name',
        category='$category',
        price=$price,
        stock=$stock
      WHERE id=$id";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

//=========================================== respons 2355201025 (jangan di ganggu gugat!)

echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => $id,
        "name" => $name,
        "category" => $category,
        "price" => $price,
        "stock" => $stock,
        "image" => $oldImage
    ]
]);
// 2355201025
?>
