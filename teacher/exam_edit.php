<?php
session_start();
require_once('../includes/db.php');

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$error = '';
$message = '';
$exam_id = intval($_GET['id'] ?? 0);

if ($exam_id <= 0) {
    header("Location: exam.php");
    exit();
}

// Fetch exam details
$stmt = $conn->prepare("SELECT title, description, status FROM exams WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $exam_id, $teacher_id);
$stmt->execute();
$stmt->bind_result($title, $description, $status);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: exam.php");
    exit();
}
$stmt->close();

// Fetch all questions for the exam (only include options for multiple choice)
$questions = [];
$stmt = $conn->prepare("SELECT id, question_text, question_type, correct_answer, options, question_number FROM exam_questions WHERE exam_id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($q_id, $q_text, $q_type, $q_answer, $q_options, $q_number);
while ($stmt->fetch()) {
    $question_data = [
        'id' => $q_id,
        'question_text' => $q_text,
        'question_type' => $q_type,
        'correct_answer' => $q_answer,
        'question_number' => $q_number,
    ];

    // Decode options only for multiple choice questions
    if ($q_type == 'multiple_choice' && !empty($q_options)) {
        $question_data['options'] = json_decode($q_options, true); // Decode options as an array
    } else {
        $question_data['options'] = []; // No options for other types
    }

    $questions[] = $question_data;
}
$stmt->close();

// Handle form submission for updating exam details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = trim($_POST['title'] ?? '');
    $new_description = trim($_POST['description'] ?? '');
    $new_status = $_POST['status'] ?? 'Draft';

    if ($new_title === '') {
        $error = 'Title is required.';
    } else {
        // Update exam details
        $stmt = $conn->prepare("UPDATE exams SET title = ?, description = ?, status = ? WHERE id = ? AND teacher_id = ?");
        $stmt->bind_param("sssii", $new_title, $new_description, $new_status, $exam_id, $teacher_id);

        if ($stmt->execute()) {
            // Success: redirect to the teacher dashboard with the exam section
            header("Location: dashboard.php?section=exam"); 
            exit();
        } else {
            $error = 'Failed to update the exam.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Exam | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css" />
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #7f7fd5, #86a8e7, #91eae4);
        color: #333;
        min-height: 100vh;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 50px 20px;
    }

    .container {
        width: 100%;
        max-width: 850px;
        background: linear-gradient(180deg, #ffffff 80%, #f0f4ff);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 40px 30px;
        animation: fadeIn 0.6s ease;
    }

    .page-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e3e8f7;
    }

    .page-header h2 {
        font-size: 2em;
        font-weight: 700;
        color: #4a4ae3;
        margin: 0;
    }

    .page-header p {
        margin-top: 8px;
        color: #555;
        font-size: 1rem;
    }

    label {
        font-weight: 600;
        display: block;
        margin-top: 15px;
    }

    input[type="text"],
    textarea,
    select {
        width: 100%;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #ccc;
        margin-top: 6px;
        font-size: 14px;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
    }

    .btn {
        background: linear-gradient(to right, #667eea, #764ba2);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        margin-top: 20px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .btn-cancel {
        background: #6c757d;
        margin-left: 10px;
    }

    .message {
        padding: 15px 20px;
        border-radius: 8px;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        animation: slideUp 0.4s ease;
    }

    .message.success {
        background-color: #d4edda;
        color: #155724;
    }

    .message.error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 16px;
        background: none;
        border: none;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        color: inherit;
    }

    .questions-section {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #e3e8f7;
    }

    .questions-section h3 {
        font-size: 1.5em;
        font-weight: 600;
        color: #4a4ae3;
        margin-bottom: 20px;
    }

    .questions-list {
        list-style: none;
        padding: 0;
    }

    .question-container {
        margin-bottom: 20px;
        padding: 20px;
        border-radius: 12px;
        background: linear-gradient(145deg, #ffffff, #f8faff);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e1e8f7;
        transition: all 0.3s ease;
        animation: slideUp 0.4s ease;
    }

    .question-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .question-container h4 {
        margin: 0 0 12px 0;
        color: #4a4ae3;
        font-size: 1.1em;
        font-weight: 600;
    }

    .question-text {
        font-weight: 600;
        color: #333;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .question-meta {
        margin-bottom: 12px;
        color: #666;
    }

    .question-meta strong {
        color: #4a4ae3;
    }

    .options {
        margin: 15px 0;
        padding: 15px;
        background: #f0f4ff;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }

    .options strong {
        color: #4a4ae3;
        display: block;
        margin-bottom: 8px;
    }

    .options p {
        margin: 5px 0;
        padding: 4px 8px;
        background: white;
        border-radius: 4px;
        font-size: 0.9em;
    }

    .question-actions {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e1e8f7;
    }

    .question-actions a {
        font-size: 0.9em;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 6px;
        margin-right: 8px;
        transition: all 0.3s ease;
    }

    .question-actions a:first-child {
        background: #e3f2fd;
        color: #1976d2;
    }

    .question-actions a:first-child:hover {
        background: #bbdefb;
    }

    .question-actions a:last-child {
        background: #ffebee;
        color: #d32f2f;
    }

    .question-actions a:last-child:hover {
        background: #ffcdd2;
    }

    .no-questions {
        text-align: center;
        padding: 40px;
        color: #666;
        font-style: italic;
    }

    .add-question-btn {
        background: linear-gradient(to right, #4caf50, #45a049);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        margin-top: 20px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .add-question-btn:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>
</head>
<body>

<div class="container">
    <header class="page-header">
        <h2>Edit Exam</h2>
        <p>Modify exam details and manage questions efficiently</p>
    </header>

    <?php if ($error): ?>
        <div class="message error" id="errorBox">
            <?= htmlspecialchars($error) ?>
            <button onclick="document.getElementById('errorBox').style.display='none';" class="close-btn">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="message success" id="messageBox">
            <?= htmlspecialchars($message) ?>
            <button onclick="document.getElementById('messageBox').style.display='none';" class="close-btn">&times;</button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>

        <label>Description:</label>
        <textarea name="description" rows="5"><?= htmlspecialchars($description) ?></textarea>

        <label>Status:</label>
        <select name="status">
            <option value="Draft" <?= $status === 'Draft' ? 'selected' : '' ?>>Draft</option>
            <option value="Published" <?= $status === 'Published' ? 'selected' : '' ?>>Published</option>
        </select>

        <button type="submit" class="btn">Save Exam</button>
        <a href="dashboard.php?section=exam" class="btn btn-cancel">Cancel</a>
    </form>

    <div class="questions-section">
        <h3>Questions (<?= count($questions) ?>)</h3>
        
        <?php if (empty($questions)): ?>
            <div class="no-questions">
                <p>No questions added yet. Start building your exam by adding questions!</p>
            </div>
        <?php else: ?>
            <ul class="questions-list">
                <?php foreach ($questions as $q): ?>
                    <li class="question-container">
                        <h4>Question <?= $q['question_number'] ?></h4>
                        <div class="question-text"><?= htmlspecialchars($q['question_text']) ?></div>
                        <div class="question-meta">
                            <strong>Type:</strong> <?= ucfirst(str_replace('_', ' ', $q['question_type'])) ?>
                        </div>
                        <div class="question-meta">
                            <strong>Correct Answer:</strong> <?= htmlspecialchars($q['correct_answer']) ?>
                        </div>
                        
                        <?php if ($q['question_type'] === 'multiple_choice' && !empty($q['options'])): ?>
                            <div class="options">
                                <strong>Options:</strong>
                                <?php foreach ($q['options'] as $option): ?>
                                    <p><?= htmlspecialchars($option) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="question-actions">
                            <a href="exam_edit_question.php?id=<?= $q['id'] ?>&exam_id=<?= $exam_id ?>">Edit Question</a>
                            <a href="exam_create.php?action=delete_question&id=<?= $q['id'] ?>&exam_id=<?= $exam_id ?>" onclick="return confirm('Are you sure you want to delete this question?');">Delete Question</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <a href="exam_create.php?exam_id=<?= $exam_id ?>" class="add-question-btn">Add New Question</a>
    </div>
</div>

</body>
</html>