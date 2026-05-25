<?php
session_start();
include('../include/config.php');

if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {
    header("Location: ../login.php");
    exit();
}

$query = "SELECT * FROM students ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - SMS</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .student-img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e3e6f0;
        }
        .placeholder-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="main-content">
    <div class="card shadow border-0 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark mb-0"><i class="fa fa-users text-primary me-2"></i>Manage Students</h2>
            <a href="add-student.php" class="btn btn-primary fw-semibold">
                <i class="fa fa-user-plus me-2"></i>Add Student
            </a>
        </div>

        <?php if (isset($_SESSION['msg'])) { ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['msg']); ?>
                <?php unset($_SESSION['msg']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle border-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">ID</th>
                        <th class="border-0">Photo</th>
                        <th class="border-0">Name</th>
                        <th class="border-0">Email</th>
                        <th class="border-0">Course</th>
                        <th class="border-0 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { 
                            // Extract first letter of name for initials fallback
                            $initial = strtoupper(substr($row['name'], 0, 1));
                    ?>
                    <tr>
                        <td class="fw-semibold text-secondary">#<?php echo $row['id']; ?></td>
                        <td>
                            <?php if (!empty($row['image']) && file_exists("../uploads/" . $row['image'])) { ?>
                                <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" class="student-img" alt="Student Photo">
                            <?php } else { ?>
                                <div class="placeholder-avatar" title="No image uploaded"><?php echo $initial; ?></div>
                            <?php } ?>
                        </td>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1 fs-7">
                                <?php echo htmlspecialchars(!empty($row['course']) ? $row['course'] : 'N/A'); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="edit-student.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm fw-semibold me-1 px-3">
                                <i class="fa fa-edit me-1"></i>Edit
                            </a>
                            <a href="delete-student.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-sm fw-semibold px-3"
                               onclick="return confirm('Are you sure you want to delete this student? All attendance and marks records associated with this student will also be deleted.');">
                                <i class="fa fa-trash me-1"></i>Delete
                            </a>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else { 
                    ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa fa-folder-open fs-2 mb-3 d-block text-secondary"></i>
                            No students found. Click <strong>Add Student</strong> to register a new student.
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS for dismissible components -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>