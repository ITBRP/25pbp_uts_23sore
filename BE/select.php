<?php
header("Content-Type: application/json; charset=UTF-8");

// Hanya GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(500);
    echo json_encode(['status'=>'error','msg'=>'Server Error!']);
    exit();
}

// Koneksi DB
$koneksi = new mysqli('localhost','root','','uts_pbp');

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode(['status'=>'error','msg'=>'Database connection failed']);
    exit();
}

// Ambil semua data
$res = $koneksi->query("SELECT * FROM mahasiswa");

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = array_map(fn($v) => is_numeric($v) ? (int)$v : $v, $row);
}

// Response
echo json_encode([
    'status' => 'success',
    'msg'    => 'Process success',
    'data'   => $data
]);
?>
