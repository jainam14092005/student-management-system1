<?php
session_start();
include('../include/config.php');

if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {
    header("Location: ../login.php");
    exit();
}

$error = "";
$success = "";

if (isset($_POST['save_student'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $course = trim($_POST['course']);
    $gender = trim($_POST['gender']);
    $address = trim($_POST['address']);

    // Validations
    if (empty($name) || empty($email)) {
        $error = "Name and Email are required fields.";
    } else {
        // Handle file upload
        $image_name = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $image_tmp = $_FILES['image']['tmp_name'];
            $orig_name = $_FILES['image']['name'];
            $image_ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            
            // Validate image extension
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($image_ext, $allowed_exts)) {
                $error = "Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP images are allowed.";
            } else {
                $image_name = time() . '_' . uniqid() . '.' . $image_ext;
                $target_dir = "../uploads/";
                
                if (!move_uploaded_file($image_tmp, $target_dir . $image_name)) {
                    $error = "Failed to upload image. Please check directory permissions.";
                    $image_name = "";
                }
            }
        }

        if (empty($error)) {
            // Check if email already exists
            $stmt_check = $conn->prepare("SELECT id FROM students WHERE email = ?");
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                $error = "A student with this email address already exists.";
                // Delete uploaded image if check failed
                if (!empty($image_name)) {
                    unlink("../uploads/" . $image_name);
                }
            } else {
                $password = trim($_POST['password']);
                if (empty($password)) {
                    $password = "student123";
                }
                // Prepared statement to insert
                $stmt = $conn->prepare("INSERT INTO students (name, email, password, phone, course, gender, image, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $name, $email, $password, $phone, $course, $gender, $image_name, $address);
                
                if ($stmt->execute()) {
                    $success = "Student added successfully!";
                } else {
                    $error = "Database error: Failed to save student details.";
                    // Delete uploaded image if database insert failed
                    if (!empty($image_name)) {
                        unlink("../uploads/" . $image_name);
                    }
                }
                $stmt->close();
            }
            $stmt_check->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - SMS</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="main-content">
    <div class="card shadow border-0 p-4">
        <h2 class="mb-4 fw-bold text-dark"><i class="fa fa-user-plus text-success me-2"></i>Add Student</h2>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if (!empty($success)) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-user text-muted"></i></span>
                        <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Phone</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-phone text-muted"></i></span>
                        <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Course</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-book text-muted"></i></span>
                        <input type="text" name="course" class="form-control" placeholder="Enter course name">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Gender</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-venus-mars text-muted"></i></span>
                        <select name="gender" class="form-select">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Profile Image</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-image text-muted"></i></span>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-text text-muted">Supported formats: JPG, PNG, GIF, WEBP.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Login Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Enter password (default: student123)">
                    </div>
                    <div class="form-text text-muted">Leave empty to use default "student123".</div>
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label fw-semibold">Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-map-marker-alt text-muted"></i></span>
                        <textarea name="address" class="form-control" rows="3" placeholder="Enter permanent address"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" name="save_student" class="btn btn-success px-4 py-2 fw-semibold">
                <i class="fa fa-save me-2"></i>Save Student
            </button>
            <a href="students.php" class="btn btn-outline-secondary px-4 py-2 ms-2 fw-semibold">
                Cancel
            </a>
        </form>
    </div>
</div>

<!-- Bootstrap JS for dismissible alerts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>