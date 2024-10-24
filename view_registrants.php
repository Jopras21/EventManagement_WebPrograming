<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$event_id = $_GET['event_id'];

// Fetch event name
$stmt_event = $conn->prepare("SELECT event_name FROM events WHERE event_id = ?");
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$stmt_event->bind_result($event_name);
$stmt_event->fetch();
$stmt_event->close();

// Handle CSV export
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

// Fetch registrants
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
    <style>
        /* General styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header {
            background-color: #28a745;
            width: 100%;
            padding: 20px;
            text-align: center;
            color: white;
            position: relative;
        }

        .header a {
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        h1 {
            margin: 0;
            font-size: 28px;
        }

        h2 {
            margin-top: 10px;
            color: #444;
        }

        table {
            margin-top: 20px;
            border-collapse: collapse;
            width: 80%;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Styling for the CSV export button */
        form {
            margin-top: 20px;
        }

        button[name="export_csv"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        button[name="export_csv"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Event Registrants</h1>
        <a href="event_management.php">Back to Dashboard</a>
    </div>

    <!-- Display event name -->
    <h2>Event: <?php echo htmlspecialchars($event_name); ?></h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
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

    <!-- Form for CSV export button -->
    <form method="post">
        <button type="submit" name="export_csv">Export to CSV</button>
    </form>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>

