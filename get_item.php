<?php
// Connect to the database
$host = 'localhost';
$dbname = 'ecoswap';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if itemId is provided and valid
    if (isset($_GET['itemId']) && !empty($_GET['itemId'])) {
        $itemId = $_GET['itemId'];

        // Fetch item details
        $stmt = $pdo->prepare("SELECT ItemID, Title, ItemDescription, ItemCondition FROM Item WHERE ItemID = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Return JSON response with item details
            echo json_encode(['success' => true, 'item' => $item]);
        } else {
            echo json_encode(['error' => 'Item not found.']);
        }
    } else {
        echo json_encode(['error' => 'Invalid item ID.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error connecting to the database: ' . $e->getMessage()]);
}
?>
