# Programming & Web Foundations

Essential prerequisites for web development and PHP programming.

---

## 1. Programming Languages & Execution

* **Program**: A structured sequence of instructions that a computer CPU executes to perform a task.
* **Programming Language**: Formal syntax and vocabulary used to instruct computers.
  * **Low-Level (Machine / Assembly)**: Binary (`0`s and `1`s) or CPU mnemonics (`MOV`, `ADD`). Hardware-dependent.
  * **High-Level (C, Java, Python, PHP)**: Human-readable syntax, hardware-independent, translated to machine code via compilers or interpreters.

### Compiler vs. Interpreter

| Feature | Compiler (e.g., C, C++, Go) | Interpreter (e.g., PHP, Python, JS) |
| :--- | :--- | :--- |
| **Execution** | Translates entire source code into binary executable (`.exe`, ELF) before run. | Translates and executes code line-by-line in real time. |
| **Speed** | Fast execution; translation happens once. | Slightly slower per-statement; no separate build step. |
| **Error Handling** | Detects compile-time syntax errors before generating output. | Stops immediately at the line where a runtime error occurs. |
| **Workflow** | Edit $\rightarrow$ Compile $\rightarrow$ Run. | Edit $\rightarrow$ Run (refresh browser). |

* **Scripting Language**: A high-level language designed for automating or interacting with host environments (like a web server or browser) without explicit compilation steps. PHP is a **server-side scripting language**.

---

## 2. Web Technologies Overview

| Technology | Role | Purpose |
| :--- | :--- | :--- |
| **HTML** | Skeleton | Defines content structure (tags: headings, forms, tables). |
| **CSS** | Styling | Controls layout, colors, typography, responsive design. |
| **JavaScript** | Client Logic | Runs in browser: interactivity, client-side validation, DOM manipulation. |
| **PHP** | Server Logic | Runs on server: business rules, auth, database queries, renders HTML. |
| **MySQL / MariaDB** | Storage | Relational database management system (RDBMS) storing persistent data. |

---

## 3. Client-Server Architecture

Web applications follow a request-response model:

```
[Browser (Client)]  ── HTTP Request (GET / POST) ──>  [Apache Web Server]
                                                            │
                                                            ▼ (Passes .php)
                                                      [PHP Engine]
                                                            │
                                                            ▼ (SQL queries)
                                                      [MySQL Database]
                                                            │
                                                            ▼ (Result rows)
[Browser (Client)]  <── HTTP Response (Pure HTML)  <──  [PHP Engine / Apache]
```

* **Client (Frontend)**: Runs in the browser (Chrome, Firefox). Handles UI rendering and user interactions. Source code (HTML/CSS/JS) is visible to end users.
* **Server (Backend)**: Runs on the server host (Apache/Nginx + PHP + MySQL). Processes business logic and database queries securely. PHP source code is **never sent to the client**; only the generated HTML output is delivered.

---

## 4. HTTP Fundamentals

The **Hypertext Transfer Protocol (HTTP)** defines communication rules between web clients and servers.

### Common HTTP Methods

* **GET**: Requests data from server. Parameters appear in the URL query string (`page.php?id=10`). Cached and bookmarked; should not alter server state.
* **POST**: Submits data to server (forms, file uploads, credentials). Payload is in the request body, not exposed in URL.

### Key HTTP Status Codes

| Code | Meaning | Common Cause |
| :--- | :--- | :--- |
| `200 OK` | Success | Page or resource retrieved successfully. |
| `301 / 302` | Redirection | Resource moved (permanently / temporarily). |
| `400 Bad Request` | Client Error | Malformed request syntax or parameters. |
| `401 / 403` | Unauthorized / Forbidden | Missing authentication or insufficient permissions. |
| `404 Not Found` | Not Found | Requested file or route does not exist on server. |
| `500 Internal Error` | Server Error | Unhandled PHP fatal error or server misconfiguration. |

---

## 5. URL Structure & Localhost

```text
https://localhost:80/students/index.php?dept=cs#list
|___|   |_______| |_| |________________| |_____| |___|
  |         |      |          |             |      |
Scheme    Host    Port       Path         Query  Fragment
```

* **`localhost` (`127.0.0.1`)**: Loopback network address targeting the local machine. Used for local testing without external internet access.
* **Default Ports**:
  * HTTP: `80`
  * HTTPS: `443`
  * MySQL: `3306`

---

## 6. Local Development Stacks (XAMPP / WAMP / LAMP)

All-in-one software bundles providing an integrated local web server environment:

* **XAMPP**: **X**-platform (Windows, Mac, Linux), **A**pache, **M**ariaDB/MySQL, **P**HP, **P**erl.
* **WAMP**: Windows, Apache, MySQL, PHP.
* **LAMP**: Linux, Apache, MySQL, PHP (standard production Linux stack).

### Workflow in XAMPP:
1. Start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Place PHP project files inside the web root:
   * Windows: `C:\xampp\htdocs\<project_folder>`
   * macOS: `/Applications/XAMPP/htdocs/<project_folder>`
3. Access in browser via `http://localhost/<project_folder>/<file>.php`.
