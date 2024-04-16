<?php
// Function to calculate the average rating of a user
function calculateAverageRating($user_id)
{
    // Connect to the database
    $host = 'localhost';
    $dbname = 'ecoswap';
    $db_user = 'root';
    $db_pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch all reviews for the user
        $stmt = $pdo->prepare("SELECT Rating FROM Review
                               INNER JOIN Exchange ON Review.ExchangeID = Exchange.ExchangeID
                               WHERE (Exchange.Item1ID IN (SELECT ItemID FROM Item WHERE UserID = ?) OR
                                      Exchange.Item2ID IN (SELECT ItemID FROM Item WHERE UserID = ?))");
        $stmt->execute([$user_id, $user_id]);
        $ratings = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Calculate average rating
        if (!empty($ratings)) {
            $averageRating = array_sum($ratings) / count($ratings);
            return number_format($averageRating, 2); // Format to 2 decimal places
        } else {
            return 'N/A'; // No reviews yet
        }
    } catch (PDOException $e) {
        return 'Error calculating rating: ' . $e->getMessage();
    }
}
?>
