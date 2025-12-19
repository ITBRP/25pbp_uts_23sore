<?php
header("Content-Type: application/json; charset=UTF-8");


if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data Not Found'
    ]);
    exit();
}

$errors = [];


if(!isset($_POST['name'])){
    $errors['name'] = "Field name belum dikirim";
}else{
    if($_POST['name'] == ''){
        $errors['name'] = "Field name wajib diisi";
    }else{
        if(strlen($_POST['name']) < 3){
            $errors['name'] = "Nama minimal terdiri dari 3 karakter";
        }
    }
}


if(!isset($_POST['category'])){
    $errors['category'] = "Field category belum dikirim";
}else{
    if($_POST['category'] == ''){
        $errors['category'] = "Category tidak boleh kosong";
    }
}


if(!isset($_POST['price'])){
    $errors['price'] = "Field price belum dikirim";
}else{
    if($_POST['price'] == ''){
        $errors['price'] = "Price wajib diisi";
    }else{
        if(!is_numeric($_POST['price']) || $_POST['price'] <= 0){
            $errors['price'] = "Price harus berupa angka lebih dari 0";
        }
    }
}


if(isset($_POST['stock'])){
    if($_POST['stock'] == ''){
        $errors['stock'] = "Stock tidak boleh kosong";
    }else{
        if(!is_numeric($_POST['stock']) || $_POST['stock'] <= 0){
            $errors['stock'] = "Stock harus berupa angka positif";
        }
    }
}


$anyPhoto = false;
$namaPhoto = null;

if(isset($_FILES['image'])){
   
    if($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
        $allowedExt = ['jpg','jpeg','png'];
        $originalName = $_FILES['image']['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if(!in_array($extension, $allowedExt)){
            $errors['image'] = "Format gambar tidak didukung";
        }else{
            $anyPhoto = true;
            $namaPhoto = md5(date('YmdHis')) . '.' . $extension;
        }
    }
}


if(count($errors) > 0){
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Validasi gagal',
        'errors' => $errors
    ]);
    exit();
}


if($anyPhoto){
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $namaPhoto);
}


$koneksi = new mysqli('localhost', 'root', '', 'pbp');

$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$query = "INSERT INTO produk (name, category, price, stock, image)
          VALUES ('$name', '$category', $price, $stock, '$namaPhoto')";

$koneksi->query($query);
$id = $koneksi->insert_id;


echo json_encode([
    'status' => 'success',
    'msg' => 'Process success',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'stock' => $stock,
        'image' => $namaPhoto
    ]
]);
