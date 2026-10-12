# Unit 4: Database Connectivity with MySQL & PDO

A practical reference for relational database fundamentals, SQL, secure PDO connections, prepared statements, and CRUD operations.

---

## 1. RDBMS & SQL Fundamentals

* **RDBMS (Relational Database Management System)**: Organizes data into 2D tables composed of rows (records) and columns (attributes). Examples: MySQL, PostgreSQL, MariaDB.
* **Primary Key (PK)**: A column or set of columns uniquely identifying every row. Must be unique and non-null.
* **Foreign Key (FK)**: A column referencing the Primary Key of another table, establishing relational integrity.
* **Common Constraints**: `NOT NULL`, `UNIQUE`, `DEFAULT`, `AUTO_INCREMENT`.

### Core SQL Statements (DDL & DML)

```sql
-- DDL: Table Creation
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    course VARCHAR(50) NOT NULL,
    marks INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DML: Data Manipulation
INSERT INTO students (name, email, course, marks) VALUES ('Alice', 'alice@example.com', 'CS', 85);
SELECT * FROM students WHERE course = 'CS' ORDER BY marks DESC LIMIT 10;
UPDATE students SET marks = 90 WHERE id = 1; -- Always use WHERE!
DELETE FROM students WHERE id = 1;             -- Always use WHERE!
```

---

## 2. PHP Database Extensions: Comparison

| Extension | Status | Paradigm | Database Support | Prepared Statements |
| :--- | :--- | :--- | :--- | :--- |
| **`mysql_*`** | **Removed** (PHP 7.0) | Procedural | MySQL only | No (vulnerable) |
| **`mysqli`** | Active | Procedural & OOP | MySQL only | Yes (positional `?` only) |
| **`PDO`** | **Standard** | Object-Oriented | **12+ RDBMS drivers** | **Yes (named `:param` & `?`)** |

---

## 3. Connecting to MySQL with PDO

PDO uses a **Data Source Name (DSN)** string and throws `PDOException` on error.

```php
<?php
// config/database.php
$host    = "localhost";
$db      = "student_portal";
$user    = "root";
$pass    = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Database connection error. Please try again later.");
}
```

---

## 4. SQL Injection & Prepared Statements

* **SQL Injection (SQLi)** occurs when user input is directly concatenated into SQL strings:
  ```php
  // VULNERABLE: Direct concatenation
  $sql = "SELECT * FROM users WHERE email = '$email' AND pass = '$pass'";
  ```
* **Prepared Statements** eliminate SQLi by compiling the query structure first, then binding parameters as pure data:
  ```php
  // SECURE: Parameterized query
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
  $stmt->execute([':email' => $email]);
  ```

---

## 5. CRUD Operations with PDO

### 1. Create (`INSERT`)

```php
$sql = "INSERT INTO students (name, email, course, marks) VALUES (:name, :email, :course, :marks)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':name'   => $name,
    ':email'  => $email,
    ':course' => $course,
    ':marks'  => $marks
]);

$newId = $pdo->lastInsertId(); // Returns auto-increment ID
```

### 2. Read (`SELECT` - `fetch` vs `fetchAll`)

* **`fetch()`**: Returns a single row (or `false` if not found).
* **`fetchAll()`**: Returns an array of all matching rows.

```php
// Fetch single record by ID
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

// Fetch multiple records
$stmt = $pdo->prepare("SELECT * FROM students ORDER BY name ASC");
$stmt->execute();
$allStudents = $stmt->fetchAll();

foreach ($allStudents as $row) {
    echo htmlspecialchars($row['name']) . " - " . $row['marks'] . "<br>";
}
```

### 3. Update (`UPDATE`)

```php
$stmt = $pdo->prepare("UPDATE students SET marks = :marks WHERE id = :id");
$stmt->execute([':marks' => 95, ':id' => $id]);

echo "Rows affected: " . $stmt->rowCount();
```

### 4. Delete (`DELETE`)

```php
$stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);

if ($stmt->rowCount() > 0) {
    echo "Record deleted successfully.";
}
```
