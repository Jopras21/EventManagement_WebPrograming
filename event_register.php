<?php
session_start();
require_once('db-user.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session
$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : ''; // 'register' atau 'cancel'

// Cek apakah event valid
$sql = "SELECT available_slots FROM events WHERE event_id = :event_id";
$stmt = $dbu->prepare($sql);
$stmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if ($event) {
    $dbu->beginTransaction();
    try {
        if ($action === 'register') {
            $checkSql = "SELECT * FROM participate WHERE event_id = :event_id AND user_id = :user_id";
            $checkStmt = $dbu->prepare($checkSql);
            $checkStmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
            $checkStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $checkStmt->execute();
            $participation = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$participation && $event['available_slots'] > 0) {
                // User belum terdaftar dan slot tersedia, lakukan pendaftaran
                $insertSql = "INSERT INTO participate (event_id, user_id) 
                              VALUES (:event_id, :user_id)";
                $insertStmt = $dbu->prepare($insertSql);
                $insertStmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
                $insertStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $insertStmt->execute();

                // Decrement available_slots
                $updateSql = "UPDATE events SET available_slots = available_slots - 1 WHERE event_id = :event_id";
                $updateStmt = $dbu->prepare($updateSql);
                $updateStmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
                $updateStmt->execute();

                echo "Pendaftaran berhasil!";
            } else {
                echo "Anda sudah terdaftar atau event sudah penuh.";
            }
        } elseif ($action === 'cancel') {
            // Cek apakah user terdaftar pada event
            $checkSql = "SELECT * FROM participate WHERE event_id = :event_id AND user_id = :user_id";
            $checkStmt = $dbu->prepare($checkSql);
            $checkStmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
            $checkStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $checkStmt->execute();
            $participation = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($participation) {
                // Hapus partisipasi user dari tabel participate
                $deleteSql = "DELETE FROM participate WHERE event_id = :event_id AND user_id = :user_id";
                $deleteStmt = $dbu->prepare($deleteSql);
                $deleteStmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
                $deleteStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $deleteStmt->execute();

                // Increment available_slots
                $updateSql = "UPDATE events SET available_slots = available_slots + 1 WHERE event_id = :event_id";
                $updateStmt = $dbu->prepare($updateSql);
                $updateStmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
                $updateStmt->execute();

                echo "Pendaftaran berhasil dibatalkan.";
            } else {
                echo "Anda tidak terdaftar pada event ini.";
            }
        }

        $dbu->commit();
    } catch (Exception $e) {
        $dbu->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Event tidak ditemukan.";
}
?>
