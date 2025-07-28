bookings.php
bookings.php
<?php
session_start();
require '../vendor/autoload.php';
include('../includes/db.php');
include('includes/auth.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Helper: Fetch complete guest list for a booking (primary guest + companions)
function fetchGuestList($conn, $bookingId)
{
  $guestList = [];

  // Get primary guest info from booking and users table
  $stmt = $conn->prepare("
    SELECT u.full_name as guest_name, u.gender, rt.title AS room_type, 'primary' as guest_type
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    LEFT JOIN room_types rt ON b.room_type_id = rt.id
    WHERE b.id = ?
  ");
  $stmt->bind_param("i", $bookingId);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($primaryGuest = $result->fetch_assoc()) {
    $guestList[] = $primaryGuest;
  }
  $stmt->close();

  // Get companions from guest_lists table
  $stmt = $conn->prepare("
    SELECT gl.guest_name, gl.gender, rt.title AS room_type, 'companion' as guest_type
    FROM guest_lists gl
    LEFT JOIN room_types rt ON gl.room_type_id = rt.id
    WHERE gl.booking_id = ?
    ORDER BY gl.id ASC
  ");
  $stmt->bind_param("i", $bookingId);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($companion = $result->fetch_assoc()) {
    $guestList[] = $companion;
  }
  $stmt->close();

  return $guestList;
}

// Helper: Get room type breakdown for a booking
function getRoomTypeBreakdown($conn, $bookingId, $primaryRoomTypeId)
{
  $roomCounts = [];

  // Start with primary guest room
  $stmt = $conn->prepare("SELECT title FROM room_types WHERE id = ?");
  $stmt->bind_param("i", $primaryRoomTypeId);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($row = $result->fetch_assoc()) {
    $roomCounts[$primaryRoomTypeId] = [
      'title' => $row['title'],
      'count' => 1  // Primary guest gets 1 room
    ];
  }
  $stmt->close();

  // Add companion rooms from guest_lists table
  $stmt = $conn->prepare("
    SELECT gl.room_type_id, rt.title, COUNT(*) as count
    FROM guest_lists gl
    LEFT JOIN room_types rt ON gl.room_type_id = rt.id
    WHERE gl.booking_id = ?
    GROUP BY gl.room_type_id, rt.title
  ");
  $stmt->bind_param("i", $bookingId);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $roomTypeId = $row['room_type_id'];
    if (isset($roomCounts[$roomTypeId])) {
      // Add to existing count
      $roomCounts[$roomTypeId]['count'] += $row['count'];
    } else {
      // New room type
      $roomCounts[$roomTypeId] = [
        'title' => $row['title'],
        'count' => $row['count']
      ];
    }
  }
  $stmt->close();

  return $roomCounts;
}

// Helper: Get all bookings data
function getAllBookings($conn)
{
  $bookings = [];
  $result = $conn->query("
    SELECT b.*, u.full_name, u.email, u.phone, u.gender, rt.title AS room_type, rt.price AS room_price
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.id
    LEFT JOIN room_types rt ON b.room_type_id = rt.id
    WHERE b.status != 'confirmed'
    ORDER BY b.created_at DESC
  ");
  while ($row = $result->fetch_assoc()) {
    // Get complete guest list (primary + companions)
    $row['guests'] = fetchGuestList($conn, $row['id']);

    // Separate primary guest and companions for easier access
    $row['primary_guest'] = null;
    $row['companions'] = [];

    foreach ($row['guests'] as $guest) {
      if ($guest['guest_type'] === 'primary') {
        $row['primary_guest'] = $guest;
      } else {
        $row['companions'][] = $guest;
      }
    }

    // Get room type breakdown
    $row['room_breakdown'] = getRoomTypeBreakdown($conn, $row['id'], $row['room_type_id']);

    // Calculate total cost
    $nights = max(1, (strtotime($row['check_out']) - strtotime($row['check_in'])) / 86400);
    $totalCost = 0;
    foreach ($row['room_breakdown'] as $roomData) {
      $totalCost += $roomData['count'] * $row['room_price'] * $nights;
    }
    $row['total_cost'] = $totalCost;
    $row['nights'] = $nights;

    $bookings[] = $row;
  }

  return $bookings;
}

// Handle AJAX request for real-time updates
if (isset($_GET['action']) && $_GET['action'] === 'get_bookings') {
  header('Content-Type: application/json');

  $bookings = getAllBookings($conn);

  // Return JSON response with booking data and last update timestamp
  echo json_encode([
    'success' => true,
    'bookings' => $bookings,
    'timestamp' => time(),
    'count' => count($bookings)
  ]);
  exit;
}

// Handle Confirm/Reject Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
  $id = intval($_POST['id']);
  $action = $_POST['action'];
  $status = ($action === 'confirm') ? 'confirmed' : (($action === 'reject') ? 'cancelled' : 'pending');
  $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $status, $id);
  $stmt->execute();
  $stmt->close();
  $stmt = $conn->prepare("
    SELECT u.full_name, u.email, u.phone 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ?
  ");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($full_name, $email, $phone);
  $stmt->fetch();
  $stmt->close();
  // Send Email
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port = SMTP_PORT;
    $mail->setFrom(FROM_EMAIL, FROM_NAME);
    $mail->addAddress($email, $full_name);
    $mail->isHTML(true);
    $mail->Subject = "Booking " . ucfirst($status) . " - Subic Bay Hostel";
    $color = "#DF5219";
    $body = $status === 'cancelled'
      ? "<p>Your booking has been <strong style='color:$color;'>rejected</strong>.</p><p>We apologize for the inconvenience.</p>"
      : "<p>Your booking has been <strong style='color:$color;'>$status</strong>.</p><p>Thank you for choosing us!</p>";
    $mail->Body = "<h3>Dear $full_name,</h3>$body<hr><small>This is an automated message. Please do not reply.</small>";
    $mail->AltBody = "Dear $full_name,\n\nYour booking is $status.";
    $mail->send();
  } catch (Exception $e) {
    error_log("PHPMailer Error: " . $mail->ErrorInfo);
  }
  // Return JSON response for AJAX requests
  if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode([
      'success' => true,
      'message' => "Booking successfully $status.",
      'action' => $action,
      'status' => $status
    ]);
    exit;
  }
  $_SESSION['success_message'] = "Booking successfully $status.";
  $_SESSION['show_modal'] = true;
  $_SESSION['booking_data'] = $id;
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// Load all bookings with enhanced data - EXCLUDING CONFIRMED BOOKINGS
$bookings = getAllBookings($conn);
// Get last success data if available
$successMessage = $_SESSION['success_message'] ?? '';
$showModal = $_SESSION['show_modal'] ?? false;
$bookingToShow = null;
if (isset($_SESSION['booking_data'])) {
  $bookingId = $_SESSION['booking_data'];
  foreach ($bookings as $b) {
    if ($b['id'] == $bookingId) {
      $bookingToShow = $b;
      break;
    }
  }
}
unset($_SESSION['success_message'], $_SESSION['show_modal'], $_SESSION['booking_data']);
?>
<script></script>
<?php include('includes/header.php'); ?>
<!-- Custom Scrollbar + Toast + UI Styles -->
<style>
  /* Custom Scrollbar Styling */
  .custom-scroll {
    overflow-y: auto;
    max-height: 90vh;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 transparent;
    /* Tailwind slate-300 */
  }

  .custom-scroll::-webkit-scrollbar {
    width: 8px;
  }

  .custom-scroll::-webkit-scrollbar-track {
    background: transparent;
  }

  .custom-scroll::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 9999px;
    border: 3px solid transparent;
    background-clip: content-box;
  }

  .custom-scroll:hover::-webkit-scrollbar-thumb {
    background-color: #a0aec0;
    /* Tailwind slate-400 */
  }

  .dark .custom-scroll::-webkit-scrollbar-thumb {
    background-color: #4a5568;
    /* Tailwind gray-700 */
  }

  .dark .custom-scroll:hover::-webkit-scrollbar-thumb {
    background-color: #a0aec0;
  }

  /* Status Badges */
  .status-badge {
    @apply px-2 py-1 text-xs font-medium rounded-full;
  }

  .status-pending {
    @apply bg-yellow-100 text-yellow-800;
  }

  .status-confirmed {
    @apply bg-green-100 text-green-800;
  }

  .status-cancelled {
    @apply bg-red-100 text-red-800;
  }

  /* Fade-in animation for new bookings */
  .booking-row-new {
    animation: fadeInHighlight 2s ease-in-out;
  }

  @keyframes fadeInHighlight {
    0% {
      background-color: #fef3c7;
      transform: translateY(-10px);
    }

    50% {
      background-color: #fef3c7;
    }

    100% {
      background-color: transparent;
      transform: translateY(0);
    }
  }

  /* Toast Notification Positioning */
  #update-notification[data-position="below-search"] {
    top: 70px;
    /* Adjust based on header height */
    right: 20px;
  }

  #update-notification[data-position="top-right"] {
    top: 20px;
    right: 20px;
  }

  #update-notification[data-position="bottom-right"] {
    bottom: 20px;
    right: 20px;
  }

  #update-notification[data-position="bottom-left"] {
    bottom: 20px;
    left: 20px;
  }

  #update-notification[data-position="top-left"] {
    top: 20px;
    left: 20px;
  }

  /* Prevent overlap with other UI elements */
  #update-notification {
    z-index: 9999;
    border-radius: 0.5rem;
  }
