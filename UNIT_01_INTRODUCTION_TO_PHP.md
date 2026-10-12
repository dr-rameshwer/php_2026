# Unit 1: Introduction to PHP

A concise, practical guide to core PHP syntax, data types, operators, and variable handling.

---

## 1. Overview & Evolution of PHP

* **PHP** (*Hypertext Preprocessor*, originally *Personal Home Page* tools by Rasmus Lerdorf in 1994) is an open-source, server-side HTML-embedded scripting language.
* **Core Characteristics**:
  * **Server-side**: Scripts execute on the server; the client receives only the generated HTML.
  * **Dynamic / Loosely Typed**: Variable types are determined at runtime.
  * **Cross-platform**: Runs on Linux, Windows, macOS via web servers (Apache, Nginx).
  * **Native RDBMS Drivers**: Built-in support for MySQL, PostgreSQL, SQLite, and PDO.

### Evolution Highlights
* **PHP 3.0 (1998)**: Re-written parser by Zeev Suraski and Andi Gutmans; introduced extensible architecture.
* **PHP 4.0 (2000)**: Introduced Zend Engine 1.0, session handling, and output buffering.
* **PHP 5.0 (2004)**: Complete OOP model, Zend Engine 2.0, PDO database abstraction.
* **PHP 7.0 (2015)**: Zend Engine 3 (PHPNG), doubled execution speed, strict type declarations.
* **PHP 8.0+ (2020+)**: JIT compilation, union types, match expressions, attributes.

### Comparative Study

