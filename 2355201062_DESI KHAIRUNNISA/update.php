<?php
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER["REQUEST_METHOD"];
if (isset($_SERVER['method'])) {
    $method = $_SERVER['method'];
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'ERROR',
        'msg' => 'METHOD SALAH!'
    ]);
    exit;
}

// validasi payload
$errors = [];
if(!isset($_POST['name'])){
    $errors['name'] = "NAMA BELUM DIKIRIM";
}else{
    if($_POST['name']==''){
        $errors['name'] = "NAMA TAK BOLEH KOSONG";
    }else{
        if((strlen($_POST['name']))<3){
            $errors['name'] = "FORMAT NAMA MINIMAL 3 KARAKTER";
        }
    }
}

if(!isset($_POST['category'])){
    $errors['category'] = "CATEGORY BELUM DIKIRIM";
}else{
    $category = $_POST['category'];
    // Misalkan kategori yang valid hanya 3 pilihan, seperti Elektronik, Fashion, dan Makanan
    $validCategories = ['Elektronik', 'Fashion', 'Makanan'];
    if (!in_array($category, $validCategories)) {
        $errors['category'] = "Kategori tidak valid";
    }
}

if(!isset($_POST['price'])){
    $errors['price'] = "PRICE BELUM DIKIIRIM";
}else{
    if($_POST['price']==''){
        $errors['price'] = "PRICE TIDAK BOLEH KOSONG";
    }else{
        if(!is_numeric($_POST['price']) || $_POST['price']<=0){
            $errors['price'] = "PRICE HARUS ANGKA DAN LEBIH BESAR DARI 0";
        }
    }
}

if(isset($_POST['stock'])){
    if($_POST['stock']==''){
        $errors['stok'] = "STOCK TIDAK BOLEH KOSONG";
    }else{
        if(!is_numeric($_POST['stock']) || $_POST['stock']<=0){
            $errors['stok'] = "STOCK HARUS ANGKA DAN LEBIH DARI 0";
        }
    }
}

$anyPhoto = false;
$namaPhoto = null;
if (isset($_FILES['image'])) {

    // User memilih file
    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['image']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // hasilnya jadi jpeg

        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = "FILE HARUS JPG, JPEG, ATAU PNG";
        } else {
            $anyPhoto = true; // photo valid, siap disave
            $namaPhoto = md5(date('dmyhis')) . "." . $fileExt; // fjsadlfjiajflsdjflsadkjfsad.jpeg
        }
    }

}

if( count($errors) > 0 ){
    http_response_code(400);
    $res = [
        'status' => 'ERROR',
        'msg' => "DATA ERROR",
        'errors' => $errors
    ];

    echo json_encode($res);
    exit();
}

if ($anyPhoto) {
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $namaPhoto);
}

// insert ke db
$koneksi = new mysqli('localhost', 'root', '', 'data_buku');
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$id = $_GET['id'];
$q = "UPDATE mahasiswa SET
        name = '$name',
        category = '$category',
        price = $price,
        stock = $stock,
        image = " . ($namaPhoto !== null ? "'$namaPhoto'" : "image") . "
      WHERE id = $id";


$koneksi->query($q);

$id = $_GET['id'];
$cek = $koneksi->query("SELECT id FROM data_buku WHERE id = $id");
$data = mysqli_fetch_assoc($cek);

if(!$data){
    http_response_code(404);
    echo json_encode([
        'status' => 'ERROR',
        'msg' => 'DATA NOT FOUND'
    ]);
    exit();
}
 
echo json_encode([
    'status' => 'SUCCESS',
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
