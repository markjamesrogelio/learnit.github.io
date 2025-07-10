<?php
session_start();
require_once('../includes/db.php');
require_once('../includes/auth.php');

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$section = $_GET['section'] ?? '';
$parent_id = $_GET['parent_id'] ?? 0;


$section = $_GET['section'] ?? 'home';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }

        .modal-content {
            background: #fff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            position: relative;
        }

        .modal-content input,
        .modal-content select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }

        .modal-content button {
            background: #3344cc;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="logo">Learn<span>IT</span></div>
            <ul class="nav-section">
                <li class="section-title">ADMIN</li>
                <li><a href="dashboard.php?section=home"><i class="fas fa-home"></i> Home</a></li>

                <li class="section-title">ACADEMICS</li>
                <li><a href="dashboard.php?section=accounts"><i class="fas fa-user-graduate"></i> Accounts</a></li>
                <li><a href="dashboard.php?section=sections"><i class="fas fa-layer-group"></i> Sections</a></li>
                <li><a href="dashboard.php?section=courses"><i class="fas fa-layer-group"></i> Course</a></li>
                <li><a href="dashboard.php?section=professors"><i class="fas fa-layer-group"></i> Professors</a></li>
                <li><a href="dashboard.php?section=departments"><i class="fas fa-layer-group"></i> Department</a></li>
                <li class="section-title">ARCHIVES</li>
                <li><a href="dashboard.php?section=archives"><i class="fas fa-archive"></i> Archives</a></li>

            </ul>
        </aside>

        <header class="topbar">
            <div class="admin-label">ADMIN</div>
            <div class="admin-info" style="position: relative;">
                <i class="fas fa-user-circle"></i>
                <button onclick="toggleLogoutDropdown()" style="background: none; border: none; color: white; font-weight: bold; cursor: pointer;">
                    ADMIN ADMIN ▼
                </button>
                <div id="logoutDropdown" style="display: none; position: absolute; top: 100%; right: 0; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.2); z-index: 999;">
                    <a href="../auth/logout.php" style="display: block; padding: 10px 15px; text-decoration: none; color: #333;">Logout</a>
                </div>
            </div>


        </header>

        <main class="main-content">
            <?php if (in_array($section, ['accounts', 'requests', 'add'])): ?>
                <div class="admin-tabs">
                    <div class="tab-group">
                        <button class="tab-btn" data-section="accounts">Accounts</button>
                        <button class="tab-btn" onclick="location.href='dashboard.php?section=add'">Add New</button>
                    </div>
                    <div class="search-group">
                        <button class="clear-btn"><i class="fas fa-key"></i> Clear</button>
                        <input type="text" id="searchInput" placeholder="Search...">
                    </div>
                </div>
            <?php endif; ?>


            <div class="admin-content">
                <?php
                if ($section === 'accounts') include('accounts.php');
                elseif ($section === 'requests') include('request.php');
                elseif ($section === 'add') include('add_user.php');
                elseif ($section === 'sections') include('sections.php');
                elseif ($section === 'courses') include('courses.php');
                elseif ($section === 'professors') include('professors.php');
                elseif ($section === 'departments') include('departments.php');
                elseif ($section === 'archives') include('archives.php');
                elseif ($section === 'professors') include('professors.php');
                elseif ($section === 'explorer') {
                    $parent_id = $_GET['parent_id'] ?? 0;
                    include('explorer.php');
                }
                elseif ($section === 'trash') include('trash.php');

                 elseif ($section === 'home') include('home_quick_access.php');
                else include('home_quick_access.php'); // fallback
                ?>
            </div>

        </main>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addUserModal')">&times;</span>
            <h3>Add New User</h3>
            <form action="admin/add_user_action.php" method="POST">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="Student">Student</option>
                    <option value="Admin">Admin</option>
                </select>
                <button type="submit">Create Account</button>
            </form>
        </div>
    </div>
    <script>
        function toggleLogoutDropdown() {
            const dropdown = document.getElementById("logoutDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        // Optional: Close dropdown if clicked outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById("logoutDropdown");
            const button = event.target.closest('.admin-info');
            if (!button) {
                dropdown.style.display = "none";
            }
        });
    </script>

    <script src="../assets/js/dashboard.js"></script>
    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>
</body>

</html>