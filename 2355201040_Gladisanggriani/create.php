<?php 
header("Content-Type: application/json; charset=UTF-8");
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(405);
    $res = [
        'status' => 'error',
        'msg' => 'Method salah !'
    ];
    echo json_encode($res);
    exit();
}

// validasi payload
$errors = [];
if(!isset($_POST['name'])){
    $errors['name'] = "Minimal 3 karakter";
}else{
    if($_POST['name']==''){
        $errors['name'] = "Minimal 3 karakter";
    }else{
        if(strlen($_POST['name']) < 3){
            $errors['name'] = "Minimal 3 karakter";
        }
    }
}

if(!isset($_POST['category'])){
    $errors['category'] = "Kategori tidak valid";
}else{
    if($_POST['category']==''){
        $errors['category'] = "Kategori tidak valid";
    }else{
        $kategoriList = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];
        if(!in_array($_POST['category'], $kategoriList)){
            $errors['category'] = "Kategori tidak valid";
        }
    }
}

if(!isset($_POST['price'])){
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}else{
    if($_POST['price']==''){
        $errors['price'] = "Harus berupa angka dan lebih dari 0";
    }else{
        if(!is_numeric($_POST['price']) || $_POST['price'] <= 0){
            $errors['price'] = "Harus berupa angka dan lebih dari 0";
        }
    }
}

if(isset($_POST['stock'])){
    if($_POST['stock'] !== '' && (!is_numeric($_POST['stock']) || $_POST['stock'] < 0)){
        $errors['stock'] = "Harus angka, minimal 0";
    }
}

$anyImage = false;
$namaImage = null;
if (isset($_FILES['image'])) {

    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $extList = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['image']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $extList)) {
            $errors['image'] = "Format file tidak valid (jpg, jpeg, png)";
        } else {
            $anyImage = true;
            $namaImage = md5(date('dmyhis')) . "." . $fileExt;
        }
    }
}

if( count($errors) > 0 ){
    http_response_code(400);
    $res = [
        'status' => 'error',
        'msg' => "Data error",
        'errors' => $errors
    ];
    echo json_encode($res);
    exit();
}

if ($anyImage) {
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $namaImage);
}

// insert ke db
$koneksi = new mysqli('localhost', 'root', '', 'uts_be');
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'] ?? 0;

$q = "INSERT INTO mahasiswa(name, category, price, stock, image) VALUES('$name','$category','$price','$stock','$namaImage')";
$koneksi->query($q);
$id = $koneksi->insert_id;

$res = [
    'status' => 'success',
    'msg' => 'Process success',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => (int)$price,
        'stock' => (int)$stock,
        'image' => $namaImage
    ]
];
http_response_code(201);
echo json_encode($res);
