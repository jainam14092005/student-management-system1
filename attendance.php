<?php
session_start();
include('../include/config.php');

if (!isset($_SESSION['student_login']) || $_SESSION['student_login'] !== TRUE) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// 1. Fetch attendance summary stats
$stmt_stats = $conn->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent
    FROM attendance WHERE student_id = ?");
$stmt_stats->bind_param("i", $student_id);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();
$stmt_stats->close();

$total_days = $stats['total'] ?? 0;
$present_days = $stats['present'] ?? 0;
$absent_days = $stats['absent'] ?? 0;
$percentage = $total_days > 0 ? round(($present_days / $total_days) * 100) : 100;

// 2. Fetch full attendance log list
$stmt_logs = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY attendance_date DESC");
$stmt_logs->bind_param("i", $student_id);
$stmt_logs->execute();
$result_logs = $stmt_logs->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance - Student Portal</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom Student CSS -->
    <link rel="stylesheet" href="../css/student-style.css">
</head>
<body>

<?php include '../include/student-sidebar.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title mb-0">My Attendance</h2>
        <span class="badge bg-light text-dark p-2 fs-6 shadow-sm border border-light-subtle">
            Overall: <?php echo $percentage; ?>%
        </span>
    </div>

    <!-- Attendance Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Present card -->
        <div class="col-md-4">
            <div class="card glass-card text-center p-3">
                <h5 class="metric-title text-success mb-1"><i class="fa fa-circle-check me-1"></i>Present Days</h5>
                <h2 class="fw-bold mb-0 text-white"><?php echo $present_days; ?></h2>
            </div>
        </div>
        <!-- Absent card -->
        <div class="col-md-4">
            <div class="card glass-card text-center p-3">
                <h5 class="metric-title text-danger mb-1"><i class="fa fa-circle-xmark me-1"></i>Absent Days</h5>
                <h2 class="fw-bold mb-0 text-white"><?php echo $absent_days; ?></h2>
            </div>
        </div>
        <!-- Total logged card -->
        <div class="col-md-4">
            <div class="card glass-card text-center p-3">
                <h5 class="metric-title text-info mb-1"><i class="fa fa-calendar-alt me-1"></i>Total Classes Logged</h5>
                <h2 class="fw-bold mb-0 text-white"><?php echo $total_days; ?></h2>
            </div>
        </div>
    </div>

    <!-- Attendance Logs list -->
    <div class="card glass-card p-4">
        <h3 class="fw-bold mb-4 text-white"><i class="fa fa-history text-purple me-2"></i>Attendance Log History</h3>

        <div class="table-responsive">
            <table class="table align-middle student-table text-white border-0">
                <thead>
                    <tr>
                        <th class="border-0">#</th>
                        <th class="border-0">Date</th>
                        <th class="border-0 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result_logs) > 0) {
                        $counter = 1;
                        while ($row = mysqli_fetch_assoc($result_logs)) {
                    ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td class="fw-bold"><?php echo date('d M, Y (l)', strtotime($row['attendance_date'])); ?></td>
                        <td class="text-center">
                            <?php if ($row['status'] == 'Present') { ?>
                                <span class="badge status-badge-present px-3 py-1.5 fw-semibold text-success">
                                    <i class="fa fa-check me-1"></i>Present
                                </span>
                            <?php } else { ?>
                                <span class="badge status-badge-absent px-3 py-1.5 fw-semibold text-danger">
                                    <i class="fa fa-xmark me-1"></i>Absent
                                </span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="3" class="text-center py-5 text-muted">
                            <i class="fa fa-calendar-times fs-2 mb-3 d-block text-secondary"></i>
                            No attendance logs have been recorded for you yet.
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
