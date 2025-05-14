<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan_id'])) {
    $_SESSION['current_workout'] = [
        'plan_id' => (int)$_POST['plan_id'],
        'plan_name' => $_POST['plan_name'] ?? 'Unknown Plan',
        'start_time' => time()
    ];
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit();
}
?>