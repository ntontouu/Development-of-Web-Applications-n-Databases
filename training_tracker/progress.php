<?php 
include 'includes/header.php';
require_once 'config/db.php';

// 1. Καταγραφή νέας προόδου
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Επεξεργασία και επικύρωση δεδομένων
    $date = $_POST['date'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $weight = isset($_POST['weight']) && $_POST['weight'] !== '' ? (float)$_POST['weight'] : null;
    $performance_rating = isset($_POST['performance_rating']) && $_POST['performance_rating'] !== '' ? (int)$_POST['performance_rating'] : null;

    // Εισαγωγή στη βάση
    try {
        $stmt = $pdo->prepare("
            INSERT INTO progress 
            (user_id, date, notes, weight, performance_rating) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $date,
            $notes,
            $weight,
            $performance_rating
        ]);
        
        $_SESSION['message'] = "✅ Η πρόοδος καταγράφηκε επιτυχώς!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Σφάλμα: " . $e->getMessage();
    }
    header("Location: progress.php");
    exit();
}

// 2. Λήψη μηνυμάτων
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

// 3. Λήψη προόδου χρήστη (15 τελευταίες καταγραφές)
$stmt = $pdo->prepare("
    SELECT * FROM progress 
    WHERE user_id = ? 
    ORDER BY date DESC 
    LIMIT 15
");
$stmt->execute([$_SESSION['user_id']]);
$progress_entries = $stmt->fetchAll();

// 4. Προετοιμασία δεδομένων για γραφήματα
$weight_data = [];
$performance_data = [];

foreach ($progress_entries as $entry) {
    if (isset($entry['weight']) && $entry['weight'] !== null) {
        $weight_data[] = [
            'date' => $entry['date'],
            'value' => $entry['weight']
        ];
    }
    if (isset($entry['performance_rating']) && $entry['performance_rating'] !== null) {
        $performance_data[] = [
            'date' => $entry['date'],
            'value' => $entry['performance_rating']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Πρόοδος Προπόνησης</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <style>
        /* ... (το ίδιο style όπως πριν) ... */
    </style>
</head>
<body>
    <div class="container">
        <h2>📈 Καταγραφή Προόδου</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <!-- Φόρμα Καταγραφής -->
        <div class="card">
            <h3>➕ Νέα Καταγραφή</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Ημερομηνία:</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Βάρος (kg):</label>
                    <input type="number" name="weight" step="0.1" placeholder="π.χ. 75.5">
                </div>
                
                <div class="form-group">
                    <label>Απόδοση (1-10):</label>
                    <input type="number" name="performance_rating" min="1" max="10" placeholder="π.χ. 8">
                </div>
                
                <div class="form-group">
                    <label>Σημειώσεις:</label>
                    <textarea name="notes" required></textarea>
                </div>
                
                <button type="submit" class="btn-primary">Αποθήκευση</button>
            </form>
        </div>

        <!-- Γραφήματα -->
        <div class="card">
            <h3>📉 Πρόοδος Βάρους</h3>
            <canvas id="weightChart" height="200"></canvas>
        </div>
        
        <div class="card">
            <h3>📊 Απόδοση Προπόνησης</h3>
            <canvas id="performanceChart" height="200"></canvas>
        </div>

        <!-- Ιστορικό -->
        <div class="card">
            <h3>🕒 Πρόσφατες Καταγραφές</h3>
            <div class="progress-entries">
                <?php if (count($progress_entries) > 0): ?>
                    <?php foreach ($progress_entries as $entry): ?>
                        <div class="entry">
                            <div class="entry-date"><?= $entry['date'] ?></div>
                            <div class="entry-content">
                                <?php if (isset($entry['weight']) && $entry['weight'] !== null): ?>
                                    <span class="badge">Βάρος: <?= $entry['weight'] ?> kg</span>
                                <?php endif; ?>
                                
                                <?php if (isset($entry['performance_rating']) && $entry['performance_rating'] !== null): ?>
                                    <span class="badge">Απόδοση: <?= $entry['performance_rating'] ?>/10</span>
                                <?php endif; ?>
                                
                                <p><?= nl2br(htmlspecialchars($entry['notes'] ?? '')) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Δεν υπάρχουν καταγραφές.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Γράφημα Βάρους
        <?php if (!empty($weight_data)): ?>
            new Chart(document.getElementById('weightChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($weight_data, 'date')) ?>,
                    datasets: [{
                        label: 'Βάρος (kg)',
                        data: <?= json_encode(array_column($weight_data, 'value')) ?>,
                        borderColor: '#4CAF50',
                        tension: 0.1
                    }]
                }
            });
        <?php else: ?>
            document.getElementById('weightChart').parentElement.innerHTML += 
                '<p style="text-align:center; color:#666;">Δεν υπάρχουν δεδομένα βάρους</p>';
        <?php endif; ?>

        // Γράφημα Απόδοσης
        <?php if (!empty($performance_data)): ?>
            new Chart(document.getElementById('performanceChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($performance_data, 'date')) ?>,
                    datasets: [{
                        label: 'Απόδοση (1-10)',
                        data: <?= json_encode(array_column($performance_data, 'value')) ?>,
                        backgroundColor: '#2196F3'
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 10
                        }
                    }
                }
            });
        <?php else: ?>
            document.getElementById('performanceChart').parentElement.innerHTML += 
                '<p style="text-align:center; color:#666;">Δεν υπάρχουν δεδομένα απόδοσης</p>';
        <?php endif; ?>
    });
    </script>
</body>
</html>