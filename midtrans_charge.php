<?php
// Cek jika metode bukan HTTP POST, tampilkan 404
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(404);
  echo json_encode(["error" => "Metode permintaan tidak valid"]); exit();
}

// Mendapatkan body dari HTTP POST request
$request_body = file_get_contents('php://input');

// Setel tipe konten response sebagai JSON
header('Content-Type: application/json');

// Cek dan log request body
error_log("Request Body: " . $request_body);

// Response dengan request body yang diterima
echo json_encode(["message" => "Permintaan POST diterima", "data" => json_decode($request_body)]);
?>
