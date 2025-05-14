<?php 
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
include '../includes/header.php';
require '../includes/db.php';

// Πάρε αριθμούς
$userCountQuery = $conn->query("SELECT COUNT(*) as total FROM users");
$userCount = $userCountQuery->fetch_assoc()['total'];

$trainingCountQuery = $conn->query("SELECT COUNT(*) as total FROM trainings");
$trainingCount = $trainingCountQuery->fetch_assoc()['total'];
?>

<h2>Πίνακας Διαχειριστή</h2>
<p>Σύνολο Χρηστών: <strong><?php echo $userCount; ?></strong></p>
<p>Σύνολο Προπονήσεων: <strong><?php echo $trainingCount; ?></strong></p>

<a href="users.php">Διαχείριση Χρηστών</a> | 
<a href="trainings.php">Διαχείριση Προπονήσεων</a>

<?php include '../includes/footer.php'; ?>
