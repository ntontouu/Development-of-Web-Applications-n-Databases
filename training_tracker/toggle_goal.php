<?php
require_once 'config/db.php';
include 'includes/header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['goal_id'])) {
    $goal_id = intval($_POST['goal_id']);
    
    // Ελέγχουμε αν ο στόχος ανήκει στον χρήστη
    $stmt = $pdo->prepare("SELECT user_id FROM goals WHERE id = ?");
    $stmt->execute([$goal_id]);
    $goal = $stmt->fetch();
    
    if ($goal && $goal['user_id'] == $_SESSION['user_id']) {
        // Αλλάζουμε την κατάσταση ολοκλήρωσης
        $stmt = $pdo->prepare("UPDATE goals SET completed = NOT completed WHERE id = ?");
        $stmt->execute([$goal_id]);
        $_SESSION['message'] = "Η κατάσταση στόχου ενημερώθηκε!";
    }
}

header("Location: goals.php");
exit();
?>