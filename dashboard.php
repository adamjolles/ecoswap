<?php
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - EcoSwap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .header {
            background-color: #008000;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .nav {
            background-color: #333;
            overflow: hidden;
        }
        .nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .nav a:hover {
            background-color: #ddd;
            color: black;
        }
        .content {
            margin: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>EcoSwap Dashboard</h1>
    </div>
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="items.php">Items</a>
        <a href="account.php">Account</a>
    </div>
    <div class="content">
        <h2>Welcome to your Dashboard</h2>
        <p>This is your main dashboard where you can manage everything related to your EcoSwap activities.</p>
    </div>
</body>
</html>
