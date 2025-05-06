<?php
include 'includes/header.php';

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user data with error handling
try {
    $stmt = $pdo->prepare("
        SELECT 
            username, 
            email, 
            COALESCE(profile_pic, 'images/default-avatar.jpg') as profile_pic, 
            level, 
            xp 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    // Fallback if profile_pic column doesn't exist
    $stmt = $pdo->prepare("SELECT username, email, level, xp FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    $user['profile_pic'] = 'images/default-avatar.jpg';
}

// Fetch workout stats with error handling
try {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_workouts,
            SUM(duration_minutes) as total_minutes,
            MAX(completed_at) as last_workout
        FROM workout_logs 
        WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch();
} catch (PDOException $e) {
    $stats = [
        'total_workouts' => 0,
        'total_minutes' => 0,
        'last_workout' => null
    ];
}

// Fetch achievements (if table exists)
$achievements = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM user_achievements WHERE user_id = ? ORDER BY unlocked_at DESC LIMIT 3");
    $stmt->execute([$user_id]);
    $achievements = $stmt->fetchAll();
} catch (PDOException $e) {
    // Achievements table doesn't exist
    error_log("Achievements table not found: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Προφίλ | FitQuest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2196F3;
            --secondary: #1976D2;
            --accent: #FF9800;
            --text-dark: #333;
            --text-medium: #555;
            --white: #fff;
            --light-gray: #f5f5f5;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--light-gray);
            color: var(--text-dark);
            margin: 0;
        }
        
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Profile Header */
        .profile-header {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            align-items: center;
        }
        
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--primary);
        }
        
        .profile-info {
            flex: 1;
            min-width: 250px;
        }
        
        .profile-name {
            font-size: 2rem;
            margin: 0 0 10px 0;
            color: var(--text-dark);
        }
        
        .profile-email {
            color: var(--text-medium);
            margin-bottom: 20px;
        }
        
        .level-badge {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        
        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary);
            margin: 10px 0;
        }
        
        /* Achievements */
        .achievements-section {
            background: var(--white);
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin: 30px 0;
        }
        
        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .achievement-card {
            text-align: center;
        }
        
        .achievement-badge {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
            border: 3px solid var(--accent);
        }
        
        /* Edit Form */
        .edit-form {
            background: var(--white);
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin: 30px 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
        }
        
        .btn {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: var(--secondary);
        }
        
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                 alt="Profile Picture" 
                 class="profile-pic">
                 
            <div class="profile-info">
                <h1 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h1>
                <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
                
                <div class="level-badge">
                    Επίπεδο <?php echo $user['level']; ?> • <?php echo $user['xp']; ?> XP
                </div>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Συνολικές Προπονήσεις</h3>
                <div class="stat-value"><?php echo $stats['total_workouts']; ?></div>
                <p>Από την αρχή</p>
            </div>
            
            <div class="stat-card">
                <h3>Συνολική Διάρκεια</h3>
                <div class="stat-value"><?php echo $stats['total_minutes']; ?></div>
                <p>Λεπτά</p>
            </div>
            
            <div class="stat-card">
                <h3>Τελευταία Προπόνηση</h3>
                <div class="stat-value">
                    <?php echo $stats['last_workout'] ? date('d/m/Y', strtotime($stats['last_workout'])) : "Καμία"; ?>
                </div>
            </div>
        </div>
        
        <!-- Achievements Section -->
        <div class="achievements-section">
            <h2>Τα Επιτεύγματά Μου</h2>
            
            <?php if (!empty($achievements)): ?>
                <div class="achievements-grid">
                    <?php foreach ($achievements as $achievement): ?>
                        <div class="achievement-card">
                            <img src="badges/<?php echo htmlspecialchars($achievement['badge_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($achievement['title']); ?>"
                                 class="achievement-badge">
                            <p><?php echo htmlspecialchars($achievement['title']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Δεν έχεις ξεκλειδώσει ακόμα επιτεύγματα!</p>
            <?php endif; ?>
        </div>
        
        <!-- Edit Profile Section -->
        <div class="edit-form">
            <h2>Επεξεργασία Προφίλ</h2>
            
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>"
                           class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Νέος Κωδικός (αφήστε κενό για να μείνει ίδιος)</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="profile_pic">Φωτογραφία Προφίλ</label>
                    <input type="file" id="profile_pic" name="profile_pic" accept="image/*" class="form-control">
                </div>
                
                <button type="submit" class="btn">Αποθήκευση Αλλαγών</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>