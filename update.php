<?php
header("Content-Type: application/json; charset=UTF-8");

// HARUS PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// KONEKSI DB
$koneksi = new mysqli("localhost", "root", "", "mahasiswa");

// CEK ID DULU (PENTING!)
$cek = $koneksi->query("SELECT * FROM products WHERE id = $id");

if (!$cek || $cek->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit;
}

// SETELAH ID VALID → BARU AMBIL DATA PUT
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// VALIDASI FIELD
$errors = [];

// --- VALIDASI NAME ---
if (!isset($data['name'])) {
    $errors['name'] = "name wajib dikirim";
} else if ($data['name'] == "") {
    $errors['name'] = "name tidak boleh kosong";
}

// --- VALIDASI CATEGORY ---
$allowedCat = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];

if (!isset($data['category'])) {
    $errors['category'] = "category wajib dikirim";
} else if (!in_array($data['category'], $allowedCat)) {
    $errors['category'] = "category harus Elektronik/Fashion/Makanan/Lainnya";
}

// --- VALIDASI PRICE ---
if (!isset($data['price'])) {
    $errors['price'] = "price wajib dikirim";
} else {
    $price = trim($data['price']);
    if ($price == '') {
        $errors['price'] = "price tidak boleh kosong";
    } elseif (!preg_match('/^[0-9]+$/', $price) || (int)$price <= 0) {
        $errors['price'] = "price harus angka dan > 0";
    }
}

// --- VALIDASI STOCK ---
if (!isset($data['stock'])) {
    $errors['stock'] = "stock wajib dikirim";
} else {
    $stock = trim($data['stock']);
    if ($stock == '') {
        $errors['stock'] = "stock tidak boleh kosong";
    } elseif (!preg_match('/^[0-9]+$/', $stock) || (int)$stock < 0) {
        $errors['stock'] = "stock harus angka dan ≥ 0";
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit;
}

// UPDATE DATA
$q = "
    UPDATE products SET
        name = '".$koneksi->real_escape_string($data['name'])."',
        category = '".$koneksi->real_escape_string($data['category'])."',
        price = '".$koneksi->real_escape_string($data['price'])."',
        stock = '".$koneksi->real_escape_string($data['stock'])."'
    WHERE id = $id
";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error saat update"
    ]);
    exit;
}

// RESPONSE SUCCESS
http_response_code(200);

echo json_encode([
    "status" => "success",
    "msg" => "Update success",
    "data" => [
        "id" => (int)$id,
        "name" => $data['name'],
        "category" => $data['category'],
        "price" => (int)$data['price'],
        "stock" => (int)$data['stock']
    ]
]);
