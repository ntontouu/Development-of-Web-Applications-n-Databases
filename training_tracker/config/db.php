<?php

$host = 'localhost';
$dbname = 'training_tracker';
$user = 'root'; // άλλαξε αν έχεις άλλο χρήστη
$pass = '';     // βάλε κωδικό αν έχεις

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
