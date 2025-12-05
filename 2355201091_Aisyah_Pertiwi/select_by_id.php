<?php
require "db.php";

// Cek metode
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    apiResponse(405, "Method salah!");
}

$id = $_GET['id'] ?? 0;

// Validasi ID
if ($id == 0) {
    apiResponse(400, "ID tidak valid");
}

// Query data
$sql = mysqli_prepare($koneksi, "SELECT * FROM buku WHERE id = ?");
mysqli_stmt_bind_param($sql, "i", $id);
mysqli_stmt_execute($sql);
$result = mysqli_stmt_get_result($sql);

// Data tidak ditemukan
if (mysqli_num_rows($result) == 0) {
    apiResponse(404, "Data tidak ditemukan");
}

// Berhasil
$data = mysqli_fetch_assoc($result);

apiResponse(200, "Process success", $data);
?>
