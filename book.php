
<?php
ob_start();
session_start();
include('includes/db.php');
include('includes/header.php');
// Flash messages
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);
// Retain old input
$old = $_SESSION['old'] ?? [];
$full_name = $old['full_name'] ?? '';
$email = $old['email'] ?? '';
$phone = $old['phone'] ?? '';
$gender = $old['gender'] ?? '';
$room_type = $old['room_type'] ?? '';
$check_in = $old['check_in'] ?? '';
$check_out = $old['check_out'] ?? '';
$message_field = $old['message_field'] ?? '';
$total_guests = $old['total_guests'] ?? ''; // companions only
$agree = isset($old['agree']);
unset($_SESSION['old']);
// Fetch room prices
$roomPrices = [];
$query = "SELECT id, title, price FROM room_types";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $roomPrices[$row['id']] = [
        'title' => $row['title'],
        'price' => (float)$row['price']
    ];
}
?>
<main x-data="termsHandler()">
  <section class="py-16 px-4 min-h-screen flex justify-center items-start bg-[#FFFCFB]">
    <div class="w-full max-w-3xl bg-white shadow-xl rounded-2xl p-8 sm:p-12">
      <h1 class="text-4xl font-bold mb-8 text-center">Room Reservation</h1>
      <?php if ($message): ?>
        <div class="<?= $messageType === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?> p-4 rounded mb-6 text-center">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>
      <form method="POST" action="book-handler.php" class="space-y-8" novalidate>
        <div>
          <label for="full_name" class="block text-sm text-gray-600 mb-2">Full Name (Primary Guest)</label>
          <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required
            class="w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black transition-colors" 
            placeholder="Juan Dela Cruz">
          <div id="full_name_error" class="text-red-500 text-sm mt-1 hidden">Full name is required</div>
        </div>
        <div>
          <label for="email" class="block text-sm text-gray-600 mb-2">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required
            class="w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black transition-colors" 
            placeholder="example@gmail.com">
          <div id="email_error" class="text-red-500 text-sm mt-1 hidden">Please enter a valid email address</div>
        </div>
        <div>
          <label for="phone" class="block text-sm text-gray-600 mb-2">Phone Number</label>
          <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" pattern="^(\+63|0)9\d{9}$" required
            class="w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black transition-colors" 
            placeholder="0917xxxxxxx"
            oninput="validatePhoneInput(this)"
            onkeypress="return allowOnlyNumbersAndPlus(event)">
          <div id="phone_error" class="text-red-500 text-sm mt-1 hidden">Please enter a valid Philippine phone number (0917xxxxxxx)</div>
        </div>
        <div>
          <label for="gender" class="block text-sm text-gray-600 mb-2">Gender</label>
          <select id="gender" name="gender" required class="w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black transition-colors">
            <option value="">Select Gender</option>
            <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
          </select>
          <div id="gender_error" class="text-red-500 text-sm mt-1 hidden">Please select your gender</div>
        </div>
        <div>
          <label for="room_type" class="block text-sm text-gray-600 mb-2">Room Type</label>
          <select id="room_type" name="room_type" required class="w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black transition-colors">
            <option value="">Select Room</option>
            <?php foreach ($roomPrices as $id => $room): ?>
              <option value="<?= $id ?>" <?= $room_type == $id ? 'selected' : '' ?>>
                <?= htmlspecialchars($room['title']) ?> - ₱<?= number_format($room['price'], 2) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div id="room_type_error" class="text-red-500 text-sm mt-1 hidden">Please select a room type</div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div>
            <label for="check_in" class="block text-sm text-gray-600 mb-2">Check-in</label>
            <input type="date" id="check_in" name="check_in" value="<?= htmlspecialchars($check_in) ?>" min="<?= date('Y-m-d') ?>" required
              class="w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black transition-colors">
            <div id="check_in_error" class="text-red-500 text-sm mt-1 hidden">Please select a check-in date</div>
          </div>
          <div>
            <label for="check_out" class="block text-sm text-gray-600 mb-2">Check-out</label>
            <input type="date" id="check_out" name="check_out" value="<?= htmlspecialchars($check_out) ?>" min="<?= date('Y-m-d') ?>" required
              class="w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black transition-colors">
            <div id="check_out_error" class="text-red-500 text-sm mt-1 hidden">Check-out must be after check-in date</div>
          </div>
        </div>
        <div>
          <label for="total_guests" class="block text-sm text-gray-600 mb-2">Number of Additional Guests (Optional)</label>
          <input type="number" id="total_guests" name="total_guests" value="<?= htmlspecialchars($total_guests) ?>" min="0" max="10"
            onchange="updateGuestFields()" autocomplete="off"
            class="w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black transition-colors"
            placeholder="E.g., 2 (for two additional guests)">
          <div id="total_guests_error" class="text-red-500 text-sm mt-1 hidden">Please enter number of additional guests</div>
        </div>
        <div id="guest-info-section" class="space-y-6 hidden">
          <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">Additional Guest Information</h3>
          <div id="guest-fields"></div>
        </div>
        <div>
          <label for="message_field" class="block text-sm text-gray-600 mb-2">Special Request</label>
          <textarea id="message_field" name="message_field" rows="4"
            class="w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black transition-colors"><?= htmlspecialchars($message_field) ?></textarea>
        </div>
        <div class="flex items-center space-x-3">
          <input type="checkbox" name="agree" id="agree" class="accent-black w-5 h-5" required @click.prevent="termsOpen = true">
          <label for="agree" class="text-sm text-gray-700 cursor-pointer">
            I agree to the
            <span @click="termsOpen = true" class="underline text-blue-600 cursor-pointer hover:text-blue-800">
              terms & conditions
            </span>
          </label>
          <div id="agree_error" class="text-red-500 text-sm hidden">You must agree to the terms and conditions</div>
        </div>
        <button type="button" id="previewBtn"
          class="w-full bg-black hover:bg-gray-900 text-white font-bold py-4 rounded-lg transition duration-300">
          Preview Booking
        </button>
      </form>
    </div>
  </section>
  <!-- terms-conditions.php -->
