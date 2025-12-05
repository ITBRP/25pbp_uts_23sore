<?php
header("Content-Type: application/json; charset=UTF-8");

// Validasi metode
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "server error!"
    ]);
    exit();
}

// Koneksi database
$koneksi = new mysqli('localhost', 'root', '', 'buku_data');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

// Query GET ALL
$q = "SELECT * FROM only";
$result = $koneksi->query($q);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => intval($row['id']),
            "name" => $row['name'],
            "category" => $row['category'],
            "price" => intval($row['price']),
            "stock" => intval($row['stock']),
            "image" => $row['image'] // boleh null
        ];
    }
}

$koneksi->close();

// Response sukses
http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => $data
]);
