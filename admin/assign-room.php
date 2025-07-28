<?php
// assign-room.php

// Enable error reporting for debugging (development only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Require DB connection
require_once('../includes/db.php');
header('Content-Type: application/json');

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Read and validate JSON input
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid or empty JSON payload.']);
    exit;
}

// Extract and validate required fields
$guest_id   = isset($data['guest_id']) ? (int)$data['guest_id'] : 0;
$booking_id = isset($data['booking_id']) ? (int)$data['booking_id'] : 0;
$room_id    = isset($data['room_id']) ? (int)$data['room_id'] : 0;
$force      = isset($data['force']) && $data['force'] === true;

if ($guest_id <= 0 || $booking_id <= 0 || $room_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

// Step 1: Check current room assignment
$currentRoomSql = "SELECT room_id FROM guest_lists WHERE id = ? AND booking_id = ?";
$stmt = $conn->prepare($currentRoomSql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error (room check): ' . $conn->error]);
    exit;
}
$stmt->bind_param('ii', $guest_id, $booking_id);
$stmt->execute();
$stmt->bind_result($existing_room_id);
$stmt->fetch();
$stmt->close();

if (!empty($existing_room_id) && $existing_room_id != $room_id && !$force) {
    echo json_encode([
        'success' => false,
        'requires_confirmation' => true,
        'message' => 'Guest is already assigned to a different room. Reassign?'
    ]);
    exit;
}

// Step 2: Check if room exists and is available
$roomCheckSql = "SELECT is_occupied, room_number FROM rooms WHERE id = ?";
$stmt = $conn->prepare($roomCheckSql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error (room fetch): ' . $conn->error]);
    exit;
}
$stmt->bind_param('i', $room_id);
$stmt->execute();
$stmt->bind_result($is_occupied, $room_number);
if (!$stmt->fetch()) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Selected room not found.']);
    exit;
}
$stmt->close();

if ($is_occupied == 1 && $existing_room_id != $room_id) {
    echo json_encode(['success' => false, 'message' => "Room $room_number is already occupied."]);
    exit;
}

// Step 2.5: Validate room type matches
$validateRoomTypeSql = "
    SELECT r.room_type_id, gl.room_type_id
    FROM rooms r
    JOIN guest_lists gl ON gl.id = ?
    WHERE r.id = ?
";
$stmt = $conn->prepare($validateRoomTypeSql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error (room type match): ' . $conn->error]);
    exit;
}
$stmt->bind_param('ii', $guest_id, $room_id);
$stmt->execute();
$stmt->bind_result($room_room_type_id, $guest_room_type_id);
if (!$stmt->fetch()) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Room or guest not found.']);
    exit;
}
$stmt->close();

if ($room_room_type_id != $guest_room_type_id) {
    echo json_encode([
        'success' => false,
        'message' => 'This room type does not match the guest\'s assigned booking room type.'
    ]);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Step 3: Unassign old room if different
    if (!empty($existing_room_id) && $existing_room_id != $room_id) {
        $sql = "UPDATE rooms SET is_occupied = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Failed to prepare unassign old room: " . $conn->error);
        $stmt->bind_param('i', $existing_room_id);
        $stmt->execute();
        $stmt->close();
    }

    // Step 4: Assign new room
    $sql = "UPDATE guest_lists SET room_id = ? WHERE id = ? AND booking_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Failed to assign new room: " . $conn->error);
    $stmt->bind_param('iii', $room_id, $guest_id, $booking_id);
    $stmt->execute();
    $stmt->close();

    // Step 5: Mark room as occupied
    $sql = "UPDATE rooms SET is_occupied = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Failed to mark room as occupied: " . $conn->error);
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    $stmt->close();

    // Step 6: Update check-in status in guest_lists
    $sql = "UPDATE guest_lists SET check_in_status = 'checked_in' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Failed to update guest check-in: " . $conn->error);
    $stmt->bind_param('i', $guest_id);
    $stmt->execute();
    $stmt->close();

    // Step 7: Check if guest is the booker, update booking if yes
    $sql = "SELECT is_booker FROM guest_lists WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Failed to check if booker: " . $conn->error);
    $stmt->bind_param('i', $guest_id);
    $stmt->execute();
    $stmt->bind_result($is_booker);
    $stmt->fetch();
    $stmt->close();

    if ($is_booker == 1) {
        $sql = "UPDATE bookings SET check_in_status = 'checked_in' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Failed to update booking check-in: " . $conn->error);
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
        $stmt->close();
    }

    // Commit transaction
    $conn->commit();

    // Step 8: Fetch updated guest name and room number
    $sql = "
        SELECT gl.guest_name, r.room_number 
        FROM guest_lists gl 
        LEFT JOIN rooms r ON gl.room_id = r.id 
        WHERE gl.id = ? LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Failed to fetch updated guest info: " . $conn->error);
    $stmt->bind_param('i', $guest_id);
    $stmt->execute();
    $stmt->bind_result($guest_name, $updated_room_number);
    $guest_name = 'Guest';
    $updated_room_number = '';
    if ($stmt->fetch()) {
        $guest_name = $guest_name ?? 'Guest';
        $updated_room_number = $updated_room_number ?? '';
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'room_id' => $room_id,
        'room_number' => $updated_room_number,
        'guest_name' => $guest_name,
        'message' => "$guest_name has been assigned to Room $updated_room_number and checked in successfully."
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Room assignment failed: ' . $e->getMessage()
    ]);
}
