<?php
session_start();
require_once('db-user.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$sql_event = "SELECT event_name, max_participants, available_slots,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) AS open_events,
            SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) AS cancel_events,
            COUNT(*) AS total_events
        FROM events
        GROUP BY event_name, max_participants, available_slots";
$stmt_event = $dbu->prepare($sql_event);
$stmt_event->execute();
$events = $stmt_event->fetchAll(PDO::FETCH_ASSOC);

$sql_user = "SELECT COUNT(*) AS total_users
            FROM user
            WHERE role = 'user'";
$stmt_user = $dbu->prepare($sql_user);
$stmt_user->execute();
$user_count = $stmt_user->fetch(PDO::FETCH_ASSOC)['total_users'];

$sql_participate = "SELECT COUNT(*) AS register_to_event 
                    FROM participate";
$stmt_participate = $dbu->prepare($sql_participate);
$stmt_participate->execute();
$participation_count = $stmt_participate->fetch(PDO::FETCH_ASSOC)['register_to_event'];

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style_admin_dashboard.css">
</head>

<body>
    <div class="admin-dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <div class="admin-dashboard-overview">
            <div class="admin-dashboard-open-events">
                <h3>You have</h3>
                <h2>
                    <?php
                    $totalOpenEvents = 0;
                    foreach ($events as $event) {
                        $totalOpenEvents += $event['open_events'];
                    }
                    echo htmlspecialchars($totalOpenEvents) . '/' . htmlspecialchars(count($events));
                    ?>
                </h2>
                <h3>active events</h3>
            </div>

            <div class="admin-dashboard-cancel-events">
                <h3>You have</h3>
                <h2>
                    <?php
                    $totalCanceledEvents = 0;
                    foreach ($events as $event) {
                        $totalCanceledEvents += $event['cancel_events'];
                    }
                    echo htmlspecialchars($totalCanceledEvents) . '/' . htmlspecialchars(count($events));
                    ?>
                </h2>
                <h3>canceled events</h3>
            </div>

            <div class="admin-dashboard-active-users">
                <h3>There are</h3>
                <h2>
                    <?php
                    echo htmlspecialchars($user_count);
                    ?>
                </h2>
                <h3>active users</h3>
            </div>

            <div class="admin-dashboard-registered-events">
                <h3>They registered to</h3>
                <h2>
                    <?php
                    echo htmlspecialchars($participation_count);
                    ?>
                </h2>
                <h3>events (cumulative)</h3>
            </div>
        </div>
        <div class="admin-dashboard-action-choice">
            <div class="admin-manage-container">
                <button><a href="event_management.php" class="admin-manage">Manage Events</a></button>
                <div class="admin-dashboard-events-list">
                    <div class="admin-dashboard-event-cards">
                        <?php foreach ($events as $event): ?>
                            <div class="event-card">
                                <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                <?php echo htmlspecialchars($event['max_participants'] - $event['available_slots']); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="admin-manage-container">
                <button><a href="user_management.php" class="admin-manage">Manage User</a></button>
            </div>
        </div>
        <div class="logout-button">
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</body>

</html>