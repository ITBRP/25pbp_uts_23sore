<?php
// ini code untuk proses request yang formatnya formdata
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method salah !'
    ]);
    exit();
}

//  validasi payload
$errors = [];

//  VALIDASI NAME 
if (!isset($_POST['name'])) {
    $errors['name'] = "Name belum dikirim";
} else {
    if ($_POST['name'] == '') {
        $errors['name'] = "Name tidak boleh kosong";
    } else if (strlen($_POST['name']) < 3) {
        $errors['name'] = "Minimal 3 karakter";
    }
}

//  validasi category //
$validCat = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];

if (!isset($_POST['category'])) {
    $errors['category'] = "Kategori belum dikirim";
} else {
    if ($_POST['category'] == '') {
        $errors['category'] = "Kategori tidak boleh kosong";
    } else if (!in_array($_POST['category'], $validCat)) {
        $errors['category'] = "Kategori tidak valid";
    }
}

//  validasi price //
if (!isset($_POST['price'])) {
    $errors['price'] = "Price belum dikirim";
} else {
    if ($_POST['price'] == '' || !is_numeric($_POST['price']) || $_POST['price'] <= 0) {
        $errors['price'] = "Harus berupa angka dan lebih dari 0";
    }
}

//  validasi stock //
if (isset($_POST['stock'])) {
    if ($_POST['stock'] !== "" && (!is_numeric($_POST['stock']) || $_POST['stock'] < 0)) {
        $errors['stock'] = "Stock harus angka minimal 0";
    }
}

//  validasi gambar//
$anyImage = false;
$imageName = null;

if (isset($_FILES['image'])) {

    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

        $allowed = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = "Format file tidak valid (hanya jpg, jpeg, png)";
        } else if ($_FILES['image']['size'] > 3000000) {
            $errors['image'] = "Ukuran file max 3MB";
        } else {
            $anyImage = true;
            $imageName = md5(date('dmyhis')) . "." . $fileExt;
        }
    }
}


if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => "Data error",
        'errors' => $errors
    ]);
    exit();
}


if ($anyImage) {
    move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/img/' . $imageName);
}

$koneksi = new mysqli('localhost', 'root', '', 'uts_be');

$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'] ?? 0;

$q = "INSERT INTO data_buku(name, category, price, stock, image)
      VALUES('$name', '$category', '$price', '$stock', '$imageName')";

$koneksi->query($q);
$id = $koneksi->insert_id;


echo json_encode([
    'status' => 'success',
    'msg'    => 'Process success',
    'data'   => [
        'id'       => $id,
        'name'     => $name,
        'category' => $category,
        'price'    => (int)$price,
        'stock'    => (int)$stock,
        'image'    => $imageName
    ]
]);
