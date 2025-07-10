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

    .form-section {
        margin-bottom: 25px;
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

    .options-section {
        background: #f8faff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e3e8f7;
        margin: 20px 0;
        animation: slideUp 0.4s ease;
    }

    .options-section h4 {
        margin: 0 0 15px 0;
        color: #4a4ae3;
        font-size: 1.1em;
        font-weight: 600;
    }

    .option-input {
        margin-bottom: 12px;
    }

    .option-input label {
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

    .button-group {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e3e8f7;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .question-type-info {
        background: #e8f4fd;
        padding: 12px 15px;
        border-radius: 8px;
        margin-top: 8px;
        font-size: 0.9em;
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }

    @media (max-width: 768px) {
        body {
            padding: 10px;
        }
        
        .container {
            padding: 20px 15px;
        }
        
        .page-header h2 {
            font-size: 1.5em;
        }
        
        .button-group {
            text-align: center;
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
        <header class="page-header">
            <h2>Edit Question</h2>
            <p>Modify question details and options</p>
        </header>

        <!-- Error/Success Messages -->
        <div class="message error" id="errorBox" style="display: none;">
            Question text and correct answer are required.
            <button onclick="document.getElementById('errorBox').style.display='none';" class="close-btn">&times;</button>
        </div>

        <div class="message success" id="messageBox" style="display: none;">
            Question updated successfully.
            <button onclick="document.getElementById('messageBox').style.display='none';" class="close-btn">&times;</button>
        </div>

        <form method="post" action="quiz_edit_question.php?id=123&quiz_id=456">
            <div class="form-group">
                <label for="question_text">Question Text:</label>
                <input type="text" id="question_text" name="question_text" value="What is the capital of France?" required />
            </div>

            <div class="form-group">
                <label for="question_type">Question Type:</label>
                <select name="question_type" id="question_type" required onchange="toggleOptionsFields()">
                    <option value="identification">Identification</option>
                    <option value="multiple_choice" selected>Multiple Choice</option>
                    <option value="true_false">True/False</option>
                </select>
                <div class="question-type-info">
                    <strong>Multiple Choice:</strong> Students select from predefined options you provide.
                </div>
            </div>

            <div id="options_fields" class="options-section">
                <h4>Answer Options</h4>
                <div class="option-input">
                    <label for="option_1">Option 1:</label>
                    <input type="text" id="option_1" name="option_1" value="Paris" placeholder="Enter first option..." />
                </div>
                <div class="option-input">
                    <label for="option_2">Option 2:</label>
                    <input type="text" id="option_2" name="option_2" value="London" placeholder="Enter second option..." />
                </div>
                <div class="option-input">
                    <label for="option_3">Option 3:</label>
                    <input type="text" id="option_3" name="option_3" value="Berlin" placeholder="Enter third option..." />
                </div>
                <div class="option-input">
                    <label for="option_4">Option 4:</label>
                    <input type="text" id="option_4" name="option_4" value="Madrid" placeholder="Enter fourth option..." />
                </div>
            </div>

            <div class="form-group">
                <label for="correct_answer">Correct Answer:</label>
                <input type="text" id="correct_answer" name="correct_answer" value="Paris" required />
                <div class="question-type-info">
                    Enter the exact correct answer. For multiple choice, this should match one of your options exactly.
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn">Save Changes</button>
                <a href="quiz_create.php?quiz_id=456" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

<script>
function toggleOptionsFields() {
    const typeSelect = document.getElementById('question_type');
    const optionsDiv = document.getElementById('options_fields');
    const questionTypeInfo = document.querySelector('.question-type-info');
    
    if (typeSelect.value === 'multiple_choice') {
        optionsDiv.style.display = 'block';
        questionTypeInfo.innerHTML = '<strong>Multiple Choice:</strong> Students select from predefined options you provide.';
    } else if (typeSelect.value === 'true_false') {
        optionsDiv.style.display = 'none';
        questionTypeInfo.innerHTML = '<strong>True/False:</strong> Students answer with either "True" or "False".';
    } else {
        optionsDiv.style.display = 'none';
        questionTypeInfo.innerHTML = '<strong>Identification:</strong> Students type their answer in a text field.';
    }
}

document.addEventListener('DOMContentLoaded', toggleOptionsFields);
</script>
</body>
</html>