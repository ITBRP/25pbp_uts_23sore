<?php
require "db.php";

// Cek metode
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    apiResponse(405, "Method salah!");
}

$id = $_GET['id'] ?? 0;

// Validasi ID
if ($id == 0) {
    apiResponse(400, "ID tidak valid");
}

// Cek apakah data ada
$cek = mysqli_query($koneksi, "SELECT * FROM buku WHERE id='$id'");

if (mysqli_num_rows($cek) == 0) {
    apiResponse(404, "Data tidak ditemukan");
}

// Hapus data
$hapus = mysqli_query($koneksi, "DELETE FROM buku WHERE id='$id'");

if ($hapus) {
    apiResponse(200, "Delete data success", [
        "id" => $id
    ]);
}

apiResponse(500, "Server error");
?>
