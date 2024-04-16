<?php
session_start();

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the rating form data is set
    if (isset($_POST['ratedUserID']) && isset($_POST['rating'])) {
        $ratedUserID = $_POST['ratedUserID'];
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

            // Insert rating into the Review table
            $stmt = $pdo->prepare("INSERT INTO Review (RatedUserID, Rating, ReviewerUserID) VALUES (?, ?, ?)");
            $stmt->execute([$ratedUserID, $rating, $user_id]);

            // Calculate the new average rating for the rated user
            $avgStmt = $pdo->prepare("SELECT AVG(Rating) AS AvgRating FROM Review WHERE RatedUserID = ?");
            $avgStmt->execute([$ratedUserID]);
            $avgRating = $avgStmt->fetch(PDO::FETCH_ASSOC)['AvgRating'];

            // Update the rated user's rating in the User table
            $updateStmt = $pdo->prepare("UPDATE User SET Rating = ? WHERE UserID = ?");
            $updateStmt->execute([$avgRating, $ratedUserID]);

            // Return success response
            echo json_encode(['success' => 'User rated successfully.']);
            exit;
        } catch (PDOException $e) {
            // Return error response
            echo json_encode(['error' => 'Error rating user: ' . $e->getMessage()]);
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
