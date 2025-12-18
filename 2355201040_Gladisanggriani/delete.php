<?php
header("Content-Type: application/json; charset=UTF-8");
// validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'status' => 'ERROR',
        'msg' => 'METHOD SALAH !'
    ]);
    exit;
}

$koneksi = new mysqli('localhost', 'root', '', 'uts_be');

// NULL jika tidak upload file
$id = $_GET['id'];
$q = "SELECT * FROM mahasiswa WHERE id=$id";
$dtQuery = mysqli_query($koneksi, $q);

if(mysqli_num_rows($dtQuery)==0){
    http_response_code(404);
    echo json_encode([
        'status' => 'ERROR',
        'msg' => 'DATA NOT FOUND'
    ]);
    exit;
}else{
    $imageLama = (mysqli_fetch_array($dtQuery))['image'];
    unlink('img/'.$imageLama);
}

$q = "DELETE FROM mahasiswa WHERE id=$id";
mysqli_query($koneksi, $q);

echo json_encode([
    'status' => 'success',
    'msg' => 'DELETE DATA BERHASIL',
    'data' => [
        'id' => $id
    ]
]);