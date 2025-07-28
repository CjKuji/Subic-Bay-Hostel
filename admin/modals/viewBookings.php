<!-- ✅ Booking Details Modal -->
<div
  x-show="isOpen"
  x-transition.opacity.duration.300ms
  x-cloak
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
  @click.self="closeModal()"
  x-trap.noscroll="isOpen"
  @keydown.escape.window="isOpen && closeModal()"
  aria-modal="true"
  role="dialog"
  aria-labelledby="modal-title">
  <div
    class="relative bg-white rounded-3xl shadow-2xl max-w-5xl w-full mx-4 border border-gray-200"
    @click.stop
    tabindex="0">
    <!-- Scrollable Content -->
    <div class="max-h-[90vh] overflow-y-auto p-8 custom-scroll rounded-3xl">

      <!-- Close Button -->
      <button
        @click="closeModal"
        class="absolute top-5 right-5 text-gray-400 hover:text-red-500 focus:outline-none rounded-full p-2 border border-gray-200 bg-white transition"
        aria-label="Close modal">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>

      <!-- Modal Header -->
      <div class="flex items-center justify-between mb-6">
        <h2 id="modal-title" class="flex items-center gap-3 text-3xl font-bold text-[#DF5219]">
          <i data-lucide="notebook-pen" class="w-7 h-7 text-[#DF5219]"></i>
          Booking Details
        </h2>
        <div class="flex items-center gap-3">
          <span class="text-sm text-gray-600">Booking ID:</span>
          <span class="text-lg font-semibold text-gray-800" x-text="booking.id || '2'"></span>
        </div>
      </div>

      <!-- Status Badge -->
      <div class="mb-6">
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
          <i data-lucide="clock" class="w-3 h-3"></i>
          <span x-text="booking.status || 'Pending'"></span>
        </span>
      </div>

      <!-- Two Column Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Left Column - Guest Information -->
        <div class="space-y-6">
          <!-- Guest Information Section -->
          <div>
            <h3 class="flex items-center gap-2 text-lg font-semibold text-[#DF5219] mb-4">
              <i data-lucide="user" class="w-5 h-5"></i>
              Guest Information
            </h3>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <i data-lucide="user" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Full Name:</span>
                  <div class="text-blue-600 font-medium" x-text="booking.full_name || 'P Cabaltera'"></div>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <i data-lucide="mail" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Email:</span>
                  <div class="text-blue-600 font-medium" x-text="booking.email || 'jampolbahala@gmail.com'"></div>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <i data-lucide="phone" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Phone:</span>
                  <div class="text-blue-600 font-medium" x-text="booking.phone || '09157198677'"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Stay Dates Section -->
          <div>
            <h3 class="flex items-center gap-2 text-lg font-semibold text-[#DF5219] mb-4">
              <i data-lucide="calendar-days" class="w-5 h-5"></i>
              Stay Dates
            </h3>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <i data-lucide="calendar-days" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Check-in Date:</span>
                  <div class="text-gray-800 font-medium" x-text="formatDate(booking.check_in) || 'Jul 14, 2025'"></div>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <i data-lucide="calendar-check" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Check-out Date:</span>
                  <div class="text-gray-800 font-medium" x-text="formatDate(booking.check_out) || 'Jul 16, 2025'"></div>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Duration:</span>
                  <div class="text-gray-800 font-medium" x-text="getDurationText()"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Room & Stay Details -->
        <div class="space-y-6">
          <!-- Room & Stay Details Section -->
          <div>
            <h3 class="flex items-center gap-2 text-lg font-semibold text-[#DF5219] mb-4">
              <i data-lucide="bed" class="w-5 h-5"></i>
              Room & Stay Details
            </h3>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <i data-lucide="bed" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Room Type:</span>
                  <div class="text-blue-600 font-medium" x-text="getRoomBreakdownDisplay()"></div>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <i data-lucide="users" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Number of Guests:</span>
                  <div class="text-gray-800 font-medium" x-text="booking.No_of_guests || '0'"></div>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <i data-lucide="bed" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Number of Rooms:</span>
                  <div class="text-gray-800 font-medium" x-text="booking.No_of_guests || '0'"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Booking Information Section -->
          <div>
            <h3 class="flex items-center gap-2 text-lg font-semibold text-[#DF5219] mb-4">
              <i data-lucide="info" class="w-5 h-5"></i>
              Booking Information
            </h3>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Booking Date:</span>
                  <div class="text-gray-800 font-medium" x-text="formatDateTime(booking.created_at) || 'Jul 14, 2025, 10:15 AM'"></div>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <i data-lucide="activity" class="w-4 h-4 text-gray-500"></i>
                <div>
                  <span class="text-sm font-medium text-gray-700">Current Status:</span>
                  <div class="text-gray-800 font-medium" x-text="booking.status || 'Pending'"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Special Request Section -->
      <div class="mt-8 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
        <h3 class="flex items-center gap-2 text-lg font-semibold text-[#DF5219] mb-2">
          <i data-lucide="message-square" class="w-5 h-5"></i>
          Special Request
        </h3>
        <p class="text-gray-700 italic" x-text="booking.special_request || 'gusto ko may antik'"></p>
      </div>

      <!-- Guest List Table -->
      <template x-if="guests.length > 0">
        <div class="mt-8">
          <!-- Guest List Header -->
          <h3 class="text-lg font-semibold text-[#DF5219] mb-3 flex items-center gap-2">
            <i data-lucide="users" class="w-5 h-5"></i>
            Guest List
          </h3>

          <!-- Table -->
          <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full text-sm text-left text-gray-700">
              <thead class="bg-[#DF5219] text-white">
                <tr>
                  <th class="px-4 py-3 font-medium">Name</th>
                  <th class="px-4 py-3 font-medium">Gender</th>
                  <th class="px-4 py-3 font-medium">Room Type</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <template x-for="(guest, index) in guests" :key="index">
                  <tr class="hover:bg-[#fff6f4]">
                    <td class="px-4 py-3" x-text="guest.guest_name || 'N/A'"></td>
                    <td class="px-4 py-3 capitalize" x-text="guest.gender || 'N/A'"></td>
                    <td class="px-4 py-3" x-text="guest.room_type || 'N/A'"></td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </template>

      <!-- Last Updated -->
      <div class="mt-8 flex items-center gap-2 text-sm text-gray-500">
        <i data-lucide="clock" class="w-4 h-4"></i>
        <span>Last updated: Jul 14, 2025, 10:15 AM</span>
      </div>

      <!-- Action Buttons -->
      <div class="mt-8 flex justify-end gap-4" x-show="!loading">
        <button
          @click="handleBookingAction('confirm')"
          class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition shadow-md">
          <i data-lucide="check-circle" class="h-5 w-5"></i>
          Confirm Booking
        </button>
        <button
          @click="handleBookingAction('reject')"
          class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg transition shadow-md">
          <i data-lucide="x-circle" class="h-5 w-5"></i>
          Reject Booking
        </button>
      </div>

      <!-- Loading Spinner -->
      <div x-show="loading" class="text-center py-8" x-transition>
        <svg class="animate-spin h-8 w-8 mx-auto text-[#DF5219]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
        </svg>
        <p class="text-sm text-gray-600 mt-3">Processing...</p>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div
  x-show="showToast"
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0 transform translate-y-2"
  x-transition:enter-end="opacity-100 transform translate-y-0"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100 transform translate-y-0"
  x-transition:leave-end="opacity-0 transform translate-y-2"
  class="fixed top-4 right-4 z-50 max-w-md w-full"
  x-cloak>
  <div class="bg-white rounded-lg shadow-lg border-l-4 p-4"
       :class="toastType === 'success' ? 'border-green-500' : 'border-red-500'">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <i :data-lucide="toastType === 'success' ? 'check-circle' : 'x-circle'" 
           class="w-5 h-5"
           :class="toastType === 'success' ? 'text-green-500' : 'text-red-500'"></i>
      </div>
      <div class="ml-3">
        <p class="text-sm font-medium text-gray-900" x-text="toastMessage"></p>
      </div>
      <div class="ml-auto pl-3">
        <button @click="hideToast()" class="text-gray-400 hover:text-gray-600">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>
</div>