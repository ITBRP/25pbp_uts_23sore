<?php
mysqli_report(MYSQLI_REPORT_OFF);
header("Content-Type: application/json; charset=UTF-8");


if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["status"=>"error","msg"=>"Method salah!"]);
    exit();
}


if (!isset($_GET['id']) || $_GET['id'] === "") {
    http_response_code(400);
    echo json_encode(["status"=>"error","msg"=>"ID belum dikirim"]);
    exit();
}
$id = $_GET['id'];


$koneksi = new mysqli("localhost","root","","uts_be");


$check = $koneksi->query("SELECT * FROM data_barang WHERE id='$id'");


if ($check === false) {
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Server error"]);
    exit();
}


if ($check->num_rows == 0) {
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data not found"]);
    exit();
}

$del = $koneksi->query("DELETE FROM data_barang WHERE id='$id'");


if ($del === false) {
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Server error"]);
    exit();
}


http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg"    => "Delete data success",
    "data"   => [
        "id" => (int)$id
    ]
]);