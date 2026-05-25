<?php
session_start();
include('../include/config.php');

if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {
    header("Location: ../login.php");
    exit();
}

// Build query with filters
$where_clauses = [];
$params = [];
$types = "";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = "%" . trim($_GET['search']) . "%";
    $where_clauses[] = "s.name LIKE ?";
    $params[] = $search;
    $types .= "s";
}

if (isset($_GET['date']) && !empty(trim($_GET['date']))) {
    $date = trim($_GET['date']);
    $where_clauses[] = "a.attendance_date = ?";
    $params[] = $date;
    $types .= "s";
}

$query = "SELECT a.id, a.attendance_date, a.status, s.name, s.course 
          FROM attendance a 
          JOIN students s ON a.student_id = s.id";

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY a.attendance_date DESC, s.name ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance - SMS</title>
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
        <h2 class="mb-4 fw-bold text-dark"><i class="fa fa-calendar-check text-primary me-2"></i>Attendance Log History</h2>

        <!-- Filters Form -->
        <form method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Search Student</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Filter Date</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa fa-calendar text-muted"></i></span>
                    <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($_GET['date'] ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-5">
                <button type="submit" class="btn btn-primary fw-semibold px-4"><i class="fa fa-filter me-2"></i>Filter</button>
                <a href="view-attendance.php" class="btn btn-outline-secondary fw-semibold px-4 ms-2">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle border-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">#</th>
                        <th class="border-0">Student Name</th>
                        <th class="border-0">Course</th>
                        <th class="border-0">Date</th>
                        <th class="border-0 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result->num_rows > 0) {
                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td class="text-secondary fw-semibold">#<?php echo $counter++; ?></td>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>
                            <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1">
                                <?php echo htmlspecialchars(!empty($row['course']) ? $row['course'] : 'N/A'); ?>
                            </span>
                        </td>
                        <td class="fw-semibold text-secondary"><?php echo date('d M, Y', strtotime($row['attendance_date'])); ?></td>
                        <td class="text-center">
                            <?php if ($row['status'] == 'Present') { ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 fw-semibold">
                                    <i class="fa fa-check-circle me-1"></i>Present
                                </span>
                            <?php } else { ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 fw-semibold">
                                    <i class="fa fa-times-circle me-1"></i>Absent
                                </span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else { 
                    ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa fa-folder-open fs-2 mb-3 d-block text-secondary"></i>
                            No attendance records matched the search filters.
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
<?php 
$stmt->close();
?>
