<?php
require "db.php";

function send($code, $msg, $data = null){
    http_response_code($code);
    echo json_encode([
        "code" => $code,
        "message" => $msg,
        "data" => $data
    ]);
    exit;
}

$nama     = trim($_POST['nama'] ?? '');
$kategori = trim($_POST['category'] ?? '');
$harga    = $_POST['price'] ?? 0;
$stok     = $_POST['stock'] ?? 0;

$validasi = [];

// validasi nama
if(strlen($nama) < 3){
    $validasi['nama'] = "Nama minimal 3 huruf";
}

// validasi kategori
$kategori_valid = ['Elektronik','Fashion','Makanan','Lainnya'];
if(!in_array($kategori, $kategori_valid)){
    $validasi['category'] = "Kategori tidak tersedia";
}

// validasi harga
if(!is_numeric($harga) || $harga <= 0){
    $validasi['price'] = "Harga harus lebih dari 0";
}

// validasi stok
if(!is_numeric($stok) || $stok < 0){
    $validasi['stock'] = "Stok tidak valid";
}

// validasi gambar
$gambar = null;
if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
    $tipe = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $tipe = strtolower($tipe);

    if(!in_array($tipe, ['jpg','jpeg','png'])){
        $validasi['image'] = "Format gambar salah";
    }

    if($_FILES['image']['size'] > 3000000){
        $validasi['image'] = "Ukuran maksimal 3MB";
    }

    if(empty($validasi)){
        $gambar = uniqid() . '.' . $tipe;
        move_uploaded_file($_FILES['image']['tmp_name'], "upload/" . $gambar);
    }
}

if(!empty($validasi)){
    send(400, "Validasi gagal", $validasi);
}

$sql = "INSERT INTO buku (nama, category, price, stock, image) VALUES (?, ?, ?, ?, ?)";
$query = $koneksi->prepare($sql);

if(!$query){
    send(500, "Query gagal disiapkan");
}

$query->bind_param("ssiis", $nama, $kategori, $harga, $stok, $gambar);

if($query->execute()){
    send(201, "Data berhasil disimpan", [
        "id" => $query->insert_id,
        "nama" => $nama,
        "category" => $kategori,
        "price" => (int)$harga,
        "stock" => (int)$stok,
        "image" => $gambar
    ]);
}

send(500, "Gagal menyimpan data");
