<?php 
header("Content-Type: application/json; charset=UTF-8");

/* ---------------------------------
    VALIDASI METHOD
-----------------------------------*/
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "msg" => "Method salah!"
    ]);
    exit();
}

/* ---------------------------------
    BACA INPUT (JSON / URLENCODE / QUERY)
-----------------------------------*/
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

// Jika JSON tidak valid, pakai parse_str (urlencoded)
if (!is_array($input)) {
    parse_str($raw, $input);
}

// Jika masih kosong, coba dari query param
if ((!isset($input['id']) || $input['id'] === "") && isset($_GET['id'])) {
    $input['id'] = $_GET['id'];
}

/* ---------------------------------
    VALIDASI ID
-----------------------------------*/
$errors = [];

if (!isset($input['id']) || $input['id'] === "") {
    $errors['id'] = "ID tidak boleh kosong";
} else {
    $id = intval($input['id']);
    if ($id <= 0) {
        $errors['id'] = "ID tidak valid";
    }
}

/* ---------------------------------
    RETURN ERROR VALIDASI
-----------------------------------*/
if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit();
}

/* ---------------------------------
    KONEKSI DB
-----------------------------------*/
$koneksi = new mysqli('localhost', 'root', '', 'be');

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

/* ---------------------------------
    CEK DATA ADA ATAU TIDAK
-----------------------------------*/
$id = $koneksi->real_escape_string($id);

$cek = $koneksi->query("SELECT id FROM products WHERE id = $id");

if ($cek->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "msg" => "Data not found"
    ]);
    exit();
}

/* ---------------------------------
    DELETE DATA
-----------------------------------*/
$delete = $koneksi->query("DELETE FROM products WHERE id = $id");

if (!$delete) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "msg" => "Server error"
    ]);
    exit();
}

/* ---------------------------------
    RESPONSE SUKSES
-----------------------------------*/
http_response_code(200);
echo json_encode([
    "status" => "success",
    "msg" => "Delete data success",
    "data" => [
        "id" => $id
    ]
]);
