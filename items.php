<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (isset($_GET['logout'])) {
    // Clear all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header('Location: index.php');
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

    // Fetch the user's items
    $stmt = $pdo->prepare("SELECT ItemID, Title, ItemDescription, ItemCondition FROM Item WHERE UserID = ?");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Items - EcoSwap</title>
    <link rel="stylesheet" href="pages.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="items.php">Your Items</a>
        <a href="profile.php">Your Profile</a>
        <a href="?logout=true">Logout</a>
    </div>

    <div class="main-content">
        <h1>Your Items</h1>
        <button id="openModal">Add New Item</button>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <form id="addItemForm" method="post">
                    <h2>Add New Item</h2>
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                    <label for="itemDescription">Description:</label>
                    <textarea id="itemDescription" name="itemDescription" required></textarea>
                    <label for="itemCondition">Condition:</label>
                    <input type="text" id="itemCondition" name="itemCondition" required>
                    <button type="submit">Add Item</button>
                </form>
            </div>
        </div>

        <!-- Items Display Section -->
        <div class="item-list">
            <?php if (!empty($items)): ?>
                <table>
                    <tr>
                        <th>ItemID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Condition</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['ItemID']) ?></td>
                        <td><?= htmlspecialchars($item['Title']) ?></td>
                        <td><?= htmlspecialchars($item['ItemDescription']) ?></td>
                        <td><?= htmlspecialchars($item['ItemCondition']) ?></td>
                        <td><button class="editItem" data-itemid="<?= $item['ItemID'] ?>">Edit</button></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No items found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="closeEdit">&times;</span>
            <form id="editItemForm" method="post">
                <h2>Edit Item</h2>
                <input type="hidden" id="editItemId" name="editItemId">
                <label for="editTitle">Title:</label>
                <input type="text" id="editTitle" name="editTitle" required>
                <label for="editDescription">Description:</label>
                <textarea id="editDescription" name="editDescription" required></textarea>
                <label for="editCondition">Condition:</label>
                <input type="text" id="editCondition" name="editCondition" required>
                <button type="submit">Update Item</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#openModal").click(function() {
                $("#myModal").show();
            });

            $(".close").click(function() {
                $("#myModal").hide();
            });

            $(".closeEdit").click(function() {
                $("#editModal").hide();
            });

            $(".editItem").click(function() {
                var itemId = $(this).data("itemid");
                $.ajax({
                    type: "GET",
                    url: "get_item.php",
                    data: { itemId: itemId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $("#editItemId").val(response.item.ItemID);
                            $("#editTitle").val(response.item.Title);
                            $("#editDescription").val(response.item.ItemDescription);
                            $("#editCondition").val(response.item.ItemCondition);
                            $("#editModal").show();
                        } else {
                            alert(response.error);
                        }
                    },
                    error: function(xhr) {
                        alert("An error occurred: " + xhr.statusText);
                    }
                });
            });

            $("#editItemForm").submit(function(event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "process_editItem.php",
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.success);
                            $("#editModal").hide();
                            window.location.reload();
                        } else {
                            alert(response.error);
                        }
                    },
                    error: function(xhr) {
                        alert("An error occurred: " + xhr.statusText);
                    }
                });
            });
        });
    </script>
</body>
</html>
