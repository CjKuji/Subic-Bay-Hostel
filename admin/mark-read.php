<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Only mark as read if not already responded
    $stmt = $conn->prepare("UPDATE contact_messages SET read_at = NOW() WHERE id = ? AND responded_at IS NULL");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed']);
        exit;
    }

    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Execute failed']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