<div x-show="termsOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden relative flex flex-col">
        <button @click="termsOpen = false"
            class="absolute top-4 right-4 text-gray-400 hover:text-black transition text-xl">
            ✕
        </button>
        <div class="bg-black text-white py-5 px-6 rounded-t-2xl">
            <h2 class="text-xl font-bold uppercase tracking-wide text-center">Terms & Conditions</h2>
        </div>
        <div class="px-6 py-6 overflow-y-auto max-h-[60vh] text-gray-800 space-y-5 text-sm leading-relaxed"
            @scroll="checkScroll">
            <p class="text-center">Welcome to Subic Bay Hostel & Dormitory. Please read carefully.</p>
            <div>
                <h3 class="text-black font-semibold mb-2">Booking & Rates</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>Discounts available for extended stays & groups.</li>
                    <li>PHP 600/night Standard, PHP 900/night Deluxe Capsule.</li>
                    <li>280 Standard & 80 Deluxe capsules available.</li>
                </ul>
            </div>
            <div>
                <h3 class="text-black font-semibold mb-2">Stay Options</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>We accept daily, weekly, monthly, and yearly stays.</li>
                    <li>Group discounts are also available.</li>
                </ul>
            </div>
            <div>
                <h3 class="text-black font-semibold mb-2">Reservation & Payments</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>PHP 200 advance payment (refundable).</li>
                    <li>Pay on check-in: extra PHP 400 or PHP 700 respectively.</li>
                </ul>
            </div>
            <div>
                <h3 class="text-black font-semibold mb-2">Need Help?</h3>
                <p>For any questions, visit our <a href="contact.php" class="underline text-red-700 hover:text-red-900 transition">Contact Page</a>.</p>
            </div>
            <p class="text-center text-xs text-gray-500">
                By completing this process, you agree to these terms.
            </p>
        </div>
        <div class="relative bg-gray-100 py-4 px-6 rounded-b-2xl">
            <div class="h-2 w-full bg-gray-300 rounded-full overflow-hidden mb-4">
                <div class="h-full bg-black transition-all duration-300"
                    :style="{ width: scrolledToEnd ? '100%' : '30%' }"></div>
            </div>
            <button :disabled="!scrolledToEnd"
                @click="finish()"
                class="w-full py-3 rounded-lg font-semibold transition
                           bg-black text-white hover:bg-gray-800
                           disabled:bg-gray-300 disabled:cursor-not-allowed">
                I Understand & Agree
            </button>
            <div class="text-xs mt-2 text-gray-500 text-center" x-show="!scrolledToEnd">
                Scroll to the bottom to enable
            </div>
        </div>
    </div>
</div>
  <!-- Validation Error Modal -->
