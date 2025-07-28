<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require '../vendor/autoload.php';
require '../includes/db.php';
require 'includes/auth.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 0);
error_reporting(E_ALL);

// ---------- AJAX HANDLER ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  header('Content-Type: application/json');

  function respond($success, $message = '') {
    echo json_encode([
      'success' => $success,
      'error'   => $success ? null : $message
    ]);
    exit;
  }

  try {
    $action = $_POST['action'];
    $id = intval($_POST['id'] ?? 0);

    if (!$id) respond(false, 'Message ID is required');

    // MARK AS READ
    if ($action === 'mark_read') {
      $stmt = $conn->prepare("UPDATE contact_messages SET read_at = NOW() WHERE id = ? AND responded_at IS NULL");
      if (!$stmt) respond(false, 'Prepare failed: ' . $conn->error);
      $stmt->bind_param("i", $id);
      if (!$stmt->execute()) respond(false, 'Execute failed: ' . $stmt->error);
      $stmt->close();
      respond(true);
    }

    // SEND EMAIL
    if ($action === 'send_email') {
      $to = filter_var(trim($_POST['to_email'] ?? ''), FILTER_VALIDATE_EMAIL);
      $subject = trim($_POST['subject'] ?? '');
      $body = trim($_POST['message'] ?? '');

      if (!$to || !$subject || !$body) {
        respond(false, 'Missing or invalid input fields');
      }

      $mail = new PHPMailer(true);
      $mail->isSMTP();
      $mail->Host       = SMTP_HOST;
      $mail->SMTPAuth   = true;
      $mail->Username   = SMTP_USER;
      $mail->Password   = SMTP_PASS;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port       = SMTP_PORT;

      $mail->setFrom(FROM_EMAIL, FROM_NAME);
      $mail->addAddress($to);
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body    = nl2br(htmlspecialchars($body, ENT_QUOTES | ENT_HTML5));
      $mail->AltBody = $body;

      $mail->send();

      $stmt = $conn->prepare("UPDATE contact_messages SET responded_at = NOW() WHERE id = ?");
      if (!$stmt) respond(false, 'Prepare failed: ' . $conn->error);
      $stmt->bind_param("i", $id);
      if (!$stmt->execute()) respond(false, 'Execute failed: ' . $stmt->error);
      $stmt->close();

      respond(true);
    }

    respond(false, 'Unrecognized action');
  } catch (Throwable $e) {
    error_log("Fatal error: " . $e->getMessage());
    respond(false, 'Server error: ' . $e->getMessage());
  }
}

// ---------- FETCH MESSAGES ----------
$messages = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
include('includes/header.php');
?>

