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

// Connect to the database
$host = 'localhost';
$dbname = 'ecoswap';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all items from the database
    $stmt = $pdo->prepare("SELECT ItemID, Title, ItemDescription, ItemCondition FROM Item");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
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
        <a href="?logout=true">Logout</a>
    </div>

    <div class="main-content">
        <h1>Welcome to EcoSwap, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h1>
        <p>You are now logged in. Choose an option from the navigation bar.</p>

        <!-- Display all items -->
        <h2>All Items</h2>
        <div class="item-list">
            <?php if (!empty($items)): ?>
                <table>
                    <tr>
                        <th>ItemID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Condition</th>
                    </tr>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['ItemID']) ?></td>
                        <td><?= htmlspecialchars($item['Title']) ?></td>
                        <td><?= htmlspecialchars($item['ItemDescription']) ?></td>
                        <td><?= htmlspecialchars($item['ItemCondition']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No items found.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
