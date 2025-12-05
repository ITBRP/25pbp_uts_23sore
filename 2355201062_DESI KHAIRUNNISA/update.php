<?php
header("Content-Type: application/json; charset=UTF-8");

// Cek apakah metode adalah PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendResponse(405, 'Method Salah!');
}

// Cek ID pada URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    sendResponse(400, 'ID tidak dikirim');
}

$id = intval($_GET['id']); // Pastikan ID adalah integer

// Baca input JSON dari body request
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Cek apakah JSON valid
if (!$data) {
    sendResponse(400, 'Format JSON tidak valid');
}

$errors = validateData($data); // Validasi data yang diterima

// Jika ada error, kirimkan response error
if (count($errors) > 0) {
    sendResponse(400, 'Data error', $errors);
}

// Koneksi ke database
error_reporting(0);                                                   // wajib yang lain sunah
mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = new mysqli("localhost", "root", "", "uts_desi");
if ($koneksi->connect_errno) {
    sendResponse(500, 'Server error');
}

// Cek apakah data dengan ID tersebut ada
$stmtCheck = $koneksi->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmtCheck->bind_param("i", $id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
if ($resultCheck->num_rows == 0) {
    sendResponse(404, 'Data not found');
}

$old = $resultCheck->fetch_assoc();
$oldImage = $old['image']; // Simpan gambar lama

// Update data produk
$stmtUpdate = $koneksi->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ? WHERE id = ?");
$stmtUpdate->bind_param("ssiii", $data['name'], $data['category'], $data['price'], $data['stock'], $id);
if (!$stmtUpdate->execute()) {
    sendResponse(500, 'Server error');
}

// Kirimkan response sukses
sendResponse(200, 'Process success', [
    "id" => $id,
    "name" => $data['name'],
    "category" => $data['category'],
    "price" => $data['price'],
    "stock" => $data['stock'],
    "image" => $oldImage
]);

// Fungsi untuk mengirim response JSON
function sendResponse($statusCode, $msg, $data = []) {
    http_response_code($statusCode);
    echo json_encode([
        "status" => $statusCode == 200 ? "success" : "error",
        "msg" => $msg,
        "data" => $data
    ]);
    exit;
}

// Fungsi untuk validasi data input
function validateData($data) {
    $errors = [];

    // Validasi nama
    if (!isset($data['name']) || trim($data['name']) === "") {
        $errors['name'] = 'Name tidak boleh kosong';
    } else {
        $name = trim($data['name']);
        if (strlen($name) < 3) {
            $errors['name'] = 'Minimal 3 karakter';
        }
    }

    // Validasi harga
    if (!isset($data['price']) || $data['price'] <= 0) {
        $errors['price'] = 'Harus berupa angka dan lebih dari 0';
    }

    // Validasi kategori
    $allowedCategories = ['Elektronik', 'Pakaian', 'Makanan'];
    if (!isset($data['category']) || trim($data['category']) === "") {
        $errors['category'] = 'Kategori tidak boleh kosong';
    } else {
        $category = trim($data['category']);
        if (!in_array($category, $allowedCategories)) {
            $errors['category'] = 'Kategori tidak valid';
        }
    }

    // Validasi stock
    if (!isset($data['stock']) || $data['stock'] < 0) {
        $errors['stock'] = 'Stock tidak boleh minus';
    }

    return $errors;
}
?>
