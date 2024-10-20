<?php
session_start();
require_once('db.php');

// Get the event_id from the URL
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Fetch event details from the database
    $stmt = $conn->prepare("SELECT * FROM Events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        echo "Event not found.";
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $event_name = $_POST['event_name'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $location = $_POST['location'];
        $description = $_POST['description'];
        $max_participants = $_POST['max_participants'];
        $available_slots = $_POST['available_slots'];
        $status = $_POST['status'];

        // Update event in the database
        $stmt = $conn->prepare("UPDATE Events SET event_name = ?, date = ?, time = ?, location = ?, description = ?, max_participants = ?, available_slots = ?, status = ? WHERE event_id = ?");
        $stmt->bind_param("sssssiisi", $event_name, $date, $time, $location, $description, $max_participants, $available_slots, $status, $event_id);

        if ($stmt->execute()) {
            // Redirect to event management page after successful update
            header("Location: event_management.php");
            exit; // Stop further execution
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    echo "No event ID provided.";
    exit;
}

$conn->close();
?>

<!-- Edit Event Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Event</title>
</head>
<body>
    <h1>Edit Event</h1>
    <form action="edit_event.php?event_id=<?php echo $event_id; ?>" method="POST">
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required><br>

        <label for="date">Date:</label>
        <input type="date" name="date" value="<?php echo htmlspecialchars($event['date']); ?>" required><br>

        <label for="time">Time:</label>
        <input type="time" name="time" value="<?php echo htmlspecialchars($event['time']); ?>" required><br>

        <label for="location">Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea><br>

        <label for="max_participants">Max Participants:</label>
        <input type="number" name="max_participants" value="<?php echo htmlspecialchars($event['max_participants']); ?>" required><br>

        <label for="available_slots">Available Slots:</label>
        <input type="number" name="available_slots" value="<?php echo htmlspecialchars($event['available_slots']); ?>" required><br>

        <label for="status">Status:</label>
        <select name="status">
            <option value="open" <?php if ($event['status'] == 'open') echo 'selected'; ?>>Open</option>
            <option value="closed" <?php if ($event['status'] == 'closed') echo 'selected'; ?>>Closed</option>
            <option value="canceled" <?php if ($event['status'] == 'canceled') echo 'selected'; ?>>Canceled</option>
        </select><br>

        <button type="submit">Update Event</button>
    </form>

    <p><a href="event_management.php">Back to Event Management</a></p>
</body>
</html>
