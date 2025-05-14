<?php
session_start();
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validation
    if (empty($username)) {
        $errors['username'] = "Το όνομα χρήστη είναι υποχρεωτικό";
    } elseif (strlen($username) < 4) {
        $errors['username'] = "Το όνομα χρήστη πρέπει να έχει τουλάχιστον 4 χαρακτήρες";
    }

    if (empty($email)) {
        $errors['email'] = "Το email είναι υποχρεωτικό";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Μη έγκυρη διεύθυνση email";
    }

    if (empty($password)) {
        $errors['password'] = "Ο κωδικός είναι υποχρεωτικός";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Οι κωδικοί δεν ταιριάζουν";
    }

    // Check if username/email exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors['general'] = "Το όνομα χρήστη ή το email χρησιμοποιείται ήδη";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password]);
                
                $_SESSION['success_message'] = "Εγγραφήκατε με επιτυχία! Μπορείτε τώρα να συνδεθείτε.";
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors['general'] = "Σφάλμα βάσης δεδομένων: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εγγραφή | FitQuest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2196F3;
            --error: #f44336;
            --success: #4CAF50;
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
        
        .register-container {
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
            margin-bottom: 1rem;
            position: relative;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-gray);
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
        
        .error-message {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: block;
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
            margin-top: 1rem;
        }
        
        button:hover {
            background: #1976D2;
        }
        
        .login-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        
        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .password-strength {
            height: 4px;
            background: #eee;
            margin-top: 0.5rem;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            background: var(--error);
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Δημιουργία Λογαριασμού</h2>
        
        <?php if (isset($errors['general'])): ?>
            <div class="error-message" style="margin-bottom: 1rem;"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>
        
        <form method="POST" id="registerForm">
            <div class="input-group">
                <label for="username">Όνομα Χρήστη</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                <?php if (isset($errors['username'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['username']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="input-group">
                <label for="email">Διεύθυνση Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="input-group">
                <label for="password">Κωδικός Πρόσβασης</label>
                <input type="password" id="password" name="password" required>
                <span class="password-toggle" onclick="togglePassword('password')">👁️</span>
                <div class="password-strength">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="input-group">
                <label for="confirm_password">Επιβεβαίωση Κωδικού</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span class="password-toggle" onclick="togglePassword('confirm_password')">👁️</span>
                <?php if (isset($errors['confirm_password'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></span>
                <?php endif; ?>
            </div>
            
            <button type="submit">Εγγραφή</button>
        </form>
        
        <div class="login-link">
            Έχετε ήδη λογαριασμό; <a href="login.php">Σύνδεση</a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('strengthBar');
            let strength = 0;
            
            if (password.length > 0) strength += 20;
            if (password.length >= 8) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;
            
            strengthBar.style.width = strength + '%';
            
            if (strength < 40) {
                strengthBar.style.backgroundColor = '#f44336'; // Red
            } else if (strength < 80) {
                strengthBar.style.backgroundColor = '#ff9800'; // Orange
            } else {
                strengthBar.style.backgroundColor = '#4CAF50'; // Green
            }
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Οι κωδικοί δεν ταιριάζουν!');
            }
        });
    </script>
</body>
</html>