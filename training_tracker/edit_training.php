<?php
include '../includes/db.php';
include '../includes/header.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];
$query = "SELECT * FROM trainings WHERE id = $id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $duration = $_POST['duration'];
    $type = $_POST['type'];
    $notes = $_POST['notes'];

    $update = "UPDATE trainings SET date='$date', duration='$duration', type='$type', notes='$notes' WHERE id=$id";
    mysqli_query($conn, $update);
    header("Location: admin_trainings.php");
}
?>

<div class="container mt-4">
    <h2>Επεξεργασία Προπόνησης</h2>
    <form method="POST">
        <div class="form-group">
            <label>Ημερομηνία</label>
            <input type="date" name="date" class="form-control" value="<?php echo $row['date']; ?>" required>
        </div>
        <div class="form-group">
            <label>Διάρκεια (λεπτά)</label>
            <input type="number" name="duration" class="form-control" value="<?php echo $row['duration']; ?>" required>
        </div>
        <div class="form-group">
            <label>Τύπος</label>
            <input type="text" name="type" class="form-control" value="<?php echo $row['type']; ?>" required>
        </div>
        <div class="form-group">
            <label>Σημειώσεις</label>
            <textarea name="notes" class="form-control"><?php echo $row['notes']; ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Αποθήκευση</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
