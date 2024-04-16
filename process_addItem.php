<?php
session_start();

header('Content-Type: application/json'); // Ensure we're outputting a JSON response

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Session not set']);
    exit;
}

$host = 'localhost';
$dbname = 'ecoswap';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $title = $_POST['title'] ?? '';
    $itemDescription = $_POST['itemDescription'] ?? '';
    $itemCondition = $_POST['itemCondition'] ?? '';
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($itemDescription) || empty($itemCondition)) {
        echo json_encode(['error' => 'Please fill all fields']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO Item (Title, ItemDescription, ItemCondition, UserID) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $itemDescription, $itemCondition, $user_id]);

    echo json_encode(['success' => 'Item added successfully']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
