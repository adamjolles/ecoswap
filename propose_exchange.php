<?php
session_start();
require_once 'config.php'; // Database connection settings

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$item1_id = $_POST['item1_id']; // User's own item they want to exchange
$item2_id = $_POST['item2_id']; // Item from another user they want in exchange

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the exchange proposal already exists to avoid duplicates
    $stmt = $pdo->prepare("SELECT * FROM Exchange WHERE (Item1ID = :item1_id AND Item2ID = :item2_id) OR (Item1ID = :item2_id AND Item2ID = :item1_id)");
    $stmt->execute(['item1_id' => $item1_id, 'item2_id' => $item2_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['error' => 'An exchange proposal already exists between these items.']);
        exit;
    }

    // Insert new exchange proposal
    $stmt = $pdo->prepare("INSERT INTO Exchange (Item1ID, Item2ID, Status) VALUES (:item1_id, :item2_id, 'Pending')");
    $stmt->execute(['item1_id' => $item1_id, 'item2_id' => $item2_id]);

    echo json_encode(['success' => 'Exchange proposed successfully!']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
