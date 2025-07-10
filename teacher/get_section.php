<?php
require_once('../includes/db.php');

if (isset($_GET['course_id']) && isset($_GET['year_level'])) {
    $course_id = intval($_GET['course_id']);
    $year_level = $_GET['year_level'];

    // Fetch sections for the selected course and year level
    $stmt = $conn->prepare("SELECT id, section_name FROM course_year_section_data WHERE course_id = ? AND year_level = ?");
    $stmt->bind_param("is", $course_id, $year_level);
    $stmt->execute();
    $result = $stmt->get_result();

    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }

    // Return sections as a JSON response
    echo json_encode($sections);
    $stmt->close();
}
?>
