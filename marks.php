<?php
session_start();
include('../include/config.php');

if (!isset($_SESSION['student_login']) || $_SESSION['student_login'] !== TRUE) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// 1. Fetch Marks Stats
$stmt_stats = $conn->prepare("SELECT 
    COUNT(*) as total_subjects,
    AVG(marks) as avg_marks,
    MAX(marks) as max_marks,
    MIN(marks) as min_marks
    FROM marks WHERE student_id = ?");
$stmt_stats->bind_param("i", $student_id);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();
$stmt_stats->close();

$total_sub = $stats['total_subjects'] ?? 0;
$avg_marks = isset($stats['avg_marks']) ? round($stats['avg_marks'], 1) : 0;
$max_marks = $stats['max_marks'] ?? 0;
$min_marks = $stats['min_marks'] ?? 0;

// Helper to determine Grade and color class
function getGradeDetails($score) {
    if ($score >= 90) {
        return ['grade' => 'A+', 'color' => 'text-success', 'bg' => 'rgba(16, 185, 129, 0.15)', 'remark' => 'Outstanding'];
    } elseif ($score >= 80) {
        return ['grade' => 'A', 'color' => 'text-success', 'bg' => 'rgba(16, 185, 129, 0.12)', 'remark' => 'Excellent'];
    } elseif ($score >= 70) {
        return ['grade' => 'B', 'color' => 'text-info', 'bg' => 'rgba(6, 182, 212, 0.12)', 'remark' => 'Good'];
    } elseif ($score >= 60) {
        return ['grade' => 'C', 'color' => 'text-warning', 'bg' => 'rgba(245, 158, 11, 0.12)', 'remark' => 'Satisfactory'];
    } elseif ($score >= 50) {
        return ['grade' => 'D', 'color' => 'text-warning', 'bg' => 'rgba(245, 158, 11, 0.08)', 'remark' => 'Pass'];
    } else {
        return ['grade' => 'F', 'color' => 'text-danger', 'bg' => 'rgba(239, 68, 68, 0.15)', 'remark' => 'Fail'];
    }
}

$avg_grade_details = getGradeDetails($avg_marks);

// 2. Fetch full marks logs list
$stmt_logs = $conn->prepare("SELECT * FROM marks WHERE student_id = ? ORDER BY subject ASC");
$stmt_logs->bind_param("i", $student_id);
$stmt_logs->execute();
$result_logs = $stmt_logs->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Marks - Student Portal</title>
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
        <h2 class="page-title mb-0">My Academic Marks</h2>
        <span class="badge bg-light text-dark p-2 fs-6 shadow-sm border border-light-subtle">
            Avg Grade: <strong class="<?php echo $avg_grade_details['color']; ?>"><?php echo $avg_grade_details['grade']; ?></strong>
        </span>
    </div>

    <!-- Marks Summary Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Subjects card -->
        <div class="col-md-3 col-sm-6">
            <div class="card glass-card text-center p-3">
                <h5 class="metric-title text-info mb-1"><i class="fa fa-book me-1"></i>Subjects</h5>
                <h2 class="fw-bold mb-0 text-white"><?php echo $total_sub; ?></h2>
            </div>
        </div>
        <!-- Average Score card -->
        <div class="col-md-3 col-sm-6">
            <div class="card glass-card text-center p-3">
                <h5 class="metric-title text-purple mb-1"><i class="fa fa-chart-bar me-1"></i>Average Marks</h5>
                <h2 class="fw-bold mb-0 text-white"><?php echo $avg_marks; ?>%</h2>
            </div>
        </div>
        <!-- Highest marks card -->
        <div class="col-md-3 col-sm-6">
            <div class="card glass-card text-center p-3">
                <h5 class="metric-title text-success mb-1"><i class="fa fa-circle-arrow-up me-1"></i>Highest Marks</h5>
                <h2 class="fw-bold mb-0 text-white"><?php echo $max_marks; ?>%</h2>
            </div>
        </div>
        <!-- Lowest marks card -->
        <div class="col-md-3 col-sm-6">
            <div class="card glass-card text-center p-3">
                <h5 class="metric-title text-warning mb-1"><i class="fa fa-circle-arrow-down me-1"></i>Lowest Marks</h5>
                <h2 class="fw-bold mb-0 text-white"><?php echo $min_marks; ?>%</h2>
            </div>
        </div>
    </div>

    <!-- Academic Report Table -->
    <div class="card glass-card p-4">
        <h3 class="fw-bold mb-4 text-white"><i class="fa fa-receipt text-purple me-2"></i>Academic Report Card</h3>

        <div class="table-responsive">
            <table class="table align-middle student-table text-white border-0">
                <thead>
                    <tr>
                        <th class="border-0">#</th>
                        <th class="border-0">Subject</th>
                        <th class="border-0">Scored Marks</th>
                        <th class="border-0 text-center">Grade</th>
                        <th class="border-0 text-center">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result_logs) > 0) {
                        $counter = 1;
                        while ($row = mysqli_fetch_assoc($result_logs)) {
                            $gradeInfo = getGradeDetails($row['marks']);
                    ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td class="fw-bold text-white"><?php echo htmlspecialchars($row['subject']); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold"><?php echo $row['marks']; ?></span>
                                <span class="text-white-50 fs-8">/ 100</span>
                                <div class="progress flex-grow-1" style="height: 5px; max-width: 100px; background: rgba(255,255,255,0.08);">
                                    <div class="progress-bar bg-purple" role="progressbar" style="width: <?php echo $row['marks']; ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge fw-bold px-3 py-1.5 fs-7" style="background: <?php echo $gradeInfo['bg']; ?>; color: <?php echo $gradeInfo['color']; ?> !important;">
                                <?php echo $gradeInfo['grade']; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fs-7 <?php echo $gradeInfo['color']; ?>"><?php echo $gradeInfo['remark']; ?></span>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa fa-receipt fs-2 mb-3 d-block text-secondary"></i>
                            No marks have been recorded for you yet.
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
