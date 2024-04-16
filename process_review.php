<?php
session_start();

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the exchange ID and rating are set in the POST data
    if (isset($_POST['exchangeID'], $_POST['rating'])) {
        $exchangeID = $_POST['exchangeID'];
        $rating = $_POST['rating'];
        $user_id = $_SESSION['user_id'];

        // Connect to the database
        $host = 'localhost';
        $dbname = 'ecoswap';
        $db_user = 'root';
        $db_pass = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if the user has already reviewed this exchange
            $checkReviewStmt = $pdo->prepare("SELECT COUNT(*) FROM Review WHERE ExchangeID = ? AND UserID = ?");
            $checkReviewStmt->execute([$exchangeID, $user_id]);
            $reviewExists = $checkReviewStmt->fetchColumn();

            if ($reviewExists) {
                // User has already reviewed this exchange
                echo json_encode(['error' => 'You have already reviewed this exchange.']);
                exit;
            }

            // Insert the review into the Review table
            $insertReviewStmt = $pdo->prepare("INSERT INTO Review (ExchangeID, UserID, Rating) VALUES (?, ?, ?)");
            $insertReviewStmt->execute([$exchangeID, $user_id, $rating]);

            // Return success response
            echo json_encode(['success' => 'Review submitted successfully.']);
            exit;
        } catch (PDOException $e) {
            // Return error response
            echo json_encode(['error' => 'Error processing review: ' . $e->getMessage()]);
            exit;
        }
    } else {
        // Return error response for missing parameters
        echo json_encode(['error' => 'Missing parameters.']);
        exit;
    }
} else {
    // Return error response for non-POST requests
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}
?>
