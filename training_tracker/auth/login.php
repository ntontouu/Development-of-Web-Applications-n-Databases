<?php
session_start();
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = "Συμπλήρωσε όλα τα πεδία!";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, level, xp FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);
            
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["level"] = $user["level"];
            $_SESSION["xp"] = $user["xp"];
            
            header("Location: ../dashboard.php");
            exit();
        } else {
            $error = "Λάθος όνομα χρήστη ή κωδικός!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σύνδεση | FitQuest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2196F3;
            --error: #f44336;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--light-gray);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        h2 {
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
        }
        
        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #1976D2;
        }
        
        .error-message {
            color: var(--error);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .register-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        
        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Σύνδεση</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Όνομα χρήστη" required>
            </div>
            
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Κωδικός πρόσβασης" required>
                <span class="password-toggle" onclick="togglePassword()">👁️</span>
            </div>
            
            <button type="submit">Είσοδος</button>

            <?php if (isset($_GET['logout']) && isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_GET['message']) ?>
                </div>
            <?php endif; ?>


        </form>
        
        <div class="register-link">
            Δεν έχεις λογαριασμό; <a href="register.php">Εγγραφή</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>