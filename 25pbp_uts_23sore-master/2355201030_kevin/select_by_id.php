<?php 
header("Content-Type: application/json; charset=UTF-8");



if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}




if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit();
}

$id = intval($_GET['id']);


$kon = new mysqli('localhost', 'root', '', '');

if ($kon->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}



$query = "SELECT id, name, category, price, stock, image 
      FROM products WHERE id = $id LIMIT 1";

$result = $kon->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    $kon->close();
    exit();
}


if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    $kon->close();
    exit();
}

$row = $result->fetch_assoc();
$kon->close();



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