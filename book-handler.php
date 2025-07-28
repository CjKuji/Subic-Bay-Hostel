
<?php
session_start();
require_once 'includes/db.php';

// Get form input
$full_name       = trim($_POST['full_name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$gender          = $_POST['gender'] ?? '';
$room_type_id    = $_POST['room_type'] ?? '';
$check_in        = $_POST['check_in'] ?? '';
$check_out       = $_POST['check_out'] ?? '';
$companion_count = (int) ($_POST['total_guests'] ?? 0);
$agree           = isset($_POST['agree']);
$message_field   = trim($_POST['message_field'] ?? '');
$errors          = [];
$companions      = [];

$_SESSION['old'] = $_POST;

// Validate main guest
if (!$full_name) $errors[] = "❌ Full name is required.";
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "❌ A valid email is required.";
if (!$phone || !preg_match('/^(\+639|09)\d{9}$/', $phone)) $errors[] = "❌ A valid phone number is required.";
if (!$gender) $errors[] = "❌ Gender is required.";
if (!$room_type_id || !is_numeric($room_type_id)) $errors[] = "❌ Please select a valid room type.";
if (!$check_in || !$check_out) $errors[] = "❌ Check-in and check-out dates are required.";
if (!$agree) $errors[] = "❌ You must agree to the terms and conditions.";
if ($companion_count < 0) $errors[] = "❌ Guest companion count must be zero or more.";

// Validate date logic
$checkInDate  = DateTime::createFromFormat('Y-m-d', $check_in);
$checkOutDate = DateTime::createFromFormat('Y-m-d', $check_out);
if (!$checkInDate || !$checkOutDate || $checkOutDate <= $checkInDate) {
    $errors[] = "❌ Check-out date must be after check-in.";
}

// Collect companion data
if (isset($_POST['guest_name']) && is_array($_POST['guest_name'])) {
    foreach ($_POST['guest_name'] as $i => $name) {
        $companions[] = [
            'name'       => trim($name),
            'gender'     => $_POST['guest_gender'][$i] ?? '',
            'room_type'  => $_POST['guest_room_type'][$i] ?? ''
        ];
    }
}

// Validate companion count
if (count($companions) !== $companion_count) {
    $errors[] = "❌ Companion guests must match the number you entered.";
}

// Validate each companion
foreach ($companions as $i => $g) {
    if (!$g['name']) $errors[] = "❌ Companion " . ($i + 1) . " name is required.";
    if (!$g['gender']) $errors[] = "❌ Companion " . ($i + 1) . " gender is required.";
    if (!is_numeric($g['room_type'])) {
        $errors[] = "❌ Companion " . ($i + 1) . " has an invalid room type.";
    }
}

// Redirect on error
if (!empty($errors)) {
    $_SESSION['message'] = implode("<br>", $errors);
    $_SESSION['message_type'] = 'error';
    header("Location: book.php");
    exit;
}

try {
    // Check for existing pending booking
    $stmt = $conn->prepare("
        SELECT b.id FROM bookings b
        JOIN users u ON b.user_id = u.id
        WHERE u.email = ? AND b.status = 'pending'
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['pending_booking'] = $email;
        header("Location: book.php");
        exit;
    }
    $stmt->close();

    $conn->begin_transaction();

    // Find or create user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_id = $result->fetch_assoc()['id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, gender) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $phone, $gender);
        $stmt->execute();
        $user_id = $stmt->insert_id;
    }
    $stmt->close();

    // Count rooms by type
    $roomCounts = [];
    $roomCounts[$room_type_id] = 1;

    foreach ($companions as $g) {
        $rt_id = $g['room_type'];
        if (!isset($roomCounts[$rt_id])) $roomCounts[$rt_id] = 0;
        $roomCounts[$rt_id]++;
    }

    arsort($roomCounts);
    $primary_room_type_id = array_key_first($roomCounts);
    $total_rooms  = array_sum($roomCounts);
    $total_guests = $companion_count + 1;

    // Insert into bookings
    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, room_type_id, check_in, check_out, number_of_rooms, No_of_guests, special_request, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->bind_param("iisssis", $user_id, $primary_room_type_id, $check_in, $check_out, $total_rooms, $total_guests, $message_field);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // Current timestamp
    $created_at = date('Y-m-d H:i:s');

    // Insert booker into guest_lists (with email + phone + created_at)
    $stmt = $conn->prepare("
        INSERT INTO guest_lists (booking_id, guest_name, gender, room_type_id, check_in, check_out, is_booker, email, phone, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?)
    ");
    $stmt->bind_param("ississsss", $booking_id, $full_name, $gender, $room_type_id, $check_in, $check_out, $email, $phone, $created_at);
    $stmt->execute();
    $stmt->close();

    // Insert companions into guest_lists (with created_at)
    $stmt = $conn->prepare("
        INSERT INTO guest_lists (booking_id, guest_name, gender, room_type_id, check_in, check_out, is_booker, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 0, ?)
    ");
    foreach ($companions as $g) {
        $guest_name = $g['name'];
        $guest_gender = $g['gender'];
        $guest_room_type_id = $g['room_type'];
        $stmt->bind_param("ississs", $booking_id, $guest_name, $guest_gender, $guest_room_type_id, $check_in, $check_out, $created_at);
        $stmt->execute();
    }
    $stmt->close();

    $conn->commit();
    unset($_SESSION['old']);
    $_SESSION['booking_success'] = true;
    header("Location: book.php");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'] = "❌ Booking failed: " . $e->getMessage();
    $_SESSION['message_type'] = 'error';
    header("Location: book.php");
    exit;
}