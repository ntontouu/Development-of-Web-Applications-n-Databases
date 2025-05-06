<?php
include 'includes/header.php';
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch workout history
try {
    $stmt = $pdo->prepare("
        SELECT wl.*, wp.name as plan_name 
        FROM workout_logs wl
        LEFT JOIN workout_plans wp ON wl.plan_id = wp.id
        WHERE wl.user_id = ?
        ORDER BY wl.completed_at DESC
        LIMIT 50
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $workouts = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Σφάλμα ανάκτησης ιστορικού: " . $e->getMessage());
}

// Calculate stats
$total_workouts = count($workouts);
$total_minutes = array_sum(array_column($workouts, 'duration_minutes'));
$total_xp = $total_minutes * 2 + ($total_workouts * 10);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ιστορικό Προπονήσεων | FitQuest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2196F3;
            --secondary: #1976D2;
            --white: #fff;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--light-gray);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 2em;
            font-weight: bold;
            color: var(--primary);
            margin: 10px 0;
        }
        .workout-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .workout-table th {
            background: var(--primary);
            color: white;
            padding: 12px;
            text-align: left;
        }
        .workout-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .workout-table tr:last-child td {
            border-bottom: none;
        }
        .workout-table tr:hover {
            background: #f9f9f9;
        }
        .xp-badge {
            background: #4CAF50;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary);
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
        <h1>Ιστορικό Προπονήσεων</h1>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Συνολικές Προπονήσεις</h3>
                <div class="stat-value"><?= $total_workouts ?></div>
            </div>
            <div class="stat-card">
                <h3>Συνολικά Λεπτά</h3>
                <div class="stat-value"><?= $total_minutes ?></div>
            </div>
            <div class="stat-card">
                <h3>Συνολικό XP</h3>
                <div class="stat-value"><?= $total_xp ?></div>
            </div>
        </div>
        
        <h2>Πρόσφατες Προπονήσεις</h2>
        <?php if (empty($workouts)): ?>
            <p>Δεν έχετε καταγράψει προπονήσεις ακόμα.</p>
        <?php else: ?>
            <table class="workout-table">
                <thead>
                    <tr>
                        <th>Ημερομηνία</th>
                        <th>Πλάνο</th>
                        <th>Διάρκεια</th>
                        <th>XP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($workouts as $workout): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($workout['completed_at'])) ?></td>
                        <td><?= htmlspecialchars($workout['plan_name'] ?? 'Γενική Προπόνηση') ?></td>
                        <td><?= $workout['duration_minutes'] ?> λεπτά</td>
                        <td><span class="xp-badge">+<?= 10 + ($workout['duration_minutes'] * 2) ?> XP</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>