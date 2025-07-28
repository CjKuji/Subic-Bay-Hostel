<?php
// No session needed
include('../includes/db.php');

$today = date('Y-m-d');

// Fetch expired confirmed bookings
$expiredStmt = $conn->prepare("SELECT * FROM bookings WHERE status = 'confirmed' AND check_out < ?");
$expiredStmt->bind_param("s", $today);
$expiredStmt->execute();
$expiredResult = $expiredStmt->get_result();

while ($expired = $expiredResult->fetch_assoc()) {
    $conn->begin_transaction();

    try {
        $insertLog = $conn->prepare("
            INSERT INTO booking_logs (
                user_id, room_id, room_type_id, check_in, check_out, 
                adults, children, number_of_rooms, special_request, created_at, moved_at
            ) VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $insertLog->bind_param(
            "iissiiiss",
            $expired['user_id'],
            $expired['room_type_id'],
            $expired['check_in'],
            $expired['check_out'],
            $expired['adults'],
            $expired['children'],
            $expired['number_of_rooms'],
            $expired['special_request'],
            $expired['created_at']
        );
        $insertLog->execute();
        $insertLog->close();

        $delete = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $delete->bind_param("i", $expired['id']);
        $delete->execute();
        $delete->close();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        error_log("CRON Archiving failed for booking ID {$expired['id']}: " . $e->getMessage());
    }
}
