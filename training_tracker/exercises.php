<?php 
include 'includes/header.php';
require_once 'config/db.php';

$plan_id = isset($_GET['plan_id']) ? intval($_GET['plan_id']) : 0;

// Έλεγχος πρόσβασης χρήστη
if ($plan_id > 0) {
    $stmt = $pdo->prepare("SELECT id, name FROM workout_plans WHERE id = ? AND user_id = ?");
    $stmt->execute([$plan_id, $_SESSION['user_id']]);
    $plan = $stmt->fetch();
    
    if (!$plan) {
        header("Location: plan.php");
        exit();
    }
}

// Προσθήκη άσκησης
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_exercise'])) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO plan_exercises 
            (plan_id, exercise_id, sets, reps) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $plan_id,
            $_POST['exercise_id'],
            $_POST['sets'] ?? 3,
            $_POST['reps'] ?? 10
        ]);
        $_SESSION['success'] = "Η άσκηση προστέθηκε!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Η άσκηση υπάρχει ήδη στο πρόγραμμα";
    }
    header("Location: exercises.php?plan_id=".$plan_id);
    exit();
}

// Λήψη ασκήσεων προγράμματος
$program_exercises = [];
if ($plan_id > 0) {
    $stmt = $pdo->prepare("
        SELECT e.id, e.title, e.description, pe.sets, pe.reps
        FROM plan_exercises pe
        JOIN exercises e ON pe.exercise_id = e.id
        WHERE pe.plan_id = ?
    ");
    $stmt->execute([$plan_id]);
    $program_exercises = $stmt->fetchAll();
}

// Λήψη μηνυμάτων
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Διαχείριση Ασκήσεων</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2196F3;
            --secondary: #4CAF50;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
            --white: #fff;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: var(--light-gray);
            color: var(--dark-gray);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .back-link {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .section-title {
            margin: 30px 0 20px;
            color: var(--dark-gray);
        }
        
        .exercises-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .exercise-card {
            background: var(--white);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .exercise-card:hover {
            transform: translateY(-5px);
        }
        
        .exercise-title {
            margin: 0 0 10px;
            color: var(--primary);
        }
        
        .exercise-description {
            color: #666;
            margin-bottom: 15px;
        }
        
        .add-form {
            margin-top: 15px;
        }
        
        .form-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .form-row label {
            font-size: 0.9em;
        }
        
        .form-row input {
            padding: 5px;
            width: 50px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: #3e8e41;
        }
        
        .btn-danger {
            background: #f44336;
            color: white;
        }
        
        .btn-danger:hover {
            background: #d32f2f;
        }
        
        .added-label {
            color: var(--primary);
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }
        
        .current-exercises {
            background: var(--white);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .exercise-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .exercise-table th {
            background: var(--primary);
            color: white;
            padding: 12px;
            text-align: left;
        }
        
        .exercise-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .exercise-table tr:hover {
            background: #f9f9f9;
        }
        
        @media (max-width: 768px) {
            .exercises-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Διαχείριση Ασκήσεων</h2>
            <?php if ($plan_id > 0): ?>
                <a href="plan.php" class="back-link">
                    ← Πίσω στο "<?= htmlspecialchars($plan['name']) ?>"
                </a>
            <?php endif; ?>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($plan_id > 0 && !empty($program_exercises)): ?>
            <div class="current-exercises">
                <h3 class="section-title">Τρέχουσες Ασκήσεις</h3>
                <table class="exercise-table">
                    <thead>
                        <tr>
                            <th>Άσκηση</th>
                            <th>Σετ</th>
                            <th>Επαναλήψεις</th>
                            <th>Ενέργειες</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($program_exercises as $ex): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($ex['title']) ?></strong>
                                <?php if (!empty($ex['description'])): ?>
                                    <p style="color:#666; margin-top:5px;"><?= htmlspecialchars($ex['description']) ?></p>
                                <?php endif; ?>
                            </td>
                            <td><?= $ex['sets'] ?></td>
                            <td><?= $ex['reps'] ?></td>
                            <td>
                                <a href="remove_exercise.php?plan_id=<?= $plan_id ?>&exercise_id=<?= $ex['id'] ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Διαγραφή άσκησης;');">
                                   Διαγραφή
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <h3 class="section-title">Διαθέσιμες Ασκήσεις</h3>
        <div class="exercises-grid">
            <?php
            $stmt = $pdo->query("SELECT * FROM exercises");
            while ($row = $stmt->fetch()):
                $is_added = $plan_id > 0 && in_array($row['id'], array_column($program_exercises, 'id'));
            ?>
                <div class="exercise-card">
                    <h3 class="exercise-title"><?= htmlspecialchars($row['title']) ?></h3>
                    <?php if (!empty($row['description'])): ?>
                        <p class="exercise-description"><?= htmlspecialchars($row['description']) ?></p>
                    <?php endif; ?>
                    
                    <?php if ($plan_id > 0 && !$is_added): ?>
                        <form method="POST" class="add-form">
                            <input type="hidden" name="exercise_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="add_exercise" value="1">
                            
                            <div class="form-row">
                                <label>Σετ:</label>
                                <input type="number" name="sets" value="3" min="1">
                            </div>
                            
                            <div class="form-row">
                                <label>Επαναλήψεις:</label>
                                <input type="number" name="reps" value="10" min="1">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Προσθήκη στο Πρόγραμμα</button>
                        </form>
                    <?php elseif ($is_added): ?>
                        <span class="added-label">✔ Προστέθηκε στο πρόγραμμα</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>