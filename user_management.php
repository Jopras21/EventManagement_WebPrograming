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
    <link rel="stylesheet" href="style_user_management.css">
    <title>User Management</title>
</head>

<body>
    <h1>View Event Registered by User</h1>
    <div class="event-browsing-search">
        <input type="text" id="event-filter" name="search" placeholder="Search event by name or location" />
        <button onclick="clearSearch()" class="clear-search">Clear</button>
    </div>  

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

        <div class="user-management-container">
            <?php foreach ($events as $username => $data): ?>
                <div class="event-browsing-content">
                    <h2><?php echo ($username) . ' - ' . ($data['name']); ?></h2>
                    
                    <details>
                        <summary>Event yang diikuti</summary>
                        <ul>
                            <?php foreach ($data['events'] as $event): ?>
                                <li><?php echo ($event); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </details>
                    
                    <a href="?delete_username=<?php echo ($username); ?>" 
                    class="delete-btn" 
                    onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </div>
            <?php endforeach; ?>
        </div>

    <script>
        document.getElementById('event-filter').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const eventContents = document.querySelectorAll('.user-management-container');
            
            eventContents.forEach(eventContent => {
                const username = eventContent.querySelector('h2').textContent.toLowerCase();
                const eventList = Array.from(eventContent.querySelectorAll('ul li')).map(li => li.textContent.toLowerCase());
                
                if (username.includes(filter) || eventList.some(event => event.includes(filter))) {
                    eventContent.style.display = '';
                } else {
                    eventContent.style.display = 'none';
                }
            });
        });

        function clearSearch() {
            document.getElementById('event-filter').value = '';
            const event = new Event('input');
            document.getElementById('event-filter').dispatchEvent(event);
        }
    </script>
</body>

</html>