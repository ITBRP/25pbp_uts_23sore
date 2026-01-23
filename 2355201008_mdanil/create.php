<?php 
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(500);
    echo json_encode(['status'=>'error','msg'=>'Server Error!']);
    exit();
}

// VALIDASI (pakai $_POST karena form-data)
$errors = [];

/* ---- NAME ---- */
if(!isset($_POST['name']) || trim($_POST['name']) == ''){
    $errors['name'] = "Nama tidak boleh kosong";
} elseif(strlen($_POST['name']) < 3){
    $errors['name'] = "Nama minimal 3 karakter";
}

/* ---- CATEGORY ---- */
$allowedCategory = ['Elektronik','Fashion','Makanan','Lainnya'];

if(!isset($_POST['category']) || !in_array($_POST['category'], $allowedCategory)){
    $errors['category'] = "Category harus: Elektronik, Fashion, Makanan, Lainnya";
}

/* ---- PRICE ---- */
if(!isset($_POST['price']) || !is_numeric($_POST['price']) || $_POST['price'] <= 0){
    $errors['price'] = "Price harus angka dan lebih dari 0";
}

/* ---- STOCK ---- */
if(!isset($_POST['stock']) || !is_numeric($_POST['stock']) || $_POST['stock'] <= 0){
    $errors['stock'] = "Stock harus angka dan lebih dari 0";
}

/* ---- IMAGE ---- */
$anyPhoto = false;
$namaPhoto = null;

if(isset($_FILES['image'])){
    if($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){

        $allowed = ['jpg','jpeg','png'];
        $fileName = $_FILES['image']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(!in_array($fileExt,$allowed)){
            $errors['image'] = "Format file tidak valid (hanya jpg, jpeg, png)";
        } else {
            $anyPhoto = true;
            $namaPhoto = md5(time()) . "." . $fileExt;
        }

    } else {
        $errors['image'] = "Image wajib diupload";
    }
} else {
    $errors['image'] = "IMAGE belum dikirim";
}

if(count($errors) > 0){
    http_response_code(400);
    echo json_encode([
        'status'=>'error',
        'msg'=>'Error data',
        'errors'=> $errors
    ]);
    exit();
}

if($anyPhoto){
    move_uploaded_file($_FILES['image']['tmp_name'], "img/".$namaPhoto);
}

/* ---- INSERT DB ---- */
$koneksi = new mysqli('localhost','root','','uts_pbp');
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$q = "INSERT INTO mahasiswa(name,category,price,stock,image)
      VALUES('$name','$category','$price','$stock','$namaPhoto')";
$koneksi->query($q);
$id = $koneksi->insert_id;

/* ---- RESPONSE ---- */
echo json_encode([
    'status'=>'success',
    'msg'=>'Proses berhasil',
    'data'=>[
        'id'=>$id,
        'name'=>$name,
        'category'=>$category,
        'price'=>$price,
        'stock'=>$stock,
        'image'=>$namaPhoto
    ]
]);
?>