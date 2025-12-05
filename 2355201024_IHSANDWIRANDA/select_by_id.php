<?php
header("Content-Type: application/json; charset=UTF-8");

// Hanya mengizinkan GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method Salah!"
    ]);
    exit;
}

// Cek apakah parameter id dikirim
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "ID tidak valid"
    ]);
    exit;
}

$id = (int)$_GET['id'];

// ==========================
// DATABASE
// ==========================
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = new mysqli("localhost", "root", "", "uts_github");

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// ==========================
// SELECT DATA BY ID
// ==========================
$stmt = $koneksi->prepare("SELECT id, name, category, price, stock, image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit;
}

$row = $result->fetch_assoc();

// ==========================
// SUCCESS RESPONSE
// ==========================
http_response_code(200);
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

?>
