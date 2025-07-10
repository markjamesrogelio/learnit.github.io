<?php
require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $course = $_POST['course'];
    $section_name = $_POST['section_name'];
    $year_level = $_POST['year_level'];

    $stmt = $conn->prepare("UPDATE sections SET course = ?, section_name = ?, year_level = ? WHERE id = ?");
    $stmt->bind_param("sssi", $course, $section_name, $year_level, $id);
    $stmt->execute();

    header("Location: dashboard.php?section=sections");
    exit;
}
