<?php
session_start();

$username = $_SESSION['username'] ?? 'Φίλε';

// Clear session
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Αποσύνδεση | FitQuest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
        }
        .logout-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        h1 {
            color: #2196F3;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h1>👋 Αποσυνδεθήκατε!</h1>
        <p>Ελπίζουμε να σας ξαναδούμε σύντομα, <?= htmlspecialchars($username) ?>!</p>
        <p>Μπορείτε πάντα να συνδεθείτε ξανά όταν θέλετε.</p>
        <a href="login.php" class="btn">Σύνδεση Ξανά</a>
    </div>
</body>
</html>