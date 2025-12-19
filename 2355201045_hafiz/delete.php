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

$koneksi = new mysqli('localhost', 'root', '', 'uts');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit;
}

// NULL jika tidak upload file
$id = $_GET['id'];
$q = "SELECT * FROM data_buku WHERE id=$id";
$dtQuery = mysqli_query($koneksi, $q);

if (!$dtQuery) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit;
}

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

$q = "DELETE FROM data_buku WHERE id=$id";
if (!mysqli_query($koneksi, $q)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit;
}


$q = "DELETE FROM data_buku WHERE id=$id";
mysqli_query($koneksi, $q);
echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    "data"   => [
        "id" => $id
    ]
]);
