<?php 
include 'includes/header.php';
require_once 'config/db.php';

// Προσθήκη νέου στόχου
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("INSERT INTO goals (user_id, description, target_date) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION["user_id"], $_POST["description"], $_POST["target_date"]]);
    $_SESSION['message'] = "Ο στόχος προστέθηκε!";
    header("Location: goals.php");
    exit();
}

// Λήψη μηνυμάτων
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Λήψη στόχων χρήστη
$stmt = $pdo->prepare("SELECT * FROM goals WHERE user_id = ? ORDER BY target_date");
$stmt->execute([$_SESSION["user_id"]]);
$goals = $stmt->fetchAll();
?>

<h2>Στόχοι Προπόνησης</h2>

<?php if ($message): ?>
    <div style="color:green; padding:10px; background:#e8f8e8; margin-bottom:15px;">
        <?= $message ?>
    </div>
<?php endif; ?>

<form method="POST" style="margin-bottom:30px;">
    <div style="margin-bottom:10px;">
        <input type="text" name="description" placeholder="Περιγραφή στόχου" required 
               style="padding:8px; width:300px;">
    </div>
    <div style="margin-bottom:10px;">
        <label style="display:block; margin-bottom:5px;">Μέχρι πότε:</label>
        <input type="date" name="target_date" required style="padding:8px;">
    </div>
    <button type="submit" style="padding:8px 15px; background:#4CAF50; color:white; border:none; cursor:pointer;">
        Προσθήκη Στόχου
    </button>
</form>

<h3>Οι Στόχοι μου</h3>

<?php if (count($goals) > 0): ?>
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px;">
        <?php foreach ($goals as $goal): 
            $today = new DateTime();
            $target_date = new DateTime($goal['target_date']);
            $days_remaining = $today->diff($target_date)->format('%a');
            $is_completed = $goal['completed'];
            $is_past_due = !$is_completed && $today > $target_date;
        ?>
            <div style="border:1px solid #ddd; padding:15px; border-radius:5px; 
                        <?= $is_past_due ? 'border-left:4px solid #f44336;' : 'border-left:4px solid #4CAF50;' ?>">
                <h4 style="margin-top:0;">
                    <?= $is_completed ? '✅' : ($is_past_due ? '⚠️' : '🎯') ?>
                    <?= htmlspecialchars($goal['description']) ?>
                </h4>
                
                <div style="margin:10px 0;">
                    <strong>Προθεσμία:</strong> <?= $goal['target_date'] ?>
                </div>
                
                <div style="font-size:1.1em; 
                            color:<?= $is_past_due ? '#f44336' : '#2196F3' ?>;">
                    <?php if ($is_completed): ?>
                        Ολοκληρώθηκε!
                    <?php elseif ($is_past_due): ?>
                        Έχει παρέλθει κατά <?= $days_remaining ?> μέρες
                    <?php else: ?>
                        Απομένουν: <?= $days_remaining ?> μέρες
                    <?php endif; ?>
                </div>
                
                <div style="margin-top:15px;">
                    <form method="POST" action="toggle_goal.php" style="display:inline;">
                        <input type="hidden" name="goal_id" value="<?= $goal['id'] ?>">
                        <button type="submit" style="padding:5px 10px; background:#<?= $is_completed ? 'f44336' : '4CAF50' ?>; color:white; border:none; cursor:pointer;">
                            <?= $is_completed ? 'Ακύρωση Ολοκλήρωσης' : 'Ολοκλήρωση' ?>
                        </button>
                    </form>
                    <form method="POST" action="delete_goal.php" style="display:inline;">
                        <input type="hidden" name="goal_id" value="<?= $goal['id'] ?>">
                        <button type="submit" style="padding:5px 10px; background:#607d8b; color:white; border:none; cursor:pointer;"
                                onclick="return confirm('Διαγραφή στόχου;');">
                            Διαγραφή
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Δεν έχετε ορίσει στόχους ακόμα.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>