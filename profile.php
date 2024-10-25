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
            $pfp = $target_file;
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
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_pfp'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $birth_date = $_POST['birth_date'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender'];
    $hobbies = $_POST['hobbies'];

    // Update tabel user
    if (!empty($name) && !empty($email)) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("UPDATE user SET name = ?, email = ?, password = ? WHERE user_id = ?");
            $stmt->bind_param('sssi', $name, $email, $hashed_password, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE user SET name = ?, email = ? WHERE user_id = ?");
            $stmt->bind_param('ssi', $name, $email, $user_id);
        }

        // Eksekusi update untuk tabel user
        if ($stmt->execute()) {
            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['username'] = $name;
            $_SESSION['email'] = $email;
        } else {
            $_SESSION['message'] = "Error updating profile.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all required fields.";
    }

    if (!empty($birth_date) || !empty($phone_number) || !empty($gender) || !empty($hobbies)) {
        $stmt = $conn->prepare("UPDATE data_user SET tanggal_lahir = ?, nomor_telepon = ?, gender = ?, hobi = ? WHERE user_id = ?");
        $stmt->bind_param('ssssi', $birth_date, $phone_number, $gender, $hobbies, $user_id);

        if ($stmt->execute()) {
            $_SESSION['message'] .= " Biodata updated successfully!";
        } else {
            $_SESSION['message'] .= " Error updating biodata.";
        }
    }

    header('Location: profile.php');
    exit();
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
                <img src="<?php echo htmlspecialchars($pfp); ?>" alt="Profile Picture">
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="pfp" id="pfp">
                    <button type="submit">Upload New Profile Picture</button>
                </form>
                <form method="post">
                    <button type="submit" name="delete_pfp" class="delete">Delete Profile Picture</button>
                </form>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($username); ?></h1>
                <p>Email: <?php echo htmlspecialchars($email); ?></p>
                <p>Birth Date: <?php echo htmlspecialchars($birth_date); ?></p>
                <p>Phone: <?php echo htmlspecialchars($phone_number); ?></p>
                <p>Gender: <?php echo htmlspecialchars($gender); ?></p>
                <p>Hobbies: <?php echo htmlspecialchars($hobbies); ?></p>
                <button class="edit-button" onclick="openModal()">Edit Profile</button>
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
<!-- Modal Structure -->
<div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="closeModal()">&times;</span>
                <h2>Edit Profile</h2>
                <form method="post" action="edit_profile.php">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($username); ?>" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <label for="password">Password (Leave blank if not changing):</label>
                    <input type="password" id="password" name="password">
                    <label for="birth_date">Birth Date:</label>
                    <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($birth_date); ?>">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender">
                        <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $gender === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <label for="hobbies">Hobbies:</label>
                    <input type="text" id="hobbies" name="hobbies" value="<?php echo htmlspecialchars($hobbies); ?>">
                    <button type="submit" class="save-button">Save Changes</button>
                </form>
            </div>
        </div>

    <script>
        // Show/Hide Edit Form
        document.getElementById('edit-button').addEventListener('click', function() {
            document.getElementById('edit-form').style.display = 'flex';
        });

        function openModal() {
            document.getElementById('editModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeModal();
            }
        }

    </script>

</body>
</html>
