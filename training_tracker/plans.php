<?php
include 'includes/header.php';
require_once 'config/db.php';

// Ανάκτηση όλων των πλάνων του χρήστη
$stmt = $pdo->prepare("SELECT * FROM workout_plans WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$plans = $stmt->fetchAll();
?>

<div class="container">
    <h1>Τα Πλάνα Προπόνησής Μου</h1>
    
    <div class="plans-grid">
        <?php foreach ($plans as $plan): ?>
            <div class="plan-card">
                <h3><?= htmlspecialchars($plan['name']) ?></h3>
                <a href="plan.php?plan_id=<?= $plan['id'] ?>" class="btn">🔍 Λεπτομέρειες</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>