</style>

<main x-data="bookingModal(
  <?= $bookingToShow ? htmlspecialchars(json_encode($bookingToShow), ENT_QUOTES) : 'null' ?>,
  <?= $showModal ? 'true' : 'false' ?>
)" class="p-6">

  <div class="bg-white/60 backdrop-blur-md border border-gray-200 shadow-lg rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-semibold text-[#DF5219] flex items-center gap-2">
        <i data-lucide="calendar-check" class="w-5 h-5"></i>
        Pending Guest Bookings
        <span x-show="bookingCount > 0" x-text="`(${bookingCount})`" class="text-sm text-gray-500"></span>
      </h2>

      <!-- Real-time status indicator -->
      <div class="flex items-center gap-2 text-sm text-gray-500">
        <div class="flex items-center gap-1">
          <div x-show="isOnline" class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <div x-show="!isOnline" class="w-2 h-2 bg-red-500 rounded-full"></div>
          <span x-text="isOnline ? 'Live' : 'Offline'"></span>
        </div>
        <span x-text="`Last updated: ${lastUpdated}`"></span>
      </div>
    </div>

    <div id="bookings-container">
      <div x-show="bookingCount > 0" class="overflow-x-auto rounded-lg">
        <table class="min-w-full bg-transparent text-sm text-gray-800 border border-gray-300">
          <thead class="bg-[#DF5219] text-white text-left">
            <tr>
              <th class="px-5 py-3">Name</th>
              <th class="px-5 py-3">Email</th>
              <th class="px-5 py-3">Phone</th>
              <th class="px-5 py-3">Check-in</th>
              <th class="px-5 py-3">Check-out</th>
              <th class="px-5 py-3">Guests</th>
              <th class="px-5 py-3">Status</th>
              <th class="px-5 py-3 text-center">Action</th>
            </tr>
          </thead>
          <tbody id="bookings-tbody" class="divide-y divide-gray-200">
            <!-- Bookings will be populated here -->
          </tbody>
        </table>
      </div>

      <div x-show="bookingCount === 0" class="text-center text-gray-500 py-20">
        <i data-lucide="inbox" class="w-12 h-12 mb-4 mx-auto text-[#DF5219]"></i>
        <p>No pending bookings found.</p>
      </div>
    </div>
  </div>

  <!-- Update notification (Alpine-controlled) -->
  <div
    id="update-notification"
    x-show="showNotification"
    x-transition
    class="fixed shadow-xl border border-gray-200 p-4 min-w-[300px] max-w-[400px] bg-white"
    :data-position="notificationPosition">
    <div class="flex items-start justify-between gap-3">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full flex items-center justify-center"
          :class="notificationType === 'success' ? 'bg-green-100' : notificationType === 'error' ? 'bg-red-100' : 'bg-blue-100'">
          <i data-lucide="bell" class="w-4 h-4"
            :class="notificationType === 'success' ? 'text-green-600' : notificationType === 'error' ? 'text-red-600' : 'text-blue-600'"></i>
        </div>
        <div>
          <p class="text-sm font-medium text-gray-900" x-text="notificationMessage"></p>
          <p class="text-xs text-gray-500" x-text="new Date().toLocaleTimeString()"></p>
        </div>
      </div>
      <button
        @click="hideNotification"
        class="text-gray-400 hover:text-gray-600 focus:outline-none">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <?php include('modals/viewBookings.php'); ?>
