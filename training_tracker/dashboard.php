<?php 
include 'includes/header.php';

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

// User stats
$user_id = $_SESSION['user_id'];
$xp = $_SESSION['xp'] ?? 0;
$level = $_SESSION['level'] ?? 1;
$progress = $xp % 100;
$next_level_xp = 100 - $progress;

// 1. Real Data from Database (with error handling)
try {
    // Weekly workouts count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM workout_logs WHERE user_id = ? AND completed_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)");
    $stmt->execute([$user_id]);
    $weekly_workouts = $stmt->fetchColumn() ?? 0;

    // Total sets/reps
    $stmt = $pdo->prepare("
        SELECT SUM(sets) as total_sets, SUM(reps) as total_reps 
        FROM plan_exercises pe
        JOIN workout_logs wl ON pe.plan_id = wl.plan_id
        WHERE wl.user_id = ? AND wl.completed_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)
    ");
    $stmt->execute([$user_id]);
    $workout_data = $stmt->fetch();
    $total_sets = $workout_data['total_sets'] ?? 0;
    $total_reps = $workout_data['total_reps'] ?? 0;

    // Recent Activities
    $stmt = $pdo->prepare("
        SELECT plan_name, duration_minutes, completed_at 
        FROM workout_logs 
        WHERE user_id = ? 
        ORDER BY completed_at DESC 
        LIMIT 3
    ");
    $stmt->execute([$user_id]);
    $recent_workouts = $stmt->fetchAll();

} catch (PDOException $e) {
    // Fallback data if tables don't exist
    $weekly_workouts = 0;
    $total_sets = 0;
    $total_reps = 0;
    $recent_workouts = [];
    error_log("Database error: " . $e->getMessage());
}

// Prepare chart data
$labels = ['Δευ', 'Τρί', 'Τετ', 'Πέμ', 'Παρ', 'Σάβ', 'Κυρ'];
$workouts_data = array_fill(0, 7, 0);
$minutes_data = array_fill(0, 7, 0);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | FitQuest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2196F3;
            --secondary: #1976D2;
            --accent: #FF9800;
            --text-dark: #333;
            --text-medium: #555;
            --text-light: #777;
            --white: #fff;
            --light-gray: #f5f5f5;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--light-gray);
            color: var(--text-dark);
            margin: 0;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* ===== Welcome Section ===== */
        .welcome-section {
            background: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .welcome-title {
            color: var(--text-dark);
            font-size: 2.2rem;
            margin: 0 0 10px 0;
        }
        
        .user-level {
            color: var(--primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .progress-container {
            background: #f0f0f0;
            border-radius: 20px;
            height: 20px;
            margin: 20px auto;
            max-width: 500px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 10px;
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .xp-info {
            color: var(--text-medium);
            font-size: 0.9rem;
        }
        
        /* ===== Stats Grid ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .stat-card {
            background: var(--white);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .stat-card h3 {
            color: var(--primary);
            margin: 0 0 10px 0;
            font-size: 1.1rem;
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 10px 0;
        }
        
        .stat-subtext {
            color: var(--text-medium);
            font-size: 0.9rem;
        }
        
        /* ===== Quick Actions ===== */
        .quick-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 12px 25px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
        
        .action-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(33, 150, 243, 0.2);
        }
        
        /* ===== Chart Section ===== */
        .chart-container {
            background: var(--white);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin: 30px 0;
        }
        
        .chart-title {
            color: var(--text-dark);
            margin: 0 0 20px 0;
            font-size: 1.3rem;
        }
        
        /* ===== Recent Activity ===== */
        .recent-activity {
            background: var(--white);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin: 30px 0;
        }
        
        .activity-title {
            color: var(--text-dark);
            margin: 0 0 20px 0;
            font-size: 1.3rem;
        }
        
        .activity-item {
            margin: 20px 0;
            padding-left: 25px;
            position: relative;
            border-left: 3px solid var(--primary);
            padding-left: 15px;
        }
        
        .activity-item strong {
            color: var(--text-dark);
            display: block;
            margin-bottom: 5px;
        }
        
        .activity-item p {
            color: var(--text-medium);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .empty-state {
            color: var(--text-light);
            font-style: italic;
            text-align: center;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .welcome-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1 class="welcome-title">Καλώς ήρθες, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
            <p class="user-level">Επίπεδο <?php echo $level; ?> • <?php echo $xp; ?> XP</p>
            
            <div class="progress-container">
                <div class="progress-bar" style="width: <?php echo $progress; ?>%">
                    <?php echo $progress; ?>%
                </div>
            </div>
            <p class="xp-info">Χρειάζεσαι <?php echo $next_level_xp; ?> XP για το επόμενο επίπεδο!</p>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="plan.php" class="action-btn">📝 Πλάνο Προπόνησης</a>
            <a href="exercises.php" class="action-btn">💪 Ασκήσεις</a>
            <a href="profile.php" class="action-btn">👤 Προφίλ</a>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Προπονήσεις</h3>
                <div class="stat-value"><?php echo $weekly_workouts; ?></div>
                <p class="stat-subtext">Αυτή την εβδομάδα</p>
            </div>
            
            <div class="stat-card">
                <h3>Σετ</h3>
                <div class="stat-value"><?php echo $total_sets; ?></div>
                <p class="stat-subtext">Αυτή την εβδομάδα</p>
            </div>
            
            <div class="stat-card">
                <h3>Επαναλήψεις</h3>
                <div class="stat-value"><?php echo $total_reps; ?></div>
                <p class="stat-subtext">Αυτή την εβδομάδα</p>
            </div>
        </div>
        
        <!-- Chart Section -->
        <div class="chart-container">
            <h2 class="chart-title">Δραστηριότητα Αυτήν την Εβδομάδα</h2>
            <canvas id="workoutsChart"></canvas>
        </div>
        
        <!-- Recent Activity -->
        <div class="recent-activity">
            <h2 class="activity-title">Πρόσφατες Προπονήσεις</h2>
            
            <?php if (!empty($recent_workouts)): ?>
                <?php foreach ($recent_workouts as $workout): ?>
                    <div class="activity-item">
                        <strong>🏋️‍♂️ <?php echo htmlspecialchars($workout['plan_name']); ?></strong>
                        <p><?php echo date('d/m/Y H:i', strtotime($workout['completed_at'])); ?> • Διάρκεια: <?php echo $workout['duration_minutes']; ?> λεπτά</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state">Δεν βρέθηκαν πρόσφατες προπονήσεις</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Workouts Chart
        const ctx = document.getElementById('workoutsChart').getContext('2d');
        const workoutsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [
                    {
                        label: 'Προπονήσεις',
                        data: <?php echo json_encode($workouts_data); ?>,
                        backgroundColor: '#2196F3',
                        borderColor: '#1976D2',
                        borderWidth: 1
                    },
                    {
                        label: 'Λεπτά Προπόνησης',
                        data: <?php echo json_encode($minutes_data); ?>,
                        backgroundColor: '#FF9800',
                        borderColor: '#F57C00',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Προπονήσεις'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Λεπτά'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>