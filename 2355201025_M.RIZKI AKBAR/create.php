<?php 
// ini code untuk proses request yang formatnya formdata
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
        if(!is_numeric($_POST['price']) || $_POST['price']<=0){
            $errors['price'] = "Price harus angka dan lebih besar dari 0";
        }
    }
}

if(isset($_POST['stock'])){
    if($_POST['stock']==''){
        $errors['stock'] = "stock tidak boleh kosong";
    }else{
        if(!is_numeric($_POST['stock']) || $_POST['stock']<=0){
            $errors['stock'] = "stock harus angka dan lebih besar dari 0";
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
            $errors['image'] = "File harus jpg, jpeg atau png";
        } else {
            $anyPhoto = true;
            $namaPhoto = md5(date('dmyhis')) . "." . $fileExt;
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

// ================== PENYESUAIAN DATABASE ==================
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli('localhost', 'root', '', 'uts_backend');

if ($koneksi->connect_error) {
    http_response_code(500);
    $res = [
        'status' => 'error',
        'msg' => 'Server error'
    ];
    echo json_encode($res);
    exit();
}

$name     = $_POST['name'];
$category = $_POST['category'];
$price    = $_POST['price'];
$stock    = $_POST['stock'];

$q = "INSERT INTO products (name, category, price, stock, image)
      VALUES ('$name', '$category', $price, $stock, '$namaPhoto')";

if (!$koneksi->query($q)) {
    http_response_code(500);
    $res = [
        'status' => 'error',
        'msg' => 'Server error'
    ];
    echo json_encode($res);
    exit();
}

$id = $koneksi->insert_id;



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
