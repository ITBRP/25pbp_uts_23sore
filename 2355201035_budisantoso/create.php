<?php
header("Content-Type: application/json; charset=UTF-8");
mysqli_report(MYSQLI_REPORT_OFF);


//    VALIDASI METHOD

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method salah!'
    ]);
    exit();
}


$errors = [];

// name
if (!isset($_POST['name'])) {
    $errors['name'] = "name belum dikirim";
} else {
    if ($_POST['name'] == '') {
        $errors['name'] = "name tidak boleh kosong";
    } elseif (strlen($_POST['name']) < 3) {
        $errors['name'] = " Minimal 3 karakter";
    }
}

// category
$validCat = ["Elektronik", "Fashion", "Makanan", "Lainnya"];
if (!isset($_POST['category'])) {
    $errors['category'] = "category belum dikirim";
} else {
    if ($_POST['category'] == '') {
        $errors['category'] = "category tidak boleh kosong";
    } elseif (!in_array($_POST['category'], $validCat)) {
        $errors['category'] = "Kategori tidak valid";
    }
}

// price
if (!isset($_POST['price'])) {
    $errors['price'] = "price belum dikirim";
} else {
    if ($_POST['price'] == '') {
        $errors['price'] = "price tidak boleh kosong";
    } elseif (!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
        $errors['price'] = "Price harus angka dan lebih besar dari 0";
    }
}

// stock (optional)
if (!isset($_POST['stock'])) {
    $errors['stock'] = "stock belum dikirim";
} else {
    if ($_POST['stock'] === '') {
        $errors['stock'] = "stock tidak boleh kosong";
    } elseif (!is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
        $errors['stock'] = "stock harus angka minimal 0";
    }
}

$stock = $_POST['stock'] ?? 0;


$anyPhoto  = false;
$namaPhoto = null;

if (isset($_FILES['image'])) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

        $allowed  = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['image']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = "File harus jpg, jpeg atau png";
        } elseif ($_FILES['image']['size'] > 3000000) {
            $errors['image'] = "Ukuran file max 3MB";
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
        'msg' => 'Error data',
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

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

$name     = $_POST['name'];
$category = $_POST['category'];
$price    = $_POST['price'];

$q = "INSERT INTO data_barang (name, category, price, stock, image)
      VALUES ('$name', '$category', '$price', '$stock', '$namaPhoto')";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

$id = $koneksi->insert_id;


http_response_code(201);
echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => (int)$price,
        'stock' => (int)$stock,
        'image' => $namaPhoto
    ]
]);
