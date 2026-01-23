<?php 
// ini code untuk proses request yang formatnya formdata
header("Content-Type: application/json; charset=UTF-8");
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(405);
    $res = [
        'status' => 'Error',
        'msg' => 'Method salah!'
    ];
    echo json_encode($res);
    exit();
}

$errors = [];
if(!isset($_POST['name'])){
    $errors['name'] = "Nama belum dikirim";
}else{
    if($_POST['name']==''){
        $errors['name'] = "Nama tidak boleh kosong";
    }else{
        if((strlen($_POST['name']))<3){
            $errors['name'] = "Format nama minimal 3 karakter";
        }
    }
}

if(!isset($_POST['category'])){
    $errors['category'] = "Category belum dikirim";
}else{
    if($_POST['category']==''){
        $errors['category'] = "Category tidak boleh kosong";
    }
}

if(!isset($_POST['price'])){
    $errors['price'] = "Price belum dikirim";
}else{
    if($_POST['price']==''){
        $errors['price'] = "Price tidak boleh kosong";
    }else{
        if(!is_numeric($_POST['price']) || $_POST['price']<=0){
            $errors['price'] = "Price harus angka dan lebih besar dari 0";
        }
    }
}

if(!isset($_POST['stock'])){
    $errors['stock'] = "Stock belum dikirim";
}else{
    if($_POST['stock']==''){
        $errors['stock'] = "Stock tidak boleh kosong";
    }else{
        if(!is_numeric($_POST['stock']) || $_POST['stock']<=0){
            $errors['stock'] = "Stock harus angka dan lebih besar dari 0";
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
        'status' => 'Error',
        'msg' => "Error data!",
        'errors' => $errors
    ];

    echo json_encode($res);
    exit();
}

if ($anyPhoto) {
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $namaPhoto);
}

$koneksi = new mysqli('localhost', 'root', '', 'db_be_uts');
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$q = "INSERT INTO buku(name, category, price, stock, image) VALUES('$name','$category', $price, $stock, '$namaPhoto')";
$koneksi->query($q);
$id = $koneksi->insert_id;

http_response_code(201);

echo json_encode([
    'status' => 'Success',
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
