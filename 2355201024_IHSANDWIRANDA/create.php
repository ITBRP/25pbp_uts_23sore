<?php
header("Content-Type: application/json; charset=UTF-8");

// Method check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah!'
    ]);
    exit;
}

$errors = [];

// ==========================
// VALIDATION
// ==========================

// name (required, min 3 char)
if (!isset($_POST['name'])) {
    $errors['name'] = "Name tidak dikirim";
} else {
    $name = trim($_POST['name']);
    if ($name === "") {
        $errors['name'] = "Name tidak boleh kosong";
    } elseif (strlen($name) < 3) {
        $errors['name'] = "Minimal 3 karakter";
    }
}

// category (required, only allowed values)
$allowedCategory = ["Elektronik", "Fashion", "Makanan", "Lainnya"];

if (!isset($_POST['category'])) {
    $errors['category'] = "Category tidak dikirim";
} else {
    $category = trim($_POST['category']);
    if (!in_array($category, $allowedCategory)) {
        $errors['category'] = "Kategori tidak valid";
    }
}

// price (required, int > 0)
if (!isset($_POST['price'])) {
    $errors['price'] = "Price tidak dikirim";
} else {
    $price = $_POST['price'];
    if (!is_numeric($price) || $price <= 0) {
        $errors['price'] = "Harus berupa angka dan lebih dari 0";
    }
}

// stock (optional, int >= 0)
$stock = null;

if (isset($_POST['stock']) && $_POST['stock'] !== "") {
    $stock = $_POST['stock'];
    if (!is_numeric($stock) || $stock < 0) {
        $errors['stock'] = "Minimal 0";
    }
}

// image (optional, jpg/jpeg/png, max 3MB)
$imageName = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

    $allowedExt = ['jpg', 'jpeg', 'png'];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        $errors['image'] = "Format file tidak valid (hanya jpg, jpeg, png)";
    }

    if ($fileSize > (3 * 1024 * 1024)) {
        $errors['image'] = "Ukuran file maks 3MB";
    }
}

// ==========================
// SEND ERROR RESPONSE
// ==========================
if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data error',
        'errors' => $errors
    ]);
    exit;
}

// ==========================
// DATABASE
// ==========================
error_reporting(0);                                                   // wajib yang lain sunah
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli("localhost", "root", "", "uts_github");

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// ==========================
// SAVE IMAGE IF EXISTS
// ==========================
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageName = md5(uniqid()) . "." . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], "img/" . $imageName);
}

// ==========================
// INSERT DATABASE
// ==========================
$q = "INSERT INTO products (name, category, price, stock, image)
      VALUES ('$name', '$category', '$price', " .
      ($stock !== null ? "'$stock'" : "NULL") . ", " .
      ($imageName ? "'$imageName'" : "NULL") . ")";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server Error",
        "sql_error" => $koneksi->error
    ]);
    exit;
}

$id = $koneksi->insert_id;

// ==========================
// SUCCESS RESPONSE (201)
// ==========================
http_response_code(201);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => $id,
        "name" => $name,
        "category" => $category,
        "price" => (int)$price,
        "stock" => $stock !== null ? (int)$stock : 0,
        "image" => $imageName
        ]
]);
?>