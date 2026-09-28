# BCA Student Management System (PHP + MySQL + Bootstrap 5)
## University Degree Curriculum Capstone Project

---

### Project Overview
The **BCA Student Management System** is a full-featured, secure, production-grade web application developed in pure PHP 8+, MySQL (PDO), and Bootstrap 5. It was engineered specifically to satisfy practical web programming and database management course requirements, demonstrating:

1. **Authentication & Authorization**: Session-based login/logout, route protection, session fixation mitigation.
2. **Database Operations (CRUD)**: Create, Read, Update, Delete students using PDO prepared statements (immune to SQL Injection).
3. **Advanced Filtering & Pagination**: Real-time multi-column search, course/semester dropdown filters, and dynamic pagination.
4. **Secure File Handling**: Multi-layer validated image upload (extension whitelist, binary MIME verification via `finfo`, size restriction) and path-traversal-proof binary downloads.
5. **Security Best Practices**: Anti-CSRF token defense, output escaping via `htmlspecialchars()`, Bcrypt password hashing (`password_hash` / `password_verify`).
6. **Responsive UI**: Built with Bootstrap 5.3, Bootstrap Icons, and custom CSS for mobile and desktop screens.

---

### Folder & File Architecture

```text
bca-student-management/
│
├── config/
│   └── database.php         # PDO connection & options setup
│
├── database/
│   └── schema.sql           # Database creation & seed data
│
├── includes/
│   ├── auth.php             # Authentication middleware & session guards
│   ├── flash.php            # Flash alert notification system
│   ├── functions.php        # CSRF, XSS escaping, validation & upload helpers
│   ├── header.php           # Global HTML head, navbar & flash alert rendering
│   └── footer.php           # Global footer, Bootstrap JS bundle
│
├── assets/
│   ├── css/
│   │   └── style.css        # Custom PTU styling, badges, avatars & preview
│   └── js/
│       └── script.js        # Alert auto-dismiss, file preview & delete confirmation
│
├── public/
│   ├── index.php            # Landing page redirector
│   ├── login.php            # Admin login form & verification
│   ├── logout.php           # Session destruction & redirect
│   ├── dashboard.php        # Metric cards, statistics & recent registrations
│   │
│   ├── students/
│   │   ├── index.php        # Directory table, search, filters & pagination
│   │   ├── create.php       # New student registration form
│   │   ├── store.php        # POST insertion handler (Validation & Upload)
│   │   ├── show.php         # Student profile details view & photo download link
│   │   ├── edit.php         # Pre-populated edit form
│   │   ├── update.php       # POST update handler (Replaces photo if needed)
│   │   ├── delete.php       # POST delete handler (Unlinks photo & removes DB row)
│   │   └── download.php     # Safe file download streamer
│   │
│   └── uploads/             # Permanent storage for uploaded profile photos
│       └── .gitkeep
│
└── README.md
```

---

### Step-by-Step Installation & Setup

#### 1. Copy Project into Web Server Document Root
- **Windows (XAMPP)**: Copy the entire `bca-student-management` folder to `C:\xampp\htdocs\bca-student-management`
- **Mac (XAMPP)**: Copy to `/Applications/XAMPP/xamppfiles/htdocs/bca-student-management`
- **Linux (Apache)**: Copy to `/var/www/html/bca-student-management`

#### 2. Start Services
- Open **XAMPP Control Panel** and click **Start** on both **Apache** and **MySQL**.

#### 3. Import MySQL Database
1. Open your web browser and navigate to: `http://localhost/phpmyadmin`
2. Click the **Import** tab at the top.
3. Click **Choose File** and select `database/schema.sql` from this project folder.
4. Click **Import** (or **Go**) at the bottom.
5. The database `bca_student_management` and tables `users` and `students` will be created automatically with 10 seed student records and 1 admin account.

#### 4. Access the Application
- Open Chrome or Firefox and navigate to:
  `http://localhost/bca-student-management/public/login.php`

#### 5. Default Administrator Login Credentials
- **Email ID**: `admin@bca.edu`
- **Password**: `AdminPassword123`

---

### Viva Voce & Practical Exam Questions on This Project

1. **Why is the database connected using PDO instead of mysqli?**
   - *Answer*: PDO is object-oriented, supports prepared statements with named parameters, has robust exception handling, and works across 12 different database management systems.
2. **How does the project prevent SQL Injection in the search bar?**
   - *Answer*: All user queries are passed as bound parameters into a PDO prepared statement (`:search_name => "%{$search}%"`). The database engine compiles the query structure first, treating user input purely as literal text.
3. **Why is student deletion handled via POST instead of a GET link?**
   - *Answer*: GET requests are idempotent and can be triggered accidentally by browser pre-fetching or malicious CSRF links. Handling deletion via POST with an anti-CSRF token guarantees that deletions only occur when an authenticated administrator intentionally clicks and confirms.
4. **How does `handlePhotoUpload()` protect against malicious files?**
   - *Answer*: It enforces a 2MB size limit, validates the extension against an allowed whitelist, verifies the true binary MIME type using PHP's `finfo_file()`, and generates a randomized filename with `uniqid()` so existing files cannot be overwritten.
