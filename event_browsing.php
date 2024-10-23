<?php
session_start();
require_once('db-user.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT event_name, date, time, location, description, max_participants, available_slots, image_url, status
        FROM events
        WHERE status = 'open' AND date >= CURDATE()";

if (!empty($search)) {
    $sql .= " AND event_name LIKE :search";
}

$stmt = $dbu->prepare($sql);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%");
}

$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Browsing</title>
    <link rel="stylesheet" href="style_event_browse.css">
</head>

<body>
    <div class="event-browsing-container">
        <h1>Events for You</h1>        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search event by name" value="<?php echo htmlspecialchars($search); ?>" />
            <button type="submit">Search</button>
        </form>

        <div class="event-browsing-contents">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $key => $event): ?>
                    <div class="event-browsing-content">
                        <h2><?php echo ($event['event_name']); ?></h2><br>
                        <?php
                        $date = date("j F Y", strtotime($event['date']));
                        echo $date; ?><br>
                        <?php echo ($event['time']); ?><br>
                        <?php echo ($event['location']); ?><br>
                        <button class="detail-button" onclick="showDetail(<?php echo $key; ?>)">View Details</button>
                    </div>

                    <div class="event-browsing-details" id="event-detail-<?php echo $key; ?>">
                        <div class="event-browsing-detail">
                            <span class="close" onclick="closeDetail(<?php echo $key; ?>)">&times;</span>
                            <img src="uploads/<?php echo $event['image_url']; ?>" alt="Image" style="max-width: 360px">
                            <h2><?php echo ($event['event_name']); ?></h2>
                            <p><strong>Date:</strong> <?php echo $date; ?></p>
                            <p><strong>Time:</strong> <?php echo ($event['time']); ?></p>
                            <p><strong>Location:</strong> <?php echo ($event['location']); ?></p>
                            <p><strong>Description:</strong> <?php echo ($event['description']); ?></p>
                            <p><?php echo $event['available_slots'] . '/' . $event['max_participants'] . ' slots left!'; ?></p>
                            <button type="submit" class="event-register-button"><a href="event_register.php">Register</a></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No events found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showDetail(key) {
            document.getElementById('event-detail-' + key).style.display = 'block';
        }

        function closeDetail(key) {
            document.getElementById('event-detail-' + key).style.display = 'none';
        }

        window.onclick = function(event) {
            <?php foreach ($events as $key => $event): ?>
                if (event.target == document.getElementById('event-detail-<?php echo $key; ?>')) {
                    document.getElementById('event-detail-<?php echo $key; ?>').style.display = 'none';
                }
            <?php endforeach; ?>
        }
    </script>
</body>

</html>
