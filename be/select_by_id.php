<?php 
header("Content-Type: application/json; charset=UTF-8");

/* ---------------------------------
    VALIDASI METHOD
-----------------------------------*/
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Server Eror!"
    ]);
    exit();
}

/* ---------------------------------
    VALIDASI PARAMETER ID
-----------------------------------*/
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit();
}

$id = intval($_GET['id']);

/* ---------------------------------
    KONEKSI DATABASE (DIUBAH JADI 'pbp')
-----------------------------------*/
$koneksi = new mysqli('localhost', 'root', '', 'pbp');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

/* ---------------------------------
    QUERY: SELECT BY ID
-----------------------------------*/
$q = "SELECT id, name, category, price, stock, image 
      FROM produk WHERE id = $id LIMIT 1";

$result = $koneksi->query($q);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    $koneksi->close();
    exit();
}

/* ---------------------------------
    CEK DATA ADA ATAU TIDAK
-----------------------------------*/
if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    $koneksi->close();
    exit();
}

$row = $result->fetch_assoc();
$koneksi->close();

/* ---------------------------------
    RESPONSE SUKSES
-----------------------------------*/
http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id"       => intval($row['id']),
        "name"     => $row['name'],
        "category" => $row['category'],
        "price"    => intval($row['price']),
        "stock"    => intval($row['stock']),
        "image"    => $row['image']
    ]
]);
