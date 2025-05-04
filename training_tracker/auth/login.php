<?php
session_start();
require_once '../config/db.php';



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["level"] = $user["level"];
        $_SESSION["xp"] = $user["xp"];
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "Λάθος στοιχεία.";
    }
}
?>

<form method="POST">
  <h2>Σύνδεση</h2>
  <input type="text" name="username" placeholder="Όνομα χρήστη" required><br>
  <div style="position: relative;">
  <input type="password" id="password" name="password" required>
  <span onclick="togglePassword()" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
    👁️
  </span>
</div>
  <button type="submit">Είσοδος</button>
</form>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

<script>
function togglePassword() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>