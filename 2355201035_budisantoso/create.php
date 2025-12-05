<?php
header("Content-Type: application/json; charset=UTF-8");


mysqli_report(MYSQLI_REPORT_OFF);

// Hanya izinkan POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit;
}

$errors = [];




if (!isset($_POST["name"]) || $_POST["name"] === "") {
    $errors["name"] = "Name tidak boleh kosong";
} elseif (strlen($_POST["name"]) < 3) {
    $errors["name"] = "Minimal 3 karakter";
}

$validCat = ["Elektronik", "Fashion", "Makanan", "Lainnya"];

if (!isset($_POST["category"]) || $_POST["category"] === "") {
    $errors["category"] = "Kategori tidak boleh kosong";
} elseif (!in_array($_POST["category"], $validCat)) {
    $errors["category"] = "Kategori tidak valid";
}


if (!isset($_POST["price"]) || $_POST["price"] === "" || !is_numeric($_POST["price"]) || $_POST["price"] <= 0) {
    $errors["price"] = "Harus berupa angka dan lebih dari 0";
}


$stock = $_POST["stock"] ?? 0;
if (!is_numeric($stock) || $stock < 0) {
    $errors["stock"] = "Stock harus angka minimal 0";
}




$imageName = null;
$hasImage = false;

if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {

    $allowed = ["jpg", "jpeg", "png"];
    $fileName = $_FILES["image"]["name"];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowed)) {
        $errors["image"] = "Format file tidak valid (hanya jpg, jpeg, png)";
    } elseif ($_FILES["image"]["size"] > 3000000) {
        $errors["image"] = "Ukuran file max 3MB";
    } else {
        $hasImage = true;
        $imageName = md5(time()) . "." . $fileExt;
    }
}

// Jika validasi error 400
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit;
}


//  UPLOAD FILE


if ($hasImage) {
    $folder = __DIR__ . "/img/";

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $folder . $imageName)) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "msg" => "Server error"
        ]);
        exit;
    }
}



$koneksi = new mysqli("localhost", "root", "", "uts_be");

// Jika koneksi gagal  500
if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

$name     = $_POST["name"];
$category = $_POST["category"];
$price    = $_POST["price"];



$q = "INSERT INTO data_barang(name, category, price, stock, image)
      VALUES('$name', '$category', '$price', '$stock', '$imageName')";

// Jika query gagal  500
if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}




$id = $koneksi->insert_id;

http_response_code(201);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => $id,
        "name" => $name,
        "category" => $category,
        "price" => (int)$price,
        "stock" => (int)$stock,
        "image" => $imageName
    ]
]);
