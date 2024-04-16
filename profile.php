<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (isset($_GET['logout'])) {
    // Clear all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Connect to the database
$host = 'localhost';
$dbname = 'ecoswap';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the user's information
    $stmt = $pdo->prepare("SELECT UserID, Name, Email, Rating FROM User WHERE UserID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - EcoSwap</title>
    <link rel="stylesheet" href="pages.css">
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="items.php">Your Items</a>
        <a href="exchange.php">Exchanges</a>
        <a href="profile.php">Your Profile</a>
        <a href="?logout=true">Logout</a>
    </div>

    <div class="main-content">
        <h1>Your Profile</h1>
        <?php if ($user): ?>
        <div class="profile-details">
            <p><strong>User ID:</strong> <?= htmlspecialchars($user['UserID']) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($user['Name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
            <p><strong>Rating:</strong> <?= htmlspecialchars($user['Rating']) ?></p>
        </div>
        <?php else: ?>
        <p>No user information found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
