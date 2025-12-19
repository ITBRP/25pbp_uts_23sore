<?php
header("Content-Type: application/json; charset=UTF-8");
// validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit;
}

error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli('localhost', 'root', '', 'uts_backend');

// NULL jika tidak upload file
$id = $_GET['id'];
$q = "SELECT * FROM products WHERE id=$id";
$dtQuery = mysqli_query($koneksi, $q);

if(mysqli_num_rows($dtQuery)==0){
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit;
}else{
    $imageLama = (mysqli_fetch_array($dtQuery))['image'];
    unlink('img/'.$imageLama);
}

$q = "DELETE FROM products WHERE id=$id";
mysqli_query($koneksi, $q);
echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => [
            'id' => (int)$id
        ]
]);
