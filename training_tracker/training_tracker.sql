-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Εξυπηρετητής: 127.0.0.1
-- Χρόνος δημιουργίας: 06 Μάη 2025 στις 02:41:41
-- Έκδοση διακομιστή: 10.4.32-MariaDB
-- Έκδοση PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `training_tracker`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `muscle_group` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `exercises`
--

INSERT INTO `exercises` (`id`, `title`, `description`, `muscle_group`, `category`, `image`) VALUES
(1, 'Καθίσματα', 'Σκύψτε τα γόνατα σε γωνία 90 μοιρών και σηκωθείτε επαναλαμβανόμενα', NULL, 'Καρδιακή', 'squats.jpg'),
(2, 'Ώμους', 'Σηκώστε βάρη από το ύψος των ώμων προς τα πάνω με τεντωμένα χέρια', NULL, 'Αντοχή', 'shoulder_press.jpg'),
(3, 'Τραβήγματα', 'Τραβήξτε μια μπάρα προς το στήθος σας από ψηλά κρατώντας τις παλάμες προς τα μέσα', NULL, 'Αντοχή', 'pullups.jpg'),
(4, 'Βάρη', 'Σηκώστε βάρη από το πλάι προς το ύψος των ώμων με λυγισμένα χέρια', NULL, 'Δύναμη', 'dumbbells.jpg'),
(5, 'Πλάτη', 'Ξαπλώστε στο πάτωμα και σηκώστε το πάνω μέρος του σώματος προς τα γόνατα', NULL, 'Κοιλιακοί', 'sit-ups.jpg'),
(6, 'Σανίδα', 'Κρατήστε τη θέση push-up για μεγάλο χρονικό διάστημα', NULL, 'Πυρήνας', 'plank.jpg'),
(7, 'Γόνατα', 'Πηδήξτε εναλλάσσοντας τα γόνατα ψηλά ενώ τρέχετε στη θέση σας', NULL, 'Καρδιακή', 'high_knees.jpg');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `description`, `target_date`, `completed`) VALUES
(1, 2, 'abs', '2025-05-30', 0),
(2, 2, 'abs', '2025-05-30', 0);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `plan_exercises`
--

CREATE TABLE `plan_exercises` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `exercise_id` int(11) DEFAULT NULL,
  `sets` int(11) DEFAULT NULL,
  `reps` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `performance_rating` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `progress`
--

INSERT INTO `progress` (`id`, `user_id`, `date`, `notes`, `weight`, `performance_rating`) VALUES
(1, 1, '2025-05-29', 'ewdfw', NULL, NULL),
(2, 1, '2025-05-06', 'εφαγα πολυ το πασχα', 52.20, 8),
(3, 1, '2025-07-24', 'τρωω μονο σταφυλια', 51.00, 7);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `xp` int(11) DEFAULT 0,
  `level` int(11) DEFAULT 1,
  `profile_pic` varchar(255) DEFAULT 'images/default-avatar.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `xp`, `level`, `profile_pic`) VALUES
(1, 'ntontouu-admin', 'maria.alexadra.ntontou@gmail.com', '$2y$10$nbRQBz8WC2CNmRUEWy6PDOyTxksOQkMW9afwsPKXpwEgg9IFlgORa', 'admin', 48, 1, 'images/profiles/1_1746482900_Screenshot 2025-03-18 194742.png'),
(2, 'admin', 'j.spoiler75@gmail.com', '$2y$10$CLO24522vMGYakwBWDlOouOfC835x50rZsxhne4gKaRg9SsBBpiIy', 'user', 0, 1, 'images/default-avatar.jpg'),
(3, 'ilias', 'zamponotyri@gmail.com', '$2y$10$A0PDweUHBJW8P3.Yo2AFVey4zH7rcA5tcHP8xYnUBJSJ7il5XDUt6', 'user', 0, 1, 'images/default-avatar.jpg'),
(4, 'ntontouu2', 'int02799@uoi.gr', '$2y$10$Lg6eBYj2EQ9fxpAZmEh4mOp5ylZRcfDz3mowdcd0yyuskS0jJB/HS', 'user', 0, 1, 'images/default-avatar.jpg');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `workout_logs`
--

CREATE TABLE `workout_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `completed_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `workout_logs`
--

INSERT INTO `workout_logs` (`id`, `user_id`, `plan_id`, `plan_name`, `duration_minutes`, `completed_at`) VALUES
(1, 1, 14, 'Maria', 1, '2025-05-06 02:00:45'),
(2, 1, 14, 'Maria', 1, '2025-05-06 02:01:33'),
(3, 1, 14, 'Maria', 1, '2025-05-06 02:05:43'),
(4, 1, 14, 'Maria', 1, '2025-05-06 02:08:26');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `workout_plans`
--

CREATE TABLE `workout_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `day` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `workout_plans`
--

INSERT INTO `workout_plans` (`id`, `user_id`, `name`, `day`) VALUES
(1, 2, 'Maria', 'Saturday');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Ευρετήρια για πίνακα `plan_exercises`
--
ALTER TABLE `plan_exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Ευρετήρια για πίνακα `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Ευρετήρια για πίνακα `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Ευρετήρια για πίνακα `workout_logs`
--
ALTER TABLE `workout_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Ευρετήρια για πίνακα `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT για πίνακα `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT για πίνακα `plan_exercises`
--
ALTER TABLE `plan_exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT για πίνακα `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT για πίνακα `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT για πίνακα `workout_logs`
--
ALTER TABLE `workout_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT για πίνακα `workout_plans`
--
ALTER TABLE `workout_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Περιορισμοί για πίνακα `plan_exercises`
--
ALTER TABLE `plan_exercises`
  ADD CONSTRAINT `plan_exercises_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `workout_plans` (`id`),
  ADD CONSTRAINT `plan_exercises_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`);

--
-- Περιορισμοί για πίνακα `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Περιορισμοί για πίνακα `workout_logs`
--
ALTER TABLE `workout_logs`
  ADD CONSTRAINT `workout_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Περιορισμοί για πίνακα `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD CONSTRAINT `workout_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
