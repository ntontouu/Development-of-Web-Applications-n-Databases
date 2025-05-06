<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/header.php';
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission for new plan creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_plan'])) {
    $plan_name = trim($_POST['plan_name']);
    
    if (empty($plan_name)) {
        $_SESSION['error'] = "Το όνομα πλάνου δεν μπορεί να είναι κενό!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO workout_plans (user_id, name) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $plan_name]);
            $_SESSION['success'] = "Το πλάνο «{$plan_name}» δημιουργήθηκε με επιτυχία!";
            header("Location: plan.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Σφάλμα βάσης δεδομένων: " . $e->getMessage();
        }
    }
}

// Fetch all plans for the current user
try {
    $stmt = $pdo->prepare("SELECT * FROM workout_plans WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $plans = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Σφάλμα ανάκτησης πλάνων: " . $e->getMessage());
}

// Get user stats
$user_stats = [];
try {
    $stmt = $pdo->prepare("SELECT level, xp FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_stats = $stmt->fetch();
} catch (PDOException $e) {
    $_SESSION['error'] = "Σφάλμα φόρτωσης στατιστικών: " . $e->getMessage();
}

// Check for level up notification
$level_up = $_SESSION['level_up'] ?? null;
if (isset($_SESSION['level_up'])) {
    unset($_SESSION['level_up']);
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Διαχείριση Πλάνων | FitQuest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2196F3;
            --secondary: #1976D2;
            --accent: #FF9800;
            --white: #fff;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
            --success: #4CAF50;
            --warning: #FFC107;
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
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: var(--secondary);
        }
        .btn-warning {
            background: var(--warning);
            color: #000;
        }
        .btn-warning:hover {
            background: #e0a800;
        }
        .btn-success {
            background: var(--success);
        }
        .btn-success:hover {
            background: #3d8b40;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .plan-card {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .create-plan-form {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .create-plan-form input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .user-stats {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .xp-bar-container {
            width: 70%;
            background: #e0e0e0;
            border-radius: 10px;
            height: 20px;
            margin-top: 5px;
        }
        .xp-bar {
            height: 100%;
            background: var(--success);
            border-radius: 10px;
            width: <?= ($user_stats['xp'] % 100) ?>%;
            transition: width 0.5s;
        }
        .timer-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--white);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            display: none;
        }
        .timer-display {
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        .level-up-notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--warning);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 2000;
            text-align: center;
            display: none;
        }
        .level-up-notification h2 {
            margin-top: 0;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- User Stats -->
        <div class="user-stats">
            <div>
                <h3>Επίπεδο <?= $user_stats['level'] ?? 1 ?></h3>
                <div>XP: <?= $user_stats['xp'] ?? 0 ?> / <?= ($user_stats['level'] ?? 1) * 100 ?></div>
                <div class="xp-bar-container">
                    <div class="xp-bar"></div>
                </div>
            </div>
            <div>
                <a href="workout_history.php" class="btn">📊 Ιστορικό Προπονήσεων</a>
            </div>
        </div>

        <h1>Διαχείριση Πλάνων Προπόνησης</h1>

        <!-- Display success/error messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Create New Plan Form -->
        <div class="create-plan-form">
            <h2>Δημιουργία Νέου Πλάνου</h2>
            <form method="POST">
                <input type="text" name="plan_name" placeholder="Όνομα Πλάνου" required>
                <button type="submit" name="create_plan" class="btn">➕ Δημιουργία</button>
            </form>
        </div>

        <!-- Plans List -->
        <h2>Τα Πλάνα Μου</h2>
        <?php if (empty($plans)): ?>
            <p>Δεν έχετε δημιουργήσει ακόμα πλάνα προπόνησης.</p>
        <?php else: ?>
            <div class="plans-grid">
                <?php foreach ($plans as $plan): ?>
                    <div class="plan-card">
                        <h3><?= htmlspecialchars($plan['name']) ?></h3>
                        <div class="plan-actions">
                            <a href="exercises.php?plan_id=<?= $plan['id'] ?>" class="btn">🏋️ Διαχείριση Ασκήσεων</a>
                            <a href="view_plan.php?plan_id=<?= $plan['id'] ?>" class="btn">👀 Προβολή</a>
                            <button class="btn btn-success start-workout-btn" 
                                    data-plan-id="<?= $plan['id'] ?>" 
                                    data-plan-name="<?= htmlspecialchars($plan['name']) ?>">
                                ⏱️ Έναρξη Προπόνησης
                            </button>
                            <a href="delete_plan.php?plan_id=<?= $plan['id'] ?>" 
                               class="btn" 
                               style="background: #f44336; margin-top: 10px;" 
                               onclick="return confirm('Διαγραφή αυτού του πλάνου;')">
                               🗑️ Διαγραφή
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Timer Container -->
    <div class="timer-container" id="timerContainer">
        <h3>Πρόγραμμα: <span id="currentPlanName"></span></h3>
        <div class="timer-display" id="timerDisplay">00:00:00</div>
        <button class="btn btn-warning" id="pauseBtn">⏸️ Παύση</button>
        <button class="btn btn-success" id="completeBtn">✅ Ολοκλήρωση</button>
        <button class="btn" style="background: #f44336;" id="cancelBtn">✖ Ακύρωση</button>
    </div>

    <!-- Level Up Notification -->
    <?php if ($level_up): ?>
        <div class="level-up-notification" id="levelUpNotification">
            <h2>ΣΥΓΧΑΡΗΤΗΡΙΑ!</h2>
            <p>Έφτασες το <strong>Επίπεδο <?= $level_up ?></strong>!</p>
            <button class="btn btn-success" onclick="document.getElementById('levelUpNotification').style.display = 'none'">OK</button>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('levelUpNotification').style.display = 'block';
            });
        </script>
    <?php endif; ?>

    <script>
        // Timer functionality
        let timerInterval;
        let seconds = 0;
        let isPaused = false;
        let currentPlanId = null;
        let currentPlanName = '';

        document.querySelectorAll('.start-workout-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentPlanId = this.getAttribute('data-plan-id');
                currentPlanName = this.getAttribute('data-plan-name');
                
                // Show timer container
                document.getElementById('timerContainer').style.display = 'block';
                document.getElementById('currentPlanName').textContent = currentPlanName;
                
                // Start timer
                seconds = 0;
                updateTimerDisplay();
                timerInterval = setInterval(incrementTimer, 1000);
                
                // Send AJAX request to start workout
                fetch('start_workout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `plan_id=${currentPlanId}&plan_name=${encodeURIComponent(currentPlanName)}`
                });
            });
        });

        document.getElementById('completeBtn').addEventListener('click', async function() {
    clearInterval(timerInterval);
    const minutes = Math.ceil(seconds / 60);
    
    try {
        // Χρησιμοποιούμε απόλυτη διαδρομή για αποφυγή προβλημάτων
        const url = '/training_tracker/complete_workout.php';
        
        console.log('Sending request to:', url);
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `plan_id=${currentPlanId}&duration=${minutes}`,
            credentials: 'include' // Σημαντικό για cookies/session
        });

        // Έλεγχος για μη-JSON responses
        const responseText = await response.text();
        let data;
        
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Invalid JSON response:', responseText);
            throw new Error('Η απόκριση από τον server δεν ήταν έγκυρη');
        }

        if (!response.ok) {
            throw new Error(data.error || `Σφάλμα server: ${response.status}`);
        }

        // Εμφάνιση μηνύματος επιτυχίας
        alert(`Η προπόνηση ολοκληρώθηκε! Κέρδισες ${data.xp_earned} XP!`);
        
        if (data.level_up) {
            alert(`ΣΥΓΧΑΡΗΤΗΡΙΑ! Ανέβηκες στο επίπεδο ${data.level_up}!`);
        }
        
        // Ανανέωση της σελίδας
        location.reload();
        
    } catch (error) {
        console.error('Σφάλμα:', error);
        alert(`Σφάλμα κατά την ολοκλήρωση: ${error.message}`);
    } finally {
        document.getElementById('timerContainer').style.display = 'none';
    }
});

        document.getElementById('cancelBtn').addEventListener('click', function() {
            clearInterval(timerInterval);
            document.getElementById('timerContainer').style.display = 'none';
        });

        function incrementTimer() {
            if (!isPaused) {
                seconds++;
                updateTimerDisplay();
            }
        }

        function updateTimerDisplay() {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            document.getElementById('timerDisplay').textContent = 
                `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>