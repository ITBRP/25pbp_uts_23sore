<?php
// 1. Set Header JSON
header("Content-Type: application/json; charset=UTF-8");

// 2. Koneksi dan Query
$koneksi = new mysqli('localhost', 'root', '', '2355201043');
$result = !$koneksi->connect_error ? 
$koneksi->query("SELECT * FROM db_baru") : false;

// 3. Cek hasil: Jika sukses tampilkan 200, jika gagal tampilkan 500
if ($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['id']    = (int)$row['id'];
        $row['price'] = (int)$row['price'];
        $row['stock'] = (int)$row['stock'];
        $data[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "msg"    => "Process success",
        "data"   => $data
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg"    => "Server error"
    ]);

}

$koneksi->close();
?>