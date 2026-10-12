# Unit 3: Forms, Files, Directories & Image Generation

A practical reference for HTML form processing, filesystem management, file uploads/downloads, and dynamic graphics using GD in PHP.

---

## 1. HTML Forms & Superglobals

### GET vs. POST

| Feature | GET | POST |
| :--- | :--- | :--- |
| **Data Transmission** | Appended to URL query string (`page.php?id=5`) | Sent in the HTTP request body payload |
| **Data Visibility** | Visible in browser address bar and history | Hidden from URL address bar |
| **Data Limit** | ~2,048 characters | Configured via `post_max_size` (8MB - 64MB+) |
| **File Uploads** | Not supported | **Required** (`multipart/form-data`) |
| **Idempotency** | Idempotent (safe for retrieval/search) | Non-idempotent (creates/updates records) |

### Superglobal Arrays

Built-in arrays automatically accessible in all scopes:

| Superglobal | Source / Purpose |
| :--- | :--- |
| `$_GET` | URL query parameters |
| `$_POST` | Form fields sent via HTTP POST |
| `$_REQUEST` | Merged array of `$_GET`, `$_POST`, and `$_COOKIE` |
| `$_FILES` | Uploaded file metadata and temporary paths |
| `$_SERVER` | Server environment, script headers, request method |
| `$_SESSION` | Persistent session data across requests |
| `$_COOKIE` | HTTP cookies stored on the client |
| `$_ENV` | Operating system environment variables |
| `$GLOBALS` | All global variables in script execution |

---

## 2. Form Processing & Input Sanitization

```php
<?php
$errors = [];
$name = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Sanitize input
    $name  = trim($_POST["student_name"] ?? "");
    $email = trim($_POST["student_email"] ?? "");

    // 2. Validate input
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // 3. Process if valid
    if (empty($errors)) {
        // Save to DB or redirect
        header("Location: success.php");
        exit(); // Crucial: terminate execution after redirect
    }
}
?>

<form method="POST" action="">
    <label>Name:</label>
    <input type="text" name="student_name" value="<?= htmlspecialchars($name) ?>">
    
    <label>Email:</label>
    <input type="email" name="student_email">

    <!-- Hidden Field: transmits data without user interaction -->
    <input type="hidden" name="user_id" value="101">

    <button type="submit">Submit</button>
</form>
```

> **Header Redirection Rules**:
> * `header("Location: url")` sends an HTTP 302 redirect.
> * Must be called **before** any HTML or whitespace output (otherwise triggers *"headers already sent"*).
> * Always call `exit()` immediately following `header()`.

---

## 3. Files & Directories

### File Modes in `fopen($path, $mode)`

* `'r'`: Read-only; pointer at start.
* `'w'`: Write-only; truncates file to 0 length (or creates new).
* `'a'`: Append-only; writes at end of file (or creates new).
* `'r+'`: Read and write.

### Reading & Writing Files

```php
// Low-level stream functions
$handle = fopen("log.txt", "a");
if ($handle) {
    fwrite($handle, "User logged in at " . date("Y-m-d H:i:s") . "\n");
    fclose($handle);
}

// Line-by-line reading
$handle = fopen("log.txt", "r");
while (!feof($handle)) {
    $line = fgets($handle);
    echo htmlspecialchars($line) . "<br>";
}
fclose($handle);

// High-level helpers (simplest for full files)
file_put_contents("notes.txt", "Updated content");
$data = file_get_contents("notes.txt");
```

### File Lifecycle & Directory Operations

```php
// File operations
if (file_exists("old.txt")) {
    copy("old.txt", "backup.txt");
    rename("backup.txt", "archive.txt");
    unlink("archive.txt"); // Deletes file
}

// Directory operations
if (!is_dir("uploads")) {
    mkdir("uploads", 0755, true); // Create directory
}

$files = scandir("uploads"); // Returns array of contents
foreach ($files as $file) {
    if ($file !== "." && $file !== "..") {
        echo "$file\n";
    }
}
rmdir("empty_folder"); // Removes empty directory
```

---

## 4. File Uploading & Downloading

### Secure File Uploading

Requirements:
1. `<form method="POST" enctype="multipart/form-data">`
2. Validate `$_FILES['input_name']['error'] === UPLOAD_ERR_OK`.
3. Check size, extension, and true MIME type using `finfo`.
4. Use `move_uploaded_file()` to transfer from temp path to destination.

```php
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["avatar"])) {
    $file = $_FILES["avatar"];

    if ($file["error"] === UPLOAD_ERR_OK) {
        $allowedExt = ["jpg", "jpeg", "png", "pdf"];
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        // MIME validation
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);

        $allowedMime = ["image/jpeg", "image/png", "application/pdf"];

        if (in_array($ext, $allowedExt) && in_array($mime, $allowedMime) && $file["size"] <= 2 * 1024 * 1024) {
            $dest = "uploads/" . uniqid("file_") . "." . $ext;
            move_uploaded_file($file["tmp_name"], $dest);
            echo "Uploaded successfully!";
        } else {
            echo "Invalid file type or size exceeded (max 2MB).";
        }
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="avatar" required>
    <button type="submit">Upload</button>
</form>
```

### Secure File Downloading

```php
<?php
$filename = basename($_GET["file"] ?? ""); // basename() prevents directory traversal (../../)
$filepath = "uploads/" . $filename;

if (!empty($filename) && file_exists($filepath)) {
    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Length: " . filesize($filepath));
    
    readfile($filepath);
    exit();
} else {
    die("File not found.");
}
```

---

## 5. Dynamic Image Generation (GD Library)

PHP's GD extension generates and manipulates raster graphics dynamically.

### 6-Step Image Workflow:
1. Canvas: `imagecreatetruecolor($width, $height)`
2. Color Allocation: `imagecolorallocate($img, $r, $g, $b)`
3. Draw: `imagefill()`, `imageline()`, `imagefilledrectangle()`
4. Text: `imagestring($img, $font, $x, $y, $text, $color)`
5. Output Header & Image: `header("Content-Type: image/png")` $\rightarrow$ `imagepng($img)`
6. Free Memory: `imagedestroy($img)`

```php
<?php
header("Content-Type: image/png");

$img = imagecreatetruecolor(300, 80);

$bg    = imagecolorallocate($img, 240, 240, 240);
$blue  = imagecolorallocate($img, 0, 51, 102);
$dark  = imagecolorallocate($img, 30, 30, 30);

imagefill($img, 0, 0, $bg);
imagefilledrectangle($img, 0, 0, 300, 25, $blue);
imagestring($img, 5, 20, 5, "Portal Security", imagecolorallocate($img, 255, 255, 255));
imagestring($img, 4, 20, 40, "Captcha: 8 K 4 W 2", $dark);

imagepng($img);
imagedestroy($img);
?>
```
