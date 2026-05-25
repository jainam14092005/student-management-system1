<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar student-sidebar">
    <div class="text-center text-white mb-4 py-2 border-bottom border-secondary-subtle">
        <h3 class="fw-bold mb-0">Portal</h3>
        <small class="text-white-50">Student Panel</small>
    </div>
    <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        <i class="fa fa-th-large me-2"></i>Dashboard
    </a>
    <a href="attendance.php" class="<?php echo ($current_page == 'attendance.php') ? 'active' : ''; ?>">
        <i class="fa fa-calendar-check me-2"></i>My Attendance
    </a>
    <a href="marks.php" class="<?php echo ($current_page == 'marks.php') ? 'active' : ''; ?>">
        <i class="fa fa-graduation-cap me-2"></i>My Marks
    </a>
    <a href="logout.php">
        <i class="fa fa-sign-out-alt me-2"></i>Logout
    </a>
</div>
