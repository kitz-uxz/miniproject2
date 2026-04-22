<?php
session_start();
include 'db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = htmlspecialchars($_POST['username']);
    $pass = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT id, password FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Phase 3: Verify the hashed password
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $user;
            header("Location: dashboard.php");
            exit();
        }
    }
    $error = "<div class='alert alert-danger'>Invalid username or password.</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container col-md-4">
        <div class="card p-4 shadow border-0" style="border-radius: 15px;">
            <h2 class="text-center fw-bold text-primary">Student Portal</h2>
            <p class="text-center text-muted">Please sign in to continue</p>
            <?php echo $error; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Admin username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
            </form>
            <div class="text-center mt-3">
                <small>New admin? <a href="register.php">Create account</a></small>
            </div>
        </div>
    </div>
</body>
</html>