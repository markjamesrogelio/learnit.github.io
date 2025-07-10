<?php
require_once('../includes/db.php');
session_start();

$teacher_id = $_SESSION['user_id'] ?? null;
if (!$teacher_id) {
    header('Location: ../auth/login.php');
    exit();
}

$message = '';
$error = '';

if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'created': $message = '✅ Exam created successfully.'; break;
        case 'updated': $message = '✏️ Exam updated successfully.'; break;
        case 'deleted': $message = '🗑️ Exam deleted successfully.'; break;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $exam_id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM exams WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param("ii", $exam_id, $teacher_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php?section=exam&msg=deleted");
    exit();
}

$search = $_GET['search'] ?? '';
$search_param = "%$search%";

$stmt = $conn->prepare("SELECT id, title, description, created_at, status FROM exams WHERE teacher_id = ? AND (title LIKE ? OR description LIKE ?) ORDER BY created_at DESC");
$stmt->bind_param("iss", $teacher_id, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #7f7fd5, #86a8e7, #91eae4);
            color: #333;
            padding: 40px 20px;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
            color: #4a4ae3;
        }
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: bold;
        }
        header p {
            font-size: 1.1em;
            color: #444;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            max-width: 800px;
            margin: 0 auto 30px;
            flex-wrap: wrap;
        }

        .search-bar {
            display: flex;
            flex: 1;
            gap: 10px;
            min-width: 300px;
        }

        .search-bar input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .search-bar button {
            background: #4a4ae3;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .search-bar button:hover {
            background: #3737d1;
        }

        .create-btn {
            padding: 10px 16px;
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            color: white;
            font-size: 0.9em;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
            white-space: nowrap;
        }

        .create-btn:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .quiz-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .quiz-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.7s ease-in;
            transition: transform 0.3s;
        }

        .quiz-card:hover {
            transform: translateY(-5px);
        }

        .quiz-card h3 {
            margin-bottom: 10px;
            font-size: 1.3em;
            font-weight: 600;
        }

        .quiz-card .description {
            color: #555;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .quiz-card .meta,
        .quiz-card .status {
            font-size: 13px;
            color: #666;
            margin-bottom: 6px;
        }

        .quiz-card .actions {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 0.9em;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
        }

        .btn.edit {
            background-color: #007bff;
            color: white;
        }

        .btn.delete {
            background-color: #dc3545;
            color: white;
        }

        .btn:hover {
            filter: brightness(90%);
        }

        .alert {
            position: relative;
            max-width: 600px;
            margin: 0 auto 20px;
            padding: 12px 40px 12px 16px;
            border-radius: 8px;
            font-weight: 600;
            animation: fadeSlideIn 0.4s ease;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
        }

        .close-btn {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            color: inherit;
        }

        .empty-state {
            text-align: center;
            padding: 20px;
            background: #fff5f5;
            border-radius: 10px;
            font-style: italic;
            color: #555;
            max-width: 500px;
            margin: 20px auto;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<header>
    <h1>Exam Manager</h1>
    <p>Manage your exams efficiently and intuitively</p>
</header>

<?php if ($message): ?>
    <div class="alert success" id="messageBox">
        <?= htmlspecialchars($message) ?>
        <button onclick="fadeOutEffect(this.parentElement)" class="close-btn">&times;</button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert error" id="errorBox">
        <?= htmlspecialchars($error) ?>
        <button onclick="fadeOutEffect(this.parentElement)" class="close-btn">&times;</button>
    </div>
<?php endif; ?>

<div class="top-bar">
    <form method="GET" class="search-bar">
        <input type="hidden" name="section" value="exam">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search exams...">
        <button type="submit">🔍 Search</button>
    </form>
    <a href="dashboard.php?section=exam&action=create" class="create-btn">➕ Create Exam</a>
</div>

<?php if ($result->num_rows === 0): ?>
    <p class="empty-state">📭 No exams found.</p>
<?php else: ?>
    <div class="quiz-grid">
        <?php while ($exam = $result->fetch_assoc()): ?>
            <div class="quiz-card">
                <h3><?= htmlspecialchars($exam['title']) ?></h3>
                <p class="description">📝 <?= htmlspecialchars($exam['description']) ?></p>
                <p class="meta">📅 <?= date("Y-m-d", strtotime($exam['created_at'])) ?></p>
                <p class="status">📌 Status: <strong><?= htmlspecialchars($exam['status']) ?></strong></p>
                <div class="actions">
                    <a href="dashboard.php?section=exam&action=edit&id=<?= $exam['id'] ?>" class="btn edit">✏️</a>
                    <a href="dashboard.php?section=exam&action=delete&id=<?= $exam['id'] ?>" class="btn delete" onclick="return confirm('Delete this exam?')">🗑️</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<script>
function fadeOutEffect(el) {
    let opacity = 1;
    const timer = setInterval(() => {
        if (opacity <= 0.1) {
            clearInterval(timer);
            el.style.display = 'none';
        }
        el.style.opacity = opacity;
        opacity -= 0.1;
    }, 30);
}
</script>

</body>
</html>
