<?php


session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Training Tracker</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
  <h1>Training Tracker</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="exercises.php">Ασκήσεις</a>
    <a href="plan.php">Πρόγραμμα</a>
    <a href="progress.php">Πρόοδος</a>
    <a href="goals.php">Στόχοι</a>
    <a href="auth/logout.php">Αποσύνδεση</a>
  </nav>
</header>
<main>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">