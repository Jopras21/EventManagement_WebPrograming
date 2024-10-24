<?php
session_start();
require_once('db-user.php');

$sql = "SELECT event_id, event_name, date, time, location, description, max_participants, available_slots, image_url, status
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
</head>

<body>
    <div class="event-browsing-container">
        <h1>Events for You</h1>   
        <div class="event-browsing-search">
            <input type="text" id="event-filter" name="search" placeholder="Search event by name or location" />
            <button onclick="clearSearch()" class="clear-search">Clear</button>
        </div>   
        <div class="event-browsing-contents">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $key => $event): ?>
                    <div class="event-browsing-content">
                        <h2><?php echo htmlspecialchars($event['event_name']); ?></h2><br>
                        <?php
                        $date = date("j F Y", strtotime($event['date']));
                        echo $date; ?><br>
                        <?php echo htmlspecialchars($event['time']); ?><br>
                        <?php echo htmlspecialchars($event['location']); ?><br>
                        <button class="detail-button" onclick="showDetail(<?php echo $key; ?>)">View Details</button>
                    </div>

                    <div class="event-browsing-details" id="event-detail-<?php echo $key; ?>">
                        <div class="event-browsing-detail">
                            <div class="close-button">
                                <span class="close" id="close" onclick="closeDetail(<?php echo $key; ?>)">&times;</span>
                            </div>
                            <img src="uploads/<?php echo htmlspecialchars($event['image_url']); ?>" alt="Event Image" style="max-width: 360px">
                            <h2><?php echo htmlspecialchars($event['event_name']); ?></h2>
                            <p><strong>Date:</strong> <?php echo $date; ?></p>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars($event['time']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                            <p><?php echo $event['available_slots'] . '/' . $event['max_participants'] . ' slots left!'; ?></p>
                            <button type="submit" class="event-register-button">
                                <a href="event_register.php?event_id=<?php echo $event['event_id']; ?>">Register</a>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No events found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('event-filter').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const eventContents = document.querySelectorAll('.event-browsing-content');
            
            eventContents.forEach(eventContent => {
                const eventName = eventContent.querySelector('h2').textContent.toLowerCase();
                const eventLocation = eventContent.textContent.toLowerCase();
                
                if (eventName.includes(filter) || eventLocation.includes(filter)) {
                    eventContent.style.display = '';
                    const eventId = eventContent.querySelector('.detail-button').getAttribute('onclick').match(/\d+/)[0];
                    const relatedDetail = document.getElementById('event-detail-' + eventId);
                    if (relatedDetail) {
                        relatedDetail.style.display = 'none';
                    }
                } else {
                    eventContent.style.display = 'none';
                    const eventId = eventContent.querySelector('.detail-button').getAttribute('onclick').match(/\d+/)[0];
                    const relatedDetail = document.getElementById('event-detail-' + eventId);
                    if (relatedDetail) {
                        relatedDetail.style.display = 'none';
                    }
                }
            });
        });

        function clearSearch() {
            document.getElementById('event-filter').value = '';
            const event = new Event('input');
            document.getElementById('event-filter').dispatchEvent(event);
        }

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
