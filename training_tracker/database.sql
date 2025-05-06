CREATE DATABASE IF NOT EXISTS training_tracker;
USE training_tracker;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    xp INT DEFAULT 0,
    level INT DEFAULT 1
);

CREATE TABLE exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    image VARCHAR(255)
);

CREATE TABLE workout_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100),
    day VARCHAR(10),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE plan_exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT,
    exercise_id INT,
    sets INT,
    reps INT,
    FOREIGN KEY (plan_id) REFERENCES workout_plans(id),
    FOREIGN KEY (exercise_id) REFERENCES exercises(id)
);

CREATE TABLE progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    date DATE,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    description VARCHAR(255),
    target_date DATE,
    completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
