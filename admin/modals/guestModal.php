<!-- Enhanced Booking Details Modal (2025 UI with refined structure and styles) -->
<div
  x-cloak
  x-show="isOpen && booking"
  x-transition:enter="transition ease-out duration-200"
  x-transition:enter-start="opacity-0 scale-95"
  x-transition:enter-end="opacity-100 scale-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100 scale-100"
  x-transition:leave-end="opacity-0 scale-95"
  @click.outside="closeModal"
  @keydown.escape.window="closeModal"
  role="dialog"
  aria-modal="true"
  aria-labelledby="modal-title"
  class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm">
  <div
    class="w-full max-w-3xl bg-white text-black shadow-2xl border border-black/10 p-6 sm:p-8 max-h-[90vh] overflow-y-auto transition-all duration-300 space-y-8">
    <!-- Close Button -->
    <button
      @click="closeModal"
      class="absolute top-4 right-4 text-black/50 hover:text-black transition"
      aria-label="Close">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>

    <!-- Header -->
    <div class="flex items-center gap-3">
      <i data-lucide="calendar-check" class="w-6 h-6 text-indigo-600"></i>
      <h2 id="modal-title" class="text-2xl font-bold tracking-tight leading-tight">
        Booking Details
      </h2>
    </div>

    <!-- Guest Info -->
    <section class="bg-white border border-black/10 rounded-xl p-6 shadow-sm space-y-6">
      <h3 class="text-xs font-semibold uppercase text-black/50 tracking-widest">
        Guest Information
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Full Name -->
        <div class="space-y-1.5">
          <label class="text-sm font-medium flex items-center gap-1">
            <i data-lucide="user" class="w-4 h-4 text-indigo-600"></i> Full Name
          </label>
          <template x-if="!isEditing">
            <p class="text-sm leading-relaxed" x-text="booking?.name || 'N/A'"></p>
          </template>
          <template x-if="isEditing">
            <input x-model="booking.name" type="text" placeholder="Enter full name"
              class="w-full border border-black/10 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none" />
          </template>
        </div>

        <!-- Email -->
        <div class="space-y-1.5">
          <label class="text-sm font-medium flex items-center gap-1">
            <i data-lucide="mail" class="w-4 h-4 text-indigo-600"></i> Email
          </label>
          <template x-if="!isEditing">
            <p class="text-sm leading-relaxed" x-text="booking?.email || 'N/A'"></p>
          </template>
          <template x-if="isEditing">
            <input x-model="booking.email" type="email" placeholder="guest@example.com"
              class="w-full border border-black/10 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none" />
          </template>
        </div>

        <!-- Phone -->
        <div class="space-y-1.5">
          <label class="text-sm font-medium flex items-center gap-1">
            <i data-lucide="phone" class="w-4 h-4 text-indigo-600"></i> Phone
          </label>
          <template x-if="!isEditing">
            <p class="text-sm leading-relaxed" x-text="booking?.phone || 'N/A'"></p>
          </template>
          <template x-if="isEditing">
            <input x-model="booking.phone" type="text" placeholder="+63 900 000 0000"
              class="w-full border border-black/10 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none" />
          </template>
        </div>

        <!-- Gender -->
        <div class="space-y-1.5">
          <label class="text-sm font-medium flex items-center gap-1">
            <i data-lucide="user-check" class="w-4 h-4 text-indigo-600"></i> Gender
          </label>
          <template x-if="!isEditing">
            <p class="text-sm leading-relaxed" x-text="booking?.gender || 'N/A'"></p>
          </template>
          <template x-if="isEditing">
            <select x-model="booking.gender"
              class="w-full border border-black/10 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none">
              <option value="">Select gender</option>
              <option>Male</option>
              <option>Female</option>
            </select>
          </template>
        </div>
      </div>
    </section>

    <!-- Booking Info -->
    <section class="bg-white border border-black/10 rounded-xl p-6 shadow-sm space-y-6">
      <h3 class="text-xs font-semibold uppercase text-black/50 tracking-widest">
        Booking Information
      </h3>
      <div class="grid gap-6">
        <div class="grid sm:grid-cols-2 gap-6">
          <!-- Check-in -->
          <div class="space-y-1.5">
            <label class="text-sm font-medium">Check-in</label>
            <template x-if="!isEditing">
              <p class="text-sm" x-text="booking?.check_in || 'N/A'"></p>
            </template>
            <template x-if="isEditing">
              <input x-model="booking.check_in" type="date"
                class="w-full border border-black/10 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none" />
            </template>
          </div>

          <!-- Check-out -->
          <div class="space-y-1.5">
            <label class="text-sm font-medium">Check-out</label>
            <template x-if="!isEditing">
              <p class="text-sm" x-text="booking?.check_out || 'N/A'"></p>
            </template>
            <template x-if="isEditing">
              <input x-model="booking.check_out" type="date"
                class="w-full border border-black/10 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none" />
            </template>
          </div>
        </div>

        <!-- Room Type -->
        <div class="space-y-1.5">
          <label class="text-sm font-medium">Room Type</label>
          <template x-if="!isEditing">
            <p class="text-sm" x-text="booking?.room_type || 'N/A'"></p>
          </template>
          <template x-if="isEditing">
            <select x-model="booking.room_type_id"
              class="w-full border border-black/10 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none">
              <option value="">Select Room Type</option>
              <template x-for="type in roomTypes" :key="type.id">
                <option :value="type.id" x-text="type.title"></option>
              </template>
            </select>
          </template>
        </div>

        <!-- Special Request -->
        <div class="space-y-1.5">
          <label class="text-sm font-medium">Special Request</label>
          <template x-if="!isEditing">
            <p class="text-sm leading-relaxed" x-text="booking?.special_request || 'None'"></p>
          </template>
          <template x-if="isEditing">
            <textarea x-model="booking.special_request" rows="3" placeholder="Any specific requests..."
              class="w-full border border-black/10 rounded-md px-3 py-2 text-sm resize-none focus:ring-2 focus:ring-indigo-600 outline-none"></textarea>
          </template>
        </div>

        <!-- Assigned Room -->
        <template x-if="!isEditing">
          <div class="space-y-1.5">
            <label class="text-sm font-medium">Assigned Room</label>
            <template x-if="booking?.room_number">
              <p class="text-sm">
                Room <span class="font-semibold text-indigo-600" x-text="booking.room_number"></span>,
                <span x-text="'Floor ' + booking.floor_number + ' – ' + booking.section_name"></span>
              </p>
            </template>
            <template x-if="!booking?.room_number">
              <p class="italic text-black/40">Not assigned yet</p>
            </template>
          </div>
        </template>

        <!-- Booked On -->
        <template x-if="!isEditing">
          <div class="space-y-1.5">
            <label class="text-sm font-medium">Booked On</label>
            <p class="text-sm" x-text="booking?.created_at || 'N/A'"></p>
          </div>
        </template>
      </div>
    </section>

    <!-- Action -->
    <div class="text-right pt-2">
      <button
        @click="toggleEdit"
        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-md bg-indigo-600 hover:bg-indigo-700 text-white transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <i data-lucide="edit" class="w-4 h-4" x-show="!isEditing"></i>
        <i data-lucide="save" class="w-4 h-4" x-show="isEditing"></i>
        <span x-text="isEditing ? 'Save Changes' : 'Edit Booking'"></span>
      </button>

      <!-- ✅ Check Out Button -->
      <button
        @click="checkOutBooking(booking)"
        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-md bg-zinc-700 hover:bg-zinc-800 text-white transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-700">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        <span>Check Out</span>
      </button>
    </div>
  </div>
