<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit();
}

if (!isset($_GET['id']) || $_GET['id'] == '') {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit();
}

$id = $_GET['id'];

$koneksi = new mysqli('localhost', 'root', '', 'uts_be');

// cek koneksi
if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

$q = "SELECT * FROM data_barang WHERE id=$id";
$dtQuery = mysqli_query($koneksi, $q);

if (!$dtQuery) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

if (mysqli_num_rows($dtQuery) == 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit();
}

$row = mysqli_fetch_assoc($dtQuery);
if ($row['image'] != null && file_exists('img/' . $row['image'])) {
    unlink('img/' . $row['image']);
}

// delete data
$q = "DELETE FROM data_barang WHERE id=$id";
$del = mysqli_query($koneksi, $q);

if (!$del) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'msg' => 'Delete data success',
    'data' => [
        'id' => (int)$id
    ]
]);