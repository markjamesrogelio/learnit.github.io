<?php
session_start();
require_once('../includes/db.php');

$teacher_id = $_SESSION['user_id'];
$quiz_id = $_GET['quiz_id'] ?? null;
$message = '';

if (!$quiz_id || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

// Fetch questions for the quiz
$stmt = $conn->prepare("SELECT id, question_text, question_type, answer FROM quiz_questions WHERE quiz_id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $quiz_id, $teacher_id);
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
    <title>Quiz Questions | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css" />
</head>
<body>
    <div class="container" style="padding:20px;">
        <h2>Questions for Quiz #<?= htmlspecialchars($quiz_id) ?></h2>

        <?php if ($message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <a href="quiz_question_form.php?quiz_id=<?= $quiz_id ?>" class="btn">➕ Add New Question</a>

        <?php if ($result->num_rows === 0): ?>
            <p>No questions for this quiz yet.</p>
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
                                <a href="quiz_question_form.php?quiz_id=<?= $quiz_id ?>&id=<?= $row['id'] ?>" class="btn">Edit</a>
                                <a href="dashboard.php?section=quiz&action=questions_delete&quiz_id=<?= $quiz_id ?>&question_id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this question?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
