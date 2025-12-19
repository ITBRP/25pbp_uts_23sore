<?php 
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}


$errors = [];


if (!isset($_POST['name'])) {
    $errors['name'] = "Minimal 3 karakter";
} else {
    $name = trim($_POST['name']);
    if ($name == '' || strlen($name) < 3) {
        $errors['name'] = "Minimal 3 karakter";
    }
}


$categoryokey = ["Elektronik", "Fashion", "Makanan", "Lainnya"];

if (!isset($_POST['category'])) {
    $errors['category'] = "Kategori tidak valid";
} else {
    $category = trim($_POST['category']);
    if (!in_array($category, $categoryokey)) {
        $errors['category'] = "Kategori tidak valid";
    }
}


if (!isset($_POST['price'])) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
} else {
    if (!is_numeric($_POST['price']) && $_POST['price'] <= 0) {
        $errors['price'] = "Harus berupa angka dan lebih dari 0";
    }
}


if (isset($_POST['stock']) && $_POST['stock'] !== "") {
    if (!is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
        $errors['stock'] = "Harus berupa angka dan minimal 0";
    } else {
        $stock = intval($_POST['stock']);
    }
}


$imageName = null;

if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
    $errors['image'] = "Format file tidak valid (hanya jpg, jpeg, png)";
} else {
    $allowedExt = ['jpg', 'jpeg', 'png'];
    $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $fileSize = $_FILES['image']['size'];

    if (!in_array($fileExt, $allowedExt)) {
        $errors['image'] = "Format file tidak valid (hanya jpg, jpeg, png)";
    } elseif ($fileSize > 3 * 1024 * 1024) {
        $errors['image'] = "Ukuran maksimal 3MB";
    } else {

        if (!is_dir("uploads")) {
            mkdir("uploads", 0755, true);
        }

        $imageName = basename($_FILES['image']['name']);

        if (!move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imageName)) {
            $errors['image'] = "Gagal menyimpan file image";
            $imageName = null;
        }
    }
}


if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit();
}


$kon = new mysqli('localhost', 'root', '', 'UTS_pbp');

if ($kon->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}


$name = $kon->real_escape_string($name);
$category = $kon->real_escape_string($category);
$price = intval($_POST['price']);
$imageValue = $imageName !== null ? $kon->real_escape_string($imageName) : null;

if ($imageValue === null) {
    $q = "INSERT INTO mahasiswa(name, category, price, stock, image)
          VALUES('$name', '$category', '$price', '$stock', NULL)";
} else {
    $q = "INSERT INTO mahasiswa(name, category, price, stock, image)
          VALUES('$name', '$category', '$price', '$stock', '$imageValue')";
}

if (!$kon->query($q)) {
    http_response_code(500);

    if ($imageName !== null && file_exists("uploads/" . $imageName)) {
        @unlink("uploads/" . $imageName);
    }

    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

$id = $kon->insert_id;
$kon->close();

http_response_code(201);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => $id,
        "name" => $name,
        "category" => $category,
        "price" => $price,
        "stock" => $stock,
        "image" => $imageName
    ]
]);