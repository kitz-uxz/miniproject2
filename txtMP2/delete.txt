<?php
session_start();
include 'db.php';

// Phase 3: Access Control
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Basic sanitization

    // 1. Fetch image path to delete the file physically
    $stmt = mysqli_prepare($conn, "SELECT image_path FROM students WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $file_path = $row['image_path'];

        // 2. Delete from database
        $del_stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
        mysqli_stmt_bind_param($del_stmt, "i", $id);
        
        if (mysqli_stmt_execute($del_stmt)) {
            // 3. Phase 5: Use unlink() to remove the image file
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            header("Location: dashboard.php?msg=StudentDeleted");
            exit();
        }
    }
}
?>