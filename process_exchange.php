<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exchangeItemID']) && isset($_POST['exchangeWithItemID'])) {
    $item1ID = $_POST['exchangeItemID'];
    $item2ID = $_POST['exchangeWithItemID'];
    $user_id = $_SESSION['user_id'];

    // Connect to the database
    $host = 'localhost';
    $dbname = 'ecoswap';
    $db_user = 'root';
    $db_pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert exchange proposal into the database
        $stmt = $pdo->prepare("INSERT INTO Exchange (Item1ID, Item2ID, Status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$item1ID, $item2ID]);

        // Return success response
        echo json_encode(['success' => 'Exchange proposal submitted successfully.']);
        exit;
    } catch (PDOException $e) {
        // Return error response
        echo json_encode(['error' => 'Error proposing exchange: ' . $e->getMessage()]);
        exit;
    }
} else {
    // Return error response if request method or parameters are invalid
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}
?>
