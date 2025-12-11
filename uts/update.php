<?php
header("Content-Type: application/json; charset=UTF-8");
$method = $_SERVER["REQUEST_METHOD"];
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
}

if ($method !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah!'
    ]);
    exit;
}

// validasi payload
$errors = [];
if(!isset($_POST['name'])){
    $errors['name'] = "name belum dikirim";
}else{
    if($_POST['name']==''){
        $errors['name'] = "name tidak boleh kosong";
    }else{
        if((strlen($_POST['name']))<3){
            $errors['name'] = "Format name minimal 3 karakter";
        }
    }
}

if(!isset($_POST['category'])){
    $errors['category'] = "category belum dikirim";
}else{
    if($_POST['category']==''){
        $errors['category'] = "category tidak boleh kosong";
    }
}

if(!isset($_POST['price'])){
    $errors['price'] = "price belum dikirim";
}else{
    if($_POST['price']==''){
        $errors['price'] = "price tidak boleh kosong";
    }else{
        if(!is_numeric($_POST['price']) && $_POST['price']<=0){
            $errors['price'] = "Price harus angka dan lebih besar dari 0";
        }
    }
}

if(isset($_POST['stock'])){
    if($_POST['stock']==''){
        $errors['stok'] = "stock tidak boleh kosong";
    }else{
        if(!is_numeric($_POST['stock']) && $_POST['stock']<=0){
            $errors['stok'] = "stock harus angka dan lebih besar dari 0";
        }
    }
}

$anyPhoto = false;
$namaPhoto = null;
if (isset($_FILES['image'])) {

    // User memilih file
    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['image']['name']; //namaaslifile.JPEG, docx
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // hasilnya jadi jpeg

        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = "File harus jpg, jpeg atau png";
        } else {
            $anyPhoto = true; // photo valid, siap disave
            $namaPhoto = md5(date('dmyhis')) . "." . $fileExt; // fjsadlfjiajflsdjflsadkjfsad.jpeg
        }
    }

}

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

if ($anyPhoto) {
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $namaPhoto);
}

// insert ke db
$koneksi = new mysqli('localhost', 'root', '', 'uts_pbpsore');
$nim = $_POST['name'];
$nama = $_POST['category'];
$nama = $_POST['price'];
$nama = $_POST['stock'];
$id = $_GET['id'];
$q = "UPDATE buku SET
        name = '$name',
        category = '$category',
        price = $price,
        stock = $stock,
        image = " . ($namPhotoe ? "'$namaPhoto'" : NULL) . "
      WHERE id = $id";


$koneksi->query($q);

echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'stock' => $stock,
        'image' => $namaPhoto
    ]
]);
