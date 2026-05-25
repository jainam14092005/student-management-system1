<?php
session_start();
include('../include/config.php');

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Secure prepared statement for authentication
        $stmt = $conn->prepare("SELECT * FROM students WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();
            $_SESSION['student_login'] = TRUE;
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            $_SESSION['student_email'] = $student['email'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Student Portal</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom Student CSS -->
    <link rel="stylesheet" href="../css/student-style.css">
</head>
<body class="student-login-body">

<div class="card login-glass-card border-0">
    <div class="login-header-glow">
        <h2 class="fw-bold mb-1">Student Portal</h2>
        <p class="mb-0 text-white-50">Please sign in to access your dashboard</p>
    </div>

    <div class="card-body p-4">
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger border-0 text-center" style="background: rgba(239, 68, 68, 0.2); color: #f87171;">
                <i class="fa fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-white-50 fw-semibold mb-2">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text student-input-group-text"><i class="fa fa-envelope"></i></span>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control student-form-control" 
                        placeholder="yourname@school.com"
                        required
                    >
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <label class="form-label text-white-50 fw-semibold mb-0">Password</label>
                    <span class="role-badge">Student</span>
                </div>
                <div class="input-group">
                    <span class="input-group-text student-input-group-text"><i class="fa fa-lock"></i></span>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control student-form-control" 
                        placeholder="••••••••"
                        required
                    >
                </div>
            </div>

            <button type="submit" name="login" class="btn student-btn-login w-100 mb-3">
                Sign In
            </button>
            
            <div class="text-center">
                <a href="../login.php" class="text-white-50 text-decoration-none fs-7 hover-text-white">
                    <i class="fa fa-user-shield me-1"></i>Are you an Admin? Click here
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