</main>

<script>
  function bookingModal(initialBooking, shouldShow) {
    return {
      isOpen: shouldShow,
      booking: initialBooking || {},
      guests: initialBooking?.guests || [],
      companions: initialBooking?.companions || [],
      primaryGuest: initialBooking?.primary_guest || null,
      roomBreakdown: initialBooking?.room_breakdown || {},
      loading: false,
      showToast: false,
      toastMessage: '',
      toastType: 'success',

      // Real-time update properties
      bookings: <?= json_encode($bookings) ?>,
      bookingCount: <?= count($bookings) ?>,
      lastUpdated: new Date().toLocaleTimeString(),
      isOnline: true,
      updateInterval: null,
      knownBookingIds: new Set(),

      // Notification properties
      showNotification: false,
      notificationMessage: '',
      notificationType: 'info',
      notificationPosition: 'top-right',
      unreadNotifications: 0,

      // Initialize with success message if available
      init() {
        const successMessage = shouldShow ? <?= json_encode($successMessage) ?> : '';
        if (successMessage) {
          this.showToastNotification(successMessage, 'success');
          this.closeModal();
        }

        // Initialize known booking IDs
        this.bookings.forEach(booking => {
          this.knownBookingIds.add(booking.id);
        });

        // Clean up stale notifications on init
        this.cleanupStaleNotifications(this.bookings);

        // Start real-time updates
        this.startRealTimeUpdates();

        // Load notification from localStorage
        this.loadNotification();

        // Initialize notification flag to prevent duplicates
        this.notificationShown = false;

        // Initial render
        this.renderBookings();
      },

      startRealTimeUpdates() {
        // Poll for updates every 5 seconds
        this.updateInterval = setInterval(() => {
          this.fetchBookings();
        }, 5000);

        // Also fetch on page focus
        document.addEventListener('visibilitychange', () => {
          if (!document.hidden) {
            this.fetchBookings();
          }
        });
      },

      async fetchBookings() {
        try {
          const response = await fetch(`${window.location.pathname}?action=get_bookings`, {
            method: 'GET',
            headers: {
              'Cache-Control': 'no-cache'
            }
          });

          if (!response.ok) {
            throw new Error('Network response was not ok');
          }

          const data = await response.json();

          if (data.success) {
            this.isOnline = true;
            this.lastUpdated = new Date().toLocaleTimeString();

            // Clean up notifications for bookings that no longer exist or are not pending
            this.cleanupStaleNotifications(data.bookings);

            // Check for NEW pending bookings
            const newBookings = data.bookings.filter(booking =>
              !this.knownBookingIds.has(booking.id) && booking.status === 'pending'
            );

            // Update bookings
            this.bookings = data.bookings;
            this.bookingCount = data.count;

            // Update known booking IDs
            this.knownBookingIds.clear();
            data.bookings.forEach(booking => {
              this.knownBookingIds.add(booking.id);
            });

            // Show notification ONLY for NEW pending bookings - NO DUPLICATES
            if (newBookings.length > 0) {
              newBookings.forEach(booking => {
                const message = `New booking from ${booking.full_name}`;

                // Check if notification already exists in localStorage
                const existingNotifications = JSON.parse(localStorage.getItem('adminNotifications') || '[]');
                const notificationExists = existingNotifications.some(n =>
                  n.bookingId === booking.id && n.message === message
                );

                // Only show if it doesn't exist
                if (!notificationExists) {
                  // ONLY call showAdminNotification - don't call showGlobalNotification separately
                  if (window.showAdminNotification) {
                    window.showAdminNotification(message, 'success', booking.id);
                  }
                }
              });
            }

            // Re-render table
            this.renderBookings(newBookings.map(b => b.id));
          }
        } catch (error) {
          console.error('Error fetching bookings:', error);
          this.isOnline = false;
        }
      },
      // NEW: Clean up stale notifications
      cleanupStaleNotifications(currentBookings) {
        try {
          const storedNotifications = localStorage.getItem('adminNotifications');
          if (!storedNotifications) return;

          let notifications = JSON.parse(storedNotifications);
          const currentBookingIds = new Set(currentBookings.map(b => b.id));
          const currentPendingIds = new Set(
            currentBookings.filter(b => b.status === 'pending').map(b => b.id)
          );

          // Filter out notifications for:
          // 1. Bookings that no longer exist
          // 2. Bookings that are no longer pending
          const validNotifications = notifications.filter(notification => {
            if (!notification.bookingId) return true; // Keep non-booking notifications

            // Remove if booking doesn't exist or is no longer pending
            return currentBookingIds.has(notification.bookingId) &&
              currentPendingIds.has(notification.bookingId);
          });

          // Update localStorage if notifications were cleaned up
          if (validNotifications.length !== notifications.length) {
            if (validNotifications.length > 0) {
              localStorage.setItem('adminNotifications', JSON.stringify(validNotifications));
            } else {
              localStorage.removeItem('adminNotifications');
            }

            // Remove stale notification elements from DOM
            const container = document.getElementById('global-notification-container');
            if (container) {
              const removedNotifications = notifications.filter(n =>
                !validNotifications.some(v => v.id === n.id)
              );

              removedNotifications.forEach(notification => {
                const element = document.getElementById(notification.id);
                if (element) {
                  element.remove();
                }
              });
            }

            // Update notification count
            if (window.updateNotificationCount) {
              window.updateNotificationCount();
            }
          }
        } catch (e) {
          console.error('Error cleaning up stale notifications:', e);
          // If there's an error parsing, clear the notifications
          localStorage.removeItem('adminNotifications');
        }
      },

      renderBookings(newBookingIds = []) {
        const tbody = document.getElementById('bookings-tbody');
        tbody.innerHTML = '';

        this.bookings.forEach(booking => {
          const row = document.createElement('tr');
          const isNew = newBookingIds.includes(booking.id);

          row.className = isNew ?
            'booking-row-new hover:bg-[#fff6f4] transition-colors' :
            'hover:bg-[#fff6f4] transition-colors';

          row.innerHTML = `
          <td class="px-5 py-3 font-medium truncate max-w-[180px]">${this.escapeHtml(booking.full_name)}</td>
          <td class="px-5 py-3 truncate max-w-[240px]">${this.escapeHtml(booking.email)}</td>
          <td class="px-5 py-3">${this.escapeHtml(booking.phone)}</td>
          <td class="px-5 py-3">${this.formatDate(booking.check_in)}</td>
          <td class="px-5 py-3">${this.formatDate(booking.check_out)}</td>
          <td class="px-5 py-3 text-center">${booking.No_of_guests}</td>
          <td class="px-5 py-3">
            <span class="status-badge status-${booking.status}">
              ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
            </span>
          </td>
          <td class="px-5 py-3 text-center">
            <button
              class="view-booking-btn inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium px-3 py-1 rounded-md transition"
              data-booking='${this.escapeHtml(JSON.stringify(booking))}'>
              <i data-lucide="eye" class="w-4 h-4"></i> View
            </button>
          </td>
        `;

          tbody.appendChild(row);
        });

        // Add event listeners to the dynamically created buttons
        const viewButtons = tbody.querySelectorAll('.view-booking-btn');
        viewButtons.forEach(button => {
          button.addEventListener('click', (e) => {
            e.preventDefault();
            const bookingData = JSON.parse(button.getAttribute('data-booking'));
            this.openModal(bookingData);
          });
        });

        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
          lucide.createIcons();
        }
      },

      showUpdateNotification(message, type = 'success') {
        // Show local notification for bookings page
        this.notificationMessage = message;
        this.notificationType = type;
        this.showNotification = true;

        // Always trigger global notification for new bookings
        if (window.showGlobalNotification) {
          window.showGlobalNotification(message, type);
        }

        this.$nextTick(() => {
          this.positionNotification();
        });
      },

      calculateNotificationPosition() {
        const searchBar = document.querySelector('input[placeholder="Search here"]');
        if (searchBar) {
          const searchRect = searchBar.getBoundingClientRect();
          return 'below-search';
        }
        return 'top-right';
      },

      positionNotification() {
        const notification = document.querySelector('#update-notification');
        if (notification) {
          notification.setAttribute('data-position', this.notificationPosition);
        }
      },

      updateBellBadge() {
        const bellBadge = document.querySelector('#notification-bell .bg-red-500');
        if (bellBadge && this.unreadNotifications > 0) {
          bellBadge.textContent = this.unreadNotifications > 9 ? '9+' : this.unreadNotifications;
          bellBadge.classList.add('px-1', 'text-xs', 'font-bold', 'text-white', 'min-w-[16px]', 'h-4', 'flex', 'items-center', 'justify-center');
        }
      },

      hideNotification() {
        this.showNotification = false;
        this.unreadNotifications = 0;
        localStorage.removeItem('bookingNotification');
      },

      saveNotification() {
        const notification = {
          message: this.notificationMessage,
          type: this.notificationType,
          position: this.notificationPosition,
          timestamp: new Date().getTime()
        };
        localStorage.setItem('bookingNotification', JSON.stringify(notification));
      },

      loadNotification() {
        const stored = localStorage.getItem('bookingNotification');
        if (stored) {
          try {
            const notification = JSON.parse(stored);
            this.notificationMessage = notification.message;
            this.notificationType = notification.type;
            this.notificationPosition = notification.position || 'top-right';
            this.showNotification = true;
          } catch (e) {
            console.error('Failed to parse stored notification', e);
            localStorage.removeItem('bookingNotification');
          }
        }
      },

      escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      },

      openModal(data) {
        this.booking = data;
        this.guests = data.guests || [];
        this.companions = data.companions || [];
        this.primaryGuest = data.primary_guest || null;
        this.roomBreakdown = data.room_breakdown || {};
        this.isOpen = true;
        this.loading = false;
      },

      closeModal() {
        this.isOpen = false;
        this.booking = {};
        this.guests = [];
        this.companions = [];
        this.primaryGuest = null;
        this.roomBreakdown = {};
      },

      async handleBookingAction(action) {
        this.loading = true;

        try {
          const formData = new FormData();
          formData.append('id', this.booking.id);
          formData.append('action', action);

          const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          if (response.ok) {
            const result = await response.json();

            if (result.success) {
              // Clean up notifications for this booking
              this.cleanupBookingNotifications(this.booking.id);

              // Close modal immediately
              this.closeModal();

              // Show persistent toast that will be visible across all admin pages
              window.showPersistentToast(result.message, 'success');

              // Immediately fetch updated bookings
              await this.fetchBookings();
            } else {
              throw new Error(result.message || 'Action failed');
            }
          } else {
            throw new Error('Network response was not ok');
          }
        } catch (error) {
          console.error('Error:', error);
          this.showToastNotification('An error occurred while processing the booking.', 'error');
        } finally {
          this.loading = false;
        }
      },
      // Clean up notifications for a specific booking
      cleanupBookingNotifications(bookingId) {
        try {
          const storedNotifications = localStorage.getItem('adminNotifications');
          if (storedNotifications) {
            let notifications = JSON.parse(storedNotifications);

            // Remove all notifications related to this booking
            notifications = notifications.filter(n => n.bookingId !== bookingId);

            // Also remove any displayed notifications for this booking
            const container = document.getElementById('global-notification-container');
            if (container) {
              const notificationElements = container.querySelectorAll('[id^="notification_"]');
              notificationElements.forEach(element => {
                // Check if this element's ID matches any notification for this booking
                const matchingNotification = notifications.find(n =>
                  n.bookingId === bookingId && element.id.includes(n.id)
                );
                if (matchingNotification) {
                  element.remove();
                }
              });
            }

            // Update localStorage
            if (notifications.length > 0) {
              localStorage.setItem('adminNotifications', JSON.stringify(notifications));
            } else {
              localStorage.removeItem('adminNotifications');
            }

            // Update notification count
            if (window.updateNotificationCount) {
              window.updateNotificationCount();
            }
          }
        } catch (e) {
          console.error('Error cleaning up booking notifications:', e);
        }
      },

      showToastNotification(message, type = 'success') {
        this.toastMessage = message;
        this.toastType = type;
        this.showToast = true;

        // Auto-hide toast after 4 seconds
        setTimeout(() => {
          this.hideToast();
        }, 4000);
      },

      hideToast() {
        this.showToast = false;
      },

      formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      },

      formatDateTime(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: 'numeric',
          minute: '2-digit',
          hour12: true
        });
      },

      formatCurrency(amount) {
        return new Intl.NumberFormat('en-PH', {
          style: 'currency',
          currency: 'PHP'
        }).format(amount);
      },

      getRoomBreakdownText() {
        let text = '';
        for (const [roomId, roomData] of Object.entries(this.roomBreakdown)) {
          text += `${roomData.title}: ${roomData.count} room(s)\n`;
        }
        return text.trim();
      },

      getGuestListText() {
        let text = '';
        this.guests.forEach((guest, index) => {
          const guestType = guest.guest_type === 'primary' ? ' (Primary Guest)' : '';
          text += `${index + 1}. ${guest.guest_name} (${guest.gender}) - ${guest.room_type}${guestType}\n`;
        });
        return text.trim();
      },

      getTotalGuests() {
        return this.guests.length;
      },

      getRoomBreakdownDisplay() {
        if (!this.roomBreakdown || Object.keys(this.roomBreakdown).length === 0) {
          return 'Standard Room';
        }

        let display = [];
        for (const [roomId, roomData] of Object.entries(this.roomBreakdown)) {
          display.push(`${roomData.title}(${roomData.count})`);
        }

        return display.join(', ');
      },

      getDurationText() {
        if (!this.booking.check_in || !this.booking.check_out) {
          return '0 nights';
        }

        const checkIn = new Date(this.booking.check_in);
        const checkOut = new Date(this.booking.check_out);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));

        return `${nights} night${nights !== 1 ? 's' : ''}`;
      },

      // Cleanup when component is destroyed
      destroy() {
        if (this.updateInterval) {
          clearInterval(this.updateInterval);
        }
      }
    };
  }

  document.addEventListener("DOMContentLoaded", function() {
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  });

  // Handle page unload
  window.addEventListener('beforeunload', function() {
    if (Alpine && Alpine.store && Alpine.store('bookingModal')?.destroy) {
      Alpine.store('bookingModal').destroy();
    }
  });

  // ADDITIONAL UTILITY FUNCTION: Clear all stale notifications manually
  function clearAllStaleNotifications() {
    try {
      localStorage.removeItem('adminNotifications');
      localStorage.removeItem('bookingNotification');

      // Clear all notification elements from DOM
      const container = document.getElementById('global-notification-container');
      if (container) {
        container.innerHTML = '';
      }

      // Update notification count
      if (window.updateNotificationCount) {
        window.updateNotificationCount();
      }

      console.log('All stale notifications cleared');
    } catch (e) {
      console.error('Error clearing stale notifications:', e);
    }
  }
</script>
<?php include('includes/footer.php'); ?>