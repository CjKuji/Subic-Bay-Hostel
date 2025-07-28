<?php
session_start();
require '../includes/db.php';
require 'includes/auth.php';

header('Content-Type: application/json');

// Normalize file paths
function cleanPath($path)
{
    $base = '/subic-bay-hostel/';
    return $base . ltrim(str_replace(['\\', '//'], '/', $path), '/');
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // === FETCH IMAGES (Main Thumbnail + Gallery) ===
    $roomTypeId = isset($_GET['room_type_id']) ? (int)$_GET['room_type_id'] : 0;
    if ($roomTypeId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid room type ID.']);
        exit;
    }

    $images = [];

    // Get main thumbnail
    $stmt = $conn->prepare("SELECT image FROM room_types WHERE id = ?");
    $stmt->bind_param('i', $roomTypeId);
    $stmt->execute();
    $stmt->bind_result($mainImage);
    if ($stmt->fetch() && !empty($mainImage)) {
        $images[] = [
            'id' => 0,
            'url' => cleanPath($mainImage),
            'type' => 'main'
        ];
    }
    $stmt->close();

    // Get gallery images
    $stmt = $conn->prepare("SELECT id, image_path FROM room_images WHERE room_type_id = ?");
    $stmt->bind_param('i', $roomTypeId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $images[] = [
            'id' => (int)$row['id'],
            'url' => cleanPath($row['image_path']),
            'type' => 'gallery'
        ];
    }
    $stmt->close();

    echo json_encode(['images' => $images]);
    exit;
} elseif ($method === 'POST' && isset($_FILES['image'])) {
    // === REPLACE THUMBNAIL ===
    $roomTypeId = isset($_POST['room_type_id']) ? (int)$_POST['room_type_id'] : 0;
    if ($roomTypeId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid room type ID.']);
        exit;
    }

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No valid image uploaded.']);
        exit;
    }

    $uploadDir = realpath(__DIR__ . '/../assets/images/rooms') . '/';
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    $tmpFile = $_FILES['image']['tmp_name'];
    $mime = mime_content_type($tmpFile);
    if (!isset($allowedTypes[$mime])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unsupported image type.']);
        exit;
    }

    // Fetch and delete old image
    $stmt = $conn->prepare("SELECT image FROM room_types WHERE id = ?");
    $stmt->bind_param("i", $roomTypeId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows !== 1) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Room type not found.']);
        exit;
    }
    $old = $result->fetch_assoc();
    $oldPath = realpath(__DIR__ . '/../' . $old['image']);
    if ($oldPath && file_exists($oldPath)) {
        @unlink($oldPath);
    }
    $stmt->close();

    // Save new image
    $ext = $allowedTypes[$mime];
    $newName = uniqid('thumb_', true) . '.' . $ext;
    $newPath = $uploadDir . $newName;
    $relativePath = 'assets/images/rooms/' . $newName;

    if (!move_uploaded_file($tmpFile, $newPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
        exit;
    }

    // Update DB
    $update = $conn->prepare("UPDATE room_types SET image = ? WHERE id = ?");
    $update->bind_param('si', $relativePath, $roomTypeId);
    if ($update->execute()) {
        echo json_encode(['success' => true, 'message' => 'Thumbnail replaced successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update thumbnail in database.']);
    }
    $update->close();
    exit;
} elseif ($method === 'POST') {
    // === DELETE IMAGES ===
    $data = json_decode(file_get_contents('php://input'), true);
    $baseDir = realpath(__DIR__ . '/../');

    // MULTIPLE GALLERY DELETE
    if (isset($data['image_ids']) && is_array($data['image_ids'])) {
        $imageIds = array_map('intval', $data['image_ids']);
        if (empty($imageIds)) {
            http_response_code(400);
            echo json_encode(['error' => 'No image IDs provided.']);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($imageIds), '?'));
        $stmt = $conn->prepare("SELECT id, image_path FROM room_images WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($imageIds)), ...$imageIds);
        $stmt->execute();
        $result = $stmt->get_result();

        $pathsToDelete = [];
        while ($row = $result->fetch_assoc()) {
            $pathsToDelete[$row['id']] = $row['image_path'];
        }
        $stmt->close();

        foreach ($pathsToDelete as $id => $path) {
            $fullPath = $baseDir . '/' . ltrim($path, '/');
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $stmt = $conn->prepare("DELETE FROM room_images WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($imageIds)), ...$imageIds);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete images from database.']);
            exit;
        }
        $stmt->close();

        echo json_encode(['success' => true, 'message' => 'Selected images deleted successfully.']);
        exit;
    }

    // DELETE MAIN THUMBNAIL
    $imageId = isset($data['id']) ? (int)$data['id'] : null;
    if (!isset($imageId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Image ID is required.']);
        exit;
    }

    if ($imageId === 0) {
        $roomTypeId = isset($data['room_type_id']) ? (int)$data['room_type_id'] : 0;
        if ($roomTypeId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing room_type_id for thumbnail deletion.']);
            exit;
        }

        $stmt = $conn->prepare("SELECT image FROM room_types WHERE id = ?");
        $stmt->bind_param('i', $roomTypeId);
        $stmt->execute();
        $stmt->bind_result($imagePath);
        $stmt->fetch();
        $stmt->close();

        if (!$imagePath) {
            http_response_code(404);
            echo json_encode(['error' => 'Thumbnail image not found.']);
            exit;
        }

        $fullPath = $baseDir . '/' . ltrim($imagePath, '/');
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $stmt = $conn->prepare("UPDATE room_types SET image = NULL WHERE id = ?");
        $stmt->bind_param('i', $roomTypeId);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to clear thumbnail in database.']);
            exit;
        }
        $stmt->close();

        echo json_encode(['success' => true, 'message' => 'Thumbnail deleted successfully.']);
        exit;
    }

    // DELETE SINGLE GALLERY IMAGE
    if ($imageId > 0) {
        $stmt = $conn->prepare("SELECT image_path FROM room_images WHERE id = ?");
        $stmt->bind_param('i', $imageId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Gallery image not found.']);
            exit;
        }

        $row = $result->fetch_assoc();
        $imagePath = $row['image_path'];
        $stmt->close();

        $fullPath = $baseDir . '/' . ltrim($imagePath, '/');
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $stmt = $conn->prepare("DELETE FROM room_images WHERE id = ?");
        $stmt->bind_param('i', $imageId);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete gallery image from database.']);
            exit;
        }
        $stmt->close();

        echo json_encode(['success' => true, 'message' => 'Gallery image deleted successfully.']);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Unsupported request method.']);
}
