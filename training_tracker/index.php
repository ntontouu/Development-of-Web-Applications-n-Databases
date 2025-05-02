<?php


session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
<html>
<head>
  <meta charset="UTF-8">
  <title>Training Tracker</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header>
    <h1>Training Tracker</h1>
  </header>

  <main>
    <h2>Καλώς ήρθες!</h2>
    <p>Οργάνωσε και παρακολούθησε τις προπονήσεις σου με ευκολία.</p>
    <a href="auth/login.php"><button>Σύνδεση</button></a>
    <a href="auth/register.php"><button>Εγγραφή</button></a>
  </main>

  <footer>
    <p>© 2025 Training Tracker</p>
  </footer>
</body>
</html>
