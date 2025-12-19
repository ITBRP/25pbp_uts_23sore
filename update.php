<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["status" => "error", "msg" => "Method not allowed"]);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "ID tidak valid"]);
    exit();
}
$id = intval($_GET['id']);

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

        if (preg_match('/filename="([^"]*)"/', $block, $f)) {
            
            $fileName = $f[1];

            preg_match("/Content-Type: (.*?)(\r\n|\n)/", $block, $typeMatch);
            $fileType = trim($typeMatch[1]);

            $fileContent = preg_split("/\r\n\r\n|\n\n/", $block, 2)[1];
            $fileContent = substr($fileContent, 0, strlen($fileContent) - 2);

            $fileData = ["name" => $fileName, "type" => $fileType, "content" => $fileContent];
        } else {
            
            $value = preg_split("/\r\n\r\n|\n\n/", $block, 2)[1];
            $value = substr($value, 0, strlen($value) - 2);
            $fields[$name] = $value;
        }
    }
}

$name = trim($fields['name'] ?? '');
$category = trim($fields['category'] ?? '');
$price = $fields['price'] ?? '';
$stock = $fields['stock'] ?? null;

$errors = [];

if ($name === '' || strlen($name) < 3) {
    $errors['name'] = "Minimal 3 karakter";
}

$allowedCategory = ["Elektronik", "Fashion", "Makanan", "Lainnya"];
if (!in_array($category, $allowedCategory)) {
    $errors['category'] = "Kategori tidak valid";
}

if (!is_numeric($price) || intval($price) <= 0) {
    $errors['price'] = "Harus berupa angka dan lebih dari 0";
}
$price = intval($price);

if ($stock !== null && (!is_numeric($stock) || intval($stock) < 0)) {
    $errors['stock'] = "Harus berupa angka dan minimal 0";
}
$stock = $stock !== null ? intval($stock) : null;

$newImageName = null;
if ($fileData !== null) {
    $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ["jpg","jpeg","png"])) {
        $errors['image'] = "Format file tidak valid";
    } elseif (strlen($fileData['content']) > 3 * 1024 * 1024) {
        $errors['image'] = "Ukuran maksimal 3MB";
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "msg" => "Data error",
        "errors" => $errors
    ]);
    exit();
}

$conn = new mysqli("localhost", "root", "", "UTS_pbp");
if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Server error"]);
    exit();
}

$conn->set_charset("utf8mb4");

$q = $conn->prepare("SELECT image FROM mahasiswa WHERE id=?");
$q->bind_param("i", $id);
$q->execute();
$r = $q->get_result();

if ($r->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status"=>"error","msg"=>"Data not found"]);
    exit();
}

$oldImage = $r->fetch_assoc()['image'];

if ($fileData !== null) {
    $newImageName = time() . "_" . $fileData['name'];
    file_put_contents("uploads/" . $newImageName, $fileData['content']);
    @unlink("uploads/" . $oldImage);
} else {
    $newImageName = $oldImage;
}



$u = $conn->prepare("UPDATE mahasiswa SET name=?, category=?, price=?, stock=?, image=? WHERE id=?");
$u->bind_param("ssdisi", $name, $category, $price, $stock, $newImageName, $id);
$ok = $u->execute();

if (!$ok) {
    http_response_code(500);
    echo json_encode(["status"=>"error","msg"=>"Server error"]);
    exit();
}

echo json_encode([
    "status"=>"success",
    "msg"=>"Process success",
    "data"=>[
        "id"=>$id,
        "name"=>$name,
        "category"=>$category,
        "price"=>$price,
        "stock"=>$stock,
        "image"=>$newImageName
    ]
]);