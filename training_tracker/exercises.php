<?php include 'includes/header.php'; ?>
<?php require_once 'config/db.php'; ?>


<h2>Βιβλιοθήκη Ασκήσεων</h2>

<?php
$stmt = $pdo->query("SELECT * FROM exercises");
while ($row = $stmt->fetch()) {
    echo "<div class='exercise'>";
    echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
    echo "<p>" . nl2br(htmlspecialchars($row["description"])) . "</p>";
    echo "<small>Κατηγορία: " . htmlspecialchars($row["category"]) . "</small>";
    echo "</div><hr>";
}
?>

<?php include 'includes/footer.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">