<?php
session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Training Tracker</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --primary-dark: #3a56d4;
      --secondary: #4cc9f0;
      --light: #f8f9fa;
      --dark: #212529;
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    header {
      background: rgba(255, 255, 255, 0.9);
      box-shadow: var(--shadow);
      padding: 1.5rem;
      text-align: center;
    }

    h1 {
      color: var(--primary);
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }

    .welcome-card {
      background: white;
      border-radius: 10px;
      padding: 2.5rem;
      text-align: center;
      box-shadow: var(--shadow);
      max-width: 500px;
      width: 100%;
      transition: transform 0.3s ease;
    }

    .welcome-card:hover {
      transform: translateY(-5px);
    }

    h2 {
      color: var(--dark);
      margin-bottom: 1rem;
      font-size: 1.8rem;
    }

    p {
      color: #666;
      margin-bottom: 2rem;
      font-size: 1.1rem;
      line-height: 1.6;
    }

    .btn {
      display: inline-block;
      padding: 0.8rem 1.5rem;
      margin: 0 0.5rem;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      font-size: 1rem;
    }

    .btn-login {
      background: var(--primary);
      color: white;
    }

    .btn-login:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .btn-register {
      background: white;
      color: var(--primary);
      border: 2px solid var(--primary);
    }

    .btn-register:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-2px);
    }

    footer {
      text-align: center;
      padding: 1.5rem;
      background: rgba(255, 255, 255, 0.9);
      margin-top: auto;
    }

    footer p {
      color: #666;
      margin: 0;
    }

    @media (max-width: 768px) {
      .welcome-card {
        padding: 1.5rem;
      }

      .btn {
        display: block;
        width: 100%;
        margin: 0.5rem 0;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1><i class="fas fa-dumbbell"></i> Training Tracker</h1>
  </header>

  <main>
    <div class="welcome-card">
      <h2>Καλώς ήρθες!</h2>
      <p>Οργάνωσε και παρακολούθησε τις προπονήσεις σου με ευκολία. Δημιούργησε λογαριασμό ή συνδέσου για να ξεκινήσεις.</p>
      <div class="btn-group">
        <a href="auth/login.php" class="btn btn-login"><i class="fas fa-sign-in-alt"></i> Σύνδεση</a>
        <a href="auth/register.php" class="btn btn-register"><i class="fas fa-user-plus"></i> Εγγραφή</a>
      </div>
    </div>
  </main>

  <footer>
    <p>© 2025 Training Tracker by ntontouu | Δημιουργήθηκε με <i class="fas fa-heart" style="color: #e63946;"></i></p>
  </footer>
</body>
</html>