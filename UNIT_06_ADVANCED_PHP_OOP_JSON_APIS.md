# Unit 6: Object-Oriented PHP, JSON APIs, Error Handling & Transactions

A practical guide to OOP, exceptions, session/cookie state, REST APIs, database transactions, and modern PHP 8+ features.

---

## 1. Object-Oriented Programming (OOP) in PHP

### Classes, Objects, and `$this`

* **Class**: The blueprint defining properties (state) and methods (behavior).
* **Object**: A concrete instance of a class allocated in memory.
* **`$this`**: Refers to the current calling object instance.

```php
<?php
class Student {
    public int $id;
    public string $name;
    private float $marks;

    public function __construct(int $id, string $name, float $marks) {
        $this->id = $id;
        $this->name = $name;
        $this->marks = $marks;
    }

    public function getGrade(): string {
        return ($this->marks >= 50) ? "Pass" : "Fail";
    }
}

$s1 = new Student(101, "Alice", 85.0);
echo "{$s1->name}: " . $s1->getGrade();
```

### Access Modifiers (Encapsulation)

| Modifier | Access Scope |
| :--- | :--- |
| **`public`** | Accessible anywhere (inside class, subclasses, external code) |
| **`protected`** | Accessible only within the defining class and its subclasses (`extends`) |
| **`private`** | Accessible strictly within the defining class itself |

### Inheritance (`extends`) & Polymorphism

Child classes inherit public and protected members from a parent class and can override methods:

```php
class Person {
    public function __construct(public string $name) {}
    public function getRole(): string { return "Member"; }
}

class CollegeStudent extends Person {
    // Method Overriding
    public function getRole(): string { return "Student: {$this->name}"; }
}
```

### Abstract Classes vs. Interfaces

| Feature | Interface (`interface` / `implements`) | Abstract Class (`abstract class` / `extends`) |
| :--- | :--- | :--- |
| **Implementation** | Method signatures only; no concrete code | Can contain both abstract and concrete methods |
| **Properties** | Constants only (`const`); no properties | Can declare properties with any access modifier |
| **Inheritance** | A class can implement **multiple** interfaces | A class can extend only **one** parent class |
| **Purpose** | Defines a behavioral contract ("what it does") | Defines an identity hierarchy ("what it is") |

```php
interface Authenticatable {
    public function authenticate(string $token): bool;
}

abstract class BaseUser implements Authenticatable {
    abstract public function getPermissions(): array;
}
```

---

## 2. Error & Exception Handling

Exceptions represent runtime failures as catchable objects.

```php
<?php
function calculateDiscount(float $price, float $percent): float {
    if ($percent < 0 || $percent > 100) {
        throw new InvalidArgumentException("Discount must be between 0 and 100.");
    }
    return $price - ($price * ($percent / 100));
}

try {
    echo calculateDiscount(500, 120);
} catch (InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage();
} finally {
    // Always runs regardless of success or error
    echo " (Calculation complete)";
}
```

### Environment Error Reporting

| Setting | Development | Production |
| :--- | :--- | :--- |
| `display_errors` | `1` (on-screen debugging) | `0` (hide from users) |
| `log_errors` | `1` | `1` (log to file) |
| `error_reporting` | `E_ALL` | `E_ALL & ~E_DEPRECATED` |

---

## 3. Cookies vs. Sessions

| Feature | Cookie | Session |
| :--- | :--- | :--- |
| **Storage** | Client's browser (text file) | Server memory / disk (`/tmp/sess_*`) |
| **Capacity** | 4 KB limit | Virtually unlimited |
| **Security** | Low (accessible unless flags set) | High (only session ID sent to client) |
| **Lifespan** | Configurable expiry timestamp | Browser close or idle timeout |

```php
// Secure Cookie Setting
setcookie("theme", "dark", [
    'expires'  => time() + (86400 * 30),
    'path'     => '/',
    'httponly' => true, // Inaccessible to JavaScript (XSS protection)
    'samesite' => 'Lax'
]);

// Session Lifecycle
session_start();
$_SESSION["user_id"] = 101;
```

---

## 4. JSON & REST API Development

* `json_encode($data)`: Converts PHP array/object to JSON string.
* `json_decode($json, true)`: Parses JSON string to associative array (set second arg to `true`).

### REST API Endpoint (`api/students.php`)

```php
<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $students = [
        ["id" => 1, "name" => "Alice", "course" => "CS"],
        ["id" => 2, "name" => "Bob", "course" => "IT"]
    ];

    http_response_code(200);
    echo json_encode(["status" => "success", "data" => $students]);
    exit();
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit();
}
```

---

## 5. Database Transactions & ACID (PDO)

A transaction executes multiple database statements as a single, atomic unit:
* **A**tomicity: All statements succeed or all are rolled back.
* **C**onsistency: Maintains relational constraints.
* **I**solation: Independent of concurrent queries.
* **D**urability: Committed data is permanently stored.

```php
<?php
try {
    $pdo->beginTransaction();

    // Deduct from account A
    $stmt1 = $pdo->prepare("UPDATE accounts SET balance = balance - :amt WHERE id = :id");
    $stmt1->execute([':amt' => 500, ':id' => 1]);

    // Credit to account B
    $stmt2 = $pdo->prepare("UPDATE accounts SET balance = balance + :amt WHERE id = :id");
    $stmt2->execute([':amt' => 500, ':id' => 2]);

    $pdo->commit(); // Both succeed: persist changes
    echo "Transfer successful.";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // Any error: revert all changes
    }
    echo "Transfer failed: " . $e->getMessage();
}
```

---

## 6. Modern PHP 8+ Features

### 1. Match Expression

Strict identity matching (`===`) that returns values directly:

```php
$status = match($code) {
    200, 201 => "Success",
    404      => "Not Found",
    500      => "Server Error",
    default  => "Unknown"
};
```

### 2. Nullsafe Operator (`?->`)

Avoids manual null-checks in nested call chains:

```php
$city = $user?->getAddress()?->city; // Returns null safely if any part is null
```

### 3. Constructor Property Promotion

Combines property declaration and parameter assignment:

```php
class Product {
    public function __construct(
        public int $id,
        public string $name,
        public float $price = 0.0
    ) {}
}
```
