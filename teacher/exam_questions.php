<?php
session_start();
require_once('../includes/db.php');

$teacher_id = $_SESSION['user_id'];
$exam_id = $_GET['exam_id'] ?? null;
$message = '';

if (!$exam_id || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

// Fetch questions for the exam
$stmt = $conn->prepare("SELECT id, question_text, question_type, answer FROM exam_questions WHERE exam_id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $exam_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Question deleted successfully.';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Exam Questions | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css" />
</head>
<body>
    <div class="container" style="padding:20px;">
        <h2>Questions for Exam #<?= htmlspecialchars($exam_id) ?></h2>

        <?php if ($message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <a href="exam_question_form.php?exam_id=<?= $exam_id ?>" class="btn">➕ Add New Question</a>

        <?php if ($result->num_rows === 0): ?>
            <p>No questions for this exam yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['question_text']) ?></td>
                            <td><?= htmlspecialchars($row['question_type']) ?></td>
                            <td>
                                <a href="exam_question_form.php?exam_id=<?= $exam_id ?>&id=<?= $row['id'] ?>" class="btn">Edit</a>
                                <a href="dashboard.php?section=exam&action=questions_delete&exam_id=<?= $exam_id ?>&question_id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this question?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
