<?php
header("Content-Type: application/json; charset=UTF-8");


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit;
}


$koneksi = new mysqli("localhost", "root", "", "uts_pbp");

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}


$q = "SELECT * FROM data_buku ORDER BY id ASC";
$result = $koneksi->query($q);

$data = [];

if ($result && $result->num_rows > 0) {
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
?>