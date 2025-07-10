<?php
session_start();
require_once('../includes/db.php');

// Ensure the user is a student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Get quiz ID from URL
$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) {
    echo "Quiz not found.";
    exit();
}

// Fetch student details
$student_id = $_SESSION['user_id'];
$student_query = "SELECT first_name, course, section FROM users WHERE id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($first_name, $course, $section);
$stmt->fetch();
$stmt->close();

// Fetch the quiz details
$quiz_query = "SELECT title, created_at FROM quizzes WHERE id = ?";
$quiz_stmt = $conn->prepare($quiz_query);
$quiz_stmt->bind_param("i", $quiz_id);
$quiz_stmt->execute();
$quiz_stmt->bind_result($quiz_title, $created_at);
$quiz_stmt->fetch();
$quiz_stmt->close();

// Fetch questions for the selected quiz
$questions_query = "
    SELECT qq.id, qq.question_text, qq.question_type, qq.options, qq.correct_answer, qq.question_number
    FROM quiz_questions qq
    WHERE qq.quiz_id = ?
    ORDER BY qq.question_number
";
$questions_stmt = $conn->prepare($questions_query);
$questions_stmt->bind_param("i", $quiz_id);
$questions_stmt->execute();
$questions_result = $questions_stmt->get_result();
$questions = $questions_result->fetch_all(MYSQLI_ASSOC);
$questions_stmt->close();

$total_questions = count($questions);

