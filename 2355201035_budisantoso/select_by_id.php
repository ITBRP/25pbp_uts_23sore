<?php
header("Content-Type: application/json; charset=UTF-8");
mysqli_report(MYSQLI_REPORT_OFF);

// Wajib pakai GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "msg" => "Method salah!"]);
    exit();
}

// Validasi ID
if (!isset($_GET['id']) || $_GET['id'] === "") {
    http_response_code(404);
    echo json_encode(["status" => "error", "msg" => "Data not found"]);
    exit();
}

$id = $_GET['id'];

$koneksi = new mysqli("localhost", "root", "", "uts_be");

// Jika koneksi error  500
if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Server error"]);
    exit();
}


$q = "SELECT * FROM data_barang_salah WHERE id = '$id'";



$res = $koneksi->query($q);

// Jika query salah 500
if (!$res) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Server error"]);
    exit();
}

// Jika tidak ada data 404
if ($res->num_rows == 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "msg" => "Data not found"]);
    exit();
}

// Jika data ditemukan
$row = $res->fetch_assoc();

http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg"    => "Process success",
    "data"   => [
        "id"       => (int) $row["id"],
        "name"     => $row["name"],
        "category" => $row["category"],
        "price"    => (int) $row["price"],
        "stock"    => (int) $row["stock"],
        "image"    => $row["image"]
    ]
]);
