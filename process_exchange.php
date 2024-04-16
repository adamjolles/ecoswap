<?php
session_start();

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the exchange proposal form data is set
    if (isset($_POST['exchangeItemID']) && isset($_POST['exchangeWithItemID'])) {
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
    } elseif (isset($_POST['action']) && isset($_POST['exchangeID'])) {
        // Handling accepting or canceling exchanges
        $action = $_POST['action'];
        $exchangeID = $_POST['exchangeID'];

        // Connect to the database
        $host = 'localhost';
        $dbname = 'ecoswap';
        $db_user = 'root';
        $db_pass = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($action === 'accept') {
                // Update the status of the exchange to 'Accepted' in the database
                $stmt = $pdo->prepare("UPDATE Exchange SET Status = 'Accepted' WHERE ExchangeID = ?");
                $stmt->execute([$exchangeID]);

                // Return success response
                echo json_encode(['success' => 'Exchange accepted successfully.']);
                exit;
            } elseif ($action === 'cancel') {
                // Update the status of the exchange to 'Cancelled' in the database
                $stmt = $pdo->prepare("UPDATE Exchange SET Status = 'Cancelled' WHERE ExchangeID = ?");
                $stmt->execute([$exchangeID]);

                // Return success response
                echo json_encode(['success' => 'Exchange cancelled successfully.']);
                exit;
            } else {
                // Return error response for invalid action
                echo json_encode(['error' => 'Invalid action.']);
                exit;
            }
        } catch (PDOException $e) {
            // Return error response
            echo json_encode(['error' => 'Error processing exchange: ' . $e->getMessage()]);
            exit;
        }
    } else {
        // Return error response for invalid request parameters
        echo json_encode(['error' => 'Invalid request parameters.']);
        exit;
    }
} else {
    // Return error response for non-POST requests
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}
?>
