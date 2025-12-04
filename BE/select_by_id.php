<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    echo json_encode(['status'=>'error','msg'=>'Server Error!']);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['status'=>'error','msg'=>'ID tidak valid']);
    exit();
}

$id = $_GET['id'];

$koneksi = new mysqli('localhost','root','','uts_pbp');

if ($koneksi->connect_errno) {
    echo json_encode(['status'=>'error','msg'=>'Database connection failed']);
    exit();
}

$q = "SELECT * FROM mahasiswa WHERE id = $id LIMIT 1";
$res = $koneksi->query($q);

// Ambil data langsung
$data = $res->fetch_assoc();

// Jika null berarti tidak ada data
if (!$data) {
    echo json_encode(['status'=>'error','msg'=>'Data not found']);
    exit();
}

echo json_encode([
    'status' => 'success',
    'msg'    => 'Process success',
    'data'   => [
        'id'       => (int)$data['id'],
        'name'     => $data['name'],
        'category' => $data['category'],
        'price'    => (int)$data['price'],
        'stock'    => (int)$data['stock'],
        'image'    => $data['image']
    ]
]);
?>
