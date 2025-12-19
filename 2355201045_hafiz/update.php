<?php
header("Content-Type: application/json; charset=UTF-8");
$method = $_SERVER["REQUEST_METHOD"];
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
}

if ($method !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah!'
    ]);
    exit;
}
$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit($id)) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit;
}
$errors = [];

$name     = $_POST['name']     ?? null;
$category = $_POST['category'] ?? null;
$price    = $_POST['price']    ?? null;
$stock    = $_POST['stock']    ?? null;

if (!isset($name)) {
    $errors['name'] = "name belum dikirim";
} elseif ($name === '') {
    $errors['name'] = "name tidak boleh kosong";
} elseif (strlen($name) < 3) {
    $errors['name'] = "Format name minimal 3 karakter";
}

$valid = ["Elektronik", "Fashion", "Makanan", "Lainnya"];
if (!isset($category)) {
    $errors['category'] = "category belum dikirim";
} elseif ($category === '') {
    $errors['category'] = "category tidak boleh kosong";
} elseif (!in_array($category, $valid)) {
    $errors['category'] = "kategori tidak valid";
}

if (!isset($price)) {
    $errors['price'] = "price belum dikirim";
} elseif ($price === '') {
    $errors['price'] = "price tidak boleh kosong";
} elseif (!is_numeric($price) || $price <= 0) {
    $errors['price'] = "Price harus angka dan lebih besar dari 0";
}

if (isset($stock)) {
    if ($stock === '') {
        $errors['stock'] = "stock tidak boleh kosong";
    } elseif (!is_numeric($stock) || $stock <= 0) {
        $errors['stock'] = "stock harus angka dan lebih besar dari 0";
    }
}

$imageBaru = null;
$fileExt   = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors['image'] = "Terjadi kesalahan upload file";
    } else {
        $allowed = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            $errors['image'] = "File harus jpg, jpeg atau png";
        }
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Error data',
        'errors' => $errors
    ]);
    exit;
}

$koneksi = new mysqli('localhost', 'root', '', 'uts');

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit;
}

$res = $koneksi->query("SELECT image FROM data_buku WHERE id = $id");

if (!$res || $res->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data not found'
    ]);
    exit;
}

$row = $res->fetch_assoc();
$imageLama = $row['image'];


if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

    $imageBaru = md5(uniqid()) . "." . $fileExt;
    move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $imageBaru);

    if (!empty($imageLama) && file_exists('img/' . $imageLama)) {
        unlink('img/' . $imageLama);
    }

} else {
    $imageBaru = $imageLama;
}

$q = "UPDATE data_buku SET
        name = '$name',
        category = '$category',
        price = $price,
        stock = $stock,
        image = " . ($imageBaru ? "'$imageBaru'" : "NULL") . "
      WHERE id = $id";

if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error',
        'sql_error' => $koneksi->error
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'stock' => $stock,
        'image' => $imageBaru
    ]
]);
