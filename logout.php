<?php
session_start();
unset($_SESSION['student_login']);
unset($_SESSION['student_id']);
unset($_SESSION['student_name']);
unset($_SESSION['student_email']);
header("Location: login.php");
exit();
?>
