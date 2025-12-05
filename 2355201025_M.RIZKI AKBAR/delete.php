<?php
header("Content-Type: application/json; charset=UTF-8");

// deteksi delete <<php tidak bisa baca  delete secara langsung>>
$_METHOD = $_SERVER['REQUEST_METHOD'];
if ($_METHOD === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
    $_METHOD = "DELETE";
}

if ($_METHOD !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit;
}

// cek id parameter
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'ID tidak dikirim'
    ]);
    exit;
}
$id = intval($_GET['id']);

//koneksi database..........................................................23xxxxxx25
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli("localhost", "root", "", "uts_backend");

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// cek data lama
$cek = $koneksi->query("SELECT * FROM products WHERE id=$id LIMIT 1");
if ($cek->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit;
}

$oldData = $cek->fetch_assoc();
$oldImage = $oldData['image'];

//hapus data dan file pada gambarrrrrrrrrrrrrrrrrrrrrrrrrr
$q = "DELETE FROM products WHERE id=$id";
if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// menghapus file gambar jika ada.......................................................2355201025
if ($oldImage && file_exists("img/" . $oldImage)) {
    unlink("img/" . $oldImage);
}

// respons
echo json_encode([
    "status" => "success",
    "msg" => "Data berhasil dihapus",
    "data" => [
        "id" => $id
        // "name" => $oldData['name'],
        // "category" => $oldData['category'],
        // "price" => $oldData['price'],
        // "stock" => $oldData['stock'],
        // "image" => $oldImage
    ]
]);
//2355201025
?>
