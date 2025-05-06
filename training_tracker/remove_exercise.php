<?php
require_once 'config/db.php';
include 'includes/header.php';

if (isset($_GET['plan_id']) && isset($_GET['exercise_id'])) {
    $plan_id = intval($_GET['plan_id']);
    $exercise_id = intval($_GET['exercise_id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM plan_exercises WHERE plan_id = ? AND exercise_id = ?");
        $stmt->execute([$plan_id, $exercise_id]);
        $_SESSION['success'] = "Η άσκηση αφαιρέθηκε";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Σφάλμα: " . $e->getMessage();
    }
}

header("Location: exercises.php?plan_id=".$_GET['plan_id']);
exit();
?>