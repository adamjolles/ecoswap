<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to EcoSwap</title>
    <link rel="stylesheet" href="pages.css">
</head>
<body>

    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="items.php">Your Items</a>
        <a href="profile.php">Your Profile</a>
    </div>

    <div class="main-content">
        <h1>Welcome to EcoSwap, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h1>
        <p>You are now logged in. Choose an option from the navigation bar.</p>
    </div>

</body>
</html>