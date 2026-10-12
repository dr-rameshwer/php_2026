# PHP MySQLi: Database Connection & Form Insert

A practical guide to connecting PHP with MySQL and inserting form data using procedural MySQLi.

---

## 1. Database Setup

Run the following SQL in MySQL / phpMyAdmin to create the database and table:

```sql
CREATE DATABASE IF NOT EXISTS mydb;
USE mydb;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);
```

---

## 2. Complete Example (`insert.php`)

```php
<?php
// 1. Database configuration & connection
$host     = "localhost";
$user     = "root";
$password = "";
$database = "mydb";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// 2. Handle form submission
if (isset($_POST["submit"])) {
    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);

    if (!empty($name) && !empty($email)) {
        // Simple INSERT query
        $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";

        if (mysqli_query($conn, $sql)) {
            $message = "Record inserted successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert User</title>
</head>
<body>
    <h2>Insert User</h2>

    <?php if ($message): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required>
        </div>
        <br>
        <div>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required>
        </div>
        <br>
        <button type="submit" name="submit">Insert User</button>
    </form>
</body>
</html>
```

---

## 3. How It Works

### Step 1: Connecting to MySQL
* `mysqli_connect($host, $user, $password, $database)` opens a new connection to the MySQL server.
* If the connection fails, `$conn` evaluates to `false`. `die(mysqli_connect_error())` halts execution and outputs the specific error message.

### Step 2: Handling Form Submission
* `if (isset($_POST["submit"]))` ensures the insert logic only runs when the form is submitted via `POST`, preventing empty executions on initial page load.
* `$_POST["name"]` and `$_POST["email"]` retrieve the input values matching the input `name` attributes.

### Step 3: Executing the Query
* `mysqli_query($conn, $sql)` executes the SQL statement against the active database connection.
* Returns `true` on success. On failure, `mysqli_error($conn)` captures the MySQL engine error for debugging.

---

## 4. Security: Upgrading to Prepared Statements

Directly injecting `$_POST` variables into SQL strings causes **SQL Injection vulnerabilities**. In production, always use prepared statements:

```php
// Prepared Statement approach
$stmt = mysqli_prepare($conn, "INSERT INTO users (name, email) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $name, $email);

if (mysqli_stmt_execute($stmt)) {
    echo "Record inserted securely!";
} else {
    echo "Error: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
```

### Why Prepared Statements?
1. **Separates data from query logic:** User input is never parsed as executable SQL commands.
2. **Handles escaping automatically:** No need to manually escape quotes or special characters.

---

## 5. MySQLi Procedural Functions Reference

| Function | Purpose | Example |
| :--- | :--- | :--- |
| `mysqli_connect()` | Opens a connection to MySQL | `$conn = mysqli_connect($host, $u, $p, $db);` |
| `mysqli_connect_error()` | Returns connection error message | `die(mysqli_connect_error());` |
| `mysqli_query()` | Runs a query against the database | `mysqli_query($conn, $sql);` |
| `mysqli_error()` | Returns query error description | `echo mysqli_error($conn);` |
| `mysqli_close()` | Closes an open database connection | `mysqli_close($conn);` |
