<?php
// Matikan laporan error teks bawaan PHP agar tidak merusak JSON
error_reporting(0);
header("Content-Type: application/json; charset=UTF-8");

// 1. KONEKSI DATABASE 
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = '2355201043';
$koneksi = new mysqli($host, $user, $pass, $db);

// Jika koneksi gagal 
if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg"    => "Server error"
    ]);
    exit();
}

// 2. TANGKAP ID
$id = isset($_GET['id']) ? $_GET['id'] : null;
$checkData = $id ? $koneksi->query("SELECT * FROM db_baru WHERE id = '$id'") : false;

// Jika ID tidak dikirim atau data tidak ditemukan di tabel -> ERROR 404
if (!$id || !$checkData || $checkData->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg"    => "Data not found"
    ]);
    exit();
}

// 3. VALIDASI PAYLOAD 
$errors = [];
$name     = $_POST['name'] ?? '';
$category = $_POST['category'] ?? '';
$price    = $_POST['price'] ?? '';
$stock    = $_POST['stock'] ?? 0;

if (strlen($name) < 3) $errors['name'] = "Minimal 3 karakter";
$valid_categories = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];
if (!in_array($category, $valid_categories)) $errors['category'] = "Kategori tidak valid";
if (!is_numeric($price) || $price <= 0) $errors['price'] = "Harus berupa angka dan lebih dari 0";

if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg"    => "Data error",
        "errors" => $errors
    ]);
    exit();
}

// 4. PROSES GAMBAR
$namaPhoto = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $namaPhoto = "produk" . $id . "_updated." . $fileExt;
    if (!is_dir('uploads')) mkdir('uploads');
    move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $namaPhoto);
}

// 5. EKSEKUSI UPDATE
$sql = "UPDATE db_baru SET name='$name', category='$category', price='$price', stock='$stock'";
if ($namaPhoto) $sql .= ", image='$namaPhoto'";
$sql .= " WHERE id='$id'";

$update = $koneksi->query($sql);

// 6. RESPONSE AKHIR
if ($update) {
    // SUCCESS 200
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "msg"    => "Process success",
        "data"   => [
            "id"       => (int)$id,
            "name"     => $name,
            "category" => $category,
            "price"    => (int)$price,
            "stock"    => (int)$stock,
            "image"    => $namaPhoto ?? "tidak diubah"
        ]
    ]);
} else {
    // QUERY GAGAL -> ERROR 500
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg"    => "Server error"
    ]);
    
}

$koneksi->close();