<?php
session_start();
require_once('db-user.php');

$sql = "SELECT event_name, date, time, location, description, max_participants, available_slots, image_url, status
        FROM events
        WHERE status = 'open' AND date >= CURDATE()";
$stmt = $dbu->prepare($sql);
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
    </link>
</head>

<body>
    <div class="event-browsing-container">
        <h1>Events for You</h1>
        <div class="event-browsing-contents">
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
                        <h2><?php echo ($event['event_name']); ?></h2>
                        <p><strong>Date:</strong> <?php echo $date; ?></p>
                        <p><strong>Time:</strong> <?php echo ($event['time']); ?></p>
                        <p><strong>Location:</strong> <?php echo ($event['location']); ?></p>
                        <p><strong>Description:</strong> <?php echo ($event['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
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