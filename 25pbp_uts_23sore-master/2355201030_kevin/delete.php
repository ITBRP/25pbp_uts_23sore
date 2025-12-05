<?php 
header("Content-Type: application/json; charset=UTF-8");


error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);



if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}


 

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!is_array($input)) {
    parse_str($raw, $input);
}


if ((!isset($input['id']) || $input['id'] === "") && isset($_GET['id'])) {
    $input['id'] = $_GET['id'];
}


 

$errors = [];

if (!isset($input['id']) || $input['id'] === "") {
    $errors['id'] = "ID tidak boleh kosong";
} else {
    $id = intval($input['id']);
    if ($id <= 0) {
        $errors['id'] = "ID tidak valid";
    }
}




if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit();
}


$kon = new mysqli('localhost', 'root', '', '2');

if ($kon->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}


$id = $kon->real_escape_string($id);

$cek = $kon->query("SELECT id FROM products WHERE id = $id");

if ($cek->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit();
}



$delete = $kon->query("DELETE FROM products WHERE id = $id");

if (!$delete) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}




http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Delete data success",
    "data" => [
        "id" => $id
    ]
]);