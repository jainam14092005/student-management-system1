<?php
session_start();
include('../include/config.php');

if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch student details to get the image filename
    $stmt_fetch = $conn->prepare("SELECT image FROM students WHERE id = ?");
    $stmt_fetch->bind_param("i", $id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();

    if ($result_fetch->num_rows > 0) {
        $student = $result_fetch->fetch_assoc();
        $image = $student['image'];

        // Delete the image file if it exists
        if (!empty($image) && file_exists("../uploads/" . $image)) {
            unlink("../uploads/" . $image);
        }
    }
    $stmt_fetch->close();

    // Delete student record from the database (associated attendance/marks will cascade delete due to foreign keys)
    $stmt_del = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt_del->bind_param("i", $id);
    
    if ($stmt_del->execute()) {
        $_SESSION['msg'] = "Student deleted successfully!";
    } else {
        $_SESSION['msg'] = "Error deleting student record.";
    }
    $stmt_del->close();
}

header('Location: students.php');
exit();
?>