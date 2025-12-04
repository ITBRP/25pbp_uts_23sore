<?php 
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(500);
    echo json_encode(['status'=>'error','msg'=>'Server Error!']);
    exit();
}

// KONEKSI DATABASE
$koneksi = new mysqli('localhost','root','','uts_pbp');

if ($koneksi->connect_errno){
    http_response_code(500);
    echo json_encode([
        'status'=>'error',
        'msg'=>'Database connection failed'
    ]);
    exit();
}

// GET ALL DATA (SESUNGGUHNYA SESUAI DOKUMEN)
$q = "SELECT * FROM mahasiswa";
$res = $koneksi->query($q);

$data = [];
while($row = $res->fetch_assoc()){
    $data[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'category' => $row['category'],
        'price' => (int) $row['price'],
        'stock' => (int) $row['stock'],
        'image' => $row['image']
    ];
}

// RESPONSE SUKSES
echo json_encode([
    'status'=>'success',
    'msg'=>'Process success',
    'data'=>$data
]);
?>
