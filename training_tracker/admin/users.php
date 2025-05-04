<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
include '../includes/header.php';
require '../includes/db.php';

$result = $conn->query("SELECT id, username, email, role FROM users");
?>

<h2>Χρήστες</h2>
<table border="1">
    <tr>
        <th>ID</th><th>Όνομα χρήστη</th><th>Email</th><th>Ρόλος</th><th>Ενέργεια</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['role'] ?></td>
            <td>
                <?php if($row['role'] !== 'admin'): ?>
                    <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Σίγουρα;')">Διαγραφή</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>
