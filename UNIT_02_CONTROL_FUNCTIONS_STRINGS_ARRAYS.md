# Unit 2: Control Statements, Functions, Strings & Arrays

A concise reference for flow control, modular functions, string processing, and array data structures in PHP.

---

## 1. Control Statements

### Conditional Branching (`if`, `elseif`, `else`)

Executes alternative code blocks based on boolean conditions.

```php
<?php
$score = 78;

if ($score >= 90) {
    $grade = "A";
} elseif ($score >= 75) {
    $grade = "B";
} elseif ($score >= 60) {
    $grade = "C";
} else {
    $grade = "F";
}
```

### The `switch` Statement

Evaluates a single expression against discrete constant cases.

```php
<?php
$role = "editor";

switch ($role) {
    case "admin":
        $access = "Full control";
        break; // Crucial: prevents fall-through
    case "editor":
        $access = "Edit & publish";
        break;
    case "viewer":
        $access = "Read-only";
        break;
    default:
        $access = "No access";
        break;
}
```

*Note: Omitting `break;` causes execution to fall through to subsequent cases.*

### Ternary Operator (`?:`)

Shorthand syntax for simple `if-else` expressions:
`$result = (condition) ? value_if_true : value_if_false;`

```php
$status = ($age >= 18) ? "Adult" : "Minor";
```

---

## 2. Iteration (Loops)

| Loop | Type | Condition Check | Guaranteed Min Iterations | Syntax Example |
| :--- | :--- | :--- | :--- | :--- |
| **`while`** | Entry-controlled | Evaluated **before** body runs | `0` | `while ($i < 5) { $i++; }` |
| **`do-while`** | Exit-controlled | Evaluated **after** body runs | `1` | `do { $i++; } while ($i < 5);` |
| **`for`** | Counter-controlled | Evaluated **before** each step | `0` | `for ($i=0; $i<5; $i++) { ... }` |

### Loop Syntax Examples

```php
<?php
// 1. while loop
$i = 1;
while ($i <= 3) {
    echo "Count: $i\n";
    $i++;
}

// 2. do-while loop (always runs at least once)
$j = 10;
do {
    echo "Runs once: $j\n";
    $j++;
} while ($j < 5);

// 3. for loop (init; condition; increment)
for ($k = 1; $k <= 3; $k++) {
    echo "Step: $k\n";
}
```

---

## 3. Functions in PHP

A function is a reusable block of code that performs a specific task.

### Definition, Return Values & Default Parameters

```php
<?php
// Function definition with default argument
function calculateTotal($amount, $taxRate = 0.18) {
    $total = $amount + ($amount * $taxRate);
    return $total;
}

// Calling function
$orderTotal = calculateTotal(1000);        // Uses default tax (18%): 1180
$specialTotal = calculateTotal(1000, 0.05); // Overrides tax (5%): 1050
```

### Passing Arguments by Value vs. by Reference

* **By Value (Default)**: Passes a local copy. Modifications inside the function do not affect the original variable.
* **By Reference (`&`)**: Passes a reference to the memory location. Modifications directly affect the original variable.

```php
<?php
function addBonusValue($score) {
    $score += 10; // Modifies local copy only
}

function addBonusRef(&$score) {
    $score += 10; // Modifies caller's variable
}

$mark = 50;
addBonusValue($mark); // $mark is still 50
addBonusRef($mark);   // $mark is now 60
```

### Built-in Library Functions vs. Dynamic Functions

* **Built-in Examples**: `strlen()`, `count()`, `strtoupper()`, `round()`, `abs()`.
* **Dynamic (Variable) Functions**: Calling a function using a variable containing its name:

```php
<?php
function sendEmail() { return "Email sent"; }
function sendSMS()   { return "SMS sent"; }

$notifyHandler = "sendEmail";
echo $notifyHandler(); // Invokes sendEmail()
```

---

## 4. String Manipulation

### Single vs. Double Quotes

* **Single Quotes (`'...'`)**: Text is literal. Variables and escape codes are **not parsed** (`'Hello $name'` prints `Hello $name`).
* **Double Quotes (`"..."`)**: Interprets variables and escape sequences (`"Hello $name"` prints `Hello Alice`).

### Presentation Formatting

```php
// 1. number_format(): Formats currency and decimals
$price = 1500000.5;
echo number_format($price, 2); // "1,500,000.50"

// 2. nl2br(): Converts \n newlines to HTML <br> tags
echo nl2br("Line 1\nLine 2");

// 3. htmlspecialchars(): Escapes HTML entities (XSS prevention)
echo htmlspecialchars("<script>alert(1)</script>");
```

### Storage Formatting & Sanitization

```php
$email = "  USER@EXAMPLE.COM  ";
$cleanEmail = strtolower(trim($email)); // "user@example.com"

// Escaping quotes for legacy query handling
$comment = addslashes("Student's project"); // "Student\'s project"
$original = stripslashes($comment);         // "Student's project"
```

### Joining (`implode`) & Splitting (`explode`)

```php
// Splitting string to array
$csv = "PHP,MySQL,Apache";
$parts = explode(",", $csv); // ["PHP", "MySQL", "Apache"]

// Joining array elements to string
$tags = ["Web", "Dev", "Backend"];
$tagLine = implode(" | ", $tags); // "Web | Dev | Backend"
```

### String Comparison

* `strcmp($a, $b)`: Binary safe, **case-sensitive** comparison. Returns `0` if equal.
* `strcasecmp($a, $b)`: **Case-insensitive** comparison. Returns `0` if equal.
* `===`: Strict equality operator (fastest for exact matches).

---

## 5. Arrays

An array stores multiple values mapped to numeric or string keys.

### Indexed Arrays (Numeric Keys, 0-Indexed)

```php
<?php
$colors = ["Red", "Green", "Blue"];

echo $colors[0]; // "Red"
$colors[] = "Yellow"; // Appends to end (index 3)

count($colors);      // Returns 4
sort($colors);       // Sorts array ascending
array_push($colors, "Purple");
$last = array_pop($colors); // Removes and returns last item
```

### Associative Arrays (Key-Value Pairs)

```php
<?php
$user = [
    "id"    => 101,
    "name"  => "Alice",
    "email" => "alice@example.com"
];

echo $user["name"]; // "Alice"
$user["role"] = "Admin"; // Adds new key-value pair
```

### Traversal with `foreach()`

`foreach` is the standard, safest way to iterate over arrays:

```php
<?php
// Iterating values only
foreach ($colors as $color) {
    echo "$color\n";
}

// Iterating both keys and values
foreach ($user as $key => $value) {
    echo "$key: $value\n";
}
```

> **Historical Note on `each()`**: The `each()` function was deprecated in PHP 7.2 and **removed in PHP 8.0**. Always use `foreach()` in modern code.
