<?php 
header("Content-Type: application/json; charset=UTF-8");

// Cek Method
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error !'
    ]);
    exit();
}

// =====================
// VALIDASI INPUT
// =====================
$errors = [];

// Validasi name
if(!isset($_POST['name'])){
    $errors['name'] = "name wajib dikirim";
}else{
    if($_POST['name']==''){
        $errors['name'] = "name tidak boleh kosong";
    }else if(strlen($_POST['name']) < 2){
        $errors['name'] = "name minimal 2 karakter!";
    }
}

// Validasi category
if(!isset($_POST['category'])){
    $errors['category'] = "category wajib dikirim";
}else{
    $category = $_POST['category'];
    $allowedCategory = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];

    if(!in_array($category, $allowedCategory)){
        $errors['category'] = "category harus: Elektronik, Fashion, Makanan, Lainnya";
    }
}

// Validasi price
if(!isset($_POST['price'])){
    $errors['price'] = "price wajib dikirim";
}else{
    $price = trim($_POST['price']);
    if($price == ''){
        $errors['price'] = "price tidak boleh kosong";
    }else if(!preg_match('/^[0-9]+$/', $price)){
        $errors['price'] = "price harus angka!";
    }
}

// Validasi stock
if(!isset($_POST['stock'])){
    $errors['stock'] = "stock wajib dikirim";
}else{
    $stock = trim($_POST['stock']);
    if($stock == ''){
        $errors['stock'] = "stock tidak boleh kosong";
    }else if(!preg_match('/^[0-9]+$/', $stock)){
        $errors['stock'] = "stock harus angka!";
    }
}

// =====================
// VALIDASI GAMBAR
// =====================
$anyPhoto = false;
$imageName = null;

if(isset($_FILES['image'])){
    if($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){

        $allowed = ['jpg','jpeg','png'];
        $fileName = $_FILES['image']['name'];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(!in_array($extension, $allowed)){
            $errors['image'] = "image harus jpg, jpeg atau png";
        }else{
            $anyPhoto = true;
            $imageName = md5(time()) . "." . $extension;
        }

    }
}

// Jika ada error
if(count($errors) > 0){
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data error',
        'errors' => $errors
    ]);
    exit();
}

// =====================
// PROSES INSERT
// =====================

$koneksi = new mysqli('localhost', 'root', '', 'mahasiswa');

// simpan foto jika ada
if($anyPhoto){
    if(!file_exists("img")){
        mkdir("img", 0777, true);
    }
    move_uploaded_file($_FILES['image']['tmp_name'], "img/" . $imageName);
}

// ambil data
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'];

// query insert ke tabel products
$q = "INSERT INTO products(name, category, price, stock, image) 
      VALUES ('$name', '$category', '$price', '$stock', " . 
      ($imageName ? "'$imageName'" : "NULL") . ")";

$koneksi->query($q);
$id = $koneksi->insert_id;

// ======================
// RESPONSE
// ======================
http_response_code(201);
echo json_encode([
    'status' => "success",
    'msg' => "Process success",
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'stock' => $stock,
        'image' => $imageName
    ]
]);
?>
