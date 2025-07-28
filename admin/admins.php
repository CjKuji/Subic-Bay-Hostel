<?php
session_start();

// DB connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'subic_hostel_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("❌ DB connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

include('includes/auth.php');

// Add Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'], $_POST['email'])) {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);

  if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => '❌ Only Gmail addresses are allowed.'];
    header("Location: admins.php");
    exit;
  }

  $stmt = $conn->prepare("SELECT id FROM admin WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) {
    $stmt->close();
    $_SESSION['flash'] = ['type' => 'error', 'message' => '❌ Email already exists.'];
    header("Location: admins.php");
    exit;
  }
  $stmt->close();

  $stmt = $conn->prepare("SELECT id FROM admin WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) {
    $stmt->close();
    $_SESSION['flash'] = ['type' => 'error', 'message' => '❌ Username already exists.'];
    header("Location: admins.php");
    exit;
  }
  $stmt->close();

  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT INTO admin (username, password, email) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $username, $password, $email);
  $stmt->execute();
  $stmt->close();

  $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Admin created successfully.'];
  header("Location: admins.php");
  exit;
}

// Delete Admin
if (isset($_GET['user'])) {
  $targetUser = $_GET['user'];

  if ($targetUser === $_SESSION['admin']) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => '❌ You cannot delete yourself.'];
    header("Location: admins.php");
    exit;
  }

  $stmt = $conn->prepare("DELETE FROM admin WHERE username = ?");
  $stmt->bind_param("s", $targetUser);
  if ($stmt->execute()) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => "✅ Admin <strong>$targetUser</strong> deleted."];
  } else {
    $_SESSION['flash'] = ['type' => 'error', 'message' => "❌ Failed to delete admin: " . $stmt->error];
  }
  $stmt->close();

  header("Location: admins.php");
  exit;
}

// Get Admin List
$admins = $conn->query("SELECT * FROM admin");
$flashMessage = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

include('includes/header.php');
?>

<style>
  [x-cloak] {
    display: none !important;
  }
</style>

<div class="p-6">
  <main x-data="{ showModal: false }" class="text-[#222]">

    <?php if ($flashMessage): ?>
      <div class="mb-4 px-4 py-3 rounded text-white <?= $flashMessage['type'] === 'success' ? 'bg-green-500' : 'bg-red-500' ?>">
        <?= $flashMessage['message'] ?>
      </div>
    <?php endif; ?>

    <div class="bg-white shadow-lg rounded-2xl p-6 max-h-[85vh] overflow-hidden flex flex-col">
      <div class="flex justify-between items-center mb-4">
        <button @click="showModal = true"
          class="bg-[#DF5219] hover:bg-[#FFA358] text-white px-5 py-2 rounded-full font-semibold text-sm transition">
          + Add Admin
        </button>
      </div>

      <div class="flex-1 overflow-auto rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full text-gray-800 text-sm">
          <thead class="bg-[#DF5219] text-white">
            <tr>
              <th class="px-5 py-3 text-center"><i data-lucide="user" class="w-6 h-6 mx-auto mb-1"></i>Username</th>
              <th class="px-5 py-3 text-center"><i data-lucide="mail" class="w-6 h-6 mx-auto mb-1"></i>Email</th>
              <th class="px-5 py-3 text-center"><i data-lucide="trash-2" class="w-6 h-6 mx-auto mb-1"></i>Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <?php if ($admins->num_rows > 0): ?>
              <?php while ($admin = $admins->fetch_assoc()): ?>
                <tr class="hover:bg-[#fff6f4] transition-colors duration-200">
                  <td class="px-5 py-3 text-center font-medium whitespace-nowrap"><?= htmlspecialchars($admin['username']) ?></td>
                  <td class="px-5 py-3 text-center whitespace-nowrap max-w-[250px] overflow-hidden text-ellipsis"><?= htmlspecialchars($admin['email']) ?></td>
                  <td class="px-5 py-3 text-center whitespace-nowrap">
                    <?php if ($admin['username'] !== $_SESSION['admin']): ?>
                      <a href="admins.php?user=<?= urlencode($admin['username']) ?>"
                        class="text-red-600 hover:text-red-800 text-sm inline-flex items-center gap-1 transition">
                        <i data-lucide="trash-2" class="w-5 h-5"></i> Delete
                      </a>
                    <?php else: ?>
                      <span class="text-gray-400 italic text-sm">You</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="text-center py-12 text-gray-500">
                  <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 text-[#DF5219]"></i>
                  No admins found.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div @click.outside="showModal = false" class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Add Admin</h2>
        <form method="POST" action="admins.php" class="space-y-4">
          <input type="text" name="username" required placeholder="Username" class="w-full px-4 py-2 border rounded" />
          <input type="email" name="email" required placeholder="Gmail only" class="w-full px-4 py-2 border rounded" />
          <input type="password" name="password" required placeholder="Password" class="w-full px-4 py-2 border rounded" />
          <div class="flex justify-end space-x-2">
            <button type="button" @click="showModal = false" class="px-4 py-2 border rounded text-gray-600">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-[#DF5219] hover:bg-[#FFA358] text-white rounded">Create</button>
          </div>
        </form>
      </div>
    </div>

  </main>
</div>

<?php include('includes/footer.php'); ?>