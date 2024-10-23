<?php
session_start();
require_once('db-user.php');

if (isset($_GET['delete_user'])) {
    $deleteUser = $_GET['delete_user'];
    $sqlDelete = "DELETE FROM user WHERE username = :username";
    $stmtDelete = $dbu->prepare($sqlDelete);
    $stmtDelete->bindParam(':username', $deleteUser);
    $stmtDelete->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT u.username, u.name, e.event_name 
        FROM participate AS p
        INNER JOIN user AS u ON u.user_id = p.user_id
        INNER JOIN events AS e ON e.event_id = p.event_id";
$stmt = $dbu->prepare($sql);
$stmt->execute();
$participation = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>

<body>
    <h1>View Event Registered by User</h1>

    <?php
    $events = [];

    foreach ($participation as $p) {
        if (!isset($events[$p['username']])) {
            $events[$p['username']] = [
                'name' => $p['name'],
                'events' => []
            ];
        }
        $events[$p['username']]['events'][] = $p['event_name'];
    }
    ?>

    <?php foreach ($events as $username => $data): ?>
        <div class="user-management-container">
            <h2><?php echo ($username) . ' - ' . ($data['name']); ?></h2>
            <a href="?delete_username=<?php echo ($username); ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            <?php foreach ($data['events'] as $event): ?>
                <?php echo ($event); ?><br>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</body>

</html>