<?php
session_start();
include 'db.php';

// Access Control
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

$q = "%" . ($_GET['q'] ?? '') . "%";

// Prepared statement to prevent SQL injection [cite: 77, 134]
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE name LIKE ? OR matrix_no LIKE ?");
mysqli_stmt_bind_param($stmt, "ss", $q, $q);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "
        <div class='col-md-4 mb-3'>
            <div class='card shadow-sm pt-3'>
                <div class='text-center'>
                    <img src='{$row['image_path']}' class='passport-photo shadow-sm'>
                </div>
                <div class='card-body text-center'>
                    <h5 class='fw-bold'>" . htmlspecialchars($row['name']) . "</h5>
                    <p class='text-muted'>{$row['matrix_no']}<br>{$row['course']}</p>
                    <div class='d-flex justify-content-center gap-2'>
                        <a href='edit.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                        <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete student?\")'>Delete</a>
                    </div>
                </div>
            </div>
        </div>";

    }
} else {
    echo "<p class='text-center'>No students found.</p>";
}
?>