<?php include 'includes/header.php'; ?>
<?php require_once 'config/db.php'; ?>


<h2>Δημιουργία Προγράμματος</h2>

<form method="POST">
  <input type="text" name="name" placeholder="Όνομα προγράμματος" required>
  <select name="day" required>
    <option value="">Ημέρα</option>
    <option>Monday</option>
    <option>Tuesday</option>
    <option>Wednesday</option>
    <option>Thursday</option>
    <option>Friday</option>
    <option>Saturday</option>
    <option>Sunday</option>
  </select>
  <button type="submit">Δημιουργία</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("INSERT INTO workout_plans (user_id, name, day) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION["user_id"], $_POST["name"], $_POST["day"]]);
    echo "<p>Το πρόγραμμα προστέθηκε!</p>";
}
?>

<?php include 'includes/footer.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">