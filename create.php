<?php
header("Content-Type: application/json; charset=UTF-8");
require "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "msg" => "Method salah"]);
    exit;
}

// ======================================
// VALIDASI INPUT
// ======================================

$errors = [];

$name     = $_POST['name'] ?? "";
$category = $_POST['category'] ?? "";
$price    = $_POST['price'] ?? "";
$stock    = $_POST['stock'] ?? 0;

// name
if (strlen($name) < 3) {
    $errors['name'] = "Minimal 3 karakter";
}

// category
$allowed_cat = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];
if (!in_array($category, $allowed_cat)) {
    $errors['category'] = "Kategori tidak valid";
}

// price
if (!is_numeric($price) || $price <= 0) {
    $errors['price'] = "Harus angka, lebih dari 0";
}

// stock (optional)
if ($stock !== "" && (!is_numeric($stock) || $stock < 0)) {
    $errors['stock'] = "Harus angka minimal 0";
}

// ======================================
// VALIDASI IMAGE
// ======================================

$imageDBName = null;        // nama unik (DB)
$outputImageName = null;    // nama asli (output JSON)

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

    $originalName = $_FILES['image']['name']; // nama asli
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed_ext)) {
        $errors['image'] = "Format file harus jpg, jpeg, png";
    }

    if ($_FILES['image']['size'] > 3000000) {
        $errors['image'] = "Ukuran file maksimal 3MB";
    }

    if (empty($errors)) {

        // 🔵 Nama file asli untuk output Postman
        $outputImageName = $originalName;

        // 🔵 Nama unik untuk database dan penyimpanan
        $filenameOnly = pathinfo($originalName, PATHINFO_FILENAME);
        $uniqueName = $filenameOnly . "_" . uniqid() . "." . $ext;

        // simpan untuk database
        $imageDBName = $uniqueName;

        // pindahkan file
        move_uploaded_file($_FILES['image']['tmp_name'], "img/" . $uniqueName);
    }
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

// ======================================
// INSERT DATABASE
// ======================================

$stmt = $koneksi->prepare(
    "INSERT INTO buku (name, category, price, stock, image)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssiis", $name, $category, $price, $stock, $imageDBName);

if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "msg" => "Process success",
        "data" => [
            "id"       => $stmt->insert_id,
            "name"     => $name,
            "category" => $category,
            "price"    => intval($price),
            "stock"    => intval($stock),
            "image"    => $outputImageName   // TAMPILKAN NAMA ASLI
        ]
    ]);
    exit;
}

http_response_code(500);
echo json_encode(["status" => "error", "msg" => "Server error"]);
exit;
?>
