<?php
header("Content-Type: application/json; charset=UTF-8");

// Hanya boleh POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(500);
    echo json_encode(['status'=>'error','msg'=>'Server Error!']);
    exit();
}

$errors = [];

// Helper untuk validasi angka
function validNumber($val){ return isset($val) && is_numeric($val) && $val > 0; }

// VALIDASI
if (empty($_POST['name']) || strlen($_POST['name']) < 3)
    $errors['name'] = "Nama minimal 3 karakter";

$allowedCategory = ['Elektronik','Fashion','Makanan','Lainnya'];
if (!isset($_POST['category']) || !in_array($_POST['category'], $allowedCategory))
    $errors['category'] = "Kategori tidak valid";

if (!validNumber($_POST['price']))
    $errors['price'] = "Price harus angka > 0";

if (!validNumber($_POST['stock']))
    $errors['stock'] = "Stock harus angka > 0";

// VALIDASI IMAGE
if (!isset($_FILES['image']) || $_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
    $errors['image'] = "Image wajib diupload";
} else {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png'])) {
        $errors['image'] = "Format harus jpg, jpeg, png";
    } else {
        $imageName = md5(time()).".".$ext;
    }
}

// Jika ada error
if ($errors) {
    http_response_code(400);
    echo json_encode(['status'=>'error','msg'=>'Error data','errors'=>$errors]);
    exit();
}

// Upload file
move_uploaded_file($_FILES['image']['tmp_name'], "img/".$imageName);

// INSERT
$koneksi = new mysqli('localhost','root','','uts_pbp');
$stmt = $koneksi->prepare("INSERT INTO mahasiswa(name,category,price,stock,image) VALUES (?,?,?,?,?)");
$stmt->bind_param("ssdds", $_POST['name'], $_POST['category'], $_POST['price'], $_POST['stock'], $imageName);
$stmt->execute();
$id = $stmt->insert_id;

// Response
echo json_encode([
    'status'=>'success',
    'msg'=>'Proses berhasil',
    'data'=>[
        'id'       => $id,
        'name'     => $_POST['name'],
        'category' => $_POST['category'],
        'price'    => $_POST['price'],
        'stock'    => $_POST['stock'],
        'image'    => $imageName
    ]
]);
?>