<div id="validationErrorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-lg p-6 mx-4 max-w-md w-full shadow-xl">
    <div class="flex items-center mb-4">
      <div class="flex-shrink-0">
        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.68-.833-2.45 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
      </div>
      <div class="ml-3">
        <h3 class="text-lg font-medium text-gray-900">Form Incomplete</h3>
      </div>
    </div>
    <div class="mb-6">
      <p class="text-sm text-gray-600">Some details are missing or incorrect. Kindly review and try again.</p>
    </div>
    <div class="flex justify-end">
      <button id="closeValidationModal" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
        OK
      </button>
    </div>
  </div>
</div>
  <?php include 'modals/book-modal.php'; ?>
</main>
<script>
  // Terms handler for agreement modal
  function termsHandler() {
    return {
      termsOpen: false,
      scrolledToEnd: false,
      checkScroll(event) {
        const el = event.target;
        this.scrolledToEnd = el.scrollTop + el.clientHeight >= el.scrollHeight - 10;
      },
      finish() {
        this.termsOpen = false;
        this.scrolledToEnd = false;
        document.getElementById('agree').checked = true;
        validateField('agree');
      }
    }
  }
  // Room prices data (passed from PHP to JS)
  const roomPrices = <?= json_encode($roomPrices) ?>;
  // Phone input validation functions
  function allowOnlyNumbersAndPlus(event) {
    const char = String.fromCharCode(event.which);
    if (!/[0-9+]/.test(char)) {
      event.preventDefault();
      return false;
    }
    return true;
  }
  // Function to clean and validate phone number input
  function validatePhoneInput(input) {
    let value = input.value.replace(/[^0-9+]/g, '');
    if (value.indexOf('+') > 0) {
      value = value.replace(/\+/g, '');
    }
    if ((value.match(/\+/g) || []).length > 1) {
      value = value.replace(/\+(?!^)/g, '');
    }
    input.value = value;
  }
  // Validation function for each field
  function validateField(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + '_error');
    let isValid = true;
    let errorMessage = '';

    switch (fieldId) {
      case 'full_name':
        if (!field.value.trim()) {
          isValid = false;
          errorMessage = 'Full name is required';
        } else if (field.value.trim().length < 2) {
          isValid = false;
          errorMessage = 'Full name must be at least 2 characters';
        }
        break;
      case 'email':
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!field.value.trim()) {
          isValid = false;
          errorMessage = 'Email is required';
        } else if (!emailRegex.test(field.value)) {
          isValid = false;
          errorMessage = 'Please enter a valid email address';
        }
        break;
      case 'phone':
        const phoneRegex = /^(\+63|0)9\d{9}$/;
        if (!field.value.trim()) {
          isValid = false;
          errorMessage = 'Phone number is required';
        } else if (!phoneRegex.test(field.value)) {
          isValid = false;
          errorMessage = 'Please enter a valid Philippine phone number (0917xxxxxxx)';
        }
        break;
      case 'gender':
        if (!field.value) {
          isValid = false;
          errorMessage = 'Please select your gender';
        }
        break;
      case 'room_type':
        if (!field.value) {
          isValid = false;
          errorMessage = 'Please select a room type';
        }
        break;
      case 'check_in':
        if (!field.value) {
          isValid = false;
          errorMessage = 'Please select a check-in date';
        } else {
          const today = new Date();
          const checkInDate = new Date(field.value);
          if (checkInDate < today.setHours(0, 0, 0, 0)) {
            isValid = false;
            errorMessage = 'Check-in date cannot be in the past';
          }
        }
        break;
      case 'check_out':
        if (!field.value) {
          isValid = false;
          errorMessage = 'Please select a check-out date';
        } else {
          const checkInDate = new Date(document.getElementById('check_in').value);
          const checkOutDate = new Date(field.value);
          if (checkOutDate <= checkInDate) {
            isValid = false;
            errorMessage = 'Check-out must be after check-in date';
          }
        }
        break;
      case 'total_guests':
        const guestCount = parseInt(field.value) || 0;
        if (guestCount < 0 || guestCount > 10) {
          isValid = false;
          errorMessage = 'Please enter number of additional guests';
        }
        break;
      case 'agree':
        if (!field.checked) {
          isValid = false;
          errorMessage = 'You must agree to the terms and conditions';
        }
        break;
    }
    // Update field styling
    if (isValid) {
      field.classList.remove('border-red-500');
      field.classList.add('border-gray-400');
      if (errorDiv) {
        errorDiv.classList.add('hidden');
      }
    } else {
      field.classList.remove('border-gray-400');
      field.classList.add('border-red-500');
      if (errorDiv) {
        errorDiv.textContent = errorMessage;
        errorDiv.classList.remove('hidden');
      }
    }
    return isValid;
  }
  // Guest field validation function
  function validateGuestField(index, fieldType) {
    const field = document.querySelector(`[name="${fieldType}[${index}]"]`);
    const container = field.closest('.bg-gray-50');
    let errorDiv = container.querySelector(`.${fieldType}_error_${index}`);
        if (!errorDiv) {
      errorDiv = document.createElement('div');
      errorDiv.className = `${fieldType}_error_${index} text-red-500 text-xs mt-1 hidden`;
      field.parentNode.appendChild(errorDiv);
    }
    let isValid = true;
    let errorMessage = '';
    switch (fieldType) {
      case 'guest_name':
        if (!field.value.trim()) {
          isValid = false;
          errorMessage = 'Companion name is required';
        } else if (field.value.trim().length < 2) {
          isValid = false;
          errorMessage = 'Name must be at least 2 characters';
        }
        break;
      case 'guest_gender':
        if (!field.value) {
          isValid = false;
          errorMessage = 'Please select gender';
        }
        break;
      case 'guest_room_type':
        if (!field.value) {
          isValid = false;
          errorMessage = 'Please select room type';
        }
        break;
    }
    if (isValid) {
      field.classList.remove('border-red-500');
      field.classList.add('border-gray-300');
      errorDiv.classList.add('hidden');
    } else {
      field.classList.remove('border-gray-300');
      field.classList.add('border-red-500');
      errorDiv.textContent = errorMessage;
      errorDiv.classList.remove('hidden');
    }
    return isValid;
  }
  // Function to update guest input fields dynamically
  function updateGuestFields() {
    const totalGuests = parseInt(document.getElementById('total_guests').value) || 0;
    const section = document.getElementById('guest-info-section');
    const container = document.getElementById('guest-fields');
    container.innerHTML = '';

    if (totalGuests < 1) {
      section.classList.add('hidden');
      return;
    }
    section.classList.remove('hidden');
    for (let i = 0; i < totalGuests; i++) {
      let options = `<option value="">Select Room</option>`;
      for (const id in roomPrices) {
        options += `<option value="${id}">${roomPrices[id].title} - ₱${roomPrices[id].price.toLocaleString()}</option>`;
      }
      container.innerHTML += `
        <div class="bg-gray-50 p-4 rounded-lg space-y-4">
          <h4 class="font-semibold text-gray-700">Additional Guest ${i + 1}</h4>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm text-gray-600 mb-1">Full Name *</label>
              <input type="text" name="guest_name[${i}]" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-black transition-colors"
                oninput="validateGuestField(${i}, 'guest_name')"
                onblur="validateGuestField(${i}, 'guest_name')">
            </div>
            <div>
              <label class="block text-sm text-gray-600 mb-1">Gender *</label>
              <select name="guest_gender[${i}]" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-black transition-colors"
                onchange="validateGuestField(${i}, 'guest_gender')">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
            <div>
              <label class="block text-sm text-gray-600 mb-1">Room Type *</label>
              <select name="guest_room_type[${i}]" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-black transition-colors"
                onchange="validateGuestField(${i}, 'guest_room_type')">
                ${options}
              </select>
            </div>
          </div>
        </div>
      `;
    }
  }
  // Add event listeners for real-time validation
  document.addEventListener('DOMContentLoaded', () => {
    updateGuestFields();
        // Add validation listeners to all main form fields
    const fieldsToValidate = ['full_name', 'email', 'phone', 'gender', 'room_type', 'check_in', 'check_out', 'total_guests'];
    
    fieldsToValidate.forEach(fieldId => {
      const field = document.getElementById(fieldId);
      if (field) {
        if (field.type === 'select-one') {
          field.addEventListener('change', () => validateField(fieldId));
        } else {
          field.addEventListener('input', () => validateField(fieldId));
          field.addEventListener('blur', () => validateField(fieldId));
        }
      }
    });
    // Special handling for check-out date validation
    document.getElementById('check_in').addEventListener('change', () => {
      validateField('check_in');
      validateField('check_out'); // Re-validate check-out when check-in changes
    });
    document.getElementById('total_guests').addEventListener('input', () => {
      validateField('total_guests');
      updateGuestFields();
    });
    // Agreement checkbox
    document.getElementById('agree').addEventListener('change', () => validateField('agree'));
  });
  // Enhanced preview button with validation
  document.getElementById("previewBtn").addEventListener("click", function () {
    const form = document.querySelector("form");
    let isFormValid = true;
    // Validate all main fields
    const fieldsToValidate = ['full_name', 'email', 'phone', 'gender', 'room_type', 'check_in', 'check_out', 'total_guests', 'agree'];
    fieldsToValidate.forEach(fieldId => {
      if (!validateField(fieldId)) {
        isFormValid = false;
      }
    });
    // Validate guest fields only if there are companions
    const guestsCount = parseInt(form.total_guests.value) || 0;
    if (guestsCount > 0) {
      for (let i = 0; i < guestsCount; i++) {
        if (!validateGuestField(i, 'guest_name') || 
            !validateGuestField(i, 'guest_gender') || 
            !validateGuestField(i, 'guest_room_type')) {
          isFormValid = false;
        }
      }
    }
    if (!isFormValid) {
      document.getElementById("validationErrorModal").classList.remove("hidden");
      return;
    }
    // Rest of your existing preview logic...
    const name = form.full_name.value.trim();
    const email = form.email.value.trim();
    const phone = form.phone.value.trim();
    const checkIn = form.check_in.value;
    const checkOut = form.check_out.value;
    const request = form.message_field.value.trim();
    const primaryGender = form.gender.value;
    const primaryRoom = form.room_type.value;
    const nights = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
    let roomSummary = {};
    const guests = [];
    // Primary guest
    const primaryRoomTitle = roomPrices[primaryRoom]?.title || 'Unknown';
    const primaryRoomPrice = roomPrices[primaryRoom]?.price || 0;
    roomSummary[primaryRoom] = 1;
    guests.push(`<strong>1. ${name} (${primaryGender}) - ${primaryRoomTitle}</strong>`);
    for (let i = 0; i < guestsCount; i++) {
      const gName = form.elements[`guest_name[${i}]`]?.value.trim();
      const gGender = form.elements[`guest_gender[${i}]`]?.value;
      const gRoom = form.elements[`guest_room_type[${i}]`]?.value;
      const gRoomTitle = roomPrices[gRoom]?.title || 'Unknown';
      roomSummary[gRoom] = (roomSummary[gRoom] || 0) + 1;
      guests.push(`${i + 2}. ${gName} (${gGender}) - ${gRoomTitle}`);
    }
    // Populate modal
    document.getElementById("modalName").innerText = name;
    document.getElementById("modalEmail").innerText = email;
    document.getElementById("modalPhone").innerText = phone;
    document.getElementById("modalCheckIn").innerText = checkIn;
    document.getElementById("modalCheckOut").innerText = checkOut;
    document.getElementById("modalTotalGuests").innerText = guestsCount + 1;
    document.getElementById("modalRequest").innerText = request || "None";
    document.getElementById("modalGuestList").innerHTML = guests.join("<br>");
    let roomLines = '', total = 0;
    for (const id in roomSummary) {
      const count = roomSummary[id];
      const title = roomPrices[id].title;
      const price = roomPrices[id].price;
      roomLines += `${title}: ${count} × ₱${price.toLocaleString()} × ${nights} night(s)<br>`;
      total += count * price * nights;
    }
    document.getElementById("modalRoomSummary").innerHTML = roomLines;
    document.getElementById("modalTotal").innerText = total.toLocaleString();
    document.getElementById("previewModal").classList.remove("hidden");
  });
  // Validation Error Modal Handler
  document.getElementById("closeValidationModal").addEventListener("click", () => {
    document.getElementById("validationErrorModal").classList.add("hidden");
  });
  // Close modal when clicking outside
  document.getElementById("validationErrorModal").addEventListener("click", (e) => {
    if (e.target === document.getElementById("validationErrorModal")) {
      document.getElementById("validationErrorModal").classList.add("hidden");
    }
  });
  // Confirm Booking
  document.getElementById("confirmBookingBtn").addEventListener("click", () => {
    document.querySelector("form").submit();
  });
  // Cancel Preview
  document.getElementById("cancelPreview").addEventListener("click", () => {
    document.getElementById("previewModal").classList.add("hidden");
  });
</script>
<?php include('includes/footer.php');
ob_end_flush(); ?>  