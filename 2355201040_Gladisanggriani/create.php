<?php 
header("Content-Type: application/json; charset=UTF-8");
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(405);
    $res = [
        'status' => 'ERROR',
        'msg' => 'METHOD SALAH !'
    ];
    echo json_encode($res);
    exit();
}

$errors = [];
if(!isset($_POST['name'])){
    $errors['name'] = "NAMA BELUM DIKIRIM";
}else{
    if($_POST['name']==''){
        $errors['name'] = "NAMA TIDAK BOLEH KOSONG";
    }else{
        if((strlen($_POST['name']))<3){
            $errors['name'] = "FORMAT NAME MINIMAL 3 KARAKTER";
        }
    }
}

if(!isset($_POST['category'])){
    $errors['category'] = "CATEGORY BELUM DIKIRIM";
}else{
    if($_POST['category']==''){
        $errors['category'] = "CATEGORY TIDAK BOLEH KOSONG";
    }
}

if(!isset($_POST['price'])){
    $errors['price'] = "PRICE BELUM DIKIRIM";
}else{
    if($_POST['price']==''){
        $errors['price'] = "PRICE TIDAK BOLEH KOSONG";
    }else{
        if(!is_numeric($_POST['price']) || $_POST['price']<=0){
            $errors['price'] = "PRICE HARUS ANGKA DAN LEBIH DARI 0";
        }
    }
}

if(isset($_POST['stock'])){
    if($_POST['stock']==''){
        $errors['stok'] = "STOCK TIDAK BOLEH KOSONG";
    }else{
        if(!is_numeric($_POST['stock']) || $_POST['stock']<=0){
            $errors['stok'] = "STOCK HARUS ANGKA DAN LEBIH BESAR DARI 0";
        }
    }
}

$anyPhoto = false;
$namaPhoto = null;
if (isset($_FILES['image'])) {

    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['image']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = "FILE HARUS JPG, JPEG, ATAU PNG";
        } else {
            $anyPhoto = true;
            $namaPhoto = md5(date('dmyhis')) . "." . $fileExt;
        }
    }

}

if( count($errors) > 0 ){
    http_response_code(400);
    $res = [
        'status' => 'ERROR',
        'msg' => "DATA ERROR NI!",
        'errors' => $errors
    ];

    echo json_encode($res);
    exit();
}

if ($anyPhoto) {
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $namaPhoto);
}

$koneksi = new mysqli('localhost', 'root', '', 'uts_be');
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$q = "INSERT INTO mahasiswa(name, category, price, stock, image) 
        VALUES('$name','$category', $price, $stock, '$namaPhoto')";
$koneksi->query($q);
$id = $koneksi->insert_id;

echo json_encode([
    'status' => 'success',
    'msg' => 'PROSES BERHASIL',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'stock' => $stock,
        'image' => $namaPhoto
    ]
]);