<?php
include 'db.php';
$msg = "";
$popup_script = "";
$user = ""; // Initialize for sticky form

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = htmlspecialchars($_POST['username']);
    $pass = $_POST['password'];
    
    // Phase 3: Hash the password before storing
    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    try {
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $user, $hashed);
        
        if (mysqli_stmt_execute($stmt)) {
            $msg = "<div class='alert alert-success'>Admin registered! <a href='login.php'>Login here</a></div>";
            $popup_script = "Swal.fire({
                title: 'Success!',
                text: 'Admin registered successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'login.php';
            });";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // 1062 is the error code for duplicate entry
            $msg = "<div class='alert alert-danger'>Username already exists.</div>";
            $popup_script = "Swal.fire({
                title: 'Error!',
                text: 'This account has already been registered.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });";
        } else {
            $msg = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container col-md-4">
        <div class="card p-4 shadow">
            <h3 class="text-center">Admin Registration</h3>
            <?php echo $msg; ?>
            <form method="POST" id="regForm" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">Register</button>
            </form>
            <div class="text-center mt-3">
                <small>Already have an account? <a href="login.php">Login here</a></small>
            </div>
        </div>
    </div>
    <script>
        <?php echo $popup_script; ?>

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });

        // Rubric Requirement: Client-side validation using JavaScript
        function validateForm() {
            let user = document.getElementById('username').value;
            let pass = document.getElementById('password').value;
            
            if (user.length < 3) {
                Swal.fire('Validation Error', 'Username must be at least 3 characters long.', 'warning');
                return false;
            }
            if (pass.length < 6) {
                Swal.fire('Validation Error', 'Password must be at least 6 characters long.', 'warning');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>
