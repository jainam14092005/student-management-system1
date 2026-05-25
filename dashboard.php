<?php
session_start();
include('../include/config.php');

if (!isset($_SESSION['student_login']) || $_SESSION['student_login'] !== TRUE) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// 1. Fetch Student Profile details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    header("Location: logout.php");
    exit();
}
$student = $result->fetch_assoc();
$stmt->close();

// 2. Fetch Attendance calculations
$stmt_att = $conn->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present FROM attendance WHERE student_id = ?");
$stmt_att->bind_param("i", $student_id);
$stmt_att->execute();
$res_att = $stmt_att->get_result()->fetch_assoc();
$total_att = $res_att['total'] ?? 0;
$present_att = $res_att['present'] ?? 0;
$attendance_percentage = $total_att > 0 ? round(($present_att / $total_att) * 100) : 100;
$stmt_att->close();

// 3. Fetch Marks calculations
$stmt_marks = $conn->prepare("SELECT COUNT(DISTINCT subject) as total_subjects, AVG(marks) as avg_marks FROM marks WHERE student_id = ?");
$stmt_marks->bind_param("i", $student_id);
$stmt_marks->execute();
$res_marks = $stmt_marks->get_result()->fetch_assoc();
$total_subjects = $res_marks['total_subjects'] ?? 0;
$avg_marks = isset($res_marks['avg_marks']) ? round($res_marks['avg_marks'], 1) : null;
$stmt_marks->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Portal</title>
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
        <h2 class="page-title mb-0">Student Dashboard</h2>
        <span class="badge bg-light text-dark p-2 fs-6 shadow-sm border border-light-subtle">
            <i class="fa fa-graduation-cap me-1 text-primary"></i> 
            Student Portal
        </span>
    </div>

    <!-- Summary Metrics Section -->
    <div class="row g-4 mb-4">
        <!-- Profile quick summary -->
        <div class="col-lg-4">
            <div class="card glass-card h-100">
                <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center">
                    <?php if (!empty($student['image']) && file_exists("../uploads/" . $student['image'])) { ?>
                        <img src="../uploads/<?php echo htmlspecialchars($student['image']); ?>" class="profile-avatar mb-3" alt="Student Profile Photo">
                    <?php } else { 
                        $initial = strtoupper(substr($student['name'], 0, 1));
                    ?>
                        <div class="placeholder-avatar-large mb-3"><?php echo $initial; ?></div>
                    <?php } ?>
                    <h4 class="fw-bold text-white mb-1"><?php echo htmlspecialchars($student['name']); ?></h4>
                    <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-3 py-1.5 fs-7">
                        <?php echo htmlspecialchars(!empty($student['course']) ? $student['course'] : 'Not enrolled in any course'); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Attendance Stats Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card glass-card h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <span class="metric-title d-block mb-2">My Attendance</span>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h2 class="metric-value mb-1"><?php echo $attendance_percentage; ?>%</h2>
                                <span class="text-white-50 fs-7">Present for <?php echo $present_att; ?>/<?php echo $total_att; ?> days</span>
                            </div>
                            <div class="attendance-gauge" style="background: conic-gradient(#a78bfa <?php echo $attendance_percentage * 3.6; ?>deg, rgba(255,255,255,0.08) 0deg);">
                                <div class="w-75 h-75 bg-dark rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa fa-calendar-check text-purple"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px; background: rgba(255,255,255,0.08);">
                            <div class="progress-bar bg-purple" role="progressbar" style="width: <?php echo $attendance_percentage; ?>%" aria-valuenow="<?php echo $attendance_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic GPA/Marks Stats Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card glass-card h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <span class="metric-title d-block mb-2">Academic Summary</span>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h2 class="metric-value mb-1"><?php echo $avg_marks !== null ? $avg_marks : 'N/A'; ?></h2>
                                <span class="text-white-50 fs-7">Average score across <?php echo $total_subjects; ?> subjects</span>
                            </div>
                            <div class="fs-1 text-white-50">
                                <i class="fa fa-chart-line text-info"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-white-50 fs-8">Status: 
                            <strong class="text-white">
                                <?php 
                                if ($avg_marks === null) echo 'No scores yet';
                                elseif ($avg_marks >= 80) echo 'Excellent';
                                elseif ($avg_marks >= 60) echo 'Good Progress';
                                else echo 'Needs Attention';
                                ?>
                            </strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Detail Profile Information Table -->
    <div class="card glass-card p-4">
        <h3 class="fw-bold mb-4 text-white"><i class="fa fa-info-circle text-purple me-2"></i>Personal Information</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <span class="text-white-50 d-block fs-7">Email Address</span>
                    <span class="text-white fw-semibold"><?php echo htmlspecialchars($student['email']); ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-white-50 d-block fs-7">Phone Number</span>
                    <span class="text-white fw-semibold"><?php echo htmlspecialchars(!empty($student['phone']) ? $student['phone'] : 'N/A'); ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-white-50 d-block fs-7">Course Stream</span>
                    <span class="text-white fw-semibold"><?php echo htmlspecialchars(!empty($student['course']) ? $student['course'] : 'N/A'); ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <span class="text-white-50 d-block fs-7">Gender</span>
                    <span class="text-white fw-semibold"><?php echo htmlspecialchars($student['gender'] ?? 'N/A'); ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-white-50 d-block fs-7">Residential Address</span>
                    <span class="text-white fw-semibold"><?php echo htmlspecialchars(!empty($student['address']) ? $student['address'] : 'N/A'); ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-white-50 d-block fs-7">Account Created</span>
                    <span class="text-white fw-semibold"><?php echo date('d M, Y', strtotime($student['created_at'])); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
