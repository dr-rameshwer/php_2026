# Simple PHP MySQLi INSERT Form — Beginner Notes

This project is a **very simple PHP + MySQLi example**.

It demonstrates how to:

- Connect PHP to a MySQL database
- Check whether the database connection worked
- Create an HTML form
- Send form data using `POST`
- Read form values using `$_POST`
- Create an SQL `INSERT` query
- Execute the query with MySQLi
- Handle success and errors
- Understand the complete flow from browser → PHP → MySQL

> **Important:** This example is intentionally simple for learning. It directly places form values into SQL. In real applications, use **prepared statements** to prevent SQL injection.

---

## 1. Project Structure

For this version, everything is in **one PHP file**:

```text
DBCONNECTION_2026/
│
└── insert.php
```

The file contains two main parts:

```text
insert.php
│
├── PHP
│   ├── Database connection
│   ├── Form submission check
│   ├── Read form data
│   ├── Create INSERT query
│   └── Execute query
│
└── HTML
    ├── Heading
    └── Input form
```

---

# 2. Database Structure

Before running the PHP program, create a database and table in MySQL.

## Create database

```sql
CREATE DATABASE mydb;
```

Select the database:

```sql
USE mydb;
```

Create the `users` table:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100)
);
```

The table will look like:

|  id | name  | email           |
| --: | ----- | --------------- |
|   1 | Rahul | rahul@gmail.com |
|   2 | Amit  | amit@gmail.com  |

### Understanding the columns

### `id`

```sql
id INT AUTO_INCREMENT PRIMARY KEY
```

- `INT` means the value is an integer.
- `AUTO_INCREMENT` automatically generates the next ID.
- `PRIMARY KEY` uniquely identifies each record.

### `name`

```sql
name VARCHAR(100)
```

Stores text up to 100 characters.

### `email`

```sql
email VARCHAR(100)
```

Stores the user's email address.

---

# 3. Complete Program

```php
<?php

// ==========================================
// 1. DATABASE CONNECTION
// ==========================================

// Database details
$host = "localhost";
$user = "root";
$password = "";
$database = "mydb";

// Create database connection
$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// ==========================================
// 2. INSERT DATA
// ==========================================

