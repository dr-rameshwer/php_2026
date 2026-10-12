# Unit 5: Web Security Fundamentals & Bootstrap 5

A practical reference for defending PHP applications against common vulnerabilities and styling UI with Bootstrap 5.

---

## 1. Web Application Security

### 1. Password Hashing (`password_hash` & `password_verify`)

Never store plaintext passwords or use obsolete algorithms like MD5/SHA1. Use PHP's built-in cryptographic hashing:

```php
<?php
// On Registration: Hash the password
$password = "SecretPass123";
$hash = password_hash($password, PASSWORD_BCRYPT);

// On Login: Verify password against stored hash
if (password_verify($password, $hash)) {
    echo "Authentication successful.";
} else {
    echo "Invalid credentials.";
}
```

### 2. Cross-Site Scripting (XSS) Prevention

XSS occurs when unescaped user input (such as malicious `<script>` tags) is rendered in HTML. Always escape output using `htmlspecialchars()`:

```php
<?= htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8') ?>
```

### 3. Cross-Site Request Forgery (CSRF) Prevention

Tricks authenticated users into executing unwanted state-changing actions. Defend by enforcing `POST` methods and verifying anti-CSRF tokens:

```php
<?php
session_start();

// 1. Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. Validate token on POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        die("CSRF verification failed.");
    }
}
?>

<!-- Include hidden token in every form -->
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
```

### 4. Secure Session Management

* Call `session_start()` at the top of every script using sessions.
* Regenerate session ID upon login to prevent **Session Fixation**:
  ```php
  session_regenerate_id(true);
  ```
* Safe logout sequence:
  ```php
  session_start();
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
  }
  session_destroy();
  header("Location: login.php");
  exit();
  ```

---

## 2. Bootstrap 5 Essentials

Bootstrap 5 provides a mobile-first, 12-column responsive grid and pre-built UI components.

### CDN Setup

```html
<!-- In <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Before </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
```

### 12-Column Responsive Grid

Layout hierarchy: `.container` $\rightarrow$ `.row` $\rightarrow$ `.col-{breakpoint}-{columns}`:

```html
<div class="container">
    <div class="row">
        <!-- 6 columns on medium screens (50%), 12 columns on mobile (100%) -->
        <div class="col-md-6 mb-3">Column A</div>
        <div class="col-md-6 mb-3">Column B</div>
    </div>
</div>
```

### Key UI Component Classes

| Component | Bootstrap 5 Classes | Description |
| :--- | :--- | :--- |
| **Navbar** | `navbar navbar-expand-lg bg-primary navbar-dark` | Responsive header with mobile toggle |
| **Card** | `card shadow-sm`, `card-header`, `card-body` | Clean content container |
| **Table** | `table table-striped table-hover table-bordered` | Styled data table with hover states |
| **Buttons** | `btn btn-primary`, `btn-success`, `btn-danger` | Pre-styled action buttons |
| **Forms** | `form-label`, `form-control`, `form-select` | Consistent inputs and dropdowns |
| **Alerts** | `alert alert-success alert-dismissible fade show` | Feedback notification banners |
| **Badges** | `badge bg-info`, `badge bg-success` | Compact status tags |

### Form Component Example

```html
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Add Student</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="store.php">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
```
