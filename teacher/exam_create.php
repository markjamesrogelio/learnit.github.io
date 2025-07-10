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
$exam_id = intval($_GET['exam_id'] ?? 0);

// Fetch all available courses
$courses_query = "SELECT id, name FROM courses";
$courses_result = $conn->query($courses_query);

// Handle exam creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title']) && empty($exam_id)) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'Draft';
        $course_id = $_POST['course_id']; 
        $year_level = $_POST['year_level']; 
        $section_names = $_POST['section_id'] ?? []; 
        if ($title === '') {
            $error = 'Title is required.';
        } else {
            $course_query = $conn->prepare("SELECT name FROM courses WHERE id = ?");
            $course_query->bind_param("i", $course_id);
            $course_query->execute();
            $course_result = $course_query->get_result();
            $course_name = "";
            if ($course_row = $course_result->fetch_assoc()) {
                $course_name = $course_row['name'];
            }

            $stmt = $conn->prepare("INSERT INTO exams (teacher_id, title, description, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $teacher_id, $title, $description, $status);
            if ($stmt->execute()) {
                $exam_id = $stmt->insert_id;

                foreach ($section_names as $section_name) {
                    $stmt = $conn->prepare("INSERT INTO course_year_section_exam (exam_id, course, year_level, section_name) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $exam_id, $course_name, $year_level, $section_name);
                    $stmt->execute();
                }
                
                $message = 'Exam created successfully. You can now add questions.';
            } else {
                $error = 'Failed to create exam.';
            }
            $stmt->close();
        }
    }

    if (isset($_POST['question_text']) && $exam_id) {
        $question_text = trim($_POST['question_text']);
        $question_type = $_POST['question_type'] ?? 'identification';
        $correct_answer = trim($_POST['correct_answer'] ?? '');

        if ($question_text === '' || $correct_answer === '') {
            $error = 'Question text and correct answer are required.';
        } else {
            $opts_json = null;
            if ($question_type === 'multiple_choice') {
                $opts_array = [];
                for ($i = 1; $i <= 4; $i++) {
                    $opt_key = "option_$i";
                    if (!empty($_POST[$opt_key])) {
                        $opts_array[] = trim($_POST[$opt_key]);
                    }
                }
                $opts_json = json_encode($opts_array);
            }

            $stmt = $conn->prepare("SELECT COUNT(*) FROM exam_questions WHERE exam_id = ?");
            $stmt->bind_param("i", $exam_id);
            $stmt->execute();
            $stmt->bind_result($question_number);
            $stmt->fetch();
            $stmt->close();
            $question_number++;

            if (isset($_POST['question_id'])) {
                $question_id = $_POST['question_id'];
                $stmt = $conn->prepare("UPDATE exam_questions SET question_text = ?, question_type = ?, correct_answer = ?, options = ?, question_number = ? WHERE id = ? AND exam_id = ?");
                $stmt->bind_param("ssssiii", $question_text, $question_type, $correct_answer, $opts_json, $question_number, $question_id, $exam_id);
                $stmt->execute();
                $message = 'Question updated successfully.';
            } else {
                $stmt = $conn->prepare("INSERT INTO exam_questions (exam_id, question_text, question_type, correct_answer, options, question_number) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssi", $exam_id, $question_text, $question_type, $correct_answer, $opts_json, $question_number);
                $stmt->execute();
                $message = 'Question added successfully.';
            }
            $stmt->close();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete_question' && isset($_GET['id']) && isset($_GET['exam_id'])) {
    $question_id = intval($_GET['id']);
    $exam_id = intval($_GET['exam_id']);
    $stmt = $conn->prepare("DELETE FROM exam_questions WHERE id = ? AND exam_id = ?");
    $stmt->bind_param("ii", $question_id, $exam_id);
    $stmt->execute();
    $stmt->close();
    header("Location: exam_create.php?exam_id=$exam_id");
    exit();
}

$questions = [];
$questions_count = 0;
if ($exam_id) {
    $stmt = $conn->prepare("SELECT id, question_text, question_type, correct_answer, options, question_number FROM exam_questions WHERE exam_id = ? ORDER BY question_number");
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $questions[] = $row;
    }
    $questions_count = count($questions);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Create Exam | LearnIT</title>
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

    .questions-list {
        padding-left: 20px;
        margin-top: 15px;
    }

    .questions-list li {
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .questions-list a {
        margin-left: 10px;
        font-size: 0.9em;
        text-decoration: none;
    }

    .questions-list a:hover {
        text-decoration: underline;
    }

    #options_fields input {
        margin-top: 6px;
    }

    #section_checkboxes {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    #section_checkboxes label {
        background: #f0f4ff;
        padding: 6px 12px;
        border-radius: 20px;
        border: 1px solid #ccc;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
    }

    #section_checkboxes input[type="checkbox"] {
        margin-right: 5px;
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
        <h2>Create Exam</h2>
        <p>Design questions, assign sections, and manage assessments efficiently</p>
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

    <?php if (!$exam_id): ?>
        <form method="post" action="exam_create.php">
            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Description:</label>
            <textarea name="description" rows="5"></textarea>

            <label>Status:</label>
            <select name="status">
                <option value="Draft">Draft</option>
                <option value="Published">Published</option>
            </select>

            <label>Course:</label>
            <select name="course_id" id="course_id" required onchange="loadSections()">
                <?php while ($course = $courses_result->fetch_assoc()) { ?>
                    <option value="<?= $course['id']; ?>"><?= $course['name']; ?></option>
                <?php } ?>
            </select>

            <label>Year Level:</label>
            <select name="year_level" id="year_level" required onchange="loadSections()">
                <option value="1st">1st Year</option>
                <option value="2nd">2nd Year</option>
                <option value="3rd">3rd Year</option>
                <option value="4th">4th Year</option>
            </select>

            <label>Section:</label>
            <div id="section_checkboxes"></div>

            <button type="submit" class="btn">Create Exam</button>
            <a href="dashboard.php?section=exam" class="btn btn-cancel">Cancel</a>
        </form>
    <?php endif; ?>

    <?php if ($exam_id): ?>
        <h3>Add Question to Exam</h3>
        <form method="post" action="exam_create.php?exam_id=<?= $exam_id ?>">
            <input type="hidden" name="exam_id" value="<?= $exam_id ?>">

            <label>Question Text:</label>
            <input type="text" name="question_text" required>

            <label>Question Type:</label>
            <select name="question_type" id="question_type" required onchange="toggleOptionsFields()">
                <option value="identification">Identification</option>
                <option value="multiple_choice">Multiple Choice</option>
                <option value="true_false">True/False</option>
            </select>

            <div id="options_fields" style="display:none;">
                <label>Options (each in separate field):</label>
                <input type="text" name="option_1" placeholder="Option 1">
                <input type="text" name="option_2" placeholder="Option 2">
                <input type="text" name="option_3" placeholder="Option 3">
                <input type="text" name="option_4" placeholder="Option 4">
            </div>

            <label>Correct Answer:</label>
            <input type="text" name="correct_answer" required>

            <button type="submit" name="add_question" class="btn">Add Question</button>
            <a href="dashboard.php?section=exam" class="btn btn-cancel">Finish</a>
        </form>

        <h3>Questions Added (<?= $questions_count ?>)</h3>
        <?php if ($questions_count === 0): ?>
            <p>No questions added yet.</p>
        <?php else: ?>
            <ul class="questions-list">
                <?php foreach ($questions as $q): ?>
                    <li>
                        <?= $q['question_number'] . '. ' . htmlspecialchars($q['question_text']) ?> 
                        (<?= ucfirst(str_replace('_', ' ', $q['question_type'])) ?>)
                        <a href="exam_edit_question.php?id=<?= $q['id'] ?>&exam_id=<?= $exam_id ?>" style="color:#007bff;">Edit</a> |
                        <a href="exam_create.php?action=delete_question&id=<?= $q['id'] ?>&exam_id=<?= $exam_id ?>" onclick="return confirm('Delete this question?');" style="color:#dc3545;">Delete</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function loadSections() {
    const year_level = document.getElementById('year_level').value;
    const sectionCheckboxesDiv = document.getElementById('section_checkboxes');
    sectionCheckboxesDiv.innerHTML = '';

    let sections = [];
    switch(year_level) {
        case '1st': sections = ['1A', '1B', '1C', '1D']; break;
        case '2nd': sections = ['2A', '2B', '2C', '2D']; break;
        case '3rd': sections = ['3A', '3B', '3C', '3D']; break;
        case '4th': sections = ['4A', '4B', '4C', '4D']; break;
    }

    sections.forEach(section => {
        const label = document.createElement('label');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'section_id[]';
        checkbox.value = section;
        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(section));
        sectionCheckboxesDiv.appendChild(label);
    });
}
document.addEventListener('DOMContentLoaded', loadSections);

function toggleOptionsFields() {
    const typeSelect = document.getElementById('question_type');
    const optionsDiv = document.getElementById('options_fields');
    optionsDiv.style.display = (typeSelect.value === 'multiple_choice') ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleOptionsFields);
</script>

</body>
</html>
