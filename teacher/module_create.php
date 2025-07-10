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
$quiz_id = intval($_GET['quiz_id'] ?? 0);

// Fetch all available courses
$courses_query = "SELECT id, name FROM courses";
$courses_result = $conn->query($courses_query);

// Handle quiz creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title']) && empty($quiz_id)) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'Draft';
        $course_id = $_POST['course_id'];  // Get selected course ID
        $year_level = $_POST['year_level'];  // Get selected year level
        $section_names = $_POST['section_id'] ?? [];  // Get selected section names as an array

        if ($title === '') {
            $error = 'Title is required.';
        } else {
            // Fetch the course name from the selected course ID
            $course_query = $conn->prepare("SELECT name FROM courses WHERE id = ?");
            $course_query->bind_param("i", $course_id);
            $course_query->execute();
            $course_result = $course_query->get_result();
            $course_name = "";
            if ($course_row = $course_result->fetch_assoc()) {
                $course_name = $course_row['name'];
            }

            // Insert quiz into quizzes table
            $stmt = $conn->prepare("INSERT INTO quizzes (teacher_id, title, description, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $teacher_id, $title, $description, $status);
            if ($stmt->execute()) {
                $quiz_id = $stmt->insert_id;

                // Insert into course_year_section_quiz table for each selected section
                foreach ($section_names as $section_name) {
                    $stmt = $conn->prepare("INSERT INTO course_year_section_quiz (quiz_id, course, year_level, section_name) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $quiz_id, $course_name, $year_level, $section_name);  // Save course name into 'course' column
                    $stmt->execute();
                }
                
                $message = 'Quiz created successfully. You can now add questions.';
            } else {
                $error = 'Failed to create quiz.';
            }
            $stmt->close();
        }
    }

    // Handle adding or updating question
    if (isset($_POST['question_text']) && $quiz_id) {
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

            // Get the current question number in this quiz
            $stmt = $conn->prepare("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = ?");
            $stmt->bind_param("i", $quiz_id);
            $stmt->execute();
            $stmt->bind_result($question_number);
            $stmt->fetch();
            $stmt->close();
            $question_number++; // Increment question number

            if (isset($_POST['question_id'])) {
                // Update existing question
                $question_id = $_POST['question_id'];
                $stmt = $conn->prepare("UPDATE quiz_questions SET question_text = ?, question_type = ?, correct_answer = ?, options = ?, question_number = ? WHERE id = ? AND quiz_id = ?");
                $stmt->bind_param("ssssiii", $question_text, $question_type, $correct_answer, $opts_json, $question_number, $question_id, $quiz_id);
                if ($stmt->execute()) {
                    $message = 'Question updated successfully.';
                } else {
                    $error = 'Failed to update question.';
                }
            } else {
                // Insert new question
                $stmt = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question_text, question_type, correct_answer, options, question_number) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssi", $quiz_id, $question_text, $question_type, $correct_answer, $opts_json, $question_number);
                if ($stmt->execute()) {
                    $message = 'Question added successfully.';
                } else {
                    $error = 'Failed to add question.';
                }
            }
            $stmt->close();
        }
    }
}

// Handle question deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete_question' && isset($_GET['id']) && isset($_GET['quiz_id'])) {
    $question_id = intval($_GET['id']);
    $quiz_id = intval($_GET['quiz_id']);
    $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE id = ? AND quiz_id = ?");
    $stmt->bind_param("ii", $question_id, $quiz_id);
    $stmt->execute();
    $stmt->close();
    header("Location: quiz_create.php?quiz_id=$quiz_id");
    exit();
}

