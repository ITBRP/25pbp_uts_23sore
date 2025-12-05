<?php

header("Content-Type: application/json, charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(500);
    $res = [
        'status' => 'error',
        'msg' => 'Server Error !'
    ];
    echo json_encode($res);
    exit();
}

// validasi payload
$errors = [];
if (!isset($_POST['name'])) {
    $errors['name'] = "name belum dikirim";
} else {
    if ($_POST['name'] == '') {
        $errors['name'] = "name tidak boleh kosong";
    } else {
        if (strlen($_POST['name']) < 3) {
            $errors['name'] = "name minimal 3 karakter";
        }
    }
}

if (!isset(($_POST['category']))) {
    $errors['category'] = "Silahkan mengisi category";
} else {
    if ($_POST['category'] != "Elektronik" && $_POST['category'] != "Fashion" && $_POST['category'] != "Makanan" && $_POST['category'] != "Lainnya") {
        $errors['category'] = "Category hanya diisi dengan Elektronik, Fashion, Makanan, dan Lainnya";
    }
}

if (!isset($_POST['price'])) {
    $errors['price'] = "price belum dikirim";
} else {
    if ($_POST['price'] == '') {
        $errors['price'] = "price tidak boleh kosong";
    } else {
        if (!is_numeric($_POST['price'])) {
            $errors['price'] = "price harus berupa angka";
            exit();
        } else if ($_POST['price'] < 0) {
            $errors['price'] = "price harus minimal 0";
        }
    }
}

$_POST['stock'] = null;


if (isset($_POST['stock'])) {
    if (!is_numeric($_POST['stock'])) {
        $errors['stock'] = "stock harus berupa angka";
        exit();
    } else if ($_POST['stock'] < 0) {
        $errors['stock'] = "stock harus minimal 0";
    }
}


$anyPhoto = false;
$namaPhoto = null;
if (isset($_FILES['photo'])) {
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['photo']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            $errors['photo'] = "File harus jpg, jpeg atau png";
        } else {
            $anyPhoto = true;
            $namaPhoto = md5(date('dmyhis')) . "." . $fileExt;
        }
    }

}


if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['photo']['size'] > 3 * 1024 * 1024) {
        $errors['photo'] = "Maksimal size photo harus 3 mb";
    }
}

if (count($errors) > 0) {
    http_response_code(400);
    $res = [
        'status' => 'error',
        'msg' => "Error data",
        'errors' => $errors
    ];

    echo json_encode($res);
    exit();
}

if ($anyPhoto) {
    move_uploaded_file($_FILES['photo']['tmp_name'], 'img/' . $namaPhoto);
}

// insert ke db
$koneksi = new mysqli('localhost', 'root', '', 'uts_pbp');
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$q = "INSERT INTO items(name, category, price, stock, image) VALUES('$name','$category', '$price', '$stock', '$namaPhoto')";
$koneksi->query($q);
$id = $koneksi->insert_id;

http_response_code(201);

echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => [
            "id" => $id,
            "name" => $name,
            "category" => $category,
            "price" => $price,
            "stock" => $stock,
            "image" => $namaPhoto
        ]
]);

