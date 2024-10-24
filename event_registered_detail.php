<?php
session_start();
require_once('db-user.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT e.event_id, e.event_name, e.date, e.time, e.location, e.description, e.max_participants, e.available_slots, e.image_url, e.status
        FROM events AS e
        INNER JOIN participate AS p ON p.event_id = e.event_id
        WHERE p.user_id = :user_id AND e.status = 'open' AND e.date >= CURDATE()";

$stmt = $dbu->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_event_browse.css">
    <title>Registered Events</title>
</head>

<body>
    <div class="event-browsing-container">
        <h1>Your Registered Events</h1>
        <div class="event-browsing-contents" style="align-items: start;">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $key => $event): ?>
                    <div class="event-browsing-content">
                        <h2><?php echo htmlspecialchars($event['event_name']); ?></h2><br>
                        <img src="uploads/<?php echo htmlspecialchars($event['image_url']); ?>" alt="Event Image" style="max-width: 360px">
                        <p><strong>Date:</strong> <?php $date = date("j F Y", strtotime($event['date']));
                        echo $date; ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($event['time']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have not registered for any events yet.</p>
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
    </script>
</body>

</html>