// Handle form submission and save answers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers']; // This will contain the student's answers
    $score = 0;
    $correct_answers = [];
    $wrong_answers = [];

    // Store answers in the database and calculate the score
    foreach ($answers as $question_id => $answer) {
        // Fetch the correct answer for the question
        $correct_answer_query = "
            SELECT correct_answer
            FROM quiz_questions
            WHERE id = ?
        ";
        $correct_answer_stmt = $conn->prepare($correct_answer_query);
        $correct_answer_stmt->bind_param("i", $question_id);
        $correct_answer_stmt->execute();
        $correct_answer_stmt->bind_result($correct_answer);
        $correct_answer_stmt->fetch();
        $correct_answer_stmt->close();

        // Store the student's answer along with the correct answer in the student_quiz_answers table
        $insert_answer_query = "
            INSERT INTO student_quiz_answers (student_id, quiz_id, question_id, answer, correct_answer)
            VALUES (?, ?, ?, ?, ?)
        ";
        $insert_stmt = $conn->prepare($insert_answer_query);
        $insert_stmt->bind_param("iiiss", $student_id, $quiz_id, $question_id, $answer, $correct_answer);
        $insert_stmt->execute();
        $insert_stmt->close();

        // Calculate score (1 point per correct answer)
        if ($answer === $correct_answer) {
            $score++;
            $correct_answers[] = $question_id;
        } else {
            $wrong_answers[] = $question_id;
        }
    }

    // Calculate percentage and pass/fail status
    $percentage = ($score / $total_questions) * 100;
    $passed = $score >= ($total_questions / 2);

    // Return JSON response for AJAX
    echo json_encode([
        'score' => $score,
        'total' => $total_questions,
        'percentage' => round($percentage, 2),
        'passed' => $passed,
        'correct_answers' => $correct_answers,
        'wrong_answers' => $wrong_answers
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Answer Quiz | LearnIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease-out;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }

        .modal-body {
            padding: 30px;
        }

        .quiz-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .info-icon {
            color: #667eea;
            font-size: 18px;
            width: 20px;
        }

        .start-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .start-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        /* Quiz Interface Styles */
        .quiz-container {
            display: none;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .quiz-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .quiz-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .quiz-title {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        .quiz-controls {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .progress-info {
            display: flex;
            gap: 15px;
            color: white;
            font-size: 14px;
        }

        .progress-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quit-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quit-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .quiz-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .question-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: none;
            animation: questionSlideIn 0.3s ease-out;
        }

        .question-card.active {
            display: block;
        }

        .question-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }

        .question-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .question-text {
            font-size: 18px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .option-label {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .option-label:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .option-label.selected {
            background: rgba(102, 126, 234, 0.1);
            border-color: #667eea;
        }

        .option-input {
            margin-right: 15px;
            transform: scale(1.2);
        }

        .text-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .text-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .question-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .nav-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .nav-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .submit-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .submit-btn:hover {
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        /* Question Overview */
        .question-overview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
        }

        .question-dot {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .question-dot.answered {
            background: #28a745;
        }

        .question-dot.current {
            background: #007bff;
            transform: scale(1.2);
        }

        /* Result Modal */
        .result-modal .modal-content {
            text-align: center;
        }

        .result-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .result-icon.passed {
            color: #28a745;
        }

        .result-icon.failed {
            color: #dc3545;
        }

        .result-score {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .result-percentage {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }

        .result-status {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .result-status.passed {
            color: #28a745;
        }

        .result-status.failed {
            color: #dc3545;
        }

        .back-btn {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes questionSlideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .quiz-nav {
                flex-direction: column;
                gap: 15px;
            }

            .quiz-title {
                font-size: 20px;
            }

            .progress-info {
                flex-wrap: wrap;
                justify-content: center;
            }

            .question-card {
                padding: 20px;
            }

            .question-navigation {
                flex-direction: column;
                gap: 15px;
            }

            .nav-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Initial Modal -->
    <div id="quizModal" class="modal" style="display: block;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-question-circle"></i> Quiz Details</h2>
            </div>
            <div class="modal-body">
                <div class="quiz-info">
                    <div class="info-item">
                        <i class="fas fa-book info-icon"></i>
                        <div>
                            <strong>Quiz Title:</strong><br>
                            <?= htmlspecialchars($quiz_title) ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-list-ol info-icon"></i>
                        <div>
                            <strong>Total Questions:</strong><br>
                            <?= $total_questions ?> items
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar-alt info-icon"></i>
                        <div>
                            <strong>Date Created:</strong><br>
                            <?= date('F j, Y', strtotime($created_at)) ?>
                        </div>
                    </div>
                </div>
                <button class="start-btn" onclick="startQuiz()">
                    <i class="fas fa-play"></i> Start Quiz
                </button>
            </div>
        </div>
    </div>

    <!-- Quiz Interface -->
    <div id="quizContainer" class="quiz-container">
        <div class="quiz-header">
            <div class="quiz-nav">
                <h1 class="quiz-title"><?= htmlspecialchars($quiz_title) ?></h1>
                <div class="quiz-controls">
                    <div class="progress-info">
                        <div class="progress-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Answered: <span id="answeredCount">0</span></span>
                        </div>
                        <div class="progress-item">
                            <i class="fas fa-circle"></i>
                            <span>Remaining: <span id="remainingCount"><?= $total_questions ?></span></span>
                        </div>
                    </div>
                    <button class="quit-btn" onclick="quitQuiz()">
                        <i class="fas fa-times"></i> Quit
                    </button>
                </div>
            </div>
        </div>

        <div class="quiz-content">
            <div class="question-overview" id="questionOverview">
                <!-- Question dots will be generated by JavaScript -->
            </div>

            <form id="quizForm">
                <?php if (!empty($questions)): ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question-card <?= $index === 0 ? 'active' : '' ?>" data-question-id="<?= $question['id'] ?>">
                            <div class="question-header">
                                <div class="question-number">
                                    Question <?= $question['question_number'] ?> of <?= $total_questions ?>
                                </div>
                            </div>
                            
                            <div class="question-text">
                                <?= htmlspecialchars($question['question_text']) ?>
                            </div>

                            <?php if ($question['question_type'] === 'multiple_choice'): ?>
                                <div class="options">
                                    <?php 
                                        $options = json_decode($question['options'], true);
                                        foreach ($options as $key => $option): ?>
                                            <label class="option-label">
                                                <input type="radio" name="answers[<?= $question['id'] ?>]" value="<?= $key ?>" class="option-input">
                                                <?= htmlspecialchars($option) ?>
                                            </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif ($question['question_type'] === 'true_false'): ?>
                                <div class="options">
                                    <label class="option-label">
                                        <input type="radio" name="answers[<?= $question['id'] ?>]" value="True" class="option-input">
                                        True
                                    </label>
                                    <label class="option-label">
                                        <input type="radio" name="answers[<?= $question['id'] ?>]" value="False" class="option-input">
                                        False
                                    </label>
                                </div>
                            <?php else: ?>
                                <input type="text" name="answers[<?= $question['id'] ?>]" class="text-input" placeholder="Type your answer here...">
                            <?php endif; ?>

                            <div class="question-navigation">
                                <button type="button" class="nav-btn" onclick="previousQuestion()" <?= $index === 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-chevron-left"></i> Previous
                                </button>
                                
                                <?php if ($index === count($questions) - 1): ?>
                                    <button type="button" class="nav-btn submit-btn" onclick="submitQuiz()">
                                        <i class="fas fa-check"></i> Submit Quiz
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="nav-btn" onclick="nextQuestion()">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="question-card active">
                        <p>No questions available for this quiz.</p>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Result Modal -->
    <div id="resultModal" class="modal result-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-trophy"></i> Quiz Results</h2>
            </div>
            <div class="modal-body">
                <div id="resultIcon" class="result-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div id="resultScore" class="result-score">0/0</div>
                <div id="resultPercentage" class="result-percentage">0%</div>
                <div id="resultStatus" class="result-status">Status</div>
                <button class="back-btn" onclick="goToDashboard()">
                    <i class="fas fa-home"></i> Back to Dashboard
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentQuestionIndex = 0;
        const totalQuestions = <?= $total_questions ?>;
        const questions = document.querySelectorAll('.question-card');
        
        // Initialize question overview
        function initializeQuestionOverview() {
            const overview = document.getElementById('questionOverview');
            for (let i = 0; i < totalQuestions; i++) {
                const dot = document.createElement('div');
                dot.className = 'question-dot';
                dot.textContent = i + 1;
                dot.onclick = () => goToQuestion(i);
                if (i === 0) dot.classList.add('current');
                overview.appendChild(dot);
            }
        }

        // Start quiz
        function startQuiz() {
            document.getElementById('quizModal').style.display = 'none';
            document.getElementById('quizContainer').style.display = 'block';
            initializeQuestionOverview();
            updateProgress();
        }

        // Navigation functions
        function nextQuestion() {
            if (currentQuestionIndex < totalQuestions - 1) {
                questions[currentQuestionIndex].classList.remove('active');
                currentQuestionIndex++;
                questions[currentQuestionIndex].classList.add('active');
                updateQuestionOverview();
                updateProgress();
            }
        }

        function previousQuestion() {
            if (currentQuestionIndex > 0) {
                questions[currentQuestionIndex].classList.remove('active');
                currentQuestionIndex--;
                questions[currentQuestionIndex].classList.add('active');
                updateQuestionOverview();
                updateProgress();
            }
        }

        function goToQuestion(index) {
            questions[currentQuestionIndex].classList.remove('active');
            currentQuestionIndex = index;
            questions[currentQuestionIndex].classList.add('active');
            updateQuestionOverview();
            updateProgress();
        }

        // Update question overview
        function updateQuestionOverview() {
            const dots = document.querySelectorAll('.question-dot');
            dots.forEach((dot, index) => {
                dot.classList.remove('current');
                if (index === currentQuestionIndex) {
                    dot.classList.add('current');
                }
            });
        }

        // Update progress
        function updateProgress() {
            const form = document.getElementById('quizForm');
            const formData = new FormData(form);
            let answeredCount = 0;
            
            for (let pair of formData.entries()) {
                if (pair[1] && pair[1].trim() !== '') {
                    answeredCount++;
                }
            }
            
            document.getElementById('answeredCount').textContent = answeredCount;
            document.getElementById('remainingCount').textContent = totalQuestions - answeredCount;
            
            // Update question overview dots
            const dots = document.querySelectorAll('.question-dot');
            dots.forEach((dot, index) => {
                const questionId = questions[index].dataset.questionId;
                const isAnswered = formData.has(`answers[${questionId}]`) && formData.get(`answers[${questionId}]`) !== '';
                if (isAnswered) {
                    dot.classList.add('answered');
                } else {
                    dot.classList.remove('answered');
                }
            });
        }

        // Add event listeners for form changes
        document.addEventListener('change', updateProgress);
        document.addEventListener('input', updateProgress);

        // Option label selection
        document.querySelectorAll('.option-label').forEach(label => {
            label.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                const allLabels = this.closest('.options').querySelectorAll('.option-label');
                allLabels.forEach(l => l.classList.remove('selected'));
                if (radio) {
                    radio.checked = true;
                    this.classList.add('selected');
                }
            });
        });

        // Submit quiz
        function submitQuiz() {
            if (confirm('Are you sure you want to submit your quiz? This action cannot be undone.')) {
                const form = document.getElementById('quizForm');
                const formData = new FormData(form);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    showResults(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the quiz.');
                });
            }
        }

        // Show results
        function showResults(data) {
            const resultModal = document.getElementById('resultModal');
            const resultIcon = document.getElementById('resultIcon');
            const resultScore = document.getElementById('resultScore');
            const resultPercentage = document.getElementById('resultPercentage');
            const resultStatus = document.getElementById('resultStatus');
            
            resultScore.textContent = `${data.score}/${data.total}`;
            resultPercentage.textContent = `${data.percentage}%`;
            
            if (data.passed) {
                resultIcon.innerHTML = '<i class="fas fa-trophy"></i>';
                resultIcon.className = 'result-icon passed';
                resultStatus.textContent = 'PASSED';
                resultStatus.className = 'result-status passed';
            } else {
                resultIcon.innerHTML = '<i class="fas fa-times-circle"></i>';
                resultIcon.className = 'result-icon failed';
                resultStatus.textContent = 'FAILED';
                resultStatus.className = 'result-status failed';
            }
            
            resultModal.style.display = 'block';
        }

        // Quit quiz
        function quitQuiz() {
            if (confirm('Are you sure you want to quit the quiz? Your progress will be lost.')) {
                window.location.href = 'dashboard.php';
            }
        }

        // Go to dashboard
        function goToDashboard() {
            window.location.href = 'dashboard.php';
        }
    </script>
</body>
</html>