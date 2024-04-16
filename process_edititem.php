<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
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

    // Check if form data is provided
    if (isset($_POST['editItemId'], $_POST['editTitle'], $_POST['editDescription'], $_POST['editCondition'])) {
        $editItemId = $_POST['editItemId'];
        $editTitle = $_POST['editTitle'];
        $editDescription = $_POST['editDescription'];
        $editCondition = $_POST['editCondition'];

        // Update item in the database
        $stmt = $pdo->prepare("UPDATE Item SET Title = ?, ItemDescription = ?, ItemCondition = ? WHERE ItemID = ? AND UserID = ?");
        $stmt->execute([$editTitle, $editDescription, $editCondition, $editItemId, $user_id]);

        // Check if update was successful
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => 'Item updated successfully.']);
        } else {
            echo json_encode(['error' => 'Failed to update item.']);
        }
    } else {
        echo json_encode(['error' => 'Incomplete form data.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error connecting to the database: ' . $e->getMessage()]);
}
?>
