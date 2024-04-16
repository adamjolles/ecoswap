<?php
session_start();
require 'db_connect.php'; // Assume you have a db_connect.php that handles the PDO connection

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token check (pseudo code)
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['error' => 'CSRF token mismatch.']);
        exit;
    }

    if (isset($_POST['exchangeItemID'], $_POST['exchangeWithItemID'])) {
        $item1ID = $_POST['exchangeItemID'];
        $item2ID = $_POST['exchangeWithItemID'];
        $user_id = $_SESSION['user_id'];

        // Insert exchange proposal into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO Exchange (Item1ID, Item2ID, Status) VALUES (?, ?, 'Pending')");
            $stmt->execute([$item1ID, $item2ID]);
            echo json_encode(['success' => 'Exchange proposal submitted successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error proposing exchange: ' . $e->getMessage()]);
        }
        exit;
    } elseif (isset($_POST['action'], $_POST['exchangeID'])) {
        $action = $_POST['action'];
        $exchangeID = $_POST['exchangeID'];

        try {
            // Check if the user has the right to modify this exchange
            $stmt = $pdo->prepare("SELECT Item1ID, Item2ID FROM Exchange WHERE ExchangeID = ?");
            $stmt->execute([$exchangeID]);
            $exchange = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$exchange || ($exchange['Item1ID'] != $user_id && $exchange['Item2ID'] != $user_id)) {
                echo json_encode(['error' => 'You do not have permission to modify this exchange.']);
                exit;
            }

            if ($action === 'accept') {
                $stmt = $pdo->prepare("UPDATE Exchange SET Status = 'Accepted' WHERE ExchangeID = ?");
                $stmt->execute([$exchangeID]);
                echo json_encode(['success' => 'Exchange accepted successfully.']);
            } elseif ($action === 'cancel') {
                $stmt = $pdo->prepare("UPDATE Exchange SET Status = 'Cancelled' WHERE ExchangeID = ?");
                $stmt->execute([$exchangeID]);
                echo json_encode(['success' => 'Exchange cancelled successfully.']);
            } else {
                echo json_encode(['error' => 'Invalid action.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error processing exchange: ' . $e->getMessage()]);
        }
        exit;
    } else {
        echo json_encode(['error' => 'Invalid request parameters.']);
        exit;
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}
?>
