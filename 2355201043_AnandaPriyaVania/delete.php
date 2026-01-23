<?php
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0); // Mematikan error teks agar JSON tetap bersih

// 1. Koneksi & Cek Error 500
$koneksi = new mysqli('localhost', 'root', '', '2355201043');
if ($koneksi->connect_error) {
    http_response_code(500);
    exit(json_encode(["status" => "error", "msg" => "Server error"]));
}

// 2. Ambil ID & Cari Data
$id = $_GET['id'] ?? null;
$data = $koneksi->query("SELECT image FROM db_baru WHERE id = '$id'")->fetch_assoc();

// 3. Cek Error 404
if (!$id || !$data) {
    http_response_code(404);
    exit(json_encode(["status" => "error", "msg" => "Data not found"]));
}

// 4. Hapus File Gambar di Folder (Jika ada)
if ($data['image'] && file_exists("uploads/" . $data['image'])) {
    unlink("uploads/" . $data['image']);
}

// 5. Eksekusi Hapus di Database
if ($koneksi->query("DELETE FROM db_baru WHERE id = '$id'")) {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "msg"    => "Delete data success",
        "data"   => ["id" => (int)$id]
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Server error"]);
}

$koneksi->close();