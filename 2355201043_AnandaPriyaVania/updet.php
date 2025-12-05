<?php
header("Content-Type: application/json; charset=UTF-8");

// method
if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "ID tidak dikirim"
    ]);
    exit();
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "ID harus berupa angka"
    ]);
    exit();
}

// koneksi database
$koneksi = new mysqli("localhost", "root", "", "2355201043");

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

// cek data
$cek = $koneksi->query("SELECT * FROM db_baru WHERE id='$id'");

if ($cek->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit();
}

$data_lama = $cek->fetch_assoc();

// ambil data
$data = [];
parse_str(file_get_contents("php://input"), $data);

// validasi data
$errors = [];

// name
if (!isset($data['name']) || $data['name'] == '') {
    $errors['name'] = "Name tidak boleh kosong";
} elseif (strlen($data['name']) < 3) {
    $errors['name'] = "Minimal 3 karakter";
}

// category
$kategori_valid = ["Elektronik", "Fashion", "Makanan", "Lainnya"];
if (!isset($data['category'])) {
    $errors['category'] = "Kategori wajib diisi";
} elseif (!in_array($data['category'], $kategori_valid)) {
    $errors['category'] = "Kategori tidak valid";
}

// price
if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}

// stock (optional)
if (isset($data['stock'])) {
    if (!is_numeric($data['stock']) || $data['stock'] < 0) {
        $errors['stock'] = "Stock minimal 0";
    }
    $stock = $data['stock'];
} else {
    $stock = $data_lama['stock'];
}

// errornya
if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit();
}

// updet data
$name     = $data['name'];
$category = $data['category'];
$price    = $data['price'];
$image    = $data_lama['image']; // tetap pakai image lama

$q = "UPDATE db_baru SET 
        name='$name',
        category='$category',
        price='$price',
        stock='$stock'
      WHERE id='$id'";

$update = $koneksi->query($q);

if (!$update) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

// respon sukses
http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => (int)$id,
        "name" => $name,
        "category" => $category,
        "price" => (int)$price,
        "stock" => (int)$stock,
        "image" => $image
    ]
]);
