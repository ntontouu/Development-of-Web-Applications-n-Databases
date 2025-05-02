<?php include 'includes/header.php'; ?>
<?php require_once 'config/db.php'; ?>

<h2>Στόχοι Προπόνησης</h2>

<form method="POST">
  <input type="text" name="description" placeholder="Περιγραφή στόχου" required>
  <label>Μέχρι πότε:</label>
  <input type="date" name="target_date" required>
  <button type="submit">Προσθήκη</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("INSERT INTO goals (user_id, description, target_date) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION["user_id"], $_POST["description"], $_POST["target_date"]]);
    echo "<p>Ο στόχος προστέθηκε!</p>";
}
?>

<h3>Οι στόχοι μου:</h3>
<?php
$stmt = $pdo->prepare("SELECT * FROM goals WHERE user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
while ($row = $stmt->fetch()) {
    $status = $row["completed"] ? "✅" : "❌";
    echo "<p>$status <strong>" . htmlspecialchars($row["description"]) . "</strong> (έως: " . $row["target_date"] . ")</p>";
}
?>

<?php include 'includes/footer.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