<style>
  [x-cloak] {
    display: none !important;
  }

  .loader {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #DF5219;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>
<main x-data="modalHandler()" class="flex-1 bg-[#F9FAFB] h-screen overflow-hidden"
  x-init="$nextTick(() => { if (window.lucide) lucide.createIcons(); })">

  <div class="max-w-7xl mx-auto bg-white shadow rounded-lg border border-gray-200 h-full flex flex-col">

    <div class="flex-1 overflow-y-auto p-6">
      <?php if ($messages->num_rows > 0): ?>
        <div class="overflow-x-auto rounded-lg border border-gray-300">
          <table class="min-w-full text-sm text-gray-800">
            <thead class="bg-[#DF5219] text-white">
              <tr>
                <th class="px-5 py-3 text-left">Name</th>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-center">Submitted</th>
                <th class="px-5 py-3 text-center">Status</th>
                <th class="px-5 py-3 text-center">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <?php while ($row = $messages->fetch_assoc()):
                $status = ['text' => 'Unread', 'color' => 'red', 'icon' => 'mail'];
                if (!empty($row['responded_at'])) {
                  $status = ['text' => 'Responded', 'color' => 'green', 'icon' => 'check-circle'];
                } elseif (!empty($row['read_at'])) {
                  $status = ['text' => 'Read', 'color' => 'orange', 'icon' => 'book-open'];
                }
              ?>
                <tr class="hover:bg-orange-50 transition-colors">
                  <td class="px-5 py-3 font-medium"><?= htmlspecialchars($row['name']) ?></td>
                  <td class="px-5 py-3"><?= htmlspecialchars($row['email']) ?></td>
                  <td class="px-5 py-3 text-center"><?= date("M d, Y h:i A", strtotime($row['submitted_at'])) ?></td>
                  <td class="px-5 py-3 text-center">
                    <span id="status-badge-<?= $row['id'] ?>"
                      class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold text-<?= $status['color'] ?>-700 bg-<?= $status['color'] ?>-100">
                      <i data-lucide="<?= $status['icon'] ?>" class="w-4 h-4"></i> <?= $status['text'] ?>
                    </span>
                  </td>
                  <td class="px-5 py-3 text-center">
                    <button
                      @click="markReadAndView(
                        <?= (int)$row['id'] ?>,
                        '<?= addslashes($row['name']) ?>',
                        '<?= addslashes($row['email']) ?>',
                        '<?= addslashes(date('M d, Y h:i A', strtotime($row['submitted_at']))) ?>',
                        '<?= addslashes($row['message']) ?>'
                      )"
                      class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-3 py-1 rounded-md">
                      <i data-lucide="eye" class="w-4 h-4"></i> View
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="flex flex-col items-center justify-center py-20 text-center text-gray-500 gap-3">
          <i data-lucide="inbox" class="w-10 h-10 text-[#DF5219] mb-1"></i>
          <p class="text-sm font-semibold">No feedback messages found</p>
          <p class="text-xs text-gray-400">You're all caught up. Messages submitted will appear here.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal -->
  <div x-cloak x-show="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" @click.outside="closeModal">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-2xl relative max-h-[90vh] overflow-y-auto">
      <div x-show="loading" x-transition.opacity class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
        <div class="loader" aria-label="Loading"></div>
      </div>

      <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>

      <h2 class="text-xl font-semibold text-[#DF5219] mb-4 flex items-center gap-2">
        <i data-lucide="message-circle" class="w-5 h-5"></i>
        Message Details
      </h2>

      <div class="space-y-4 text-sm text-gray-700">
        <div><strong>Status:</strong>
          <span
            class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold"
            :class="{
              'text-red-700 bg-red-100': modalData.status === 'Unread',
              'text-orange-700 bg-orange-100': modalData.status === 'Read',
              'text-green-700 bg-green-100': modalData.status === 'Responded'
            }">
            <i :data-lucide="modalData.statusIcon" class="w-4 h-4"></i>
            <span x-text="modalData.status"></span>
          </span>
        </div>
        <div><strong>Name:</strong> <span x-text="modalData.name"></span></div>
        <div><strong>Email:</strong> <span x-text="modalData.email"></span></div>
        <div><strong>Submitted At:</strong> <span x-text="modalData.submitted_at"></span></div>
        <div>
          <strong>Message:</strong>
          <div class="bg-gray-100 p-4 rounded mt-1 text-sm text-gray-800 whitespace-pre-wrap" x-text="modalData.message"></div>
        </div>

        <!-- Response form -->
        <form @submit.prevent="submitResponse" class="mt-6 space-y-3">
          <h3 class="text-sm font-semibold text-gray-700">Respond to Message</h3>
          <input type="text" readonly x-model="modalData.email" class="w-full bg-gray-100 border border-gray-300 rounded px-4 py-2 text-sm" />
          <textarea x-model="responseMessage" class="w-full border border-gray-300 rounded px-4 py-2 text-sm" rows="4" placeholder="Write your response..." required></textarea>
          <button
            type="submit"
            class="bg-[#DF5219] hover:bg-[#c4440e] text-white px-4 py-2 rounded text-sm font-medium inline-flex items-center gap-1"
            :disabled="loading">
            <template x-if="loading">
              <div class="loader w-5 h-5"></div>
            </template>
            <i data-lucide="send" class="w-4 h-4"></i> Send
          </button>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
  function modalHandler() {
    return {
      isOpen: false,
      loading: false,
      modalData: {},
      responseMessage: '',

      async markReadAndView(id, name, email, submitted_at, message) {
        this.loading = true;

        const badge = document.querySelector(`#status-badge-${id}`);
        const isResponded = badge?.textContent?.trim() === 'Responded';
        if (badge && !isResponded) badge.innerHTML = '<div class="loader mx-auto"></div>';

        try {
          const res = await fetch('contacts.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `action=mark_read&id=${encodeURIComponent(id)}`
          });
          const result = await res.json();
          console.log(result); // Optional: Debugging

          this.modalData = {
            id,
            name,
            email,
            submitted_at,
            message,
            status: isResponded ? 'Responded' : 'Read',
            statusIcon: isResponded ? 'check-circle' : 'book-open',
          };
          this.isOpen = true;

          if (badge && !isResponded && result.success) {
            badge.classList.remove('text-red-700', 'bg-red-100');
            badge.classList.add('text-orange-700', 'bg-orange-100');
            badge.innerHTML = `<i data-lucide="book-open" class="w-4 h-4"></i> Read`;
            lucide.createIcons();
          }
        } catch (error) {
          alert('Failed to mark message as read.');
        } finally {
          this.loading = false;
        }
      },

      closeModal() {
        this.isOpen = false;
        this.modalData = {};
        this.responseMessage = '';
      },

      async submitResponse() {
        if (!this.responseMessage.trim()) {
          alert('Response cannot be empty.');
          return;
        }

        this.loading = true;

        const formData = new URLSearchParams();
        formData.append('action', 'send_email');
        formData.append('id', this.modalData.id);
        formData.append('to_email', this.modalData.email);
        formData.append('subject', 'Response to Your Message');
        formData.append('message', this.responseMessage);

        try {
          const res = await fetch('contacts.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData
          });
          const data = await res.json();

          if (data.success) {
            this.modalData.status = 'Responded';
            this.modalData.statusIcon = 'check-circle';
            const badge = document.querySelector(`#status-badge-${this.modalData.id}`);
            if (badge) {
              badge.classList.remove('text-orange-700', 'bg-orange-100');
              badge.classList.add('text-green-700', 'bg-green-100');
              badge.innerHTML = `<i data-lucide="check-circle" class="w-4 h-4"></i> Responded`;
              lucide.createIcons();
            }
            alert('Response sent successfully!');
            this.closeModal();
          } else {
            alert('Failed to send email: ' + (data.error || 'Unknown error'));
          }
        } catch (err) {
          alert('Failed to send response.');
        } finally {
          this.loading = false;
        }
      }
    };
  }
</script>

<?php include('includes/footer.php'); ?>