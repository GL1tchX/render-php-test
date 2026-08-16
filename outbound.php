<?php

header('Content-Type: application/json; charset=utf-8');

$ch = curl_init('https://api.paymongo.com/');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => false,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);

$body = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
$curlErrno = curl_errno($ch);

curl_close($ch);

echo json_encode([
    'ok' => $curlErrno === 0,
    'test' => 'render_outbound_https',
    'target' => 'api.paymongo.com',
    'http_status' => $httpCode,
    'curl_errno' => $curlErrno,
    'curl_error' => $curlError,
], JSON_PRETTY_PRINT);
