<?php

header("Content-Type: application/json; charset=UTF-8");


$method = $_SERVER['REQUEST_METHOD'];
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
}


if ($method !== 'POST') {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data Not Found'
    ]);
    exit();
}


$errors = [];


if (!isset($_POST['name'])) {
    $errors['name'] = 'Name belum dikirim';
} elseif ($_POST['name'] === '') {
    $errors['name'] = 'Name tidak boleh kosong';
} elseif (strlen($_POST['name']) < 3) {
    $errors['name'] = 'Name minimal 3 karakter';
}


if (!isset($_POST['category'])) {
    $errors['category'] = 'Category belum dikirim';
} elseif ($_POST['category'] === '') {
    $errors['category'] = 'Category tidak boleh kosong';
}


if (!isset($_POST['price'])) {
    $errors['price'] = 'Price belum dikirim';
} elseif ($_POST['price'] === '') {
    $errors['price'] = 'Price tidak boleh kosong';
} elseif (!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
    $errors['price'] = 'Price harus angka lebih dari 0';
}


if (isset($_POST['stock'])) {
    if ($_POST['stock'] === '') {
        $errors['stock'] = 'Stock tidak boleh kosong';
    } elseif (!is_numeric($_POST['stock']) || $_POST['stock'] <= 0) {
        $errors['stock'] = 'Harus berupa angka dan lebih dari 0';
    }
}


$uploadGambar = false;
$namaGambar = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $ekstensiValid = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $ekstensiValid)) {
        $errors['image'] = 'File gambar tidak valid';
    } else {
        $uploadGambar = true;
        $namaGambar = md5(date('YmdHis')) . '.' . $ext;
    }
}

// Jika ada error
if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Validasi gagal',
        'errors' => $errors
    ]);
    exit();
}


if ($uploadGambar) {
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $namaGambar);
}

$db = new mysqli('localhost', 'root', '', 'pbp');

$id       = $_GET['id'];
$name     = $_POST['name'];
$category = $_POST['category'];
$price    = $_POST['price'];
$stock    = $_POST['stock'] ?? 0;

$sql = "UPDATE produk SET
            name = '$name',
            category = '$category',
            price = $price,
            stock = $stock,
            image = " . ($uploadGambar ? "'$namaGambar'" : "image") . "
        WHERE id = $id";

$db->query($sql);


echo json_encode([
    'status' => 'success',
    'msg' => 'Data produk berhasil diperbarui',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'stock' => $stock,
        'image' => $namaGambar
    ]
]);
