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
$question_id = intval($_GET['id'] ?? 0);
$exam_id = intval($_GET['exam_id'] ?? 0);

// Check if question ID and exam ID are valid
if ($question_id <= 0 || $exam_id <= 0) {
    header("Location: exam.php");
    exit();
}

// Fetch the existing question data
$stmt = $conn->prepare("SELECT question_text, question_type, correct_answer, options FROM exam_questions WHERE id = ? AND exam_id = ? AND exam_id IN (SELECT id FROM exams WHERE teacher_id = ?)");
$stmt->bind_param("iii", $question_id, $exam_id, $teacher_id);
$stmt->execute();
$stmt->bind_result($question_text, $question_type, $correct_answer, $options_json);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: exam.php");
    exit();
}
$stmt->close();

// Decode options if question is multiple choice
$options = [];
if ($question_type === 'multiple_choice' && $options_json) {
    $options = json_decode($options_json, true);
}

// Handle form submission to update the question
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_question_text = trim($_POST['question_text']);
    $new_question_type = $_POST['question_type'];
    $new_correct_answer = trim($_POST['correct_answer']);
    $new_options = [];

    if ($new_question_type === 'multiple_choice') {
        for ($i = 1; $i <= 4; $i++) {
            $opt_key = "option_$i";
            if (!empty($_POST[$opt_key])) {
                $new_options[] = trim($_POST[$opt_key]);
            }
        }
    }

    if ($new_question_text === '' || $new_correct_answer === '') {
        $error = 'Question text and correct answer are required.';
    } else {
        // Properly encode options and prepare for database update
        $encoded_options = json_encode($new_options);  // Assign json_encoded options to a variable
        
        // Update question query
        $stmt = $conn->prepare("UPDATE exam_questions SET question_text = ?, question_type = ?, correct_answer = ?, options = ? WHERE id = ? AND exam_id = ?");
        $stmt->bind_param("ssssii", $new_question_text, $new_question_type, $new_correct_answer, $encoded_options, $question_id, $exam_id);  // Adjust bind_param types
        if ($stmt->execute()) {
            $message = 'Question updated successfully.';
            // Redirect to the exam question creation page (for the current exam)
            header("Location: exam_create.php?exam_id=$exam_id");
            exit();  // Always call exit after a header redirection to stop further script execution
        } else {
            $error = 'Failed to update question.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Question | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css" />
    <style>
    *, *::before, *::after {
        box-sizing: border-box;
    }

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
        padding: 20px;
    }

    .container {
        width: 100%;
        max-width: 800px;
        background: linear-gradient(180deg, #ffffff 80%, #f0f4ff);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 40px 30px;
        animation: fadeIn 0.6s ease;
        margin: 0 auto;
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e3e8f7;
        font-size: 2em;
        font-weight: 700;
        color: #4a4ae3;
    }

    .message {
        padding: 15px 20px;
        border-radius: 8px;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        animation: slideUp 0.4s ease;
        word-wrap: break-word;
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

    label {
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
        color: #333;
    }

    input[type="text"],
    textarea,
    select {
        width: 100%;
        padding: 12px 15px;
        border-radius: 10px;
        border: 1px solid #ccc;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        margin: 6px 0 15px 0;
    }

    input[type="text"]:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: #4a4ae3;
        box-shadow: 0 0 0 3px rgba(74, 74, 227, 0.1);
        transform: translateY(-1px);
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    #options_fields {
        background: #f8faff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e3e8f7;
        margin: 20px 0;
        animation: slideUp 0.4s ease;
    }

    #options_fields label {
        font-size: 0.9em;
        color: #666;
        margin-bottom: 5px;
    }

    .btn {
        background: linear-gradient(to right, #667eea, #764ba2);
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
        font-size: 14px;
    }

    .btn:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .btn-cancel {
        background: #6c757d;
        margin-left: 10px;
    }

    .btn-cancel:hover {
        background: #5a6268;
    }

    @media (max-width: 768px) {
        body {
            padding: 10px;
        }
        
        .container {
            padding: 20px 15px;
        }
        
        h2 {
            font-size: 1.5em;
        }
        
        .btn {
            display: block;
            width: 100%;
            margin: 10px 0;
        }
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
        <h2>Edit Question</h2>

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

        <form method="post" action="exam_edit_question.php?id=<?= $question_id ?>&exam_id=<?= $exam_id ?>">
            <label>Question Text:</label><br />
            <input type="text" name="question_text" value="<?= htmlspecialchars($question_text) ?>" required /><br />

            <label>Question Type:</label><br />
            <select name="question_type" id="question_type" required onchange="toggleOptionsFields()">
                <option value="identification" <?= $question_type === 'identification' ? 'selected' : '' ?>>Identification</option>
                <option value="multiple_choice" <?= $question_type === 'multiple_choice' ? 'selected' : '' ?>>Multiple Choice</option>
                <option value="true_false" <?= $question_type === 'true_false' ? 'selected' : '' ?>>True/False</option>
            </select><br />

            <div id="options_fields" style="display: <?= $question_type === 'multiple_choice' ? 'block' : 'none' ?>;">
                <label>Options (each option in separate field):</label><br />
                <input type="text" name="option_1" value="<?= htmlspecialchars($options[0] ?? '') ?>" /><br />
                <input type="text" name="option_2" value="<?= htmlspecialchars($options[1] ?? '') ?>" /><br />
                <input type="text" name="option_3" value="<?= htmlspecialchars($options[2] ?? '') ?>" /><br />
                <input type="text" name="option_4" value="<?= htmlspecialchars($options[3] ?? '') ?>" /><br />
            </div>

            <label>Correct Answer:</label><br />
            <input type="text" name="correct_answer" value="<?= htmlspecialchars($correct_answer) ?>" required /><br />

            <button type="submit" class="btn">Save Changes</button>
            <a href="exam_create.php?exam_id=<?= $exam_id ?>" class="btn btn-cancel">Cancel</a>
        </form>
    </div>

<script>
function toggleOptionsFields() {
    const typeSelect = document.getElementById('question_type');
    const optionsDiv = document.getElementById('options_fields');
    if (typeSelect.value === 'multiple_choice') {
        optionsDiv.style.display = 'block';
    } else {
        optionsDiv.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', toggleOptionsFields);
</script>
</body>
</html>