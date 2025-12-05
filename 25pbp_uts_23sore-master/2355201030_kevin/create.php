<?php 
header("Content-Type: application/json; charset=UTF-8");

// Hilangkan semua warning/notice agar JSON tetap bersih
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);

// Validasi method
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}

// VALIDASI PAYLOAD
$errors = [];

/* ---------------------------------
    VALIDASI: name
-----------------------------------*/
if (!isset($_POST['name'])) {
    $errors['name'] = "Minimal 3 karakter";
} else {
    $name = trim($_POST['name']);
    if ($name == '' || strlen($name) < 3) {
        $errors['name'] = "Minimal 3 karakter";
    }
}

/* ---------------------------------
    VALIDASI: category
-----------------------------------*/
$allowedCategory = ["Elektronik", "Fashion", "Makanan", "Lainnya"];

if (!isset($_POST['category'])) {
    $errors['category'] = "Kategori tidak valid";
} else {
    $category = trim($_POST['category']);
    if (!in_array($category, $allowedCategory)) {
        $errors['category'] = "Kategori tidak valid";
    }
}

/* ---------------------------------
    VALIDASI: price
-----------------------------------*/
if (!isset($_POST['price'])) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
} else {
    if (!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
        $errors['price'] = "Harus berupa angka dan lebih dari 0";
    }
}

/* ---------------------------------
    VALIDASI: stock (optional)
-----------------------------------*/
$stock = 0;
if (isset($_POST['stock']) && $_POST['stock'] !== "") {
    if (!is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
        $errors['stock'] = "Harus berupa angka dan minimal 0";
    } else {
        $stock = intval($_POST['stock']);
    }
}

/* ---------------------------------
    VALIDASI: image
-----------------------------------*/
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

/* ---------------------------------
    RETURN ERROR
-----------------------------------*/
if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit();
}

/* ---------------------------------
    KONEKSI DATABASE — versi aman
-----------------------------------*/

$koneksi = @new mysqli('localhost', 'root', '', 'vin_uts');

// Jika koneksi DB gagal → langsung 500
if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

/* ---------------------------------
    INSERT DATA
-----------------------------------*/

$name = $koneksi->real_escape_string($name);
$category = $koneksi->real_escape_string($category);
$price = intval($_POST['price']);
$imageValue = $imageName !== null ? $koneksi->real_escape_string($imageName) : null;

if ($imageValue === null) {
    $q = "INSERT INTO products(name, category, price, stock, image)
          VALUES('$name', '$category', '$price', '$stock', NULL)";
} else {
    $q = "INSERT INTO products(name, category, price, stock, image)
          VALUES('$name', '$category', '$price', '$stock', '$imageValue')";
}

if (!$koneksi->query($q)) {
    http_response_code(500);

    // hapus file kalau insert gagal
    if ($imageName !== null && file_exists("uploads/" . $imageName)) {
        @unlink("uploads/" . $imageName);
    }

    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

$id = $koneksi->insert_id;
$koneksi->close();

/* ---------------------------------
    RESPONSE SUKSES
-----------------------------------*/
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