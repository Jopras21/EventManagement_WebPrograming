<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];

$birth_date = '';
$phone_number = '';
$gender = '';
$hobbies = '';
$age = '';
$pfp = '';

$stmt = $conn->prepare("SELECT pfp, tanggal_lahir, nomor_telepon, gender, hobi FROM data_user WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$biodata_data = $stmt->get_result()->fetch_assoc();

if ($biodata_data) {
    $pfp = $biodata_data['pfp'];
    $birth_date = $biodata_data['tanggal_lahir'];
    $phone_number = $biodata_data['nomor_telepon'];
    $gender = $biodata_data['gender'];
    $hobbies = $biodata_data['hobi'];

    if (!empty($birth_date)) {
        $birthDate = new DateTime($birth_date);
        $currentDate = new DateTime();
        $age = $currentDate->diff($birthDate)->y;
    }
}

if ($role === 'user') {
    $stmt = $conn->prepare("SELECT e.event_name, e.date, e.location FROM events e 
                            INNER JOIN participate p ON e.event_id = p.event_id 
                            WHERE p.user_id = ?");
    $stmt->bind_param('i', $user_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    $user_events = [];
    while ($row = $result->fetch_assoc()) {
        $user_events[] = $row;
    }
}

if ($role === 'admin') {
    $stmt = $conn->prepare("SELECT e.event_name, e.date, e.location FROM events e 
                            INNER JOIN participate p ON e.event_id = p.event_id 
                            WHERE p.user_id = ?");
    $stmt->bind_param('i', $user_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    $admin_events = [];
    while ($row = $result->fetch_assoc()) {
        $admin_events[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pfp'])) {
    $foto_name = $_FILES['pfp']['name'];
    $target_file = 'uploads/' . basename($foto_name);

    if (move_uploaded_file($_FILES['pfp']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("UPDATE data_user SET pfp = ? WHERE user_id = ?");
        $stmt->bind_param('si', $target_file, $user_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Profile picture updated successfully!";
            $pfp = $target_file; // Update to the new image path
        } else {
            $_SESSION['message'] = "Error updating database.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pfp'])) {
    $foto_name = $_FILES['pfp']['name'];
    $foto_tmp = $_FILES['pfp']['tmp_name'];
    $target_dir = 'uploads/' . $foto_name;
    $target_file = $target_dir . basename($_FILES["pfp"]["name"]);
    
    // if (move_uploaded_file($_FILES["pfp"]["tmp_name"], $target_file)) {
    //     $stmt = $conn->prepare("UPDATE data_user SET pfp = ? WHERE user_id = ?");
    //     if ($stmt) {
    //         $stmt->bind_param('si', $target_file, $user_id);
    //         if ($stmt->execute()) {
    //             echo "Profile picture updated successfully!";
    //         } else {
    //             echo "Error updating database: " . $stmt->error;
    //         }
    //     } else {
    //         echo "Error preparing statement.";
    //     }
    // } else {
    //     echo "Error uploading file.";
    // }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_pfp'])) {
    // Remove profile picture from the server
    if (file_exists($pfp)) {
        unlink($pfp); 
    }
    
    $stmt = $conn->prepare("UPDATE data_user SET pfp = '' WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            $pfp = '';
            $_SESSION['message'] = "Profile picture deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting profile picture from database.";
        }
    } else {
        $_SESSION['message'] = "Error preparing statement.";
    }
}
?>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert">
        <?php echo $_SESSION['message']; ?>
        <?php unset($_SESSION['message']); // Clear the message after it's displayed ?>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
    <title>User Profile</title>
</head>
<body>
    <div class="container">
        <div class="nav-button">
            <a href="event_management.php">Back</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        <div class="profile-header">
            <div class="profile-image">
            <div class="profile-img">
                <img src="<?php echo htmlspecialchars($pfp); ?>" alt="Profile Picture">
            </div>   
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="pfp" id="pfp">
                    <button type="submit">Upload New Profile Picture</button>
                </form>
                <form method="post">
                    <button type="submit" name="delete_pfp" class="delete">Delete Profile Picture</button>
                </form>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)</h1>
                <p>Email        : <?php echo htmlspecialchars($email); ?></p>
                <?php if ($birth_date): ?>
                    <p>Date of Birth        : <?php echo htmlspecialchars($birth_date); ?></p>
                <?php endif; ?>
                <?php if ($age)     : ?>
                    <p>Age: <?php echo htmlspecialchars($age); ?></p>
                <?php endif; ?>
                <?php if ($phone_number): ?>
                    <p>Phone Number     : <?php echo htmlspecialchars($phone_number); ?></p>
                <?php endif; ?>
                <?php if ($gender): ?>
                    <p>Gender       : <?php echo htmlspecialchars($gender); ?></p>
                <?php endif; ?>
                <?php if ($hobbies): ?>
                    <p>Hobbies      : <?php echo htmlspecialchars($hobbies); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="content">
        <?php if ($role === 'user'): ?>
            <h2>Your Events</h2>
            <ul>
                <?php if (count($user_events) > 0): ?>
                    <?php foreach ($user_events as $event): ?>
                        <li><?php echo htmlspecialchars($event['event_name']); ?> - <?php echo htmlspecialchars($event['date']); ?> at <?php echo htmlspecialchars($event['location']); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You haven't joined any events yet.</p>
                <?php endif; ?>
            </ul>
        <?php elseif ($role === 'admin'): ?>
            <h2>Events You Manage</h2>
            <ul>
                <?php if (count($admin_events) > 0): ?>
                    <?php foreach ($admin_events as $event): ?>
                        <li><?php echo htmlspecialchars($event['event_name']); ?> - <?php echo htmlspecialchars($event['date']); ?> at <?php echo htmlspecialchars($event['location']); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No events to manage yet.</p>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>
