<?php
session_start();
require_once('db-user.php');

if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
    die("Invalid event.");
}

$event_id = (int)$_GET['event_id'];
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: login.php');
    exit();
}

$stmt = $dbu->prepare("SELECT event_name, available_slots FROM events WHERE event_id = :event_id AND status = 'open'");
$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event not found or is no longer open.");
}

$checkStmt = $dbu->prepare("SELECT * FROM participate WHERE event_id = :event_id AND user_id = :user_id");
$checkStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
$checkStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$checkStmt->execute();
$participation = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($participation) {
    $message = "You are already registered for this event.";
} elseif ($event['available_slots'] > 0) {
    // Register the user for the event
    $insertStmt = $dbu->prepare("INSERT INTO participate (event_id, user_id) VALUES (:event_id, :user_id)");
    $insertStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($insertStmt->execute()) {
        $updateStmt = $dbu->prepare("UPDATE events SET available_slots = available_slots - 1 WHERE event_id = :event_id");
        $updateStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $updateStmt->execute();

        $message = "Successfully registered for the event.";
    } else {
        $message = "Failed to register for the event.";
    }
} else {
    $message = "The event is fully booked.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    <link rel="stylesheet" href="style_event_register.css">
</head>

<body>
    <div class="event-registration-container">
        <h1>Event Registration</h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <div class="event-registration-choice">
            <button> <a href="cancel_registration.php">Cancel Registration</a>
            </button>
            <button> <a href="event_browsing.php">Back to Event Browsing</a>
            </button>
            <button> <a href="event_registered_detail.php">View Registered Event</a>
            </button>
        </div>
    </div>
</body>

</html>