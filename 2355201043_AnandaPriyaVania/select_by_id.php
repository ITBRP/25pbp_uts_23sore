<?php
header("Content-Type: application/json; charset=UTF-8");

//CEK METHOD
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error!"
    ]);
    exit();
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "ID harus berupa angka"
    ]);
    exit();
}

//koneksi database
$koneksi = new mysqli('localhost', 'root', '', '2355201043');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

$q = "SELECT * FROM db_baru WHERE id = '$id'";
$result = $koneksi->query($q);

//JIKA DATA TIDAK ADA
if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit();
}

$data = $result->fetch_assoc();

http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => (int)$data['id'],
        "name" => $data['name'],
        "category" => $data['category'],
        "price" => (int)$data['price'],
        "stock" => (int)$data['stock'],
        "image" => $data['image']
    ]
]);
