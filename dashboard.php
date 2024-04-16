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

// Check if the offer proposal form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['offerItemId'], $_POST['selectedItems'])) {
    $offerItemId = $_POST['offerItemId'];
    $selectedItems = $_POST['selectedItems'];

    // Connect to the database
    $host = 'localhost';
    $dbname = 'ecoswap';
    $db_user = 'root';
    $db_pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert the offer proposal into the database
        $stmt = $pdo->prepare("INSERT INTO Offer (OfferItemID, OfferedItems) VALUES (?, ?)");
        $stmt->execute([$offerItemId, implode(',', $selectedItems)]);

        // Redirect to dashboard with success message
        header('Location: dashboard.php?offer_success=true');
        exit;

    } catch (PDOException $e) {
        die("Error connecting to the database: " . $e->getMessage());
    }
}

// Connect to the database to fetch items owned by other users
$user_id = $_SESSION['user_id'];
$host = 'localhost';
$dbname = 'ecoswap';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch items owned by other users (excluding the current user)
    $stmt = $pdo->prepare("SELECT ItemID, Title, ItemDescription, ItemCondition FROM Item WHERE UserID != ?");
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
    <title>Welcome to EcoSwap</title>
    <link rel="stylesheet" href="pages.css">
</head>
<body>

    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="items.php">Your Items</a>
        <a href="profile.php">Your Profile</a>
        <a href="?logout=true">Logout</a> <!-- Logout link -->
    </div>

    <div class="main-content">
        <h1>Welcome to EcoSwap, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h1>
        <p>You are now logged in. Choose an option from the navigation bar.</p>

        <!-- Display all items -->
        <h2>All Items</h2>
        <div class="item-list">
            <?php if (!empty($items)): ?>
                <form method="POST" action="">
                    <table>
                        <tr>
                            <th>ItemID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Condition</th>
                            <th>Select</th>
                        </tr>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['ItemID']) ?></td>
                            <td><?= htmlspecialchars($item['Title']) ?></td>
                            <td><?= htmlspecialchars($item['ItemDescription']) ?></td>
                            <td><?= htmlspecialchars($item['ItemCondition']) ?></td>
                            <td><input type="checkbox" name="selectedItems[]" value="<?= $item['ItemID'] ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <input type="hidden" name="offerItemId" value="ITEM_ID_TO_OFFER_FOR">
                    <button type="submit">Propose Offer</button>
                </form>
            <?php else: ?>
                <p>No items found.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
