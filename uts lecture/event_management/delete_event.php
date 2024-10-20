<?php
session_start();
require_once('db.php');



if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Delete event from the database
    $stmt = $conn->prepare("DELETE FROM Events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "Event deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No event ID provided.";
}

header("Location: event_management.php");
exit;
?>
