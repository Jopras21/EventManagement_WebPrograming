<?php
session_start();
require_once('db-user.php');

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $user_id = $_SESSION['user_id'] ?? null;

    $cancelRegister = $_GET['cancel_register'];
    $sqlCancel = "DELETE FROM participate WHERE";

    $stmtCancel = $dbu->prepare("DELETE FROM participate WHERE event_id = :event_id AND user_id = :user_id");
    $stmtCancel->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmtCancel->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmtCancel->execute()) {
        $updateStmt = $dbu->prepare("UPDATE events SET available_slots = available_slots + 1 WHERE event_id = :event_id");
        $updateStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $updateStmt->execute();

        header('Location: event_browsing.php');
        exit();
    } else {
        echo "Failed to cancel registration.";
    }
}
