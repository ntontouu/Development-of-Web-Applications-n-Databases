<?php
// Enable strict error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/header.php';
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate plan_id
$plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;
if ($plan_id <= 0) {
    header("Location: plan.php");
    exit();
}

// Verify plan ownership and fetch exercises
try {
    // 1. Check if plan belongs to user
    $stmt = $pdo->prepare("SELECT name FROM workout_plans WHERE id = ? AND user_id = ?");
    $stmt->execute([$plan_id, $_SESSION['user_id']]);
    $plan = $stmt->fetch();

    if (!$plan) {
        $_SESSION['error'] = "Το πλάνο δεν βρέθηκε ή δεν έχετε δικαίωμα πρόσβασης";
        header("Location: plan.php");
        exit();
    }

    // 2. Fetch exercises without notes
    $stmt = $pdo->prepare("
        SELECT e.title, e.description, e.muscle_group, pe.sets, pe.reps
        FROM plan_exercises pe
        JOIN exercises e ON pe.exercise_id = e.id
        WHERE pe.plan_id = ?
        ORDER BY pe.id ASC
    ");
    $stmt->execute([$plan_id]);
    $exercises = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Σφάλμα βάσης δεδομένων: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Προβολή Πλάνου | <?= htmlspecialchars($plan['name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2196F3;
            margin-top: 0;
        }
        .exercise {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .exercise-title {
            color: #2196F3;
            margin-bottom: 5px;
        }
        .exercise-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            color: #666;
        }
        .muscle-group {
            background: #e0f7fa;
            color: #00838f;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8em;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2196F3;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="plan.php" class="back-link">← Πίσω στα Πλάνα</a>
        <h1><?= htmlspecialchars($plan['name']) ?></h1>
        
        <?php if (empty($exercises)): ?>
            <p>Δεν υπάρχουν ασκήσεις σε αυτό το πλάνο.</p>
        <?php else: ?>
            <?php foreach ($exercises as $ex): ?>
                <div class="exercise">
                    <h3 class="exercise-title"><?= htmlspecialchars($ex['title']) ?></h3>
                    <div class="exercise-meta">
                        <?php if (!empty($ex['muscle_group'])): ?>
                            <span class="muscle-group"><?= htmlspecialchars($ex['muscle_group']) ?></span>
                        <?php endif; ?>
                        <span><?= $ex['sets'] ?> σετ × <?= $ex['reps'] ?> επαναλήψεις</span>
                    </div>
                    <?php if (!empty($ex['description'])): ?>
                        <p><?= htmlspecialchars($ex['description']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>