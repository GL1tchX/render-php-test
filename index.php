<?php

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'ok' => true,
    'service' => 'render_php_test',
    'php_version' => PHP_VERSION,
    'server_time' => date(DATE_ATOM),
], JSON_PRETTY_PRINT);
