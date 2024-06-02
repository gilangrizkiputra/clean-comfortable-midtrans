<?php
// Setel server key Anda (Catatan: Server key untuk mode sandbox dan produksi berbeda)
$server_key = 'SB-Mid-server-Xzba3_5u-lTBv-e71pfEXQSw';
// Setel true untuk produksi, setel false untuk sandbox
$is_production = false;

$api_url = $is_production ? 
  'https://app.midtrans.com/snap/v1/transactions' : 
  'https://app.sandbox.midtrans.com/snap/v1/transactions';

// Cek jika metode bukan HTTP POST, tampilkan 404
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(404);
  echo json_encode(["error" => "Metode permintaan tidak valid"]); exit();
}

// Mendapatkan body dari HTTP POST request
$request_body = file_get_contents('php://input');
// Setel tipe konten response sebagai JSON
header('Content-Type: application/json');
// Panggil API charge menggunakan body permintaan yang diberikan oleh mobile SDK
$charge_result = chargeAPI($api_url, $server_key, $request_body);

// Log hasil response untuk debugging
error_log("Hasil API Charge: " . print_r($charge_result, true));

// Setel kode status HTTP response
http_response_code($charge_result['http_code']);
// Kemudian cetak body response
echo $charge_result['body'];

/**
 * Panggil API charge menggunakan Curl
 * @param string  $api_url
 * @param string  $server_key
 * @param string  $request_body
 */
function chargeAPI($api_url, $server_key, $request_body){
  $ch = curl_init();
  $curl_options = array(
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    // Tambahkan header ke permintaan, termasuk Authorization yang dihasilkan dari server key
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'Accept: application/json',
      'Authorization: Basic ' . base64_encode($server_key . ':')
    ),
    CURLOPT_POSTFIELDS => $request_body
  );
  curl_setopt_array($ch, $curl_options);
  $body = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    $body = json_encode(["error" => curl_error($ch)]);
    $http_code = 500;
  }
  curl_close($ch);
  return array(
    'body' => $body,
    'http_code' => $http_code,
  );
}
?>
