<?php
header("Content-Type: application/json; charset=UTF-8");

// Hanya DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method Salah!"
    ]);
    exit;
}

// Cek ID
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

// Cek apakah data ada
$result = $koneksi->query("SELECT * FROM products WHERE id = $id");
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit;
}

// Hapus data
if (!$koneksi->query("DELETE FROM products WHERE id = $id")) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// RESPONSE SUCCESS
http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Delete data success",
    "data" => [
        "id" => $id
    ]
]);
?>
