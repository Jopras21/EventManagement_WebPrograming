<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$event_id = $_GET['event_id'];

if (isset($_POST['export_csv'])) {
    $stmt = $conn->prepare("
        SELECT user.username, user.email 
        FROM registrations 
        JOIN user ON registrations.user_id = user.user_id 
        WHERE registrations.event_id = ?
    ");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="registrants.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Username', 'Email']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    $stmt->close();
    $conn->close();
    exit();
}

$stmt = $conn->prepare("
    SELECT user.username, user.email 
    FROM registrations 
    JOIN user ON registrations.user_id = user.user_id 
    WHERE registrations.event_id = ?
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrants</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Event Registrants</h1>
        <a href="event_management.php">Back to Dashboard</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No registrants found for this event.</p>
    <?php endif; ?>

    <!-- Form for CSV export button is always visible -->
    <form method="post">
        <button type="submit" name="export_csv">Export to CSV</button>
    </form>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
