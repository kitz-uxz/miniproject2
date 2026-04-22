# 🎓 Student Portal: Mini Project 2 Presentation Guide

## 📌 Project Overview
This project is a **Dynamic Student Management System** built using PHP, MySQL, and Bootstrap. It allows administrators to securely manage student records (CRUD) with features like image uploads and a real-time search.

---

## 🛠️ Key Components & Features

### 1. Database & Connection (`db.php`)
*   **What we did**: Created a database named `student_portal` with `users` (for admins) and `students` tables.
*   **Student Explanation**: We use `db.php` as a "bridge" between our website and our database. It uses **Procedural MySQLi** and includes a check to make sure if the database fails, the whole system stops safely.

### 2. Secure Authentication (`login.php`, `register.php`)
*   **What we did**: Implemented a login system with password security.
*   **Student Explanation**: 
    *   **Password Hashing**: We don't store passwords as plain text. We use `password_hash()` when registering and `password_verify()` when logging in. This is much safer!
    *   **Sessions**: Once an admin logs in, we start a `session`. This remembers who is logged in so they don't have to re-login on every page.
    *   **Access Control**: If someone tries to go to `dashboard.php` without logging in, they are kicked back to the login page.

### 3. CRUD Operations (Create, Read, Update, Delete)
*   **What we did**: Built a system to manage students.
*   **Student Explanation**:
    *   **Create**: Adding a new student with a profile picture.
    *   **Read**: Showing the list of students on the dashboard.
    *   **Update**: Changing a student's details or replacing their photo.
    *   **Delete**: Removing a student permanently.

### 4. Advanced File Handling & Storage
*   **What we did**: Managed student images in the `uploads/` folder.
*   **Student Explanation**: When we delete a student or update their photo, we use the `unlink()` function. This is important because it deletes the **actual file** from the computer's folder, not just the record in the database. This saves space on our server!

### 5. Double-Layer Validation & Sticky Forms
*   **What we did**: Added security and helpfulness to our forms.
*   **Student Explanation**:
    *   **JavaScript (Client-side)**: Checks for errors (like short passwords) *before* the data even hits the server. It's fast and gives instant feedback.
    *   **PHP (Server-side)**: A second check on the server for ultimate security.
    *   **Sticky Forms**: If you make a mistake, the form "remembers" what you typed so you don't have to type everything again.

### 6. AJAX Live Search (`ajax_search.php`)
*   **What we did**: Created a search bar that updates as you type.
*   **Student Explanation**: Instead of clicking "Search" and waiting for the page to refresh, we use the **Fetch API**. It sends the search query to the background, and the results "pop" onto the screen instantly using `onkeyup`.

### 7. Responsive Design (UI/UX)
*   **What we did**: Used **Bootstrap 5** for styling.
*   **Student Explanation**: The website is responsive! This means it looks great on a laptop, a tablet, and even a phone. We also added **SweetAlert2** to give modern, beautiful popup messages for success and errors.

---

## 🛡️ Technical Security Highlights
1.  **Prepared Statements**: We use these for *every* database query. It prevents "SQL Injection" (when hackers try to type code into our input boxes).
2.  **XSS Prevention**: We use `htmlspecialchars()` to make sure hackers can't inject malicious scripts into our pages.
3.  **Encapsulation**: Our logic is cleanly separated into different files for better organization.

---

## 🏁 Summary of Compliance (Marking Scheme)
By following the rubric carefully, we have ensured:
- [x] Full CRUD functionality.
- [x] Secured access control and hashed passwords.
- [x] Proper file handling with `unlink()`.
- [x] Real-time AJAX search.
- [x] Responsive UI with Bootstrap.
- [x] Clean, error-free code structure.
