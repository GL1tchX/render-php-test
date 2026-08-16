<?php

header('Content-Type: application/json');

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

$ca = '/etc/secrets/aiven-ca.pem';

try {

    $dsn = "mysql:"
         . "host={$host};"
         . "port={$port};"
         . "dbname={$db};"
         . "charset=utf8mb4;"
         . "sslmode=verify-ca;"
         . "sslrootcert={$ca}";

    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $result = $pdo->query("
        SELECT
            VERSION() AS mysql_version,
            DATABASE() AS database_name,
            USER() AS database_user
    ")->fetch();

    echo json_encode([
        'ok' => true,
        'test' => 'render_aiven_mysql',
        'ssl_ca_exists' => file_exists($ca),
        'result' => $result
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'test' => 'render_aiven_mysql',
        'error' => $e->getMessage(),
        'ssl_ca_exists' => file_exists($ca)
    ], JSON_PRETTY_PRINT);
}
