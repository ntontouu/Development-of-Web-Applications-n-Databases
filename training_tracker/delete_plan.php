<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;

if ($plan_id > 0) {
    try {
        // 1. Έλεγχος ότι το πλάνο ανήκει στον χρήστη
        $stmt = $pdo->prepare("SELECT id FROM workout_plans WHERE id = ? AND user_id = ?");
        $stmt->execute([$plan_id, $_SESSION['user_id']]);
        $plan = $stmt->fetch();

        if (!$plan) {
            $_SESSION['error'] = "Το πλάνο δεν βρέθηκε ή δεν έχετε δικαίωμα να το διαγράψετε";
            header("Location: plan.php");
            exit();
        }

        // 2. Διαγραφή των ασκήσεων του πλάνου ΠΡΙΝ διαγραφεί το πλάνο
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("DELETE FROM plan_exercises WHERE plan_id = ?");
        $stmt->execute([$plan_id]);

        // 3. Διαγραφή του ίδιου του πλάνου
        $stmt = $pdo->prepare("DELETE FROM workout_plans WHERE id = ?");
        $stmt->execute([$plan_id]);

        $pdo->commit();
        
        $_SESSION['success'] = "Το πλάνο και όλες οι ασκήσεις του διαγράφηκαν με επιτυχία";
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Σφάλμα κατά τη διαγραφή: " . $e->getMessage();
    }
}

header("Location: plan.php");
exit();
?>
