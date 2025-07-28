<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin = $_SESSION['admin'] ?? 'Admin';
$adminProfile = $_SESSION['admin_profile'] ?? 'assets/images/default-profile.png';

$currentPage = basename($_SERVER['PHP_SELF']);

$pageMap = [
    'rooms.php' => 'Room Management',
    'bookings.php' => 'Guest Booking Management',
    'guest.php' => 'Guest List',
    'contacts.php' => 'Guest Feedback and Questions',
    'admins.php' => 'Admin Account Management',
    'change-password.php' => 'Change Password',
];

$pageTitle = $pageMap[$currentPage] ?? 'Dashboard';

$menuItems = [
    'rooms.php' => ['label' => 'Rooms', 'icon' => 'home'],
    'bookings.php' => ['label' => 'Bookings', 'icon' => 'calendar-check'],
    'guest.php' => ['label' => 'Guest List', 'icon' => 'users'],
    'contacts.php' => ['label' => 'Messages', 'icon' => 'mail'],
    'monthly-report.php' => ['label' => 'Monthly Report', 'icon' => 'bar-chart'],
    'admins.php' => ['label' => 'Admin Accounts', 'icon' => 'shield'],
    '../index.php' => ['label' => 'Visit Site', 'icon' => 'globe'],
];

function isActive($file)
{
    global $currentPage;
    return $currentPage === $file
        ? 'bg-[#EBEDEF]/50 text-white font-bold'
        : 'hover:bg-grayAccent/20';
}
?>
<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false, showModal: false }" @keydown.escape.window="sidebarOpen = false; showModal = false">

<head>
    <meta charset="UTF-8" />
    <title>Admin - <?= htmlspecialchars($pageTitle) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ffffff',
                        dark: '#000000',
                        grayAccent: '#d1d5db',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    borderRadius: {
                        xl: '1rem',
                        '2xl': '1.5rem',
                    },
                    boxShadow: {
                        subtle: '0 2px 6px rgba(0,0,0,0.06)',
                    },
                    transitionTimingFunction: {
                        'in-out-soft': 'cubic-bezier(0.4, 0, 0.2, 1)',
                    },
                },
            },
        };
    </script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Utility -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</head>

<body class="bg-[#F9FAFB] font-sans">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            x-transition
            class="fixed inset-y-0 left-0 z-50 w-64 bg-black text-white transform transition-transform duration-300 ease-in-out flex flex-col md:translate-x-0">
            <div class="p-6 flex items-center font-bold text-xl tracking-tight gap-3">
                <i data-lucide="shield" class="w-6 h-6"></i>
                Admin Panel
            </div>

            <!-- Menu -->
            <nav class="flex flex-col px-4 gap-1 text-sm font-medium flex-1">
                <?php foreach ($menuItems as $file => $item): ?>
                    <a
                        href="<?= htmlspecialchars($file) ?>"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors <?= isActive($file) ?> hover:bg-white/10">
                        <i data-lucide="<?= htmlspecialchars($item['icon']) ?>" class="w-5 h-5"></i>
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Logout -->
            <div class="p-4 mt-auto">
                <a
                    href="logout.php"
                    class="w-full flex items-center justify-center gap-2 bg-white text-black font-semibold text-sm py-2 rounded-lg hover:bg-gray-200 transition">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-h-screen ml-0 md:ml-64">

            <!-- Header -->
            <header
                class="bg-white sticky top-0 z-30 shadow-sm px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu -->
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden text-black hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/20 transition">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>

                    <!-- Title -->
                    <h1
                        class="hidden md:flex items-center gap-2 text-base font-semibold text-black tracking-tight">
                        <i
                            data-lucide="<?= htmlspecialchars($menuItems[$currentPage]['icon'] ?? 'layout-dashboard') ?>"
                            class="w-6 h-6 text-black"></i>
                        <span><?= htmlspecialchars($pageTitle) ?></span>
                    </h1>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ profileDropdown: false }">
                    <button
                        @click="profileDropdown = !profileDropdown"
                        class="cursor-pointer flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg transition">
                        <img
                            src="/subic-bay-hostel/<?= htmlspecialchars($adminProfile) ?>"
                            alt="Profile"
                            class="w-9 h-9 rounded-full object-cover border-2 border-black" />
                        <span
                            class="hidden sm:block font-medium text-sm text-black"><?= htmlspecialchars($admin) ?></span>
                        <svg
                            class="w-4 h-4 text-gray-600"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <div
                        x-show="profileDropdown"
                        @click.away="profileDropdown = false"
                        x-transition
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg z-50 py-2 text-sm">
                        <button
                            @click="$dispatch('open-profile-modal'); profileDropdown = false"
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-800">
                            Settings
                        </button>
                        <a
                            href="logout.php"
                            class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            </header>

            <!-- Flash Message -->
            <?php if (!empty($_SESSION['flash'])): ?>
                <div
                    class="p-4 bg-yellow-50 text-yellow-800 rounded-md font-medium text-sm shadow-sm mx-4 my-2 border border-yellow-200">
                    <?= htmlspecialchars($_SESSION['flash']) ?>
                    <?php unset($_SESSION['flash']); ?>
                </div>
            <?php endif; ?>

            <!-- Profile Settings Modal -->
            <div
                x-data="{ showModal: false, preview: '/subic-bay-hostel/<?= htmlspecialchars($adminProfile) ?>', confirmPassword: '', error: '' }"
                @open-profile-modal.window="showModal = true">
                <!-- Modal Overlay -->
                <div
                    x-show="showModal"
                    x-cloak
                    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div
                        class="bg-white rounded-2xl w-[400px] p-6 shadow-xl relative"
                        x-transition.scale.90.duration.200ms
                        @keydown.escape.window="showModal = false">
                        <button
                            @click="showModal = false"
                            class="absolute top-4 right-4 text-gray-400 hover:text-black transition">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>

                        <h2 class="text-lg font-semibold text-gray-900 mb-4 text-center">
                            Profile Settings
                        </h2>

                        <form action="includes/update-profile.php" method="POST" enctype="multipart/form-data">
                            <!-- Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    value="<?= htmlspecialchars($admin) ?>"
                                    autocomplete="username"
                                    class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-black/20"
                                    required />
                            </div>

                            <!-- Profile Picture with Live Preview -->
                            <div>
                                <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                                <div class="flex items-center gap-3">
                                    <img
                                        :src="preview"
                                        class="w-10 h-10 rounded-full object-cover border"
                                        alt="Preview" />
                                    <input
                                        id="profile_picture"
                                        type="file"
                                        name="profile_picture"
                                        accept="image/*"
                                        class="text-sm"
                                        @change="if ($event.target.files[0]) preview = URL.createObjectURL($event.target.files[0])" />
                                </div>
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input
                                    id="new_password"
                                    type="password"
                                    name="new_password"
                                    class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-black/20" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input
                                    id="confirm_password"
                                    type="password"
                                    x-model="confirmPassword"
                                    class="w-full border rounded-md px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-black/20" />
                            </div>

                            <!-- Error Message -->
                            <template x-if="error">
                                <div
                                    class="text-red-600 text-sm font-medium"
                                    x-text="error"></div>
                            </template>

                            <!-- Submit -->
                            <div>
                                <button
                                    type="submit"
                                    class="w-full bg-black text-white py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>