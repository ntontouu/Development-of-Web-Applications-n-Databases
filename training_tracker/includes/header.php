<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Training Tracker</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="main-header">
    <!-- Banner ως μέρος του header -->
    <div class="banner">
      <img src="assets/banner.jpg" alt="Training Banner">
    </div>

    <div class="header-content">
      
      <nav class="main-nav">
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="exercises.php"><i class="fas fa-dumbbell"></i> Ασκήσεις</a>
        <a href="plan.php"><i class="fas fa-calendar-alt"></i> Πρόγραμμα</a>
        <a href="progress.php"><i class="fas fa-chart-line"></i> Πρόοδος</a>
        <a href="goals.php"><i class="fas fa-bullseye"></i> Στόχοι</a>
        <a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Αποσύνδεση</a>
      </nav>
      
      <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </header>

  <main class="main-content">