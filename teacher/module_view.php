<?php
session_start();
require_once('../includes/db.php');

// Ensure the user is a teacher
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$folder_id = $_GET['folder_id'] ?? null;
if (!$folder_id) {
    echo "Folder not found.";
    exit();
}

$teacher_id = $_SESSION['user_id'];
$files = [];

// Fetch the files in the selected folder
$stmt = $conn->prepare("SELECT * FROM modules WHERE folder_id = ? AND user_id = ?");
$stmt->bind_param("ii", $folder_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}
$stmt->close();

// Fetch folder details (for the heading)
$stmt = $conn->prepare("SELECT folder_name FROM module_folders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $folder_id, $teacher_id);
$stmt->execute();
$stmt->bind_result($folder_name);
$stmt->fetch();
$stmt->close();

// Handle file deletion
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Update the column name to match the actual table column
    $stmt = $conn->prepare("SELECT file_path FROM modules WHERE module_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();

    if ($file) {
        $file_path = $file['file_path'];

        // Delete the file from the server
        if (file_exists($file_path)) {
            unlink($file_path); // Remove the file from the server
        }

        // Delete the file record from the database
        $stmt = $conn->prepare("DELETE FROM modules WHERE module_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $delete_id, $teacher_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to the same page after deletion
        header("Location: {$_SERVER['PHP_SELF']}?folder_id=$folder_id");
        exit();
    } else {
        echo "File not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files in Folder: <?= htmlspecialchars($folder_name) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            font-size: 16px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        td a {
            text-decoration: none;
            color: #007bff;
        }

        td a:hover {
            text-decoration: underline;
        }

        .return-button {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .return-button:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-align: center;
            display: inline-block;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

    <h2>Files in Folder: <?= htmlspecialchars($folder_name) ?></h2>

    <!-- Return Button -->
    <a href="http://localhost/learnit/teacher/dashboard.php?section=module" class="return-button">Return to Dashboard</a>

    <!-- Display uploaded files -->
    <table>
        <thead>
            <tr>
                <th>File Name</th>
                <th>Type</th>
                <th>Uploaded At</th>
                <th>Download</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file): ?>
                <tr>
                    <td><?= htmlspecialchars($file['module_name']) ?></td>
                    <td><?= strtoupper($file['module_type']) ?></td>
                    <td><?= $file['uploaded_at'] ?></td>
                    <td><a href="<?= $file['file_path'] ?>" target="_blank">Download</a></td>
                    <td>
                        <!-- Delete Button inside a form -->
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this file?')">
                            <input type="hidden" name="delete_id" value="<?= $file['module_id'] ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
