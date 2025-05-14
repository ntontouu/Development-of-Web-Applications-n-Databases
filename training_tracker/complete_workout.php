<?php
// Strict error reporting
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Ensure no output before headers
if (ob_get_level()) ob_end_clean();

// Force JSON response
header('Content-Type: application/json');

// Debugging - remove in production
file_put_contents('debug.log', date('Y-m-d H:i:s')." - Request started\n", FILE_APPEND);

try {
    // Validate session
    session_start();
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Session invalid', 401);
    }

    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed', 405);
    }

    // Validate input
    $plan_id = (int)($_POST['plan_id'] ?? 0);
    $duration = (int)($_POST['duration'] ?? 0);
    
    if ($duration <= 0) {
        throw new Exception('Invalid duration', 400);
    }

    // Simulate success response - REPLACE WITH YOUR DB CODE
    $response = [
        'success' => true,
        'xp_earned' => 10 + ($duration * 2),
        'level_up' => false,
        'debug' => [
            'plan_id' => $plan_id,
            'duration' => $duration,
            'session' => session_id()
        ]
    ];

    file_put_contents('debug.log', print_r($response, true)."\n", FILE_APPEND);
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}