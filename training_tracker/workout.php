<?php
include 'includes/header.php';
require_once 'config/db.php';

// Ανάκτηση τρέχουσας προπόνησης από τη session
$workout = $_SESSION['current_workout'] ?? null;
if (!$workout) {
    header("Location: plans.php");
    exit();
}

// Ανάκτηση ασκήσεων
$stmt = $pdo->prepare("
    SELECT e.title, e.description, pe.sets, pe.reps 
    FROM plan_exercises pe
    JOIN exercises e ON pe.exercise_id = e.id
    WHERE pe.plan_id = ?
");
$stmt->execute([$workout['plan_id']]);
$exercises = $stmt->fetchAll();
?>

<!-- UI για προπόνηση (π.χ. εναλλαγή ασκήσεων, rest timer) -->
<script>
// JavaScript για εναλλαγή ασκήσεων
</script>