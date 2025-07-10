<?php
session_start();
require_once('../includes/db.php');

$teacher_id = $_SESSION['user_id'];
$quiz_id = $_GET['quiz_id'] ?? null;

if (!$quiz_id || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$error = '';
$message = '';
$question_id = $_GET['id'] ?? null;
$question_type = '';
$question_text = '';
$options = [];
$answer = '';

if ($question_id) {
    // Fetch question details if we're editing
    $stmt = $conn->prepare("SELECT question_type, question_text, answer, options FROM quiz_questions WHERE id = ? AND quiz_id = ?");
    $stmt->bind_param("ii", $question_id, $quiz_id);
    $stmt->execute();
    $stmt->bind_result($question_type, $question_text, $answer, $options);
    if ($stmt->fetch()) {
        $options = json_decode($options, true);
    } else {
        $error = 'Question not found.';
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = $_POST['question_text'] ?? '';
    $question_type = $_POST['question_type'] ?? '';
    $answer = $_POST['answer'] ?? '';
    $options = isset($_POST['options']) ? json_encode($_POST['options']) : '';

    if (empty($question_text) || empty($question_type) || empty($answer)) {
        $error = 'All fields are required.';
    } else {
        if ($question_id) {
            // Edit the existing question
            $stmt = $conn->prepare("UPDATE quiz_questions SET question_text = ?, question_type = ?, answer = ?, options = ? WHERE id = ? AND quiz_id = ?");
            $stmt->bind_param("ssssii", $question_text, $question_type, $answer, $options, $question_id, $quiz_id);
        } else {
            // Create a new question
            $stmt = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question_text, question_type, answer, options) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $quiz_id, $question_text, $question_type, $answer, $options);
        }

        if ($stmt->execute()) {
            $message = 'Question saved successfully.';
            header("Location: dashboard.php?section=quiz&action=questions_list&quiz_id=$quiz_id&msg=success");
            exit();
        } else {
            $error = 'Failed to save question.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add/Edit Question | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css" />
</head>
<body>
    <div class="container" style="padding:20px;">
        <h2><?= $question_id ? 'Edit' : 'Add' ?> Question</h2>

        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Question Text:</label><br />
            <input type="text" name="question_text" value="<?= htmlspecialchars($question_text) ?>" required /><br />

            <label>Question Type:</label><br />
            <select name="question_type" required>
                <option value="identification" <?= $question_type === 'identification' ? 'selected' : '' ?>>Identification</option>
                <option value="multiple_choice" <?= $question_type === 'multiple_choice' ? 'selected' : '' ?>>Multiple Choice</option>
                <option value="true_false" <?= $question_type === 'true_false' ? 'selected' : '' ?>>True/False</option>
            </select><br />

            <label>Answer:</label><br />
            <input type="text" name="answer" value="<?= htmlspecialchars($answer) ?>" required /><br />

            <label>Options (for Multiple Choice only):</label><br />
            <textarea name="options" rows="3"><?= htmlspecialchars($options) ?></textarea><br />

            <button type="submit" class="btn">Save Question</button>
            <a href="dashboard.php?section=quiz&action=questions_list&quiz_id=<?= $quiz_id ?>" class="btn btn-cancel">Cancel</a>
        </form>
    </div>
</body>
</html>
