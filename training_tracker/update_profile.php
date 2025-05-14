<?php
session_start();

// Ελεγχος συνδεδεμένου χρήστη
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

// Αρχικοποίηση μεταβλητών
$errors = [];
$success = '';

// Επεξεργασία φόρμας
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['password']);
    
    // Βασικός έλεγχος
    if (empty($email)) {
        $errors[] = "Το email είναι υποχρεωτικό";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Μη έγκυρη διεύθυνση email";
    }

    // Αν δεν υπάρχουν σφάλματα
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // 1. Ενημέρωση email
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $user_id]);
            
            // 2. Αλλαγή κωδικού (αν δόθηκε νέος)
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
            }
            
            // 3. Επεξεργασία φωτογραφίας
            if (!empty($_FILES['profile_pic']['name'])) {
                $upload_dir = 'images/profiles/';
                
                // Δημιουργία φακέλου αν δεν υπάρχει (για Windows)
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = $user_id . '_' . time() . '_' . basename($_FILES['profile_pic']['name']);
                $target_path = $upload_dir . $file_name;
                
                // Έλεγχος τύπος αρχείου
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_type = $_FILES['profile_pic']['type'];
                
                if (in_array($file_type, $allowed_types)) {
                    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
                        $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                        $stmt->execute([$target_path, $user_id]);
                    } else {
                        $errors[] = "Σφάλμα κατά τη μεταφόρτωση αρχείου";
                    }
                } else {
                    $errors[] = "Μόνο JPG, PNG ή GIF επιτρέπονται";
                }
            }
            
            $pdo->commit();
            $_SESSION['success'] = "Το προφίλ ενημερώθηκε με επιτυχία!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Σφάλμα βάσης δεδομένων: " . $e->getMessage();
        }
    }
    
    // Αποθήκευση σφαλμάτων στη session
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }
    
    header("Location: profile.php");
    exit();
}
?>