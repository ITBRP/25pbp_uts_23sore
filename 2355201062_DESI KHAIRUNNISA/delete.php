<?php
header("Content-Type: application/json; charset=UTF-8");

// Deteksi metode DELETE
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

// Cek ID parameter
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'ID tidak dikirim'
    ]);
    exit;
}
$id = intval($_GET['id']);

// Koneksi database
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli("localhost", "root", "", "uts_desi");

if ($koneksi->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// Cek data lama
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

// Hapus data
$q = "DELETE FROM products WHERE id=$id";
if (!$koneksi->query($q)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit;
}

// Hapus file gambar jika ada
if ($oldImage && file_exists("img/" . $oldImage)) {
    unlink("img/" . $oldImage);
}

// Respons sukses
echo json_encode([
    "status" => "success",
    "msg" => "Data berhasil dihapus",
    "data" => [
        "id" => $id
    ]
]);
?>
