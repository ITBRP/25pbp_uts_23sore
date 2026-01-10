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
// SELECT ALL DATA
// ==========================
$q = "SELECT id, name, category, price, stock, image FROM products";
$result = $koneksi->query($q);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id" => (int)$row['id'],
        "name" => $row['name'],
        "category" => $row['category'],
        "price" => (int)$row['price'],
        "stock" => (int)$row['stock'],
        "image" => $row['image']
    ];
}

// ==========================
// SUCCESS RESPONSE
// ==========================
http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => $data
]);

?>
