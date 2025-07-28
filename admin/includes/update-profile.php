<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['flash'] = 'Unauthorized access.';
    header("Location: ../login.php");
    exit;
}

$adminId = $_SESSION['admin_id'];
$username = trim($_POST['username'] ?? '');
$newPassword = $_POST['new_password'] ?? '';
$profilePicture = $_FILES['profile_picture'] ?? null;

$currentProfile = $_SESSION['admin_profile'] ?? 'assets/images/default-profile.png';
$uploadDir = '../../uploads/';
$profilePath = $currentProfile;

try {
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Handle profile image upload
    if ($profilePicture && $profilePicture['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($profilePicture['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed)) {
            $filename = uniqid('profile_', true) . '.' . $ext;
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($profilePicture['tmp_name'], $targetPath)) {
                // Delete old image if not default
                $oldPath = '../../' . $currentProfile;
                if (file_exists($oldPath) && strpos($currentProfile, 'default-profile.png') === false) {
                    unlink($oldPath);
                }

                $profilePath = 'uploads/' . $filename; // Save relative path
            } else {
                $_SESSION['flash'] = 'Failed to upload profile image.';
            }
        } else {
            $_SESSION['flash'] = 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.';
        }
    }

    // Prepare SQL update
    $sql = "UPDATE admin SET username = ?, profile_image = ?";
    $params = [$username, $profilePath];
    $types = 'ss';

    if (!empty($newPassword)) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[] = $hashed;
        $types .= 's';
    }

    $sql .= " WHERE id = ?";
    $params[] = $adminId;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // ✅ Update session
        $_SESSION['admin'] = $username;
        $_SESSION['admin_profile'] = $profilePath;
        $_SESSION['flash'] = 'Profile updated successfully.';
    } else {
        $_SESSION['flash'] = 'Failed to update profile.';
    }

    $stmt->close();

} catch (Exception $e) {
    $_SESSION['flash'] = 'Error: ' . $e->getMessage();
}

$redirectBack = $_SERVER['HTTP_REFERER'] ?? '../includes/header.php';
header("Location: $redirectBack");
exit;