<?php 
// ini code untuk proses request yang formatnya formdata
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(500);
    $res = [
        'status' => 'error',
        'msg' => 'Server error !'
    ];
    echo json_encode($res);
    exit();
}

//VALIDASI PAYLOAD
$errors = [];

// name
if(!isset($_POST['name'])){
    $errors['name'] = "Name belum dikirim";
}else{
    if($_POST['name'] == ''){
        $errors['name'] = "Name tidak boleh kosong";
    }else{
        if(strlen($_POST['name']) < 3){
            $errors['name'] = "Minimal 3 karakter";
        }
    }
}

// category
$kategori_valid = ["Elektronik", "Fashion", "Makanan", "Lainnya"];
if(!isset($_POST['category'])){
    $errors['category'] = "Kategori belum dikirim";
}else{
    if(!in_array($_POST['category'], $kategori_valid)){
        $errors['category'] = "Kategori tidak valid";
    }
}

// price
if(!isset($_POST['price'])){
    $errors['price'] = "Price belum dikirim";
}else{
    if(!is_numeric($_POST['price']) || $_POST['price'] <= 0){
        $errors['price'] = "Harus berupa angka dan lebih dari 0";
    }
}

// Stock 
if(isset($_POST['stock']) && $_POST['stock'] !== ''){
    if(!is_numeric($_POST['stock']) || $_POST['stock'] < 0){
        $errors['stock'] = "Stock minimal 0";
    }else{
        $stock = (int) $_POST['stock']; // pakai nilai user
    }
}else{
    $stock = 0; // default kalau tidak dikirim
}

// Image
$anyImage = false;
$imageName = null;

if (isset($_FILES['image'])) {

    // Jika user memilih file
    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

        $allowed = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['image']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        //VALIDASI FORMAT FILE
        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = "Format file tidak valid (hanya jpg, jpeg, png)";
        } 
        //VALIDASI UKURAN
        elseif ($_FILES['image']['size'] > 3 * 1024 * 1024) {
            $errors['image'] = "Ukuran file maksimal 3MB";
        } 
        //JIKA LOLOS SEMUA VALIDASI
        else {

            if(!is_dir('uploads')){
                mkdir('uploads', 0777, true);
            }

            // Hitung jumlah file produk di folder
            $files = glob("uploads/produk*.jpg");
            $nextNumber = count($files) + 1;

            // Nama otomatis
            $imageName = "produk" . $nextNumber . ".jpg";

            $anyImage = true;
        }
    }
}

// JIKA ADA ERROR
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

// UPLOAD IMAGE
if ($anyImage) {
    move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $imageName);
}

// INSERT KE DATABASE
$koneksi = new mysqli('localhost', 'root', '', '2355201043');
$name     = $_POST['name'];
$category = $_POST['category'];
$price    = (int) $_POST['price'];

$q = "INSERT INTO db_baru(name, category, price, stock, image) 
      VALUES('$name','$category',$price,$stock,'$imageName')";

$koneksi->query($q);
$id = $koneksi->insert_id;

http_response_code(201);
echo json_encode([
    'status' => 'success',
    'msg' => 'Process success',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => (int)$price,
        'stock' => (int)$stock,
        'image' => $imageName
    ]
    ]);