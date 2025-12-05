<?php
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] != 'PUT'){
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method salah !'
    ]);
    exit();
}

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if(!isset($data['id']) || $data['id'] == ""){
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'ID tidak boleh kosong'
    ]);
    exit();
}

$id = $data['id'];
$errors = [];

if(!isset($data['name']) || $data['name'] == ""){
    $errors['name'] = "Minimal 3 karakter";
}
if(!isset($data['category']) || $data['category'] == ""){
    $errors['category'] = "Kategori tidak valid";
}
if(!isset($data['price']) || $data['price'] == ""){
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}
if(!isset($data['stock']) || $data['stock'] == ""){
    $errors['stock'] = "Stock tidak boleh kosong";
}

if(count($errors) > 0){
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data Error',
        'errors' => $errors
    ]);
    exit();
}

$name = $data['name'];
$category = $data['category'];
$price = $data['price'];
$stock = $data['stock'];

$koneksi = mysqli_connect("localhost", "root", "", "uts_be");
if(!$koneksi){
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

// **Cek apakah data dengan ID ini ada**
$cek = $koneksi->query("SELECT * FROM mahasiswa WHERE id='$id'");
if(!$cek){
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

if($cek->num_rows == 0){
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit();
}

// **Update data**
$q = "UPDATE mahasiswa SET name='$name', category='$category', price='$price', stock='$stock' WHERE id='$id'";
$update = mysqli_query($koneksi, $q);

if(!$update){
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

// **Response sukses**
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'msg' => 'Process success',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'stock' => $stock
    ]
]);
