<?php
header("Content-Type: application/json; charset=UTF-8");


if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$errors = [];

if(!isset($data['name'])){
    $errors['name'] = "name harus diinput";
}else{
    if($data['name']==''){
        $errors['name'] = "silahkan isi name";
    }else if(strlen($data['name']) < 3){
        $errors['name'] = "name harus memiliki minimal 3 karakter!";
    }
}

if (!isset($data['category'])) {
    $errors['category'] = "Silahkan mengisi category";
} else {
    if ($data['category'] != "Elektronik" && $data['category'] != "Fashion" && $data['category'] != "Makanan" && $data['category'] != "Lainnya") {
        $errors['category'] = "Category hanya diisi dengan Elektronik, Fashion, Makanan, dan Lainnya";
    }
}

if (!isset($data['price'])) {
    $errors['price'] = "price belum dikirim";
} else {
    if ($data['price'] === '') {
        $errors['price'] = "price tidak boleh kosong";
    } else {
        if (!is_numeric($data['price'])) {
            $errors['price'] = "price harus berupa angka";
        } else if ($data['price'] < 0) {
            $errors['price'] = "price harus minimal 0";
        }
    }
}

$stock = isset($data['stock']) && $data['stock'] !== ''
    ? $data['stock']
    : 0;

if (!is_numeric($stock) || $stock < 0) {
    $errors['stock'] = "stock harus angka dan minimal 0";
}


if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "errors" => $errors
    ]);
    exit;
}


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$koneksi = new mysqli("localhost", "root", "", "uts_pbp");
$cek = $koneksi->query("SELECT * FROM items WHERE id=$id");
if (!$cek || $cek->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit;
}

$q = "
    UPDATE items SET
        name = '".$koneksi->real_escape_string($data['name'])."',
        category = '".$koneksi->real_escape_string($data['category'])."',
        price = '".$koneksi->real_escape_string($data['price'])."',
        stock = '".$koneksi->real_escape_string($data['stock'])."',
        image = '".($data['photo'] ?? "")."'
    WHERE id = $id
";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

http_response_code(200);

echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => (int) $id,
        "name" => $data['name'],
        "price" => (int) $data['price'],
        "stock" => (int) $data['stock'],
        "photo" => $data['photo'] ?? null
    ]
]);