</div>


<!-- Assign/Reassign Room Modal -->
<!-- Assign Room Modal -->
<div
  x-cloak
  x-data="{ isSubmitting: false }"
  x-show="isAssignRoomOpen"
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0 scale-95"
  x-transition:enter-end="opacity-100 scale-100"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100 scale-100"
  x-transition:leave-end="opacity-0 scale-95"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4 sm:px-6"
  @click.outside="closeAssignRoomModal"
  @keydown.escape.window="closeAssignRoomModal"
  @keydown.tab.prevent="handleTabKey($event, $refs.modalBox)"
  role="dialog"
  aria-modal="true"
  aria-labelledby="assign-room-modal-title">
  <!-- Modal Box -->
  <div
    x-ref="modalBox"
    class="w-full max-w-4xl max-h-[90vh] bg-white shadow-2xl rounded-2xl overflow-y-auto p-6 sm:p-8 relative">
    <!-- Close Button -->
    <button
      @click="closeAssignRoomModal"
      class="absolute top-5 right-5 text-gray-400 hover:text-gray-700 transition"
      aria-label="Close modal">
      <i data-lucide="x" class="w-6 h-6"></i>
    </button>

    <!-- Modal Title -->
    <h2 id="assign-room-modal-title" class="text-2xl font-semibold text-indigo-600 flex items-center gap-2 mb-6">
      <i data-lucide="home" class="w-6 h-6 text-indigo-600"></i>
      Assign Room to Booking
    </h2>

    <!-- Room Selection Section -->
    <div class="text-sm text-gray-800 space-y-6">
      <div>
        <!-- Room Header -->
        <div class="flex items-center gap-2 mb-3">
          <i data-lucide="bed" class="w-5 h-5 text-indigo-500"></i>
          <span class="font-medium text-base">Available Rooms</span>
        </div>

        <!-- No Rooms Message -->
        <template x-if="Object.keys(groupedRooms).length === 0">
          <p class="text-gray-400 italic">No rooms found for this floor/section.</p>
        </template>

        <!-- Room Group Grid -->
        <div :key="refreshKey">
          <div class="space-y-10" x-show="Object.keys(groupedRooms).length > 0">
            <template x-for="(rooms, sectionName) in groupedRooms" :key="sectionName">
              <div class="space-y-5">
                <!-- Section Title -->
                <h3 class="text-lg font-bold text-indigo-700 border-b pb-1" x-text="sectionName"></h3>

                <!-- Deluxe Capsules -->
                <template x-if="rooms.some(r => r.room_type_id === 2)">
                  <div class="space-y-2">
                    <h4 class="text-sm font-semibold text-gray-700">Deluxe Capsules</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                      <template
                        x-for="room in rooms.filter(r => r.room_type_id === 2)"
                        :key="room.room_id + '-deluxe'">
                        <div class="relative group">
                          <button
                            @click="selectRoom(room)"
                            :disabled="isRoomOccupied(room)"
                            :class="{
                              'bg-gray-300 text-white cursor-not-allowed': isRoomOccupied(room),
                              'bg-indigo-600 text-white ring-2 ring-indigo-400': selectedRoom?.room_id === room.room_id && !isRoomOccupied(room),
                              'bg-white border border-gray-300 hover:bg-indigo-50': !isRoomOccupied(room) && selectedRoom?.room_id !== room.room_id
                            }"
                            class="w-full h-16 rounded-lg font-semibold text-sm transition"
                            :aria-label="isRoomOccupied(room) ? 'Assigned to: ' + getRoomTooltip(room) : 'Assign this room'">
                            <span x-text="room.room_number"></span>
                          </button>
                          <div
                            x-show="isRoomOccupied(room)"
                            x-transition
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 pointer-events-none z-10">
                            Assigned to: <span x-text="getRoomTooltip(room)"></span>
                          </div>
                        </div>
                      </template>
                    </div>
                  </div>
                </template>

                <!-- Standard Capsules -->
                <template x-if="rooms.some(r => r.room_type_id === 1)">
                  <div class="space-y-2 pt-4 border-t">
                    <h4 class="text-sm font-semibold text-gray-700">Standard Capsules</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4">
                      <template
                        x-for="room in rooms.filter(r => r.room_type_id === 1)"
                        :key="room.room_id + '-standard'">
                        <div class="relative group">
                          <button
                            @click="selectRoom(room)"
                            :disabled="isRoomOccupied(room)"
                            :class="{
                              'bg-gray-300 text-white cursor-not-allowed': isRoomOccupied(room),
                              'bg-indigo-600 text-white ring-2 ring-indigo-400': selectedRoom?.room_id === room.room_id && !isRoomOccupied(room),
                              'bg-white border border-gray-300 hover:bg-indigo-50': !isRoomOccupied(room) && selectedRoom?.room_id !== room.room_id
                            }"
                            class="w-full h-16 rounded-lg font-semibold text-sm transition"
                            :aria-label="isRoomOccupied(room) ? 'Assigned to: ' + getRoomTooltip(room) : 'Assign this room'">
                            <span x-text="room.room_number"></span>
                          </button>
                          <div
                            x-show="isRoomOccupied(room)"
                            x-transition
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 pointer-events-none z-10">
                            Assigned to: <span x-text="getRoomTooltip(room)"></span>
                          </div>
                        </div>
                      </template>
                    </div>
                  </div>
                </template>
              </div>
            </template>
          </div>
        </div>
      </div>

      <!-- Confirm Assignment Modal -->
      <template x-if="showConfirmDialog">
        <div class="fixed inset-0 bg-black/40 z-60 flex items-center justify-center px-4">
          <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
            <div class="flex items-center gap-2 mb-4">
              <i data-lucide="check-circle" class="w-6 h-6 text-indigo-600"></i>
              <h3 class="text-lg font-semibold text-gray-800">Confirm Room Assignment</h3>
            </div>
            <p class="text-sm text-gray-700 mb-6">
              Assign <span class="font-semibold text-indigo-600" x-text="booking?.name || 'this guest'"></span>
              to room <span class="font-semibold text-gray-900" x-text="selectedRoom?.room_number"></span>?
            </p>
            <div class="flex justify-end gap-3">
              <button
                @click="showConfirmDialog = false"
                class="px-4 py-2 text-gray-700 hover:text-indigo-600 rounded-md border border-gray-300">
                Cancel
              </button>
              <button
                @click="assignRoomToBooking(false)"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-md">
                Confirm
              </button>
            </div>
          </div>
        </div>
      </template>

      <!-- Confirm Reassignment Modal -->
      <template x-if="showReassignConfirmDialog">
        <div class="fixed inset-0 bg-black/40 z-60 flex items-center justify-center px-4">
          <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
            <div class="flex items-center gap-2 mb-4">
              <i data-lucide="repeat" class="w-6 h-6 text-indigo-600"></i>
              <h3 class="text-lg font-semibold text-gray-800">Reassign Room</h3>
            </div>
            <p class="text-sm text-gray-700 mb-6">
              <span class="font-semibold text-indigo-600" x-text="booking?.name || 'This guest'"></span>
              is currently assigned to room
              <span class="font-semibold text-gray-900" x-text="booking?.current_room_number"></span>.
              Reassign to <span class="font-semibold text-gray-900" x-text="selectedRoom?.room_number"></span>?
            </p>
            <div class="flex justify-end gap-3">
              <button
                @click="showReassignConfirmDialog = false"
                class="px-4 py-2 text-gray-700 hover:text-indigo-600 rounded-md border border-gray-300">
                Cancel
              </button>
              <button
                @click="assignRoomToBooking(true)"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-md">
                Reassign
              </button>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div
  x-show="flashMessage.text"
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0 translate-y-2"
  x-transition:enter-end="opacity-100 translate-y-0"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100 translate-y-0"
  x-transition:leave-end="opacity-0 translate-y-2"
  class="fixed top-4 right-4 z-[9999] w-full max-w-md"
  x-cloak>
  <div
    class="bg-white border-l-4 p-4 rounded-lg shadow-lg"
    :class="flashMessage.success ? 'border-indigo-600' : 'border-black'">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <i
          :data-lucide="flashMessage.success ? 'check-circle' : 'x-circle'"
          class="w-5 h-5"
          :class="flashMessage.success ? 'text-indigo-600' : 'text-black'"></i>
      </div>
      <div class="ml-3">
        <p class="text-sm font-medium text-black" x-text="flashMessage.text"></p>
      </div>
      <div class="ml-auto pl-3">
        <button @click="flashMessage.text = ''" class="text-black/50 hover:text-black">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>
</div>