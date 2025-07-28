<?php
session_start();
require '../includes/db.php';
require 'includes/auth.php';

header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

// Validate room type ID
$roomTypeId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($roomTypeId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing room type ID.']);
    exit;
}

// Sanitize inputs
$title          = trim($_POST['title'] ?? '');
$description    = trim($_POST['description'] ?? '');
$price          = floatval($_POST['price'] ?? 0);
$capacity       = trim($_POST['capacity'] ?? '');
$inclusions     = trim($_POST['inclusions'] ?? '');
$alsoAvailable  = trim($_POST['also_available'] ?? '');

// Validate essential fields
if ($title === '' || $price <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Title and valid price are required.']);
    exit;
}

// Update room_types record (excluding image for now)
$stmt = $conn->prepare("
    UPDATE room_types SET
        title = ?, description = ?, price = ?, capacity = ?,
        inclusions = ?, also_available = ?
    WHERE id = ?
");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare update statement.']);
    exit;
}
$stmt->bind_param('ssdsssi', $title, $description, $price, $capacity, $inclusions, $alsoAvailable, $roomTypeId);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update room type: ' . $stmt->error]);
    $stmt->close();
    exit;
}
$stmt->close();

// Upload settings
$uploadDir = realpath(__DIR__ . '/../assets/images/rooms') . '/';
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];

$errors = [];
$successCount = 0;

// Process thumbnail image (optional, with deletion of old image)
if (!empty($_FILES['thumbnail']['name'])) {
    $thumbTmp   = $_FILES['thumbnail']['tmp_name'];
    $thumbName  = $_FILES['thumbnail']['name'];
    $thumbError = $_FILES['thumbnail']['error'];

    if ($thumbError === UPLOAD_ERR_OK) {
        $thumbMime = mime_content_type($thumbTmp);
        if (!isset($allowedTypes[$thumbMime])) {
            $errors[] = "Unsupported thumbnail file type.";
        } else {
            // Delete old thumbnail (if any)
            $result = $conn->query("SELECT image FROM room_types WHERE id = $roomTypeId LIMIT 1");
            if ($result && $old = $result->fetch_assoc()) {
                $oldImagePath = realpath(__DIR__ . '/../' . $old['image']);
                if ($oldImagePath && file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }

            // Upload new thumbnail
            $thumbExt = $allowedTypes[$thumbMime];
            $thumbFileName = uniqid('thumb_', true) . '.' . $thumbExt;
            $thumbDestination = $uploadDir . $thumbFileName;

            if (move_uploaded_file($thumbTmp, $thumbDestination)) {
                $thumbRelativePath = 'assets/images/rooms/' . $thumbFileName;

                $stmt = $conn->prepare("UPDATE room_types SET image = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param('si', $thumbRelativePath, $roomTypeId);
                    if (!$stmt->execute()) {
                        $errors[] = "Failed to update thumbnail image: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $errors[] = "Database error while updating thumbnail.";
                }
            } else {
                $errors[] = "Failed to move thumbnail upload.";
            }
        }
    } else {
        $errors[] = "Thumbnail upload error: code $thumbError.";
    }
}

// Process gallery images (optional)
if (!empty($_FILES['images']['name']) && is_array($_FILES['images']['name'])) {
    $imageFiles = $_FILES['images'];
    for ($i = 0; $i < count($imageFiles['name']); $i++) {
        $tmpName = $imageFiles['tmp_name'][$i];
        $fileName = $imageFiles['name'][$i];
        $fileError = $imageFiles['error'][$i];

        if (empty($fileName)) continue;
        if ($fileError !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading $fileName (code $fileError).";
            continue;
        }

        $mime = mime_content_type($tmpName);
        if (!isset($allowedTypes[$mime])) {
            $errors[] = "Unsupported type for $fileName.";
            continue;
        }

        $ext = $allowedTypes[$mime];
        $newName = uniqid('roomimg_', true) . '.' . $ext;
        $destination = $uploadDir . $newName;

        if (!move_uploaded_file($tmpName, $destination)) {
            $errors[] = "Failed to move $fileName.";
            continue;
        }

        $relativePath = 'assets/images/rooms/' . $newName;
        $stmt = $conn->prepare("INSERT INTO room_images (room_type_id, image_path) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param('is', $roomTypeId, $relativePath);
            if ($stmt->execute()) {
                $successCount++;
            } else {
                $errors[] = "Insert failed for $fileName: " . $stmt->error;
                unlink($destination);
            }
            $stmt->close();
        } else {
            $errors[] = "DB prepare failed for $fileName.";
            unlink($destination);
        }
    }
}

// Final response
echo json_encode([
    'success' => true,
    'message' => 'Room type updated successfully.' . ($successCount ? " $successCount gallery image(s) uploaded." : ''),
    'errors' => $errors
]);
