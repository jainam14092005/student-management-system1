<?php
session_start();
include('../include/config.php');

if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {
    header("Location: ../login.php");
    exit();
}

$error = "";
$success = "";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: students.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch current student details
$stmt_fetch = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt_fetch->bind_param("i", $id);
$stmt_fetch->execute();
$result_fetch = $stmt_fetch->get_result();

if ($result_fetch->num_rows == 0) {
    header("Location: students.php");
    exit();
}

$student = $result_fetch->fetch_assoc();
$stmt_fetch->close();

if (isset($_POST['update_student'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $course = trim($_POST['course']);
    $gender = trim($_POST['gender']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);

    // Validations
    if (empty($name) || empty($email)) {
        $error = "Name and Email are required fields.";
    } else {
        // Check if email is used by another student
        $stmt_check = $conn->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
        $stmt_check->bind_param("si", $email, $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error = "Another student with this email address already exists.";
        } else {
            $image_name = $student['image'];
            
            // Handle file upload if new image is provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $image_tmp = $_FILES['image']['tmp_name'];
                $orig_name = $_FILES['image']['name'];
                $image_ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                
                // Validate image extension
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($image_ext, $allowed_exts)) {
                    $error = "Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP images are allowed.";
                } else {
                    $new_image_name = time() . '_' . uniqid() . '.' . $image_ext;
                    $target_dir = "../uploads/";
                    
                    if (move_uploaded_file($image_tmp, $target_dir . $new_image_name)) {
                        // Delete old image if it exists
                        if (!empty($student['image']) && file_exists($target_dir . $student['image'])) {
                            unlink($target_dir . $student['image']);
                        }
                        $image_name = $new_image_name;
                    } else {
                        $error = "Failed to upload new image. Please check directory permissions.";
                    }
                }
            }

            if (empty($error)) {
                if (!empty($password)) {
                    // Prepared statement to update with password
                    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, password = ?, phone = ?, course = ?, gender = ?, image = ?, address = ? WHERE id = ?");
                    $stmt->bind_param("ssssssssi", $name, $email, $password, $phone, $course, $gender, $image_name, $address, $id);
                } else {
                    // Prepared statement to update without password
                    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, phone = ?, course = ?, gender = ?, image = ?, address = ? WHERE id = ?");
                    $stmt->bind_param("sssssssi", $name, $email, $phone, $course, $gender, $image_name, $address, $id);
                }
                
                if ($stmt->execute()) {
                    $_SESSION['msg'] = "Student details updated successfully!";
                    header("Location: students.php");
                    exit();
                } else {
                    $error = "Database error: Failed to update student details.";
                }
                $stmt->close();
            }
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - SMS</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .current-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e3e6f0;
        }
    </style>
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="main-content">
    <div class="card shadow border-0 p-4">
        <h2 class="mb-4 fw-bold text-dark"><i class="fa fa-edit text-warning me-2"></i>Edit Student Details</h2>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-user text-muted"></i></span>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Phone</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-phone text-muted"></i></span>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Course</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-book text-muted"></i></span>
                        <input type="text" name="course" class="form-control" value="<?php echo htmlspecialchars($student['course'] ?? ''); ?>">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Gender</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-venus-mars text-muted"></i></span>
                        <select name="gender" class="form-select">
                            <option value="Male" <?php echo ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($student['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Profile Image</label>
                    <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($student['image']) && file_exists("../uploads/" . $student['image'])) { ?>
                            <img src="../uploads/<?php echo htmlspecialchars($student['image']); ?>" class="current-img" alt="Current Student Photo">
                        <?php } else { ?>
                            <div class="current-img bg-light border d-flex align-items-center justify-content-center text-muted">
                                <i class="fa fa-user fs-2"></i>
                            </div>
                        <?php } ?>
                        <div class="flex-grow-1">
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text text-muted mb-0">Supported formats: JPG, PNG, GIF, WEBP. Leave empty to keep current image.</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Change Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Enter new password (optional)">
                    </div>
                    <div class="form-text text-muted">Leave blank to keep existing password.</div>
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label fw-semibold">Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-map-marker-alt text-muted"></i></span>
                        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" name="update_student" class="btn btn-warning px-4 py-2 fw-semibold text-dark">
                <i class="fa fa-save me-2"></i>Update Student
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
