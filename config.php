<?php
$conn = mysqli_connect ("localhost","root","",'student_management');
if(!$conn)
{
    die("Connection failed: " . mysqli_connect_error());
}
?>
