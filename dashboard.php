<?php
session_start();
include 'db.php';

// Access Control: check if logged in [cite: 139]
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Phase 5: Executing initial Read operation with Procedural Prepared Statements 
$stmt = mysqli_prepare($conn, "SELECT * FROM students");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .passport-photo {
            width: 150px;
            height: 200px;
            object-fit: cover;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background-color: #f8f9fa;
        }
        .card-body {
            text-align: center;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Portal Admin</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Student List</h2>
            <a href="add_student.php" class="btn btn-primary">Add New Student</a>
        </div>

        <div class="mb-4">
            <input type="text" id="search" class="form-control" placeholder="Search by name or matrix no..."
                onkeyup="searchStudent(this.value)">
        </div>

        <div class="row" id="studentArea">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm pt-3">
                        <div class="text-center">
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="passport-photo shadow-sm">
                        </div>
                        <div class="card-body">
                            <h5 class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars($row['matrix_no']); ?></p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this record?')">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function searchStudent(val) {
            // AJAX Fetch API [cite: 164]
            fetch('ajax_search.php?q=' + val)
                .then(res => res.text())
                .then(data => { document.getElementById('studentArea').innerHTML = data; });
        }
    </script>
</body>

</html>