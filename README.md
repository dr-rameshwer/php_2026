# IKGPTU BCA PHP MASTER STUDY GUIDE & PROJECT REPOSITORY
## Bachelor of Computer Applications (BCA) — Semester Examination & Practical Master Blueprint
### Based on I. K. Gujral Punjab Technical University (IKGPTU) Official Syllabus

---

Welcome to the **Complete, Exam-Oriented PHP Master Guide and Working Web Application Suite**, specially crafted for 1st-Year BCA students.

Whether you have never written a line of code in your life or you are preparing for your university theory exams, lab practicals, and viva voce, this master repository covers every single requirement from absolute zero to building a secure, production-grade CRUD Web Application.

---

## 📚 Master Curriculum Table of Contents

| Unit / Module | Title | Syllabus Focus & Key Topics |
| :--- | :--- | :--- |
| **Part 0** | [00_PROGRAMMING_AND_WEB_FOUNDATIONS.md](file:///Users/rameshwer/php_2026/00_PROGRAMMING_AND_WEB_FOUNDATIONS.md) | Computer programming from scratch, Compilers vs Interpreters, Client-Server architecture, HTTP lifecycle, Web servers (Apache), XAMPP setup. |
| **Unit I** | [UNIT_01_INTRODUCTION_TO_PHP.md](file:///Users/rameshwer/php_2026/UNIT_01_INTRODUCTION_TO_PHP.md) | Evolution of PHP, System interfaces, Hardware/Software requirements, First PHP script, Basic syntax, 8 Data types, Type display (`var_dump`, `gettype`), Type testing (`is_*`), Type conversion (`settype`, casting), All operators, Variable manipulation (`isset`, `empty`, `unset`), Dynamic variables (`$$var`), Variable scope (`local`, `global`, `static`). |
| **Unit II** | [UNIT_02_CONTROL_FUNCTIONS_STRINGS_ARRAYS.md](file:///Users/rameshwer/php_2026/UNIT_02_CONTROL_FUNCTIONS_STRINGS_ARRAYS.md) | **Control Statements:** `if`, `else`, `elseif`, `switch`, `?:` ternary. <br>**Loops:** `while`, `do-while`, `for`. <br>**Functions:** Creation, return values, library vs user-defined, dynamic functions, default arguments, pass by value. <br>**Strings:** Quotes, presentation/storage formatting, `implode`, `explode`, `strcmp`. <br>**Arrays:** Indexed, associative, `foreach`, and historical `each()`. |
| **Unit III** | [UNIT_03_FORMS_FILES_DIRECTORIES_GRAPHICS.md](file:///Users/rameshwer/php_2026/UNIT_03_FORMS_FILES_DIRECTORIES_GRAPHICS.md) | **Forms:** HTML form controls, GET vs POST, Superglobals (`$_GET`, `$_POST`, `$_SERVER`, etc.), Sanitization (`htmlspecialchars`, `trim`), Hidden fields, Header redirection. <br>**Files & Directories:** `fopen`, `fread`, `fwrite`, `fclose`, `file_get_contents`, `file_put_contents`, `copy`, `rename`, `unlink`, `mkdir`, `scandir`. <br>**File Uploads & Downloads:** `$_FILES` anatomy, validation, safe download. <br>**Image Generation:** Computer graphics basics, PHP GD library. |
| **Unit IV** | [UNIT_04_RDBMS_MYSQL_AND_PDO.md](file:///Users/rameshwer/php_2026/UNIT_04_RDBMS_MYSQL_AND_PDO.md) | RDBMS concepts, Tables, Keys (Primary, Foreign), DDL vs DML, SQL (`CREATE`, `INSERT`, `SELECT`, `UPDATE`, `DELETE`, `WHERE`, `ORDER BY`, `LIMIT`), Database connectivity (mysqli vs PDO), PDO Connection with DSN, Prepared Statements against SQL Injection, CRUD architecture. |
| **Unit V** | [UNIT_05_WEB_SECURITY_AND_BOOTSTRAP.md](file:///Users/rameshwer/php_2026/UNIT_05_WEB_SECURITY_AND_BOOTSTRAP.md) | Web Security Fundamentals (SQLi, XSS, CSRF, Password Hashing with Bcrypt, Upload validation), Bootstrap 5 complete primer (Containers, Grid, Navbars, Cards, Forms, Alerts, Tables). |
| **Project**| [PROJECT_MANUAL_STUDENT_MANAGEMENT_SYSTEM.md](file:///Users/rameshwer/php_2026/PROJECT_MANUAL_STUDENT_MANAGEMENT_SYSTEM.md) | Complete manual, architecture diagrams, step-by-step setup, and line-by-line explanation of the **BCA Student Management System** located in `bca-student-management/`. |

---

## 🚀 Complete Practical Project: BCA Student Management System

Inside the [bca-student-management](file:///Users/rameshwer/php_2026/bca-student-management/) directory, you will find a complete, production-grade, 100% working PHP & MySQL web application built with:
- **PHP 8+ / PDO** with secure prepared statements (Zero SQL injection risk).
- **MySQL / MariaDB** with normalized relational database schema and seed data.
- **Bootstrap 5 & Bootstrap Icons** for a responsive, modern UI.
- **Session-Based Authentication** with `password_hash()` and `password_verify()`.
- **Full CRUD operations**: Create, Read, Update, Delete students.
- **Search & Pagination**: Live query filtering and page splitting.
- **Secure File Upload & Safe Download**: For student profile photos and academic attachments.
- **Flash Messaging**: Beautiful dismissible Bootstrap toast/alert notifications.

### Quick Run Instructions
1. Copy the `bca-student-management` folder into your Apache document root:
   - **XAMPP (Windows):** `C:\xampp\htdocs\bca-student-management`
   - **XAMPP (Mac):** `/Applications/XAMPP/xamppfiles/htdocs/bca-student-management`
   - **LAMP (Linux):** `/var/www/html/bca-student-management`
2. Start **Apache** and **MySQL** in your XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin` in your web browser.
4. Import the SQL schema file:
   - File location: `database/schema.sql`
5. Visit the application in your browser:
   - URL: `http://localhost/bca-student-management/public/login.php`
   - **Default Admin Credentials**:
     - Email: `admin@bca.edu`
     - Password: `AdminPassword123`

---

## 🎓 Pedagogical Structure for Every Concept
Every topic across these study modules follows a 15-point standard pedagogical framework:
1. **Simple Definition**: Plain English explanation without technical jargon.
2. **Why It Exists**: The real-world computational problem it solves.
3. **Where It Is Used**: Real industry use-case.
4. **Formal Syntax**: The code blueprint and grammatical rules.
5. **Syntax Component Breakdown**: Detailed dissection of keywords and symbols.
6. **Minimal Working Example**: Smallest executable snippet.
7. **Line-by-Line Explanation**: Explaining every token, semicolon, and variable.
8. **Expected Output**: What appears in the terminal or browser.
9. **Real-World Analogy**: Non-technical metaphor (kitchen, library, post office).
10. **Practical Application Example**: Slightly larger, real-scenario program.
11. **Common Beginner Mistakes**: Common pitfalls and how to avoid them.
12. **Exam-Oriented Definition**: Exact text to write in IKGPTU university answer sheets.
13. **Likely University Exam Questions**: 2-mark, 5-mark, and 10-mark questions.
14. **Viva Voce Questions & Answers**: High-frequency teacher questions.
15. **Student Practice Exercises**: Hands-on homework problems.
