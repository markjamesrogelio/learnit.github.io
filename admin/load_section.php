<?php
session_start();
require_once('../includes/db.php');
require_once('../includes/auth.php');

if ($_SESSION['role'] !== 'admin') exit("Unauthorized");

$section = $_GET['section'] ?? 'home';

if ($section === 'accounts') include('accounts.php');
elseif ($section === 'requests') include('request.php');
elseif ($section === 'add') include('add_user.php');
elseif ($section === 'home') include('home_quick_access.php');
else echo "<h1>Invalid section.</h1>";
?>
