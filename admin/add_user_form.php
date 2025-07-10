<?php
require_once('../includes/db.php');

$role = $_GET['role'] ?? '';
if (!in_array($role, ['student', 'teacher'])) {
    echo "<p>Invalid role selected.</p>";
    exit;
}

$deptQuery = $conn->query("SELECT * FROM departments");
$departments = $deptQuery->fetch_all(MYSQLI_ASSOC);

if ($role === 'student') {
    include('student_form.php');
} else {
    include('teacher_form.php');
}
?>
