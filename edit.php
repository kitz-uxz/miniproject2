<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$popup_script = "";
$error_msg = "";

// Initialize variables for sticky form with database values as default
$name = $student['name'];
$matrix = $student['matrix_no'];
$course = $student['course'];

if (isset($_POST['update'])) {
    $name = htmlspecialchars($_POST['name']);
    $matrix = htmlspecialchars($_POST['matrix_no']);
    $course = htmlspecialchars($_POST['course']);
    $old_path = $_POST['old_path'];

    // Logic: If user uploads a new image
    if (!empty($_FILES["image"]["name"])) {
        $new_path = "uploads/" . time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $new_path);
        
        // Delete the old file to save space
        if (file_exists($old_path)) { unlink($old_path); }
    } else {
        $new_path = $old_path; // Keep existing image
    }

    // Phase 5: Update using Prepared Statements
    try {
        $upd_stmt = mysqli_prepare($conn, "UPDATE students SET name=?, matrix_no=?, course=?, image_path=? WHERE id=?");
        mysqli_stmt_bind_param($upd_stmt, "ssssi", $name, $matrix, $course, $new_path, $id);
        
        if (mysqli_stmt_execute($upd_stmt)) {
            $popup_script = "Swal.fire({
                title: 'Updated!',
                text: 'Student details updated successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'dashboard.php';
            });";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $error_msg = "Error: Another student already has this matrix number.";
            $popup_script = "Swal.fire({
                title: 'Error!',
                text: 'This matrix number is already taken by another student.',
                icon: 'error',
                confirmButtonText: 'Check Again'
            });";
        } else {
            $error_msg = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student | Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow col-md-6 mx-auto">
        <div class="card-header bg-warning">
            <h4 class="mb-0">Edit Student Record</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <input type="hidden" name="old_path" value="<?php echo $student['image_path']; ?>">
                
                <div class="mb-3">
                    <label>Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="mb-3">
                    <label>Matrix No</label>
                    <input type="text" name="matrix_no" id="matrix_no" class="form-control" value="<?php echo htmlspecialchars($matrix); ?>" required>
                </div>

                <div class="mb-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-select">
                        <option value="DDT" <?php if($course == 'DDT') echo 'selected'; ?>>DDT</option>
                        <option value="DIS" <?php if($course == 'DIS') echo 'selected'; ?>>DIS</option>
                    </select>
                </div>

                <div class="mb-3 text-center">
                    <p>Current Profile:</p>
                    <img src="<?php echo $student['image_path']; ?>" width="100" class="img-thumbnail mb-2">
                    <input type="file" name="image" id="image" class="form-control">
                    <small class="text-muted">Leave blank to keep current photo.</small>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="update" class="btn btn-primary">Update Details</button>
                    <a href="dashboard.php" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    <?php echo $popup_script; ?>

    function validateForm() {
        let name = document.getElementById('name').value;
        let matrix = document.getElementById('matrix_no').value;
        let image = document.getElementById('image').files[0];

        if (name.trim() === "") {
            Swal.fire('Error', 'Name cannot be empty.', 'error');
            return false;
        }
        if (matrix.length < 5) {
            Swal.fire('Error', 'Matrix number must be at least 5 characters.', 'error');
            return false;
        }
        if (image && image.size > 2000000) {
            Swal.fire('Error', 'File is too large! Max 2MB allowed.', 'error');
            return false;
        }
        return true;
    }
</script>
</body>
</html>
