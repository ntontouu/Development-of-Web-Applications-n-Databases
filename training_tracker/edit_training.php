<?php
require 'db.php';

if (!isset($_GET['id'])) {
    die("Λείπει το ID της άσκησης.");
}

$id = $_GET['id'];

// Αν έγινε υποβολή φόρμας
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sets = $_POST['sets'];
    $reps = $_POST['reps'];

    $stmt = $pdo->prepare("UPDATE plan_exercises SET sets = ?, reps = ? WHERE id = ?");
    $stmt->execute([$sets, $reps, $id]);

    header("Location: plan.php");
    exit();
}

// Αν φορτώνεται η σελίδα για πρώτη φορά
$stmt = $pdo->prepare("
    SELECT pe.sets, pe.reps, e.title
    FROM plan_exercises pe
    JOIN exercises e ON pe.exercise_id = e.id
    WHERE pe.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Επεξεργασία Άσκησης</title>
</head>
<body>
    <h2>Επεξεργασία για: <?= htmlspecialchars($data['title']) ?></h2>
    <form method="post">
        <label>Σετ: <input type="number" name="sets" value="<?= $data['sets'] ?>" required></label><br><br>
        <label>Επαναλήψεις: <input type="number" name="reps" value="<?= $data['reps'] ?>" required></label><br><br>
        <button type="submit">Αποθήκευση</button>
    </form>
</body>
</html>
