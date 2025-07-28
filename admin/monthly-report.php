<?php
include('includes/header.php');

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'subic_hostel_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Use selected month or default to current month
$filterMonth = $_GET['month'] ?? date('Y-m'); // e.g., '2025-07'

// Build start and end of month using check_in date
$startDate = $filterMonth . '-01';
$endDate = date("Y-m-t", strtotime($startDate));

// Fetch confirmed bookings based on check_in date
$stmt = $conn->prepare("SELECT 
    b.id,
    b.check_in,
    b.check_out,
    b.number_of_rooms,
    b.special_request,
    b.created_at,
    u.full_name,
    u.email,
    u.phone,
    rt.title AS room_type,
    rt.price
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN room_types rt ON b.room_type_id = rt.id
WHERE b.status = 'confirmed' AND b.check_in BETWEEN ? AND ?
ORDER BY b.check_in DESC");
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();
?>

<style>
    /* Modern clean styles */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .month-selector {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 24px;
        color: white;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    }
    
    .month-input {
        background: rgba(255, 255, 255, 0.95);
        border: 2px solid transparent;
        border-radius: 8px;
        padding: 12px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .month-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .table-header {
        background: linear-gradient(135deg, #DF5219 0%, #f56500 100%);
        color: white;
    }
    
    .table-row {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .table-row:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .download-btn {
        background: linear-gradient(135deg, #DF5219 0%, #f56500 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(223, 82, 25, 0.3);
    }
    
    .download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(223, 82, 25, 0.4);
    }
    
    .empty-state {
        padding: 64px 24px;
        text-align: center;
        color: #64748b;
    }
    
    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        opacity: 0.5;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #DF5219;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }
    
    .stat-label {
        color: #64748b;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    
    .page-header {
        background: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }
    
    .page-subtitle {
        color: #64748b;
        font-size: 1.125rem;
    }
</style>

<div class="md:ml-64">
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="mt-4 mx-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 fade-in">
            <?= htmlspecialchars($_SESSION['flash']) ?>
            <?php unset($_SESSION['flash']); ?>
        </div>
    <?php endif; ?>

    <main class="p-6 min-h-screen">
        <!-- Page Header -->
        <div class="page-header fade-in">
            <h1 class="page-title">Monthly Report</h1>
            <p class="page-subtitle">View and manage your monthly booking reports</p>
        </div>

        <!-- Month Selector -->
        <div class="fade-in mb-8 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="flex items-center space-x-2 bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200 cursor-pointer" onclick="toggleMonthPicker()">
                        <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                        <label class="text-sm font-medium text-gray-700">Month:</label>
                        <span id="selectedMonth" class="text-sm font-medium text-gray-700">
                            <?= date('F Y', strtotime(($_GET['month'] ?? date('Y-m')) . '-01')) ?>
                        </span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
                    </div>
                    
                    <!-- Custom Month Picker Dropdown -->
                    <div id="monthPickerDropdown" class="absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden" style="min-width: 280px;">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-4">
                                <button type="button" onclick="changeYear(-1)" class="p-1 hover:bg-gray-100 rounded">
                                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                </button>
                                <span id="currentYear" class="font-medium text-gray-700"></span>
                                <button type="button" onclick="changeYear(1)" class="p-1 hover:bg-gray-100 rounded">
                                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <button type="button" onclick="selectMonth(1)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Jan</button>
                                <button type="button" onclick="selectMonth(2)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Feb</button>
                                <button type="button" onclick="selectMonth(3)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Mar</button>
                                <button type="button" onclick="selectMonth(4)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Apr</button>
                                <button type="button" onclick="selectMonth(5)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">May</button>
                                <button type="button" onclick="selectMonth(6)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Jun</button>
                                <button type="button" onclick="selectMonth(7)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Jul</button>
                                <button type="button" onclick="selectMonth(8)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Aug</button>
                                <button type="button" onclick="selectMonth(9)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Sep</button>
                                <button type="button" onclick="selectMonth(10)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Oct</button>
                                <button type="button" onclick="selectMonth(11)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Nov</button>
                                <button type="button" onclick="selectMonth(12)" class="month-btn p-2 text-sm rounded hover:bg-blue-50 hover:text-blue-600">Dec</button>
                            </div>
                            
                            <div class="flex justify-between pt-2 border-t border-gray-200">
                                <button type="button" onclick="clearSelection()" class="text-sm text-blue-600 hover:text-blue-800">Clear</button>
                                <button type="button" onclick="selectToday()" class="text-sm text-blue-600 hover:text-blue-800">Today</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hidden form for submission -->
        <form method="GET" id="monthForm" style="display: none;">
            <input type="hidden" id="monthInput" name="month" value="">
        </form>

        <!-- Statistics Cards -->
        <?php if (count($bookings) > 0): ?>
            <div class="stats-grid fade-in">
                <div class="stat-card">
                    <div class="stat-number"><?= count($bookings) ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= array_sum(array_column($bookings, 'number_of_rooms')) ?></div>
                    <div class="stat-label">Total Rooms</div>
                </div>
                <div class="stat-card">
                    <?php 
                    // Calculate total revenue
                    $totalRevenue = 0;
                    foreach ($bookings as $booking) {
                        $checkIn = new DateTime($booking['check_in']);
                        $checkOut = new DateTime($booking['check_out']);
                        $nights = $checkIn->diff($checkOut)->days;
                        $totalRevenue += $booking['price'] * $booking['number_of_rooms'] * $nights;
                    }
                    ?>
                    <div class="stat-number">₱<?= number_format($totalRevenue, 0) ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="modern-card fade-in">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Room Number</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Full Name</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Room Type</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">No. of Rooms</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Check-in</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Check-out</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Special Request</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($bookings) > 0): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr class="table-row">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($booking['id']) ?></td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?= htmlspecialchars($booking['full_name']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($booking['email']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($booking['phone']) ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="status-badge">
                                            <?= htmlspecialchars($booking['room_type']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($booking['number_of_rooms']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($booking['check_in']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($booking['check_out']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                        <div class="truncate" title="<?= htmlspecialchars($booking['special_request']) ?>">
                                            <?= htmlspecialchars($booking['special_request']) ?: 'None' ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="empty-state">
                                    <div class="empty-state-icon">
                                        <i data-lucide="calendar-x" class="w-full h-full text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                                    <p class="text-sm text-gray-500">No confirmed bookings found for the selected month.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Download Button -->
        <div class="mt-8 flex justify-end fade-in">
            <form action="download-report.php" method="get">
                <?php $selectedMonth = isset($_GET['month']) ? htmlspecialchars($_GET['month']) : date('Y-m'); ?>
                <input type="hidden" name="month" value="<?= $selectedMonth ?>">
                <button type="submit" class="download-btn inline-flex items-center border-none cursor-pointer">
                    <i data-lucide="download" class="w-5 h-5 mr-2"></i>
                    Download PDF Report
                </button>
            </form>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();
    
    // Month picker functionality
    let currentPickerYear = new Date().getFullYear();
    let selectedMonth = <?= json_encode($_GET['month'] ?? date('Y-m')) ?>;
    
    function toggleMonthPicker() {
        const dropdown = document.getElementById('monthPickerDropdown');
        dropdown.classList.toggle('hidden');
        
        if (!dropdown.classList.contains('hidden')) {
            // Update current year display
            document.getElementById('currentYear').textContent = currentPickerYear;
            highlightSelectedMonth();
        }
    }
    
    function changeYear(direction) {
        currentPickerYear += direction;
        document.getElementById('currentYear').textContent = currentPickerYear;
        highlightSelectedMonth();
    }
    
    function selectMonth(month) {
        const monthStr = month.toString().padStart(2, '0');
        const yearMonth = `${currentPickerYear}-${monthStr}`;
        
        // Update hidden form
        document.getElementById('monthInput').value = yearMonth;
        
        // Update display
        const monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        document.getElementById('selectedMonth').textContent = `${monthNames[month]} ${currentPickerYear}`;
        
        // Close dropdown
        document.getElementById('monthPickerDropdown').classList.add('hidden');
        
        // Submit form
        document.getElementById('monthForm').submit();
    }
    
    function highlightSelectedMonth() {
        // Remove previous highlights
        document.querySelectorAll('.month-btn').forEach(btn => {
            btn.classList.remove('bg-blue-100', 'text-blue-600');
        });
        
        // Highlight current selection if year matches
        if (selectedMonth) {
            const [year, month] = selectedMonth.split('-');
            if (parseInt(year) === currentPickerYear) {
                const monthNum = parseInt(month);
                const monthBtns = document.querySelectorAll('.month-btn');
                if (monthBtns[monthNum - 1]) {
                    monthBtns[monthNum - 1].classList.add('bg-blue-100', 'text-blue-600');
                }
            }
        }
    }
    
    function clearSelection() {
        document.getElementById('selectedMonth').textContent = 'Select Month';
        document.getElementById('monthPickerDropdown').classList.add('hidden');
        selectedMonth = null;
    }
    
    function selectToday() {
        const today = new Date();
        currentPickerYear = today.getFullYear();
        selectMonth(today.getMonth() + 1);
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('monthPickerDropdown');
        const trigger = event.target.closest('[onclick="toggleMonthPicker()"]');
        
        if (!trigger && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Initialize
    if (selectedMonth) {
        const [year, month] = selectedMonth.split('-');
        currentPickerYear = parseInt(year);
    }
    
    // Add loading state when form submits
    document.getElementById('monthForm').addEventListener('submit', function() {
        const trigger = document.querySelector('[onclick="toggleMonthPicker()"]');
        const originalContent = trigger.innerHTML;
        
        // Add loading state
        trigger.innerHTML = `
            <i data-lucide="loader-2" class="w-4 h-4 text-gray-500 animate-spin"></i>
            <span class="text-sm font-medium text-gray-700">Loading...</span>
        `;
        lucide.createIcons();
    });
    
    // Add smooth scrolling for better UX
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
</body>
</html>