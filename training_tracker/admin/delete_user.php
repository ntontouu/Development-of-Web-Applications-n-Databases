<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require '../includes/db.php';

$id = intval($_GET['id']);
$conn->query("DELETE FROM users WHERE id = $id AND role != 'admin'");

header("Location: users.php");
exit;
