<?php
date_default_timezone_set("Asia/Jakarta");
// update for UTS PR


header("Content-Type: application/json; charset=UTF-8");

$config = [
    "host" => "localhost",
    "user" => "root",
    "pass" => "",
    "db"   => "uts_pbb"
];

$koneksi = mysqli_connect(
    $config['host'],
    $config['user'],
    $config['pass'],
    $config['db']
);

if (!$koneksi) {
    http_response_code(500);
    echo json_encode([
        "status" => 500,
        "message" => "Koneksi database gagal"
    ]);
    exit;
}

// Helper response
function apiResponse($status, $message, $data = null){
    http_response_code($status);

    $result = [
        "status" => $status,
        "message" => $message
    ];

    if ($data !== null) {
        $result['data'] = $data;
    }

    echo json_encode($result);
    exit;
}
?>
