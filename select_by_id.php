<?php
header("Content-Type: application/json; charset=UTF-8");

// Validasi method
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error!'
    ]);
    exit();
}

// Ambil id
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Koneksi database
$koneksi = new mysqli('localhost', 'root', '', 'mahasiswa');



// Query data berdasarkan id
$q = "SELECT id, name, price, stock, image 
      FROM products 
      WHERE id = $id";

$result = $koneksi->query($q);

// Jika data ditemukan
if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'msg' => 'Process success',
        'data' => $data
    ]);
} else {
    // Jika tidak ditemukan
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
}