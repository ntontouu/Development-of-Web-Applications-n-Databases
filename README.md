# Training Tracker: Web Application

[![University](https://img.shields.io/badge/University-University%20of%20Ioannina-blue.svg)](https://www.uoi.gr)
[![Department](https://img.shields.io/badge/Department-Informatics%20%26%20Telecommunications-lightgrey.svg)](https://www.cs.uoi.gr/)
[![Language](https://img.shields.io/badge/Language-PHP%20%7C%20JavaScript-blueviolet.svg)]()
[![Database](https://img.shields.io/badge/Database-MySQL-blue.svg)]()

A dynamic, full-stack web application designed for organizing, tracking, and managing physical training routines. The project incorporates relational database management, user authentication, and gamification elements to enhance user engagement.

---

## 📚 Table of Contents
1. [Overview](#-overview)
2. [Technical Architecture](#-technical-architecture)
3. [Gamification & User Experience](#-gamification--user-experience)
4. [Security Measures](#-security-measures)
5. [Getting Started & Installation](#-getting-started--installation)
6. [Author & Credits](#-author--credits)

---

## 🔍 Overview
"Training Tracker" serves as a comprehensive tool for fitness management. Users can register, create personalized weekly workout plans, log their progress (weight, performance ratings), and set specific training goals. The platform tracks completed workouts and dynamically updates the user's dashboard with historical data and progress charts.

---

## 📐 Technical Architecture
The application is built completely from scratch without external frameworks, emphasizing core web development principles:
- **Backend:** PHP handles server-side logic, session management, and CRUD operations.
- **Frontend:** HTML5, CSS3, and JavaScript deliver a responsive and interactive UI.
- **Database:** MySQL relational database utilizing structured tables for `users`, `workout_plans`, `exercises`, `progress`, and `goals`. Foreign keys strictly maintain data integrity across relationships.

### Directory Structure (Key Components)
| Directory/File | Description |
| :--- | :--- |
| **`auth/`** | Handles user registration, login, and secure session termination. |
| **`admin/`** | Dedicated panel for administrative tasks, user management, and global oversight. |
| **`database.sql`** | The SQL schema containing the table structures and relationships. |
| **`dashboard.php`** | The central hub providing personalized statistics and active workout plans. |
| **`plan.php` / `workout.php`** | Logic for creating custom plans and executing live workouts. |

---

## 🎮 Gamification & User Experience
To motivate consistency, the application integrates multiple gamification mechanics:
- **XP System:** Users earn Experience Points (XP) upon completing scheduled workouts.
- **Leveling & Progress Bars:** XP contributes to filling a dynamic progress bar. Once filled, the user "levels up," unlocking higher tiers.
- **Interactive Timers:** A countdown timer manages the pacing of live workout sessions, encouraging focus and timely completion of goals.

---

## 🛡️ Security Measures
Handling sensitive user data requires strict security implementations:
- **SQL Injection Prevention:** All database interactions are sanitized using **Prepared Statements** via PHP Data Objects/MySQLi.
- **Session Management:** Secure PHP sessions control authentication states, ensuring unauthorized users cannot access restricted pages.
- **Role-Based Access Control (RBAC):** Distinct privileges isolate standard `users` from the `admin` domain, safeguarding system settings.

---

## 🚀 Getting Started & Installation
1. **Local Environment:** Ensure [XAMPP](https://www.apachefriends.org/) (or a similar AMP stack) is installed.
2. **Deploy Code:** Clone or copy the `training_tracker` folder into your `htdocs` directory.
3. **Database Setup:** 
   - Open `phpMyAdmin` and create a new database named `training_tracker`.
   - Import the `training_tracker/database.sql` file to build the schema.
4. **Configuration:** If necessary, adjust DB credentials in `training_tracker/config/db.php`.
5. **Launch:** Start the Apache and MySQL modules in XAMPP, open a browser, and navigate to `http://localhost/training_tracker/`.

---

## 📝 Author & Academic Use
- **Student:** Maria Alexandra Ntontou (ΜΑΡΙΑ ΑΛΕΞΑΝΔΡΑ ΝΤΟΝΤΟΥ)
- **Student ID:** 2799
- **Institution:** University of Ioannina — Department of Informatics and Telecommunications
- **Course:** Development of Web Applications and Databases
