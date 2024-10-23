<?php
// leave_event.php
session_start();
require_once('db-user.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login first";
    header("Location: login.php");
    exit();
}

if (!isset($_POST['event_id'])) {
    $_SESSION['error'] = "Invalid event";
    header("Location: browse_events.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'];

try {
    $dbu->beginTransaction();
    
    // Check if user is actually participating
    $check_sql = "SELECT * FROM participate 
                  WHERE event_id = :event_id AND user_id = :user_id";
    $check_stmt = $dbu->prepare($check_sql);
    $check_stmt->execute([
        ':event_id' => $event_id,
        ':user_id' => $user_id
    ]);
    
    if ($check_stmt->rowCount() == 0) {
        throw new Exception("You are not registered for this event!");
    }
    
    // Delete from participate table
    $leave_sql = "DELETE FROM participate 
                  WHERE event_id = :event_id AND user_id = :user_id";
    $leave_stmt = $dbu->prepare($leave_sql);
    $leave_stmt->execute([
        ':event_id' => $event_id,
        ':user_id' => $user_id
    ]);
    
    // Update available slots
    $update_sql = "UPDATE events 
                   SET available_slots = available_slots + 1 
                   WHERE event_id = :event_id";
    $update_stmt = $dbu->prepare($update_sql);
    $update_stmt->execute([':event_id' => $event_id]);
    
    $dbu->commit();
    $_SESSION['success'] = "Successfully left the event!";
    
} catch (Exception $e) {
    $dbu->rollBack();
    $_SESSION['error'] = $e->getMessage();
}

header("Location: browse_events.php");
exit();
?>