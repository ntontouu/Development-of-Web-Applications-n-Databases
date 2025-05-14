<?php
include 'includes/db.php';
include 'includes/header.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Αναζήτηση με βάση όνομα χρήστη
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT trainings.id, trainings.date, trainings.duration, trainings.type, users.username 
        FROM trainings 
        JOIN users ON trainings.user_id = users.id";

if ($search !== '') {
    $sql .= " WHERE users.username LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

// Διαγραφή προπόνησης
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $deleteStmt = $conn->prepare("DELETE FROM trainings WHERE id = ?");
    $deleteStmt->bind_param("i", $delete_id);
    $deleteStmt->execute();
    header("Location: admin_trainings.php");
    exit();
}
?>

<style>
    .admin-container {
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 0 10px #ccc;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: center;
    }

    th {
        background-color: #17a2b8;
        color: white;
    }

    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        color: white;
        cursor: pointer;
    }

    .btn-delete {
        background-color: #dc3545;
    }

    .search-form {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .search-form input {
        flex: 1;
        padding: 6px;
    }

    .btn-search {
        background-color: #28a745;
        margin-left: 10px;
    }
</style>

<div class="admin-container">
    <h2>Διαχείριση Προπονήσεων</h2>

    <form method="get" class="search-form">
        <input type="text" name="search" placeholder="Αναζήτηση με βάση το username..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-search">Αναζήτηση</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Χρήστης</th>
            <th>Ημερομηνία</th>
            <th>Διάρκεια (λεπτά)</th>
            <th>Τύπος</th>
            <th>Ενέργεια</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= $row['duration'] ?></td>
                <td><?= htmlspecialchars($row['type']) ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Σίγουρα;')">
                        <button class="btn btn-delete">Διαγραφή</button>
                    </a>
                </td>
                <td>
                    <a href="edit_training.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Επεξεργασία</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
