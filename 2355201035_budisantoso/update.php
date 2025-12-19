<?php
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER["REQUEST_METHOD"];
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
}

if ($method != 'PUT') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah!'
    ]);
    exit();
}

if (!isset($_GET['id']) || $_GET['id'] == '') {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit();
}

$id = $_GET['id'];

$errors = [];

if (!isset($_POST['name'])) {
    $errors['name'] = "name belum dikirim";
} else {
    if ($_POST['name'] == '') {
        $errors['name'] = "name tidak boleh kosong";
    } else {
        if (strlen($_POST['name']) < 3) {
            $errors['name'] = "Minimal 3 karakter";
        }
    }
}

$validCat = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];
if (!isset($_POST['category'])) {
    $errors['category'] = "category belum dikirim";
} else {
    if ($_POST['category'] == '') {
        $errors['category'] = "category tidak boleh kosong";
    } else {
        if (!in_array($_POST['category'], $validCat)) {
            $errors['category'] = "Kategori tidak valid";
        }
    }
}


if (!isset($_POST['price'])) {
    $errors['price'] = "price belum dikirim";
} else {
    if ($_POST['price'] == '') {
        $errors['price'] = "price tidak boleh kosong";
    } else {
        if (!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
            $errors['price'] = "Harus berupa angka dan lebih dari 0";
        }
    }
}


if (!isset($_POST['stock'])) {
    $errors['stock'] = "stock belum dikirim";
} else {
    if ($_POST['stock'] == '') {
        $errors['stock'] = "stock tidak boleh kosong";
    } else {
        if (!is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
            $errors['stock'] = "stock harus angka";
        }
    }
}


$anyPhoto  = false;
$namaPhoto = null;

if (isset($_FILES['image'])) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

        $allowed = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['image']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = "File harus jpg, jpeg atau png";
        } else {
            $anyPhoto  = true;
            $namaPhoto = md5(date('dmyhis')) . "." . $fileExt;
        }
    }
}


if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data error',
        'errors' => $errors
    ]);
    exit();
}


if ($anyPhoto) {
    if (!is_dir('img')) {
        mkdir('img', 0777, true);
    }
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $namaPhoto);
}


$koneksi = new mysqli('localhost', 'root', '', 'uts_be');

$check = $koneksi->query("SELECT * FROM data_barang WHERE id=$id");
if ($check->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit();
}

$name     = $_POST['name'];
$category = $_POST['category'];
$price    = $_POST['price'];
$stock    = $_POST['stock'];

$q = "UPDATE data_barang SET
        name = '$name',
        category = '$category',
        price = $price,
        stock = $stock,
        image = " . ($anyPhoto ? "'$namaPhoto'" : "image") . "
      WHERE id = $id";

$koneksi->query($q);

echo json_encode([
    'status' => 'success',
    'msg' => 'Process success',
    'data' => [
        'id' => (int)$id,
        'name' => $name,
        'category' => $category,
        'price' => (int)$price,
        'stock' => (int)$stock,
        'image' => $namaPhoto
    ]
]);
