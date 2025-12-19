<?php
header("Content-Type: application/json; charset=UTF-8");

// Validasi method
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}

// Validasi Payload
$errors = [];

// Validasi untuk nama

if (!isset($_POST['name'])) {
    $errors['name'] = "Nama Tidak Dikirim";
} else {
    if ($_POST['name'] == '') {
        $errors['name'] = "Name tidak boleh kosong";
    } else {
        if ((strlen($_POST['name'])) < 3) {
            $errors['name'] = "Minimal 3 karakter";
        }
    }
}

// Valid category
$ct = ["Elektronik", "Fashion", "Makanan", "Lainnya"];

if (!isset($_POST['category'])) {
    $errors['category'] = "Kategori tidak dikirim";
} else {
    $cat = trim($_POST['category']);
    if (!in_array($cat, $ct)) {
        $errors['category'] = "Kategori tidak valid";
    }
}

//price
if (!isset($_POST['price'])) {
    $errors['price'] = "Price tidak dikirim";
} else {
    if (!is_numeric($_POST['price']) && $_POST['price'] <= 0) {
        $errors['price'] = "Harus berupa angka dan lebih dari 0";
    }
}

//Validasi stock

if(isset($_POST['stock'])){
    if($_POST['stock']==''){
        $errors['stok'] = "Stock tidak boleh kosong";
    }else{
        if(!is_numeric($_POST['stock']) && $_POST['stock']<=0){
            $errors['stok'] = "Stock harus angka dan lebih besar dari 0";
        }
    }
}

//Validasi Image
$anyphoto = false;
$namafoto = null;

if (isset($_FILES['image'])) {

    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allow = ['jpg', 'jpeg', 'png'];
        $filenamed = $_FILES['image']['name'];
        $Extfile = strtolower(pathinfo($filenamed, PATHINFO_EXTENSION));

        $nomor = count($allow) + 1 ;

        if (!in_array($Extfile, $allow)) {
            $errors['image'] = "File harus jpg, jpeg atau png";
        } else {
            $anyphoto = true;
            $namafoto = "Foto_" . $nomor . "." . $Extfile;
        }
    }
}

//Error Response 400
if( count($errors) > 0 ){
    http_response_code(400);
    $res = [
        'status' => 'error',
        'msg' => "Error data",
        'errors' => $errors
    ];

    echo json_encode($res);
    exit();
}

if ($anyphoto) {
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $namafoto);
}

//Koneksi & Insert Database
$koneksi = new mysqli('localhost', 'root', '', 'be');
// Error Response 500
if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

$name     = $_POST['name'];
$category = $_POST['category'];
$price    = $_POST['price'];
$stock    = $_POST['stock'];

$q = "INSERT INTO products(name, category, price, stock, image)
      VALUES('$name','$category', $price, $stock, '$namafoto')";

$koneksi->query($q);
$id = $koneksi->insert_id;


//Response Success 201
http_response_code(201);
echo json_encode([
    "status" => "success",
    "msg" => "Process success",
    "data" => [
        "id" => $id,
        "name" => $name,
        "category" => $category,
        "price" => $price,
        "stock" => $stock,
        "image" => $namafoto
    ]
]);
