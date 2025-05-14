<?php
include 'includes/db.php';
include 'includes/header.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Αναζήτηση
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT id, username, email, role FROM users";
if ($search !== '') {
    $sql .= " WHERE username LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();

// Διαγραφή χρήστη
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->bind_param("i", $delete_id);
    $deleteStmt->execute();
    header("Location: admin_panel.php");
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

    table th, table td {
        padding: 12px;
        border: 1px solid #ccc;
        text-align: center;
    }

    th {
        background: #007BFF;
        color: white;
    }

    .btn {
        padding: 6px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        color: white;
        font-weight: bold;
    }

    .btn-delete {
        background: #dc3545;
    }

    .btn-delete:hover {
        background: #c82333;
    }

    .btn-search {
        background: #28a745;
    }

    .search-form {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-form input {
        padding: 6px;
        width: 70%;
    }

</style>

<div class="admin-container">
    <h2>Πίνακας Διαχείρισης Χρηστών</h2>

    <form method="get" class="search-form">
        <input type="
