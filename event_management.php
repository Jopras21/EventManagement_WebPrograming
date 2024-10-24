<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header('location: event_browsing.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM Events ORDER BY date ASC");
$stmt->execute();
$result = $stmt->get_result();

$is_admin = $_SESSION['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_event_management.css">
    <title>Event Management</title>
</head>
<body>
    <div class="event-management-header">
        <h1>Event Management</h1>
        <div class="profile">
            <a href="admin_dashboard.php" class="create-event">Return to Dashboard</a> 
            <a href="create_event.php" class="create-event">Create New Event</a> 
        </div>
    </div>

    <div class="filter">
        <label for="event-filter">Filter Events: </label>
        <input type="text" id="event-filter" placeholder="Search by name or location">
        <button onclick="exportTableToCSV('events.csv')">Export to CSV</button>
        <button onclick="exportTableToExcel('events.xls')">Export to Excel</button>
    </div>

    <?php if ($result->num_rows > 0): ?>
    <table id="event-table">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Event Name</th>
                <th onclick="sortTable(1)">Date</th>
                <th onclick="sortTable(2)">Time</th>
                <th onclick="sortTable(3)">Location</th>
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
                        <img src="uploads/<?php echo htmlspecialchars($event['image_url']); ?>" alt="Event Image" class="event-img">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="edit_event.php?event_id=<?php echo $event['event_id']; ?>">Edit</a> 
                    <a href="delete_event.php?event_id=<?php echo $event['event_id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                    <a href="view_registrants.php?event_id=<?php echo $event['event_id']; ?>">View Registrants</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No events available.</p>
    <?php endif; ?>

    <?php
    $stmt->close();
    $conn->close();
    ?>

    <script src="script.js"></script>
</body>
</html>
