<?php
header("Content-Type: application/json; charset=UTF-8");

// Method check
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit;
}

// Pastikan ID dikirim
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'ID tidak dikirim'
    ]);
    exit;
}

$id = intval($_GET['id']);

// Koneksi database
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = new mysqli("localhost", "root", "", "uts_backend");

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// Query get by id
$q = "SELECT * FROM products WHERE id = $id LIMIT 1";
$result = $koneksi->query($q);

// Jika query gagal
if (!$result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// Jika data tidak ditemukan
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit;
}

// Ambil data
$row = $result->fetch_assoc();

// Response success
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => (int)$row['id'],
        "name" => $row['name'],
        "category" => $row['category'],
        "price" => (int)$row['price'],
        "stock" => (int)$row['stock'],
        "image" => $row['image']
    ]
]);
// 2355201025
?>
