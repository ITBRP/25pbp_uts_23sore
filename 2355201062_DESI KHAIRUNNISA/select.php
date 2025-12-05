<?php
header("Content-Type: application/json; charset=UTF-8");

// Method check
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Metode Salah!'
    ]);
    exit;
}

// Database connection
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);

$connection = new mysqli("localhost", "root", "", "uts_desi");

if ($connection->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// Query
$sqlQuery = "SELECT * FROM products ORDER BY id DESC";
$queryResult = $connection->query($sqlQuery);

$productData = [];

if ($queryResult && $queryResult->num_rows > 0) {
    while ($product = $queryResult->fetch_assoc()) {
        $productData[] = [
            "product_id"   => (int)$product['id'],
            "product_name" => $product['name'],
            "product_category" => $product['category'],
            "product_price"  => (int)$product['price'],
            "product_stock"  => (int)$product['stock'],
            "product_image"  => $product['image']
        ];
    }

    echo json_encode([
        "status" => "success",
        "msg" => "Data retrieved successfully",
        "data" => $productData
    ]);
    exit;
} else {
    echo json_encode([
        "status" => "success",
        "msg" => "No data available",
        "data" => []
    ]);
    exit;
}
