<?php

header('Content-Type: application/json; charset=utf-8');

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$name = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

$caFile = '/etc/secrets/aiven-ca.pem';

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $host,
        $port,
        $name
    );

    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_SSL_CA => $caFile,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
        ]
    );

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS render_persistence_test (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            test_value VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $value = 'render-' . bin2hex(random_bytes(4));

    $insert = $pdo->prepare("
        INSERT INTO render_persistence_test (test_value)
        VALUES (?)
    ");

    $insert->execute([$value]);

    $rows = $pdo->query("
        SELECT id, test_value, created_at
        FROM render_persistence_test
        ORDER BY id DESC
        LIMIT 5
    ")->fetchAll();

    echo json_encode([
        'ok' => true,
        'test' => 'render_aiven_mysql',
        'ssl_ca_exists' => file_exists($caFile),
        'inserted_value' => $value,
        'recent_rows' => $rows,
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'test' => 'render_aiven_mysql',
        'error' => $e->getMessage(),
        'ssl_ca_exists' => file_exists($caFile),
    ], JSON_PRETTY_PRINT);
}
