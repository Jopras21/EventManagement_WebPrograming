<?php
session_start();
require_once('db.php');



// Fetch all events from the database
$stmt = $conn->prepare("SELECT * FROM Events ORDER BY date ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Event Management</title>
</head>
<body>
    <div class="header">
    <h1>Event Management</h1>
    
    </div>
    <?php if ($result->num_rows > 0): ?>
    <table border="1">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Max Participants</th>
                <th>Available Slots</th>
                <th>Status</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($event = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                <td><?php echo htmlspecialchars($event['date']); ?></td>
                <td><?php echo htmlspecialchars($event['time']); ?></td>
                <td><?php echo htmlspecialchars($event['location']); ?></td>
                <td><?php echo htmlspecialchars($event['max_participants']); ?></td>
                <td><?php echo htmlspecialchars($event['available_slots']); ?></td>
                <td><?php echo htmlspecialchars($event['status']); ?></td>
                <td>
                    <?php if (!empty($event['image_url'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($event['image_url']); ?>" alt="Event Image" width="100" class="event-img">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_event.php?event_id=<?php echo $event['event_id']; ?>">Edit</a> | 
                    <a href="delete_event.php?event_id=<?php echo $event['event_id']; ?>" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No events available.</p>
    <?php endif; ?>

    <p><a href="create_event.php">Create New Event</a></p>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
