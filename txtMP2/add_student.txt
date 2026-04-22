<?php
session_start();
include 'db.php';

// Access Control
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables for sticky form to retain input on error 
$name = $matrix_no = $course = "";
$error_msg = "";
$popup_script = "";

if (isset($_POST['submit'])) {
    // Phase 4: Server-side validation & Sanitization [cite: 148, 149]
    $name = htmlspecialchars($_POST['name']);
    $matrix_no = htmlspecialchars($_POST['matrix_no']);
    $course = htmlspecialchars($_POST['course']);

    // File Handling logic
    $target_dir = "uploads/";
    // Create folder if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verify file size (Maximum 2MB limit added) 
    if ($_FILES["image"]["size"] > 2000000) {
        $error_msg = "Error: File is too large. Maximum allowed size is 2MB.";
    } else {
        // Verify file type: Check if file is an actual image 
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Phase 5: Procedural Prepared Statement 
                try {
                    $stmt = mysqli_prepare($conn, "INSERT INTO students (name, matrix_no, course, image_path) VALUES (?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "ssss", $name, $matrix_no, $course, $target_file);

                    if (mysqli_stmt_execute($stmt)) {
                        $popup_script = "Swal.fire({
                            title: 'Success!',
                            text: 'Student registered successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'dashboard.php';
                        });";
                    }
                } catch (mysqli_sql_exception $e) {
                    if ($e->getCode() == 1062) {
                        $error_msg = "Error: Student with this matrix number already exists.";
                        $popup_script = "Swal.fire({
                            title: 'Error!',
                            text: 'This matrix number is already registered.',
                            icon: 'error',
                            confirmButtonText: 'Check Again'
                        });";
                    } else {
                        $error_msg = "Error: " . $e->getMessage();
                    }
                }
            } else {
                $error_msg = "Error: There was a problem uploading your file.";
            }
        } else {
            $error_msg = "Error: Uploaded file is not a valid image.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student | Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Register New Student</h4>
                    </div>

                    <div class="card-body">
                        <?php if (!empty($error_msg)): ?>
                            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Matrix Number</label>
                                <input type="text" name="matrix_no" id="matrix_no" class="form-control"
                                    value="<?php echo htmlspecialchars($matrix_no); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Course</label>
                                <select name="course" id="course" class="form-select" required>
                                    <option value="">Select Course</option>
                                    <option value="Information Technology (Digital Technology)" <?php if ($course == 'Information Technology (Digital Technology)')
                                        echo 'selected'; ?>>
                                        DDT</option>
                                    <option value="Information Security" <?php if ($course == 'Information Security')
                                        echo 'selected'; ?>>DIT</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="submit" class="btn btn-success">Save Student</button>
                                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        <?php echo $popup_script; ?>

        // Rubric Requirement: Client-side validation using JavaScript
        function validateForm() {
            let name = document.getElementById('name').value;
            let matrix = document.getElementById('matrix_no').value;
            let course = document.getElementById('course').value;
            let image = document.getElementById('image').files[0];

            if (name.trim() === "") {
                Swal.fire('Error', 'Name is required.', 'error');
                return false;
            }
            if (matrix.length < 5) {
                Swal.fire('Error', 'Matrix number must be at least 5 characters.', 'error');
                return false;
            }
            if (course === "") {
                Swal.fire('Error', 'Please select a course.', 'error');
                return false;
            }
            if (!image) {
                Swal.fire('Error', 'Please upload a profile picture.', 'error');
                return false;
            }
            // File size check in JS (optional but good for UX)
            if (image.size > 2000000) {
                Swal.fire('Error', 'File is too large! Max 2MB allowed.', 'error');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>
