<?php
header('Content-Type: application/json');

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "uts_pbp");

// Hanya izinkan GET
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Server error'
    ]);
    exit();
}

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg"    => "Data not found"
    ]);
    exit();
}

$id = intval($_GET['id']);

// Gunakan prepared statement untuk keamanan
$stmt = $mysqli->prepare("DELETE FROM mhs WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    // Jika ada data yang terhapus
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "msg"    => "Delete data success",
            "data"   => [
                "id" => $id
            ]
        ]);
    } else {
        // Jika ID tidak ditemukan → 404
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "msg"    => "Data not found"
        ]);
    }

} else {
    // Query error → 500
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg"    => "Server error"
    ]);
}

$stmt->close();
$mysqli->close();
?>