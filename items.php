<?php
session_start();

if (!isset($_SESSION['user_id'])) {
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

    // Fetch the user's items
    $stmt = $pdo->prepare("SELECT ItemID, Title, Description, ItemCondition FROM Item WHERE UserID = ?");
    $stmt->execute([$user_id]);
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
    <title>Your Items - EcoSwap</title>
    <link rel="stylesheet" href="pages.css">
</head>
<body>
    <div class="navbar">
        <!-- Navigation links can be the same as provided in welcome.php -->
        <a href="dashboard.php">Dashboard</a>
        <a href="items.php">Your Items</a>
        <a href="profile.php">Your Profile</a>
    </div>

    <div class="main-content">
        <h1>Your Items</h1>
        <a href="addItem.php" class="button">Add New Item</a>
        <table>
            <tr>
                <th>ItemID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Condition</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['ItemID']) ?></td>
                <td><?= htmlspecialchars($item['Title']) ?></td>
                <td><?= htmlspecialchars($item['Description']) ?></td>
                <td><?= htmlspecialchars($item['Condition']) ?></td>
                <td>
                    <a href="editItem.php?id=<?= $item['ItemID'] ?>" class="button">Edit</a>
                    <a href="deleteItem.php?id=<?= $item['ItemID'] ?>" class="button">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
