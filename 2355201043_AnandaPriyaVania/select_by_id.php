<?php
header("Content-Type: application/json; charset=UTF-8");

// 1. Ambil ID dari parameter URL Query (?id=1)
$id = isset($_GET['id']) ? $_GET['id'] : null;

// 2. Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', '2355201043');
$result = ($id && !$koneksi->connect_error)
    ? $koneksi->query("SELECT * FROM db_baru WHERE id = '$id'")
    : false;

if ($result) {
    if ($result->num_rows > 0) {
        // Response Success (200)
        $row = $result->fetch_assoc();
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "msg"    => "Process success",
            "data"   => [
                "id"       => (int)$row['id'],
                "name"     => $row['name'],
                "category" => $row['category'],
                "price"    => (int)$row['price'],
                "stock"    => (int)$row['stock'],
                "image"    => $row['image']
            ]
        ]);
    } else {
        // Response Error (404)
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "msg"    => "Data not found"
        ]);
    }
} else {
    // Response Error (500)
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg"    => "Server error"
    ]);
    
}

$koneksi->close();