| Parameter | PHP | ASP.NET | Java (JSP / Servlets) | Node.js |
| :--- | :--- | :--- | :--- | :--- |
| **Licensing** | Open-source (Free) | Commercial / Open (.NET Core) | Open-source | Open-source |
| **Hosting Cost** | Low (shared hosts) | Moderate to High | Moderate (requires JVM) | Moderate (cloud/VPS) |
| **Learning Curve** | Gentle | Moderate (C# required) | Steep (OOP & JVM tools) | Moderate (Async/JS) |
| **HTML Embedding** | Direct via `<?php ?>` | Razor syntax (`@`) | JSP tags (`<% %>`) | Templating / Front-end |
| **Primary Use** | Dynamic websites & CMS | Enterprise & corporate apps | Enterprise backends | Real-time APIs & apps |

---

## 2. External System Interfaces

PHP interacts with external systems using built-in extensions and libraries:

1. **Databases**: PDO (database-agnostic) and `mysqli` (MySQL-specific).
2. **Filesystem**: `fopen()`, `fread()`, `fwrite()`, `unlink()`, `file_get_contents()`.
3. **Network & HTTP**: cURL extension and HTTP stream wrappers for REST APIs.
4. **Email**: Native `mail()` function or SMTP libraries (e.g., PHPMailer).
5. **Operating System**: CLI execution using `exec()`, `shell_exec()`, and `system()`.

---

## 3. Basic PHP Syntax & Script Structure

### Script Tags & File Extension
* Files must have a `.php` extension.
* Standard opening tag: `<?php` ; closing tag: `?>`. (Closing tag is optional and omitted in pure PHP files to avoid whitespace leaks).

```php
<?php
// Single-line comment
# Alternative single-line comment
/* Multi-line comment block */

echo "Hello, World!"; // Statements end with a semicolon
```

### Case Sensitivity Rules
* **Case-Insensitive**: Keywords (`echo`, `if`, `while`), function names (`strlen()`), class names.
* **Case-Sensitive**: Variable names (`$name` and `$Name` are different variables).

### Embedding PHP in HTML

```html
<!DOCTYPE html>
<html>
<body>
    <h1><?= "Welcome to PHP" ?></h1>
    <p>Server Time: <?php echo date("Y-m-d H:i:s"); ?></p>
</body>
</html>
```

---

## 4. PHP Data Types

PHP has 8 primitive types divided into 3 categories:

| Category | Type | Description | Example |
| :--- | :--- | :--- | :--- |
| **Scalar** | `int` | Integer whole numbers | `$age = 21;` |
| | `float` | Floating-point numbers (decimals) | `$gpa = 3.85;` |
| | `string` | Text enclosed in single or double quotes | `$user = "Alice";` |
| | `bool` | Boolean truth values | `$isActive = true;` |
| **Compound**| `array` | Ordered maps / key-value collections | `$items = [1, 2, 3];` |
| | `object` | Instances of user-defined classes | `$obj = new Student();` |
| **Special** | `null` | Represents a variable with no value | `$result = null;` |
| | `resource` | External resource handle (file/database) | `$f = fopen("f.txt","r");` |

---

## 5. Type Inspection & Validation

### Inspecting Types
* `gettype($var)`: Returns type name as a string (`"integer"`, `"string"`, `"array"`).
* `var_dump($var)`: Prints data type, length, and detailed value (best for debugging).
* `print_r($var)`: Human-readable display of arrays and objects.

### Testing Types (`is_*` functions)
Return `true` or `false`:
* `is_int($v)`, `is_float($v)`, `is_numeric($v)` (checks numbers or numeric strings)
* `is_string($v)`, `is_bool($v)`, `is_array($v)`, `is_null($v)`

```php
<?php
$val = "125";
if (is_numeric($val)) {
    echo $val * 2; // Output: 250
}
```

---

## 6. Type Conversion & `settype()`

1. **Automatic Conversion (Type Juggling)**: PHP coerces types depending on operators.
   ```php
   $sum = "20" + 10; // Result: int(30)
   ```
2. **Explicit Casting**: Prefix variable with target type.
   ```php
   $num = (int)"42.8"; // 42 (does not modify original variable)
   ```
3. **In-Place Modification (`settype`)**: Modifies the original variable's type permanently.
   ```php
   $val = "100";
   settype($val, "integer"); // $val is now int(100)
   ```

---

## 7. Operators

### 1. Arithmetic & Assignment
* Arithmetic: `+`, `-`, `*`, `/`, `%` (modulus), `**` (exponentiation).
* Compound Assignment: `+=`, `-=`, `*=`, `/=`, `%=`.

### 2. Comparison Operators
* `==` (Loose equality): Compares values with automatic type conversion (`"5" == 5` is `true`).
* `===` (Strict identity): Compares **value AND type** without conversion (`"5" === 5` is `false`).
* `!=` / `<>`: Not equal; `!==`: Not identical.
* `<`, `>`, `<=`, `>=`: Relational checks.
* `<=>` (Spaceship operator): Returns `-1` if `$a < $b`, `0` if `$a == $b`, `1` if `$a > $b`.

### 3. Logical Operators
* `&&` (AND), `||` (OR), `!` (NOT).
* `and`, `or`, `xor` (lower precedence than `&&` / `||`).

### 4. String Operators
* `.` (Concatenation): `"Hello" . " " . "World"`
* `.=` (Append assignment): `$str .= " more text";`

---

## 8. Variable Handling Functions

* `isset($var)`: Returns `true` if `$var` is declared and not `null`.
* `empty($var)`: Returns `true` if `$var` is undeclared, `null`, `""`, `0`, `"0"`, `false`, or `[]`.
* `unset($var)`: Destroys the variable and removes it from memory.

```php
<?php
$email = "";
isset($email); // true
empty($email); // true

$phone = null;
isset($phone); // false
empty($phone); // true
```

---

## 9. Dynamic Variables (Variable Variables)

Dynamic variables use the evaluated value of one variable as the name of another variable using `$$`:

```php
<?php
$role = "admin";
$$role = "John Doe"; // Creates $admin = "John Doe"

echo $admin; // Output: John Doe
```

*Note: In modern PHP, prefer associative arrays over dynamic variables for clarity and maintainability.*

---

## 10. Variable Scope

PHP variables have three primary scopes:

1. **Local Scope**: Declared inside a function; accessible only within that function. Destroyed when function execution ends.
2. **Global Scope**: Declared outside functions. Not directly accessible inside functions unless imported via `global $var;` or `$GLOBALS['var']`.
3. **Static Scope**: Declared inside a function with `static`. Retains value between consecutive calls to that function.

```php
<?php
$siteName = "StudentPortal"; // Global

function trackVisits() {
    global $siteName;        // Import global variable
    static $count = 0;       // Static variable persists across calls
    $count++;
    
    echo "Site: $siteName | Visit: $count\n";
}

trackVisits(); // Visit: 1
trackVisits(); // Visit: 2
```