// Fetch questions for this quiz if exists
$questions = [];
$questions_count = 0;
if ($quiz_id) {
    $stmt = $conn->prepare("SELECT id, question_text, question_type, correct_answer, options, question_number FROM quiz_questions WHERE quiz_id = ? ORDER BY question_number");
    $stmt->bind_param("i", $quiz_id);
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
    <title>Create Quiz | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css" />
    <style>
        .message {
            position: relative;
            padding: 15px 40px 15px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 600;
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
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            color: inherit;
            line-height: 1;
        }
        .btn {
            padding: 8px 14px;
            background: #0e0e6e;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-cancel {
            background: #6c757d;
            margin-left: 10px;
        }
        label {
            font-weight: bold;
        }
        input[type=text], textarea, select {
            width: 100%;
            padding: 8px;
            margin: 6px 0 15px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        .options-container {
            display: none;
        }
        ul.questions-list {
            padding-left: 20px;
        }
        ul.questions-list li {
            margin-bottom: 8px;
        }
        ul.questions-list a {
            margin-left: 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container" style="padding:20px;">
        <h2>Create New Quiz</h2>

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

        <?php if (!$quiz_id): ?>
            <!-- Quiz Creation Form -->
            <form method="post" action="quiz_create.php">
                <label>Title:</label><br />
                <input type="text" name="title" required /><br /> 

                <label>Description:</label><br />
                <textarea name="description" rows="5"></textarea><br /> 

                <label>Status:</label><br />
                <select name="status">
                    <option value="Draft">Draft</option>
                    <option value="Published">Published</option>
                </select><br /> 

                <!-- Course Dropdown -->
                <label>Course:</label><br />
                <select name="course_id" id="course_id" required onchange="loadSections()">
                    <?php while ($course = $courses_result->fetch_assoc()) { ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo $course['name']; ?></option>
                    <?php } ?>
                </select><br />

                <!-- Year Level Dropdown -->
                <label>Year Level:</label><br />
                <select name="year_level" id="year_level" required onchange="loadSections()">
                    <option value="1st">1st Year</option>
                    <option value="2nd">2nd Year</option>
                    <option value="3rd">3rd Year</option>
                    <option value="4th">4th Year</option>
                </select><br />

                <!-- Section Checkboxes -->
                <label>Section:</label><br />
                <div id="section_checkboxes"></div><br />

                <button type="submit" class="btn">Create Quiz</button>
                <a href="dashboard.php?section=quiz" class="btn btn-cancel">Cancel</a>
            </form>
        <?php endif; ?>

        <?php if ($quiz_id): ?>
            <!-- Add Question Form -->
            <h3>Add Question to Quiz</h3>
            <form method="post" action="quiz_create.php?quiz_id=<?= htmlspecialchars($quiz_id) ?>">
                <input type="hidden" name="quiz_id" value="<?= htmlspecialchars($quiz_id) ?>" />
                <label>Question Text:</label><br />
                <input type="text" name="question_text" required /><br />

                <label>Question Type:</label><br />
                <select name="question_type" id="question_type" required onchange="toggleOptionsFields()">
                    <option value="identification">Identification</option>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                </select><br />

                <div id="options_fields" class="options-container">
                    <label>Options (each option in separate field):</label><br />
                    <input type="text" name="option_1" placeholder="Option 1" /><br />
                    <input type="text" name="option_2" placeholder="Option 2" /><br />
                    <input type="text" name="option_3" placeholder="Option 3" /><br />
                    <input type="text" name="option_4" placeholder="Option 4" /><br />
                </div>

                <label>Correct Answer:</label><br />
                <input type="text" name="correct_answer" required /><br />

                <button type="submit" name="add_question" class="btn">Add Question</button>
                <a href="dashboard.php?section=quiz" class="btn btn-cancel" style="margin-left: 10px;">Finish</a>
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
                            <a href="quiz_edit_question.php?id=<?= $q['id'] ?>&quiz_id=<?= $quiz_id ?>" style="color:#007bff; text-decoration:none;">Edit</a> |
                            <a href="quiz_create.php?action=delete_question&id=<?= $q['id'] ?>&quiz_id=<?= $quiz_id ?>" onclick="return confirm('Delete this question?');" style="color:#dc3545; text-decoration:none;">Delete</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>
    </div>

<script>
// Function to load section checkboxes based on year level
function loadSections() {
    const year_level = document.getElementById('year_level').value;
    const sectionCheckboxesDiv = document.getElementById('section_checkboxes');

    // Clear previous section checkboxes
    sectionCheckboxesDiv.innerHTML = '';

    let sections = [];
    switch(year_level) {
        case '1st':
            sections = ['1A', '1B', '1C', '1D'];
            break;
        case '2nd':
            sections = ['2A', '2B', '2C', '2D'];
            break;
        case '3rd':
            sections = ['3A', '3B', '3C', '3D'];
            break;
        case '4th':
            sections = ['4A', '4B', '4C', '4D'];
            break;
    }

    // Add checkboxes to section container
    sections.forEach(section => {
        const label = document.createElement('label');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'section_id[]';  // Ensure this sends an array
        checkbox.value = section;  // Use section names
        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(section));
        sectionCheckboxesDiv.appendChild(label);
        sectionCheckboxesDiv.appendChild(document.createElement('br'));
    });
}

// Toggle options fields based on question type
function toggleOptionsFields() {
    const questionType = document.getElementById('question_type').value;
    const optionsFields = document.getElementById('options_fields');

    if (questionType === 'multiple_choice') {
        optionsFields.style.display = 'block';  // Show the options fields
    } else {
        optionsFields.style.display = 'none';  // Hide the options fields
    }
}

document.addEventListener('DOMContentLoaded', loadSections);
</script>
</body>
</html>
