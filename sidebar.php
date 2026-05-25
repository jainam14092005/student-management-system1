<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="text-center text-white mb-4 py-2 border-bottom border-secondary">
        <h3 class="fw-bold mb-0">SMS Panel</h3>
        <small class="text-white-50">Admin Panel</small>
    </div>
    <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        <i class="fa fa-tachometer-alt me-2"></i>Dashboard
    </a>
    <a href="add-student.php" class="<?php echo ($current_page == 'add-student.php') ? 'active' : ''; ?>">
        <i class="fa fa-user-plus me-2"></i>Add Student
    </a>
    <a href="students.php" class="<?php echo ($current_page == 'students.php' || $current_page == 'edit-student.php') ? 'active' : ''; ?>">
        <i class="fa fa-users me-2"></i>Manage Students
    </a>
    <a href="attendance.php" class="<?php echo ($current_page == 'attendance.php') ? 'active' : ''; ?>">
        <i class="fa fa-calendar-plus me-2"></i>Record Attendance
    </a>
    <a href="view-attendance.php" class="<?php echo ($current_page == 'view-attendance.php') ? 'active' : ''; ?>">
        <i class="fa fa-calendar-check me-2"></i>View Attendance
    </a>
    <a href="marks.php" class="<?php echo ($current_page == 'marks.php') ? 'active' : ''; ?>">
        <i class="fa fa-file-signature me-2"></i>Record Marks
    </a>
    <a href="view-marks.php" class="<?php echo ($current_page == 'view-marks.php') ? 'active' : ''; ?>">
        <i class="fa fa-graduation-cap me-2"></i>View Marks
    </a>
    <a href="logout.php">
        <i class="fa fa-sign-out-alt me-2"></i>Logout
    </a>
</div>
