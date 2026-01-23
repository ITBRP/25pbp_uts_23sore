<?php 
// 1. Set Header JSON
header("Content-Type: application/json; charset=UTF-8");

// 2. Cek Method Request
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method salah !'
    ]);
    exit();
}

// 3. Validasi Payload 
$errors = [];

// Validasi Name
if(!isset($_POST['name']) || $_POST['name'] == ''){
    $errors['name'] = "Name tidak boleh kosong";
} else if(strlen($_POST['name']) < 3){
    $errors['name'] = "Minimal 3 karakter";
}

// Validasi Category
$valid_categories = ['Elektronik', 'Fashion', 'Makanan', 'Lainnya'];
if(!isset($_POST['category']) || $_POST['category'] == ''){
    $errors['category'] = "Kategori belum dikirim";
} else if(!in_array($_POST['category'], $valid_categories)){
    $errors['category'] = "Kategori tidak valid";
}

// Validasi Price
if(!isset($_POST['price']) || $_POST['price'] == ''){
    $errors['price'] = "Price belum dikirim";
} else if(!is_numeric($_POST['price']) || $_POST['price'] <= 0){
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}

// Validasi Stock (Optional)
$stock = isset($_POST['stock']) ? $_POST['stock'] : 0;
if(isset($_POST['stock']) && $_POST['stock'] !== ''){
    if(!is_numeric($_POST['stock']) || $_POST['stock'] < 0){
        $errors['stock'] = "Harus angka, minimal 0";
    }
}

// 4. Validasi Image
$anyPhoto = false;
$namaPhoto = null;
$targetDir = "uploads/"; 

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $fileName = $_FILES['image']['name'];
    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $fileSize = $_FILES['image']['size'];

    if (!in_array($fileExt, $allowed)) {
        $errors['image'] = "Format file tidak valid (hanya jpg, jpeg, png)";
    } else if ($fileSize > 3 * 1024 * 1024) {
        $errors['image'] = "Ukuran file terlalu besar (max 3MB)";
    } else {
        $anyPhoto = true;
        
        // Logika penamaan berurutan (produk1, produk2, dst)
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true); 
        
        $files = glob($targetDir . "produk*." . $fileExt); 
        $count = count($files) + 1; 
        $namaPhoto = "produk" . $count . "." . $fileExt; 
    }
}

// 5. Cek Jika Ada Error
if(count($errors) > 0){
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => "Data error",
        'errors' => $errors
    ]);
    exit();
}

// 6. Proses Upload File
if ($anyPhoto) {
    move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $namaPhoto);
}

// 7. Insert ke Database (Menggunakan database & tabel Anda)
$koneksi = new mysqli('localhost', 'root', '', '2355201043');

$name     = $_POST['name'];
$category = $_POST['category'];
$price    = $_POST['price'];
$stock    = $_POST['stock'] ?? 0;

$q = "INSERT INTO db_baru (name, category, price, stock, image) 
      VALUES ('$name', '$category', '$price', '$stock', '$namaPhoto')";

if($koneksi->query($q)){
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
            'image' => $namaPhoto
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
}
?>