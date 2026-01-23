<?php 
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}


$kon = new mysqli('localhost', 'root', '', '');

if ($kon->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

$query = "SELECT id, name, category, price, stock, image FROM products ORDER BY id DESC";
$result = $kon->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    $ko->close();
    exit();
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id"       => intval($row['id']),
        "name"     => $row['name'],
        "category" => $row['category'],
        "price"    => intval($row['price']),
        "stock"    => intval($row['stock']),
        "image"    => $row['image']
    ];
}

$kon->close();


http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg"    => "Process success",
    "data"   => $data
]);