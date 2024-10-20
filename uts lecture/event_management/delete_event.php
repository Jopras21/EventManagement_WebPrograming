<?php
session_start();
require_once('db.php');

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    $stmt = $conn->prepare("SELECT image_url FROM Events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    $stmt->fetch();
    $stmt->close();

    if (!empty($image_url) && file_exists("uploads/" . $image_url)) {
        unlink("uploads/" . $image_url); 
    }

    $stmt = $conn->prepare("DELETE FROM Events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "Event and associated image deleted successfully.";
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
