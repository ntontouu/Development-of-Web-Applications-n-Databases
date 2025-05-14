<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
include '../includes/header.php';
require '../includes/db.php';

$query = $conn->query("SELECT t.id, u.username, t.date, t.duration, t.description 
                       FROM trainings t 
                       JOIN users u ON t.user_id = u.id 
                       ORDER BY t.date DESC");
?>

<h2>Όλες οι Προπονήσεις</h2>
<table border="1">
    <tr>
        <th>ID</th><th>Χρήστης</th><th>Ημερομηνία</th><th>Διάρκεια</th><th>Περιγραφή</th>
    </tr>
    <?php while($row = $query->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['date'] ?></td>
            <td><?= $row['duration'] ?></td>
            <td><?= $row['description'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>
