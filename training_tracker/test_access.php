<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'path' => __FILE__,
    'directory' => __DIR__,
    'request_uri' => $_SERVER['REQUEST_URI'] ?? null
]);