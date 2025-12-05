<?php
header("Content-Type: application/json; charset=UTF-8");



// Hanya izinkan metode PUT untuk endpoint ini
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["status" => "error", "msg" => "Metode tidak diperbolehkan"]);
    exit();
}

// ============================
// Mengambil ID dari query string
// ============================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Parameter ID tidak valid"]);
    exit();
}
$id = intval($_GET['id']);

// ============================
// Pemrosesan multipart form-data secara manual
// ============================
$raw = file_get_contents("php://input");

preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
$boundary = $matches[1];

$blocks = preg_split("/-+$boundary/", $raw);
array_pop($blocks);

$fields = [];
$fileData = null;
$fileName = null;

foreach ($blocks as $block) {
    if (empty(trim($block))) continue;

    if (preg_match('/name="([^"]*)"/', $block, $m)) {
        $name = $m[1];

        // Jika bagian ini berisi file
        if (preg_match('/filename="([^"]*)"/', $block, $f)) {
            $fileName = $f[1];

            preg_match("/Content-Type: (.*?)(\r\n|\n)/", $block, $typeMatch);
            $fileType = trim($typeMatch[1]);

            $fileContent = preg_split("/\r\n\r\n|\n\n/", $block, 2)[1];
            $fileContent = substr($fileContent, 0, strlen($fileContent) - 2);

            $fileData = [
                "name" => $fileName,
                "type" => $fileType,
                "content" => $fileContent
            ];
        } else {
            // Jika hanya field teks biasa
            $value = preg_split("/\r\n\r\n|\n\n/", $block, 2)[1];
            $value = substr($value, 0, strlen($value) - 2);
            $fields[$name] = $value;
        }
    }
}

// Menangkap input dari field
$name = trim($fields['name'] ?? '');
$category = trim($fields['category'] ?? '');
$price = $fields['price'] ?? '';
$stock = $fields['stock'] ?? null;

// ============================
// Bagian pengecekan input
// ============================
$errors = [];

// Validasi nama produk
if ($name === '' || strlen($name) < 3) {
    $errors['name'] = "Nama minimal 3 karakter";
}

// Validasi kategori harus sesuai daftar yang diizinkan
$allowedCategory = ["Elektronik", "Fashion", "Makanan", "Lainnya"];
if (!in_array($category, $allowedCategory)) {
    $errors['category'] = "Kategori tidak tersedia";
}

// Validasi harga
if (!is_numeric($price) || intval($price) <= 0) {
    $errors['price'] = "Harga harus berupa angka dan lebih dari nol";
}
$price = intval($price);

// Validasi stok (boleh kosong)
if ($stock !== null && (!is_numeric($stock) || intval($stock) < 0)) {
    $errors['stock'] = "Stok harus angka dan minimal 0";
}
$stock = $stock !== null ? intval($stock) : null;

// Validasi file gambar (jika ada)
$newImageName = null;
if ($fileData !== null) {
    $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ["jpg","jpeg","png"])) {
        $errors['image'] = "Jenis file tidak didukung";
    } elseif (strlen($fileData['content']) > 3 * 1024 * 1024) {
        $errors['image'] = "Ukuran file melebihi 3MB";
    }
}

// Jika ada error input, hentikan proses
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Validasi gagal",
        "errors" => $errors
    ]);
    exit();
}

// ============================
// Koneksi database
// ============================
$conn = new mysqli("localhost", "root", "", "be");
if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Gagal terhubung ke server"]);
    exit();
}

$conn->set_charset("utf8mb4");

// Mengecek apakah data exist berdasarkan ID
$q = $conn->prepare("SELECT image FROM products WHERE id=?");
$q->bind_param("i", $id);
$q->execute();
$r = $q->get_result();

if ($r->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data tidak ditemukan"]);
    exit();
}

$oldImage = $r->fetch_assoc()['image'];

// Pengolahan gambar baru bila dikirimkan
if ($fileData !== null) {
    $newImageName = time() . "_" . $fileData['name'];
    file_put_contents("uploads/" . $newImageName, $fileData['content']);
    @unlink("uploads/" . $oldImage);
} else {
    $newImageName = $oldImage;
}

// Melakukan update ke tabel
$u = $conn->prepare("UPDATE products SET name=?, category=?, price=?, stock=?, image=? WHERE id=?");
$u->bind_param("ssdisi", $name, $category, $price, $stock, $newImageName, $id);
$ok = $u->execute();

if (!$ok) {
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Terjadi kesalahan pada server"]);
    exit();
}

// Jika semua proses berhasil
echo json_encode([
    "status"=>"success",
    "msg"=>"Perubahan berhasil disimpan",
    "data"=>[
        "id"=>$id,
        "name"=>$name,
        "category"=>$category,
        "price"=>$price,
        "stock"=>$stock,
        "image"=>$newImageName
    ]
]);
