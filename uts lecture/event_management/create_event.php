<?php
session_start();
require_once('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Create Event</title>
</head>
<body>
    <h1>Create New Event</h1>
    <form action="create_event.php" method="POST" enctype="multipart/form-data">
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" required><br>

        <label for="date">Date:</label>
        <input type="date" name="date" required><br>

        <label for="time">Time:</label>
        <input type="time" name="time" required><br>

        <label for="location">Location:</label>
        <input type="text" name="location" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br>

        <label for="max_participants">Max Participants:</label>
        <input type="number" name="max_participants" required><br>

        <label for="image_url">Event Image (optional):</label>
        <input type="file" name="image_url" accept="image/*"><br> 

        <button type="submit">Create Event</button>
    </form>

    <p><a href="event_management.php"><button type="button">See All Events</button></a></p>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $event_name = $_POST['event_name'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $location = $_POST['location'];
        $description = $_POST['description'];
        $max_participants = $_POST['max_participants'];
        $available_slots = $max_participants;

        // Handle file upload
        $image_name = null; 
        if (!empty($_FILES['image_url']['name'])) {
            $target_dir = "uploads/";
            $image_name = basename($_FILES['image_url']['name']); 
            $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $valid_extensions = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'bmp', 'gif'];

            if (in_array($file_ext, $valid_extensions)) {
                if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_dir . $image_name)) {
                    // Successfully uploaded image
                } else {
                    echo "Error uploading the image.";
                }
            } else {
                echo "Invalid file type. Please upload a valid image file.";
                exit; 
            }
        }

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO Events (event_name, date, time, location, description, max_participants, available_slots, image_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'open')");
        $stmt->bind_param("sssssiis", $event_name, $date, $time, $location, $description, $max_participants, $available_slots, $image_name);

        if ($stmt->execute()) {
            header("Location: event_management.php");
            exit; 
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
    ?>
</body>
</html>
