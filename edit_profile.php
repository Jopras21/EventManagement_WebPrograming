<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Proses pembaruan profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $birth_date = $_POST['birth_date'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender'];
    $hobbies = $_POST['hobbies'];

    // Update tabel `user`
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

    // Update tabel `data_user`
    if (!empty($birth_date) || !empty($phone_number) || !empty($gender) || !empty($hobbies)) {
        $stmt = $conn->prepare("UPDATE data_user SET tanggal_lahir = ?, nomor_telepon = ?, gender = ?, hobi = ? WHERE user_id = ?");
        $stmt->bind_param('ssssi', $birth_date, $phone_number, $gender, $hobbies, $user_id);

        if ($stmt->execute()) {
            $_SESSION['message'] .= " Biodata updated successfully!";
        } else {
            $_SESSION['message'] .= " Error updating biodata.";
        }
    }

    // Update tabel `data_user`
if (!empty($birth_date) || !empty($phone_number) || !empty($gender) || !empty($hobbies)) {
    // Cek apakah data sudah ada untuk user_id tersebut
    $check = $conn->prepare("SELECT user_id FROM data_user WHERE user_id = ?");
    $check->bind_param('i', $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Jika data sudah ada, lakukan UPDATE
        $stmt = $conn->prepare("UPDATE data_user SET tanggal_lahir = ?, nomor_telepon = ?, gender = ?, hobi = ? WHERE user_id = ?");
        $stmt->bind_param('ssssi', $birth_date, $phone_number, $gender, $hobbies, $user_id);

        if ($stmt->execute()) {
            $_SESSION['message'] .= " Biodata updated successfully!";
        } else {
            $_SESSION['message'] .= " Error updating biodata.";
        }
    } else {
        // Jika data belum ada, lakukan INSERT
        $stmt = $conn->prepare("INSERT INTO data_user (user_id, tanggal_lahir, nomor_telepon, gender, hobi) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $user_id, $birth_date, $phone_number, $gender, $hobbies);

        if ($stmt->execute()) {
            $_SESSION['message'] .= " Biodata added successfully!";
        } else {
            $_SESSION['message'] .= " Error adding biodata.";
        }
    }
}

    // Redirect kembali ke halaman profile
    header('Location: profile.php');
    exit();
}
?>
