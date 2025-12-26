<?php
header("Content-Type: application/json; charset=UTF-8");

$_METHOD = $_SERVER['REQUEST_METHOD'];

if ($_METHOD === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    $_METHOD = "PUT";
}

if ($_METHOD !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'ID tidak dikirim'
    ]);
    exit;
}

$id = intval($_GET['id']);

$input = $_POST;
$files = $_FILES;

$errors = [];

if (!isset($input['name']) || trim($input['name']) === "" || strlen(trim($input['name'])) < 3) {
    $errors['name'] = "Minimal 3 karakter";
} else {
    $name = trim($input['name']);
}

$allowedCategories = ["elektronik", "fashion", "makanan", "lainnya"];

if (!isset($input['category']) || trim($input['category']) === "") {
    $errors['category'] = "Kategori tidak valid";
} else {
    $category = trim($input['category']);

    if (!in_array($category, $allowedCategories)) {
        $errors['category'] = "Kategori tidak valid";
    }
}

if (!isset($input['price']) || !is_numeric($input['price']) || $input['price'] <= 0) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
} else {
    $price = $input['price'];
}

if (!isset($input['stock']) || !is_numeric($input['stock']) || $input['stock'] < 0) {
    $errors['stock'] = "Minimal 0";
} else {
    $stock = $input['stock'];
}

$imagename = null;
$fileExt = null;

if (isset($files['image']) && $files['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $fileName = $files['image']['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowed)) {
        $errors['image'] = "Format file tidak valid (hanya JPEG, jpeg, jpg, png)";
    }
}

if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data error',
        'errors' => $errors
    ]);
    exit;
}

$koneksi = new mysqli("localhost", "root", "", "uts_pbp");

if (!$koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

$cek = $koneksi->query("SELECT * FROM data_buku WHERE id=$id LIMIT 1");
if ($cek->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit;
}

$oldData   = $cek->fetch_assoc();
$oldimage  = $oldData['image'];


if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
    $imagename = md5(uniqid()) . "." . $fileExt;
    move_uploaded_file($files['image']['tmp_name'], "img/" . $imagename);

    if ($oldimage && file_exists("img/" . $oldimage)) {
        unlink("img/" . $oldimage);
    }
} else {
    $imagename = $oldimage;
}

$q = "UPDATE data_buku SET 
        name='$name',
        category='$category',
        price='$price',
        stock='$stock',
        image='$imagename'
      WHERE id=$id";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

$new = $koneksi->query("SELECT * FROM data_buku WHERE id=$id")->fetch_assoc();

echo json_encode([
    "status" => "success",
    "msg"    => "Process success",
    "data"   => $new
]);

?>