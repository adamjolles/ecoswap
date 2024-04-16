<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$itemId = isset($_GET['id']) ? $_GET['id'] : null;
$error = '';

// Connect to the database
$host = 'localhost';
$dbname = 'ecoswap';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the item details
    $stmt = $pdo->prepare("SELECT * FROM Item WHERE UserID = ? AND ItemID = ?");
    $stmt->execute([$userId, $itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        throw new Exception("Item not found");
    }

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate and process the form here
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $itemCondition = $_POST['itemCondition'] ?? '';

        // Validate input
        if (!$title || !$description || !$itemCondition) {
            $error = "All fields are required.";
        } else {
            // Update the item in the database
            $stmt = $pdo->prepare("UPDATE Item SET Title = ?, Description = ?, itemCondition = ? WHERE ItemID = ? AND UserID = ?");
            $stmt->execute([$title, $description, $itemCondition, $itemId, $userId]);
            header("Location: items.php?success=edited");
            exit;
        }
    }

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - EcoSwap</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="items.php">Your Items</a>
        <a href="profile.php">Your Profile</a>
    </div>

    <div class="main-content">
        <h1>Edit Item</h1>
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="item_id" value="<?= htmlspecialchars($itemId) ?>">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($item['Title']) ?>" required>
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description"
                    required><?= htmlspecialchars($item['Description']) ?></textarea>
            </div>
            <div>
                <label for="itemCondition">Condition:</label>
                <input type="text" id="itemCondition" name="itemCondition"
                    value="<?= htmlspecialchars($item['itemCondition']) ?>" required>
            </div>
            <button type="submit">Save Changes</button>
            <a href="items.php" class="button">Cancel</a>
        </form>
    </div>
</body>

</html>