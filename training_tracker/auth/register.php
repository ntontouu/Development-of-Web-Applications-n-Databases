<?php


require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password]);
        header("Location: login.php?registered=1");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<form method="POST">
  <h2>Εγγραφή</h2>
  <input type="text" name="username" placeholder="Όνομα χρήστη" required><br>
  <input type="email" name="email" placeholder="Email" required><br>
  <div style="position: relative;">
  <input type="password" id="password" name="password" required>
  <span onclick="togglePassword()" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
    👁️
  </span>
</div>

  <button type="submit">Εγγραφή</button>
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