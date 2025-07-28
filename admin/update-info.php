<?php
require_once('../includes/db.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
    exit;
}

$bookingId   = isset($data['booking_id']) ? (int)$data['booking_id'] : 0;
$guestId     = isset($data['guest_id']) ? (int)$data['guest_id'] : 0;
$isBooker    = isset($data['is_booker']) ? (int)$data['is_booker'] : -1;
$checkedOut  = isset($data['checked_out']) && $data['checked_out'] === true;

if (!in_array($isBooker, [0, 1], true)) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid is_booker flag.']);
    exit;
}

try {
    if ($isBooker === 1 && $bookingId > 0) {
        // --- MAIN BOOKER UPDATE ---
        $stmt = $conn->prepare("
            SELECT u.full_name, u.email, u.phone, u.gender,
                   b.check_in, b.check_out, b.room_type_id
            FROM users u
            JOIN bookings b ON u.id = b.user_id
            WHERE b.id = ?
        ");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$current) {
            echo json_encode(['success' => false, 'message' => 'Booking not found.']);
            exit;
        }

        $updates = [
            'name'         => $data['name'] ?? $current['full_name'],
            'email'        => $data['email'] ?? $current['email'],
            'phone'        => $data['phone'] ?? $current['phone'],
            'gender'       => $data['gender'] ?? $current['gender'],
            'check_in'     => $data['check_in'] ?? $current['check_in'],
            'check_out'    => $data['check_out'] ?? $current['check_out'],
            'room_type_id' => $data['room_type_id'] ?? $current['room_type_id'],
        ];

        // Update `users`
        $stmt = $conn->prepare("
            UPDATE users u
            INNER JOIN bookings b ON u.id = b.user_id
            SET u.full_name = ?, u.email = ?, u.phone = ?, u.gender = ?
            WHERE b.id = ?
        ");
        $stmt->bind_param("ssssi", $updates['name'], $updates['email'], $updates['phone'], $updates['gender'], $bookingId);
        $stmt->execute();
        $stmt->close();

        // Update `bookings`
        $stmt = $conn->prepare("
            UPDATE bookings
            SET check_in = ?, check_out = ?, room_type_id = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssii", $updates['check_in'], $updates['check_out'], $updates['room_type_id'], $bookingId);
        $stmt->execute();
        $stmt->close();

        // Update `guest_lists` (for booker only)
        $stmt = $conn->prepare("
            UPDATE guest_lists
            SET guest_name = ?, email = ?, phone = ?, gender = ?, check_in = ?, check_out = ?, room_type_id = ?
            WHERE booking_id = ? AND is_booker = 1
        ");
        $stmt->bind_param("ssssssii", $updates['name'], $updates['email'], $updates['phone'], $updates['gender'], $updates['check_in'], $updates['check_out'], $updates['room_type_id'], $bookingId);
        $stmt->execute();
        $stmt->close();

        // If booker checked out
        if ($checkedOut) {
            $stmt = $conn->prepare("UPDATE bookings SET check_in_status = 'checked_out' WHERE id = ?");
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE guest_lists SET check_in_status = 'checked_out' WHERE booking_id = ? AND is_booker = 1");
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();
            $stmt->close();
        }

    } elseif ($isBooker === 0 && $guestId > 0) {
        // --- GUEST UPDATE ---
        $stmt = $conn->prepare("
            SELECT guest_name, email, phone, gender, check_in, check_out, room_type_id, is_booker
            FROM guest_lists
            WHERE id = ?
        ");
        $stmt->bind_param("i", $guestId);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$current || (int)$current['is_booker'] !== 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid guest ID or not a companion.']);
            exit;
        }

        $updates = [
            'name'         => $data['name'] ?? $current['guest_name'],
            'email'        => $data['email'] ?? $current['email'],
            'phone'        => $data['phone'] ?? $current['phone'],
            'gender'       => $data['gender'] ?? $current['gender'],
            'check_in'     => $data['check_in'] ?? $current['check_in'],
            'check_out'    => $data['check_out'] ?? $current['check_out'],
            'room_type_id' => $data['room_type_id'] ?? $current['room_type_id'],
        ];

        $stmt = $conn->prepare("
            UPDATE guest_lists
            SET guest_name = ?, email = ?, phone = ?, gender = ?, check_in = ?, check_out = ?, room_type_id = ?
            WHERE id = ? AND is_booker = 0
        ");
        $stmt->bind_param("ssssssii", $updates['name'], $updates['email'], $updates['phone'], $updates['gender'], $updates['check_in'], $updates['check_out'], $updates['room_type_id'], $guestId);
        $stmt->execute();
        $stmt->close();

        if ($checkedOut) {
            $stmt = $conn->prepare("UPDATE guest_lists SET check_in_status = 'checked_out' WHERE id = ?");
            $stmt->bind_param("i", $guestId);
            $stmt->execute();
            $stmt->close();
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Missing or invalid guest ID or booking ID.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Guest information updated successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
}
