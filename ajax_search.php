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
            <div class='card shadow-sm'>
                <img src='{$row['image_path']}' class='card-img-top' style='height:200px; object-fit:cover;'>
                <div class='card-body'>
                    <h5>" . htmlspecialchars($row['name']) . "</h5>
                    <p class='text-muted'>{$row['matrix_no']}<br>{$row['course']}</p>
                    <a href='edit.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                    <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete student?\")'>Delete</a>
                </div>
            </div>
        </div>";
    }
} else {
    echo "<p class='text-center'>No students found.</p>";
}
?>