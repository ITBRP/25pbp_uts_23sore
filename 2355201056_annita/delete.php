<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'status' => 'Error',
        'msg' => 'Method salah!'
    ]);
    exit;
}

$koneksi = new mysqli('localhost', 'root', '', 'db_be_uts');

$id = $_GET['id'];
$q = "SELECT * FROM buku WHERE id=$id";
$dtQuery = mysqli_query($koneksi, $q);

if (mysqli_num_rows($dtQuery) == 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'Error',
        'msg' => 'Data not found!'
    ]);
    exit();
} else {
    $imageLama = (mysqli_fetch_array($dtQuery))['image'];
    if ($imageLama && file_exists('img/' . $imageLama)) {
        unlink('img/' . $imageLama);
    }
}


$q = "DELETE FROM buku WHERE id=$id";
mysqli_query($koneksi, $q);
echo json_encode([
    'status' => 'Success',
    'msg' => 'Proses berhasil',
    'data' => [
        'id' => $id,
    ]
]);