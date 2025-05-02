<?php include 'includes/header.php'; ?>
<?php require_once 'config/db.php'; ?>

<h2>Καταγραφή Προόδου</h2>

<form method="POST">
  <label>Ημερομηνία:</label>
  <input type="date" name="date" required>
  <textarea name="notes" placeholder="Παρατηρήσεις ή επίδοση" rows="4" required></textarea><br>
  <button type="submit">Αποθήκευση</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("INSERT INTO progress (user_id, date, notes) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION["user_id"], $_POST["date"], $_POST["notes"]]);
    echo "<p>Η πρόοδος καταγράφηκε.</p>";
}
?>

<h3>Πρόσφατες καταγραφές:</h3>
<?php
$stmt = $pdo->prepare("SELECT * FROM progress WHERE user_id = ? ORDER BY date DESC LIMIT 5");
$stmt->execute([$_SESSION["user_id"]]);
while ($row = $stmt->fetch()) {
    echo "<p><strong>" . $row["date"] . "</strong>: " . htmlspecialchars($row["notes"]) . "</p>";
}
?>

<?php include 'includes/footer.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
