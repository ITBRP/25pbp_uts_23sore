<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") { 
    http_response_code(405);
    echo json_encode(["status"=>"error","msg"=>"Method salah!"]);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status"=>"error","msg"=>"ID belum dikirim"]);
    exit();
}

$id = $_GET['id'];

// VALIDASI FIELD (sama seperti create)
$errors = [];
$validCat = ['Elektronik','Fashion','Makanan','Lainnya'];

if (!isset($_POST['name']) || strlen($_POST['name']) < 3) {
    $errors['name'] = "Minimal 3 karakter";
}

if (!isset($_POST['category']) || !in_array($_POST['category'], $validCat)) {
    $errors['category'] = "Kategori tidak valid";
}

if (!isset($_POST['price']) || !is_numeric($_POST['price']) || $_POST['price'] <= 0) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}

if (isset($_POST['stock']) && (!is_numeric($_POST['stock']) || $_POST['stock'] < 0)) {
    $errors['stock'] = "Stock harus angka minimal 0";
}

// VALIDASI GAMBAR
$anyImage = false;
$imageName = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $allowed = ['jpg','jpeg','png'];
    $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowed)) {
        $errors['image'] = "Format file tidak valid";
    } else {
        $anyImage = true;
        $imageName = md5(time()).".".$fileExt;
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["status"=>"error","msg"=>"Data error","errors"=>$errors]);
    exit();
}

$koneksi = new mysqli("localhost","root","","uts_be");

// cek data ada atau tidak
$cek = $koneksi->query("SELECT * FROM data_barang WHERE id='$id'");
if ($cek->num_rows == 0) {
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data not found"]);
    exit();
}

// upload gambar jika ada
if ($anyImage) {
    move_uploaded_file($_FILES['image']['tmp_name'], __DIR__."/img/".$imageName);
}

$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'] ?? 0;

$q = "UPDATE data_barang SET 
        name='$name',
        category='$category',
        price='$price',
        stock='$stock' " .
        ($anyImage ? ", image='$imageName'" : "") .
     " WHERE id='$id'";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Server error"]);
    exit();
}

http_response_code(200);
echo json_encode([
    "status"=>"success",
    "msg"=>"Process success",
    "data"=>[
        "id"=>$id,
        "name"=>$name,
        "category"=>$category,
        "price"=>(int)$price,
        "stock"=>(int)$stock,
        "image"=>$anyImage ? $imageName : $cek->fetch_assoc()['image']
    ]
]);