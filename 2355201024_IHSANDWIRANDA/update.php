<?php
header("Content-Type: application/json; charset=UTF-8");

// Hanya PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method Salah!"
    ]);
    exit;
}

// Cek ID di query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "ID tidak valid"
    ]);
    exit;
}
$id = (int)$_GET['id'];

// ==========================
// DATABASE
// ==========================
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli("localhost", "root", "", "uts_github");

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Server error"]);
    exit;
}

// Ambil data lama
$result = $koneksi->query("SELECT * FROM products WHERE id = $id");
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data not found"]);
    exit;
}
$oldData = $result->fetch_assoc();

// ==========================
// BACA JSON BODY
// ==========================
$input = json_decode(file_get_contents("php://input"), true);

if ($input === null) {
    http_response_code(400);
    echo json_encode([
        "status"=>"error",
        "msg"=>"Data error",
        "errors"=>["json"=>"JSON tidak valid"]
    ]);
    exit;
}

// Ambil field
$name = isset($input['name']) ? trim($input['name']) : '';
$category = isset($input['category']) ? trim($input['category']) : '';
$price = isset($input['price']) ? $input['price'] : null;
$stock = isset($input['stock']) ? $input['stock'] : null;
$imageName = $oldData['image']; // tetap pakai image lama

// ==========================
// VALIDASI
// ==========================
$errors = [];

// NAME
if ($name === '') {
    $errors['name'] = "Name tidak boleh kosong";
} elseif (strlen($name) < 3) {
    $errors['name'] = "Minimal 3 karakter";
}

// CATEGORY
$allowedCategory = ["Elektronik","Fashion","Makanan","Lainnya"];
if ($category === '') {
    $errors['category'] = "Category tidak boleh kosong";
} elseif (!in_array($category, $allowedCategory)) {
    $errors['category'] = "Kategori tidak valid";
}

// PRICE
if ($price === null || $price === "") {
    $errors['price'] = "Price tidak boleh kosong";
} elseif (!is_numeric($price) || $price <= 0) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}

// STOCK
if ($stock !== null && $stock !== "" && (!is_numeric($stock) || $stock < 0)) {
    $errors['stock'] = "Minimal 0";
}

// Jika ada error → response 400
if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        "status"=>"error",
        "msg"=>"Data error",
        "errors"=>$errors
    ]);
    exit;
}

// ==========================
// UPDATE DATABASE
// ==========================
$q = "UPDATE products SET 
        name='$name',
        category='$category',
        price='$price',
        stock=" . ($stock !== null && $stock !== "" ? "'$stock'" : "0") . "
      WHERE id=$id";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Server error"]);
    exit;
}

// ==========================
// SUCCESS RESPONSE
// ==========================
http_response_code(200);
echo json_encode([
    "status"=>"success",
    "msg"=>"Process success",
    "data"=>[
        "id"=>$id,
        "name"=>$name,
        "category"=>$category,
        "price"=>(int)$price,
        "stock"=>$stock!==null && $stock!=="" ? (int)$stock : 0,
        "image"=>$imageName
    ]
]);
?>
