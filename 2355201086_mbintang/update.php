<?php
header("Content-Type: application/json; charset=UTF-8");

// DETEKSI METHOD PUT
$method = $_SERVER['REQUEST_METHOD'];

// Jika method POST + _method=PUT → anggap PUT
if ($method == 'POST' && isset($_POST['_method']) && $_POST['_method'] == 'PUT') {
    $method = 'PUT';
}

if ($method !== 'PUT') {
    http_response_code(505);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

/* =====================
   AMBIL DATA DARI POST
   ===================== */
$errors = [];

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    $errors['id'] = "ID tidak valid";
} 
$id = intval($_POST['id']);

// Validasi name
if (!isset($_POST['name']) || trim($_POST['name']) == '') {
    $errors['name'] = "Nama tidak boleh kosong";
} elseif (strlen($_POST['name']) < 3) {
    $errors['name'] = "Nama minimal 3 karakter";
}

// Validasi category
$allowedCategory = ['Elektronik','Fashion','Makanan','Lainnya'];
if (!isset($_POST['category']) || !in_array($_POST['category'], $allowedCategory)) {
    $errors['category'] = "Category tidak valid";
}

// Validasi price
if (!isset($_POST['price']) || !is_numeric($_POST['price']) || $_POST['price'] <= 0) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}

// Validasi stock
if (!isset($_POST['stock']) || !is_numeric($_POST['stock']) || $_POST['stock'] <= 0) {
    $errors['stock'] = "Stock harus angka dan > 0";
}

/* ==== CEK DATABASE ==== */
$koneksi = new mysqli('localhost','root','','uts_pbp');
$cek = $koneksi->query("SELECT * FROM mhs WHERE id=$id");

if ($cek->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit();
}

$oldData = $cek->fetch_assoc();
$namaPhoto = $oldData['image'];

/* =====================
   UPLOAD FILE (OPSIONAL)
   ===================== */

if (isset($_FILES['image'])) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

        $allowed = ['jpg','jpeg','png'];
        $fileName = $_FILES['image']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = "Format file tidak valid (jpg, jpeg, png)";
        } else {
            // Hapus file lama
            if (file_exists("img/".$oldData['image'])) {
                unlink("img/".$oldData['image']);
            }

            // Upload file baru
            $namaPhoto = md5(time()) . "." . $fileExt;
            move_uploaded_file($_FILES['image']['tmp_name'], "img/".$namaPhoto);
        }
    }
}

/* Jika ada error */
if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Error data',
        'errors' => $errors
    ]);
    exit();
}

/* =====================
   UPDATE DATABASE
   ===================== */

$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$q = "
UPDATE mhs 
SET name='$name', category='$category', price='$price', stock='$stock', image='$namaPhoto'
WHERE id=$id
";

$koneksi->query($q);

/* =====================
   RESPONSE
   ===================== */

echo json_encode([
    'status' => 'success',
    'msg' => 'Update berhasil',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'stock' => $stock,
        'image' => $namaPhoto
    ]
]);
?>