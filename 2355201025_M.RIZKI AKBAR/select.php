<?php
header("Content-Type: application/json; charset=UTF-8");

// method check
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit;
}

// koneksi database
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

// query
$q = "SELECT * FROM products ORDER BY id DESC";
$result = $koneksi->query($q);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id"       => (int)$row['id'],
            "name"     => $row['name'],
            "category" => $row['category'],
            "price"    => (int)$row['price'],
            "stock"    => (int)$row['stock'],
            "image"    => $row['image']
        ];
    }

    echo json_encode([
        "status" => "success",
        "msg" => "Process success",
        "data" => $data
    ]);
    exit;

} else {
    echo json_encode([
        "status" => "success",
        "msg" => "Tidak ada data",
        "data" => []
    ]);
    exit;
}
// 2355201025
?>