// Check whether the form was submitted
if (isset($_POST["submit"])) {

    // Get name from the form
    $name = $_POST["name"];

    // Get email from the form
    $email = $_POST["email"];


    // Create INSERT query
    $sql = "INSERT INTO users (name, email)
            VALUES ('$name', '$email')";


    // Execute INSERT query
    if (mysqli_query($conn, $sql)) {

        echo "Record inserted successfully";

    } else {

        echo "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Insert User</title>
</head>

<body>

    <h2>Insert User</h2>

    <!-- User input form -->
    <form method="POST">

        <!-- Name -->
        <label>Name:</label>
        <input type="text" name="name">

        <br><br>

        <!-- Email -->
        <label>Email:</label>
        <input type="email" name="email">

        <br><br>

        <!-- Submit -->
        <button type="submit" name="submit">
            Insert User
        </button>

    </form>

</body>

</html>
```

---

# 4. Concept 1 — PHP Opening Tag

```php
<?php
```

This tells the server:

> "The following code is PHP."

PHP code normally ends with:

```php
?>
```

In a file containing only PHP, the closing `?>` can often be omitted. Here it is shown because this example switches from PHP to HTML.

---

# 5. Concept 2 — Variables

PHP variables start with `$`.

Example:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "mydb";
```

These variables store database connection information.

### `$host`

```php
$host = "localhost";
```

`localhost` means the MySQL server is running on the same computer.

### `$user`

```php
$user = "root";
```

This is the MySQL username.

### `$password`

```php
$password = "";
```

In this XAMPP beginner setup, the MySQL `root` account may have an empty password.

### `$database`

```php
$database = "mydb";
```

This tells PHP which database to use.

---

# 6. Concept 3 — MySQLi

MySQLi means:

> **MySQL Improved**

PHP provides MySQLi for communicating with MySQL databases.

There are two common MySQLi styles:

### Procedural

```php
mysqli_connect();
mysqli_query();
```

### Object-Oriented

```php
$conn = new mysqli();
$conn->query();
```

This project uses the **procedural MySQLi style**.

---

# 7. Concept 4 — Creating the Database Connection

The connection is created with:

```php
$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);
```

The four arguments are:

```text
mysqli_connect(
    host,
    username,
    password,
    database
)
```

For this project:

```text
localhost
    ↓
root
    ↓
empty password
    ↓
mydb
```

The resulting connection is stored in:

```php
$conn
```

Think of `$conn` as:

> "The connection between PHP and MySQL."

---

# 8. Concept 5 — Checking the Connection

After creating the connection:

```php
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
```

### What does `!$conn` mean?

The `!` means **NOT**.

So:

```php
if (!$conn)
```

means:

> "If the connection was NOT successful..."

Then:

```php
die();
```

stops the PHP program.

The error message is:

```php
mysqli_connect_error()
```

which gives information about why the connection failed.

---

# 9. Concept 6 — HTML Form

The HTML form is:

```html
<form method="POST"></form>
```

The form collects data from the user.

It contains:

```html
<input type="text" name="name" />
```

and:

```html
<input type="email" name="email" />
```

The user might enter:

```text
Name: Rahul
Email: rahul@gmail.com
```

---

# 10. Concept 7 — `method="POST"`

The form uses:

```html
<form method="POST"></form>
```

`POST` sends the form data to PHP.

The data becomes available in PHP through:

```php
$_POST
```

For example:

```php
$_POST["name"]
```

gets the value from:

```html
<input type="text" name="name" />
```

And:

```php
$_POST["email"]
```

gets the value from:

```html
<input type="email" name="email" />
```

The `name` attribute of the HTML input is important.

For example:

```html
<input type="text" name="name" />
```

matches:

```php
$_POST["name"]
```

---

# 11. Concept 8 — Submit Button

The button is:

```html
<button type="submit" name="submit">Insert User</button>
```

Notice:

```html
name="submit"
```

This allows PHP to check whether the form was submitted:

```php
if (isset($_POST["submit"])) {
```

---

# 12. Concept 9 — `isset()`

This code:

```php
isset($_POST["submit"])
```

checks whether `$_POST["submit"]` exists.

So:

```php
if (isset($_POST["submit"])) {
```

means:

> "If the user submitted the form, execute the following code."

Without this check, the insertion code could run when the page is simply opened.

---

# 13. Concept 10 — Reading Form Data

The name is read using:

```php
$name = $_POST["name"];
```

The email is read using:

```php
$email = $_POST["email"];
```

Example:

If the user enters:

```text
Name: Rahul
Email: rahul@gmail.com
```

then PHP gets:

```php
$name = "Rahul";
$email = "rahul@gmail.com";
```

---

# 14. Concept 11 — SQL INSERT

The SQL query is:

```php
$sql = "INSERT INTO users (name, email)
        VALUES ('$name', '$email')";
```

The SQL itself is:

```sql
INSERT INTO users (name, email)
VALUES ('Rahul', 'rahul@gmail.com');
```

It means:

> Insert a new record into the `users` table.

We specify the columns:

```sql
(name, email)
```

and then provide the values:

```sql
('Rahul', 'rahul@gmail.com')
```

The `id` is not included because MySQL automatically generates it using `AUTO_INCREMENT`.

---

# 15. Concept 12 — Executing the Query

Creating the SQL string does NOT execute it.

This:

```php
$sql = "INSERT INTO users ...";
```

only creates the SQL statement.

This actually sends it to MySQL:

```php
mysqli_query($conn, $sql);
```

The two important pieces are:

```text
$conn
```

= database connection

```text
$sql
```

= SQL query

So:

```php
mysqli_query($conn, $sql);
```

means:

> Execute this SQL query using this database connection.

---

# 16. Concept 13 — `if/else`

The program checks whether the query succeeded:

```php
if (mysqli_query($conn, $sql)) {

    echo "Record inserted successfully";

} else {

    echo "Error: " . mysqli_error($conn);
}
```

If successful:

```php
echo "Record inserted successfully";
```

If unsuccessful:

```php
echo "Error: " . mysqli_error($conn);
```

---

# 17. Concept 14 — `echo`

`echo` displays output in the browser.

Example:

```php
echo "Record inserted successfully";
```

The browser displays:

```text
Record inserted successfully
```

---

# 18. Concept 15 — `mysqli_error()`

If the SQL query fails:

```php
mysqli_error($conn)
```

gets the MySQL error message.

For example, if the table doesn't exist, MySQL may return an error explaining that.

This is useful while learning and debugging.

---

# 19. Complete Data Flow

The complete program works like this:

```text
                    Browser
                       |
                       |
                 Open insert.php
                       |
                       v
             PHP creates connection
                       |
                       v
                MySQL connection
                       |
                       v
                  Show HTML form
                       |
                       v
             User enters information
                       |
                       v
                  Click Submit
                       |
                       v
                  POST request
                       |
                       v
               $_POST["name"]
               $_POST["email"]
                       |
                       v
                 Create SQL
                       |
                       v
                INSERT INTO users
                       |
                       v
               mysqli_query()
                       |
                       v
                    MySQL
                       |
                       v
              Record is inserted
                       |
                       v
          "Record inserted successfully"
```

---

# 20. Important Relationship Between HTML and PHP

This:

```html
<input type="text" name="name" />
```

connects to:

```php
$_POST["name"]
```

And:

```html
<input type="email" name="email" />
```

connects to:

```php
$_POST["email"]
```

Think of it as:

```text
HTML                         PHP

name="name"       →         $_POST["name"]

name="email"      →         $_POST["email"]

name="submit"     →         $_POST["submit"]
```

---

# 21. What Happens When the Page Is First Opened?

When you first open:

```text
http://localhost/dbconnection_2026/insert.php
```

the database connection is created.

Then PHP checks:

```php
isset($_POST["submit"])
```

There is no submitted form yet, so the INSERT code does not execute.

The HTML form is displayed.

---

# 22. What Happens After Clicking Insert?

Suppose the user enters:

```text
Name: John
Email: john@gmail.com
```

Then clicks:

```text
Insert User
```

PHP receives:

```php
$_POST["name"]
```

as:

```text
John
```

and:

```php
$_POST["email"]
```

as:

```text
john@gmail.com
```

The SQL becomes:

```sql
INSERT INTO users (name, email)
VALUES ('John', 'john@gmail.com');
```

MySQL executes the query.

The database now contains the new record.

---

# 23. Why `$conn` Is Important

The variable:

```php
$conn
```

is created here:

```php
$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);
```

Then it is used here:

```php
mysqli_query($conn, $sql);
```

So the relationship is:

```text
mysqli_connect()
       |
       v
     $conn
       |
       v
mysqli_query($conn, $sql)
       |
       v
     MySQL
```

If `$conn` does not exist, `mysqli_query()` cannot know which database connection it should use.

---

# 24. Procedural MySQLi Concepts Used

This program uses the following procedural MySQLi functions:

### Connect

```php
mysqli_connect()
```

Creates a connection to MySQL.

### Check connection error

```php
mysqli_connect_error()
```

Gets the connection error.

### Execute query

```php
mysqli_query()
```

Executes an SQL query.

### Get query error

```php
mysqli_error()
```

Gets an error from the database operation.

---

# 25. Beginner Terms to Remember

| Term               | Meaning                            |
| ------------------ | ---------------------------------- |
| PHP                | Server-side programming language   |
| MySQL              | Database system                    |
| MySQLi             | PHP interface for MySQL            |
| `$conn`            | Database connection                |
| `$sql`             | SQL query stored in a PHP variable |
| `$_POST`           | Contains data submitted using POST |
| `isset()`          | Checks whether something exists    |
| `mysqli_connect()` | Creates database connection        |
| `mysqli_query()`   | Executes SQL                       |
| `mysqli_error()`   | Gets database error                |
| `echo`             | Displays output                    |
| `die()`            | Stops program execution            |
| `INSERT`           | Adds a new record                  |
| `VARCHAR`          | Stores text                        |
| `AUTO_INCREMENT`   | Automatically generates numbers    |
| `PRIMARY KEY`      | Uniquely identifies a row          |

---

# 26. Security Note — SQL Injection

The current code uses:

```php
$sql = "INSERT INTO users (name, email)
        VALUES ('$name', '$email')";
```

This is useful for understanding the basic concept, but it is **not safe for production** because user input is directly placed into SQL.

For real applications, use a **prepared statement**:

```php
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users (name, email) VALUES (?, ?)"
);

mysqli_stmt_bind_param($stmt, "ss", $name, $email);

mysqli_stmt_execute($stmt);
```

The important concept is:

```text
Basic learning version
        ↓
Directly put values into SQL

Production version
        ↓
Prepared statement
        ↓
Protect against SQL injection
```

Learn the simple version first, then move to prepared statements.

---

# 27. Current Project Limitations

This beginner example currently:

- Inserts records
- Does not display existing records
- Does not validate all input
- Does not use prepared statements
- Does not redirect after insertion
- Does not have a separate database connection file

These are intentional so the basic INSERT flow is easy to understand.

---

# 28. Next Step — READ

The natural next step is to create:

```text
DBCONNECTION_2026/
│
├── insert.php
└── get_records.php
```

`insert.php` will:

```text
Form
 ↓
POST
 ↓
INSERT
 ↓
Redirect
 ↓
get_records.php
```

`get_records.php` will:

```text
SELECT * FROM users
        ↓
Fetch records
        ↓
Display records in HTML table
```

This will introduce the **READ/SELECT** part of CRUD.

---

# 29. CRUD

Once INSERT and SELECT are understood, the four basic database operations are:

```text
C = CREATE
R = READ
U = UPDATE
D = DELETE
```

In this project:

```text
CREATE → INSERT
READ   → SELECT
UPDATE → UPDATE
DELETE → DELETE
```

A typical PHP/MySQL application eventually implements all four operations.
