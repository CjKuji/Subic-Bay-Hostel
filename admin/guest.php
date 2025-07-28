<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once('../includes/db.php');
require_once('includes/auth.php');
require_once('../scripts/archived_bookings.php');

$pageTitle = 'Confirmed Bookings';

// Fetch guest data with room and floor info for confirmed bookings
$guestsQuery = "
  SELECT 
    gl.id AS guest_id,
    gl.booking_id,
    gl.guest_name AS name,
    gl.email,
    gl.phone,
    gl.gender,
    gl.check_in,
    gl.check_out,
    gl.room_type_id,
    gl.is_booker,
    gl.room_id,
    gl.created_at,
    rt.title AS room_type,
    r.room_number,
    f.floor_number,
    f.section_name
  FROM guest_lists gl
  JOIN bookings b ON gl.booking_id = b.id
  JOIN room_types rt ON gl.room_type_id = rt.id
  LEFT JOIN rooms r ON gl.room_id = r.id
  LEFT JOIN floors f ON r.floor_id = f.id
  WHERE b.status = 'confirmed'
  ORDER BY gl.booking_id DESC, gl.is_booker DESC
";
$resultGuests = $conn->query($guestsQuery);
if (!$resultGuests) {
  die("Error fetching guest data: " . $conn->error);
}

$roomTypes = [];
$resultRoomTypes = $conn->query("SELECT id, title FROM room_types");
if ($resultRoomTypes) {
  while ($row = $resultRoomTypes->fetch_assoc()) {
    $roomTypes[] = [
      'id' => (int)$row['id'],
      'title' => $row['title']
    ];
  }
}

$roomsQuery = "
  SELECT
    r.id AS room_id,
    r.room_number,
    r.floor_id,
    r.room_type_id,  -- ✅ Include this
    r.is_occupied,
    f.floor_number,
    f.section_name,
    gl.guest_name
  FROM rooms r
  JOIN floors f ON r.floor_id = f.id
  LEFT JOIN guest_lists gl ON gl.room_id = r.id
";
$resultRooms = $conn->query($roomsQuery);

$groupedRooms = [];
$assignedRooms = [];
$allRooms = [];

if ($resultRooms) {
  while ($row = $resultRooms->fetch_assoc()) {
    $sectionKey = "{$row['section_name']} (Floor {$row['floor_number']})";
    $roomId = (int)$row['room_id'];
    $roomData = [
      'room_id'      => $roomId,
      'room_number'  => $row['room_number'],
      'floor_id'     => (int)$row['floor_id'],
      'room_type_id' => (int)$row['room_type_id'], // ✅ Add this
      'section'      => $sectionKey,
      'floor_number' => $row['floor_number'],
      'section_name' => $row['section_name'],
      'is_occupied'  => (int)$row['is_occupied']
    ];
    $groupedRooms[$sectionKey][] = $roomData;
    $allRooms[] = $roomData;
    if (!empty($row['guest_name'])) {
      $assignedRooms[$roomId] = $row['guest_name'];
    }
  }
} else {
  die("Error fetching room data: " . $conn->error);
}

$checkedInGuests = [];
$pendingGuests = [];

while ($row = $resultGuests->fetch_assoc()) {
  $row['current_room_number'] = $row['room_number'];
  $isAssigned = !empty($row['room_number']);
  if ($isAssigned) {
    $checkedInGuests[] = $row;
  } else {
    $pendingGuests[] = $row;
  }
}

include('includes/header.php');
?>

<script>
  window.allRooms = <?= json_encode($allRooms, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
  window.roomTypes = <?= json_encode($roomTypes) ?>;
  window.assignedRooms = <?= json_encode($assignedRooms) ?>;
</script>

<main
  x-data="bookingModal(window.roomTypes, window.allRooms, window.assignedRooms)"
  class="flex-1 bg-[#F9FAFB] h-screen overflow-hidden"
  x-init="$nextTick(() => { if (window.lucide) lucide.createIcons(); })">

  <div
    x-data="{
      tab: localStorage.getItem('guestTab') || 'pending',
      search: '',
      setTab(t) {
        this.tab = t;
        localStorage.setItem('guestTab', t);
        $nextTick(() => { if (window.lucide) lucide.createIcons(); });
      }
    }"
    class="max-w-7xl mx-auto bg-white shadow rounded-lg border border-gray-200 h-full flex flex-col">

    <!-- Tabs + Search aligned horizontally -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-300 gap-4 pb-4 px-6 pt-6">
      <div class="flex gap-6">
        <button
          @click="setTab('pending')"
          :class="tab === 'pending' 
            ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold' 
            : 'text-gray-600 hover:text-indigo-500'"
          class="pb-2 px-4 text-sm transition">
          Pending Guests
        </button>
        <button
          @click="setTab('checkedIn')"
          :class="tab === 'checkedIn' 
            ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold' 
            : 'text-gray-600 hover:text-indigo-500'"
          class="pb-2 px-4 text-sm transition">
          Checked-In Guests
        </button>
      </div>

      <!-- Shared Search Input -->
      <div class="w-full md:w-1/3">
        <input
          type="text"
          x-model="search"
          placeholder="Search guest..."
          class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
    </div>

    <!-- Content Area -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6">

      <!-- Pending Guests -->
      <template x-if="tab === 'pending'">
        <div x-transition.opacity.duration.300ms>
          <?php if (empty($pendingGuests)): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center text-gray-500">
              <i data-lucide="users" class="w-10 h-10 mb-3 text-gray-400"></i>
              <p class="text-sm font-medium">No pending guest data found.</p>
            </div>
          <?php else: ?>
            <div class="overflow-x-auto rounded-lg border border-gray-300">
              <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-red-100 text-red-800 font-semibold">
                  <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Phone</th>
                    <th class="px-5 py-3 text-left">Room Type</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Action</th>
                  </tr>
                </thead>
                <tbody
                  x-data="{ matchCount: 0 }"
                  x-init="matchCount = 0"
                  x-effect="matchCount = Array.from($el.querySelectorAll('tr')).filter(tr => !tr.hasAttribute('x-show') || $data.search === '' || tr.getAttribute('x-show').includes(search.toLowerCase())).length"
                  class="divide-y divide-gray-200">

                  <?php foreach ($pendingGuests as $row): ?>
                    <tr
                      x-show="search === '' || '<?= strtolower($row['name']) ?>'.includes(search.toLowerCase()) || '<?= strtolower($row['email']) ?>'.includes(search.toLowerCase())"
                      class="hover:bg-red-50">
                      <td class="px-5 py-3 font-medium"><?= htmlspecialchars($row['name']) ?></td>
                      <td class="px-5 py-3"><?= htmlspecialchars($row['email']) ?></td>
                      <td class="px-5 py-3"><?= htmlspecialchars($row['phone']) ?></td>
                      <td class="px-5 py-3"><?= htmlspecialchars($row['room_type']) ?></td>
                      <td class="px-5 py-3 text-center">
                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Not Assigned</span>
                      </td>
                      <td class="px-5 py-3 text-center space-x-1 whitespace-nowrap">
                        <button @click='openModal(<?= json_encode($row) ?>)' class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-3 py-1 rounded-md">
                          <i data-lucide="eye" class="w-4 h-4 inline"></i> View
                        </button>
                        <button @click='openAssignRoomModal(<?= json_encode($row) ?>)' class="bg-black hover:bg-gray-800 text-white text-xs font-medium px-3 py-1 rounded-md">
                          <i data-lucide="home" class="w-4 h-4 inline"></i> Assign
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>

                  <!-- No results found row -->
                  <tr x-show="matchCount === 0">
                    <td colspan="6" class="text-center text-gray-500 py-5 italic">
                      <i data-lucide="search-x" class="w-6 h-6 inline text-gray-400 mr-2"></i> No guest matches your search.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </template>

      <!-- Checked-In Guests -->
      <template x-if="tab === 'checkedIn'">
        <div x-transition.opacity.duration.300ms>
          <?php if (empty($checkedInGuests)): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center text-gray-500">
              <i data-lucide="check-circle" class="w-10 h-10 mb-3 text-gray-400"></i>
              <p class="text-sm font-medium">No checked-in guest data found.</p>
            </div>
          <?php else: ?>
            <div class="overflow-x-auto rounded-lg border border-gray-300">
              <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-green-100 text-green-800 uppercase text-xs tracking-wide">
                  <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Phone</th>
                    <th class="px-5 py-3 text-left">Room Type</th>
                    <th class="px-5 py-3 text-left">Assigned Room</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Action</th>
                  </tr>
                </thead>
                <tbody
                  x-data="{ matchCount: 0 }"
                  x-init="matchCount = 0"
                  x-effect="matchCount = Array.from($el.querySelectorAll('tr')).filter(tr => !tr.hasAttribute('x-show') || $data.search === '' || tr.getAttribute('x-show').includes(search.toLowerCase())).length"
                  class="divide-y divide-gray-200">

                  <?php foreach ($checkedInGuests as $row): ?>
                    <tr
                      x-show="search === '' || '<?= strtolower($row['name']) ?>'.includes(search.toLowerCase()) || '<?= strtolower($row['email']) ?>'.includes(search.toLowerCase())"
                      class="hover:bg-green-50">
                      <td class="px-5 py-3 font-medium"><?= htmlspecialchars($row['name']) ?></td>
                      <td class="px-5 py-3"><?= htmlspecialchars($row['email']) ?></td>
                      <td class="px-5 py-3"><?= htmlspecialchars($row['phone']) ?></td>
                      <td class="px-5 py-3"><?= htmlspecialchars($row['room_type']) ?></td>
                      <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 bg-white px-2 py-1 text-sm font-semibold text-gray-700 border border-gray-300 rounded shadow-sm">
                          <i data-lucide="door-open" class="w-4 h-4 text-green-600"></i>
                          <?= htmlspecialchars($row['current_room_number'] ?? 'N/A') ?>
                        </span>
                      </td>
                      <td class="px-5 py-3 text-center">
                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-200 text-green-900 rounded-full">Assigned</span>
                      </td>
                      <td class="px-5 py-3 text-center space-x-1 whitespace-nowrap">
                        <button @click='openModal(<?= json_encode($row) ?>)' class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-3 py-1 rounded-md">
                          <i data-lucide="eye" class="w-4 h-4 inline"></i> View
                        </button>
                        <button @click='openAssignRoomModal(<?= json_encode($row) ?>)' class="bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium px-3 py-1 rounded-md">
                          <i data-lucide="refresh-ccw" class="w-4 h-4 inline"></i> Reassign
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>

                  <!-- No results found row -->
                  <tr x-show="matchCount === 0">
                    <td colspan="7" class="text-center text-gray-500 py-5 italic">
                      <i data-lucide="search-x" class="w-6 h-6 inline text-gray-400 mr-2"></i> No guest matches your search.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </template>
    </div>
  </div>

  <!-- Modal Partial -->
  <?php include('modals/guestModal.php'); ?>
</main>

<script>
  function bookingModal(roomTypes, allRooms = [], assignedRoomsFromPHP = {}, bookingList = []) {
    return {
      flashMessage: {
        text: '',
        success: true
      },

      // UI States
      isOpen: false,
      isEditing: false,
      isAssignRoomOpen: false,
      showConfirmDialog: false,
      showReassignConfirmDialog: false,
      isReassigning: false,

      // Data Models
      original: {},
      booking: {},
      assignRoomData: {
        guest_id: null,
        booking_id: null,
        old_room_id: null
      },
      selectedRoom: null,
      roomTypes,
      allRooms,
      groupedRooms: {},
      assignedRooms: {
        ...assignedRoomsFromPHP
      },
      bookingList: bookingList,
      refreshKey: Date.now(),

      // ✨ Accessibility: Trap tab key inside modal
      handleTabKey(event, modalBox) {
        const focusable = modalBox.querySelectorAll(
          'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        const [firstEl, lastEl] = [focusable[0], focusable[focusable.length - 1]];
        if (event.shiftKey && document.activeElement === firstEl) {
          event.preventDefault();
          lastEl.focus();
        } else if (document.activeElement === lastEl) {
          event.preventDefault();
          firstEl.focus();
        }
      },

      // ✏️ Modal: Open/Edit/Save
      openModal(data) {
        this.original = {
          ...data
        };
        this.booking = {
          ...data
        };
        this.isOpen = true;
        this.isEditing = false;
      },
      closeModal() {
        this.isOpen = false;
        this.booking = {};
        this.original = {};
        this.isEditing = false;
      },
      toggleEdit() {
        this.isEditing ? this.saveChanges() : this.isEditing = true;
      },
      getChanges() {
        const changes = {};
        for (const key in this.booking) {
          if (this.booking[key] !== this.original[key]) {
            changes[key] = this.booking[key];
          }
        }
        return changes;
      },
      async saveChanges() {
        const changes = this.getChanges();
        if (!Object.keys(changes).length) {
          this.showFlash('No changes made.', false);
          return;
        }
        Object.assign(changes, {
          guest_id: this.booking.guest_id,
          booking_id: this.booking.booking_id,
          is_booker: this.booking.is_booker
        });

        try {
          const res = await fetch('update-info.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(changes)
          });
          const result = await res.json();

          this.showFlash(result.success ? 'Changes saved.' : result.message || 'Save failed.', result.success);

          if (result.success) {
            const index = this.bookingList.findIndex(b => b.booking_id === this.booking.booking_id);
            if (index !== -1) {
              this.bookingList[index] = {
                ...this.booking
              };
              this.bookingList = [...this.bookingList]; // trigger reactivity
            }
            this.closeModal();
          }
        } catch (err) {
          console.error(err);
          this.showFlash('Server error during save.', false);
        }
      },

      // 🛏 Room Assignment Modal
      openAssignRoomModal(data) {
        this.isAssignRoomOpen = false;
        this.$nextTick(() => {
          this.selectedRoom = null;
          this.groupedRooms = {};
          this.booking = {
            ...data
          };
          this.assignRoomData = {
            guest_id: data.guest_id || null,
            booking_id: data.booking_id || null,
            old_room_id: data.room_id || null
          };
          this.isReassigning = !!data.room_id;
          this.showConfirmDialog = false;
          this.showReassignConfirmDialog = false;

          const gender = (this.booking?.gender || '').toLowerCase();
          const floor = gender === 'female' ? 2 : 3;

          const filtered = this.allRooms.filter(r => parseInt(r.floor_number) === floor);
          this.groupedRooms = this.groupRoomsBySectionAndFloor(filtered);
          this.refreshKey = Date.now();

          this.$nextTick(() => {
            if (window.Alpine?.refresh) Alpine.refresh();
            this.isAssignRoomOpen = true;
          });
        });
      },
      closeAssignRoomModal() {
        this.isAssignRoomOpen = false;
        this.assignRoomData = {
          guest_id: null,
          booking_id: null,
          old_room_id: null
        };
        this.selectedRoom = null;
        this.groupedRooms = {};
        this.showConfirmDialog = false;
        this.showReassignConfirmDialog = false;
        this.isReassigning = false;
      },

      // ✔️ Room Selection
      selectRoom(room) {
        if (this.isRoomOccupied(room)) return;
        this.selectedRoom = room;
        const isSameRoom = this.assignRoomData.old_room_id === room.room_id;
        this.showConfirmDialog = !this.assignRoomData.old_room_id || isSameRoom;
        this.showReassignConfirmDialog = !!this.assignRoomData.old_room_id && !isSameRoom;
      },

      // 🚀 Room Assignment
      async assignRoomToBooking(force = false) {
        if (!this.selectedRoom?.room_id) {
          alert('Please select a room.');
          return;
        }

        const payload = {
          guest_id: this.assignRoomData.guest_id,
          booking_id: this.assignRoomData.booking_id,
          room_id: this.selectedRoom.room_id,
          old_room_id: this.assignRoomData.old_room_id,
          force
        };

        try {
          const res = await fetch('assign-room.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
          });
          const result = await res.json();

          if (result.requires_confirmation) {
            this.showReassignConfirmDialog = true;
            this.showConfirmDialog = false;
            return;
          }

          if (result.success) {
            const newRoomId = this.selectedRoom.room_id;
            const guestName = result.guest_name || 'Guest';

            // Remove any previous assignment of guest
            Object.keys(this.assignedRooms).forEach(roomId => {
              if (this.assignedRooms[roomId] === guestName) {
                delete this.assignedRooms[roomId];
              }
            });

            this.assignedRooms[newRoomId] = guestName;

            // Update bookingList & local modal
            const guest = this.bookingList.find(b => b.booking_id === this.assignRoomData.booking_id);
            if (guest) {
              guest.room_id = newRoomId;
              guest.current_room_number = this.selectedRoom.room_number;
              guest.check_in_status = 'checked_in';
            }

            if (this.booking) {
              this.booking.room_id = newRoomId;
              this.booking.current_room_number = this.selectedRoom.room_number;
              this.booking.check_in_status = 'checked_in';
            }

            this.bookingList = [...this.bookingList]; // reactivity

            // Re-filter rooms based on gender/floor/room type
            const gender = (this.booking?.gender || '').toLowerCase();
            const floor = gender === 'female' ? 2 : 3;

            const filtered = this.allRooms.filter(r =>
              parseInt(r.floor_number) === floor &&
              parseInt(r.room_type_id) === parseInt(this.selectedRoom?.room_type_id)
            );

            this.groupedRooms = this.groupRoomsBySectionAndFloor(filtered);
            this.refreshKey = Date.now();

            this.$nextTick(() => {
              if (window.Alpine?.refresh) Alpine.refresh();
            });

            this.selectedRoom = null;
            this.closeAssignRoomModal();
            this.showFlash('Room assigned successfully.', true);
          } else {
            this.showFlash(result.message || 'Assignment failed.', false);
          }
        } catch (err) {
          console.error(err);
          this.showFlash('Server error during room assignment.', false);
        }
      },

      // 📤 Check-out
      checkOutBooking(guest) {
        if (!guest) return alert("Missing guest data.");

        const isBooker = Number(guest.is_booker) === 1;
        const payload = {
          checked_out: true,
          is_booker: isBooker ? 1 : 0
        };

        if (isBooker && !guest.booking_id) return alert("Missing booking ID for booker.");
        if (!isBooker && !guest.guest_id) return alert("Missing guest ID.");

        if (isBooker) payload.booking_id = guest.booking_id;
        else payload.guest_id = guest.guest_id;

        fetch('update-info.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
          })
          .then(res => res.json())
          .then(result => {
            const msg = result.message || 'Check-out failed.';
            this.showFlash(result.success ? 'Guest checked out successfully.' : msg, result.success);

            if (result.success) {
              if (guest.room_id && this.assignedRooms?.[guest.room_id]) {
                delete this.assignedRooms[guest.room_id];
              }
              guest.room_id = null;
              guest.current_room_number = null;
              guest.check_in_status = 'checked_out';
              this.bookingList = [...this.bookingList];
              this.refreshKey = Date.now();
              this.closeModal();
            }
          })
          .catch(err => {
            console.error('Check-out Error:', err);
            this.showFlash('Server error during check-out.', false);
          });
      },

      // 🧠 Helpers
      groupRoomsBySectionAndFloor(rooms) {
        const grouped = {};
        rooms.forEach(r => {
          const section = r.section_name || 'Unspecified';
          if (!grouped[section]) grouped[section] = [];
          grouped[section].push(r);
        });

        const sorted = {};
        Object.keys(grouped).sort().forEach(sec => {
          sorted[sec] = grouped[sec].sort((a, b) => a.room_number - b.room_number);
        });

        return sorted;
      },
      getRoomTooltip(room) {
        return this.assignedRooms[room.room_id] || '';
      },
      isRoomOccupied(room) {
        return !!this.assignedRooms[room.room_id];
      },
      showFlash(msg, isSuccess = true) {
        this.flashMessage = {
          text: msg,
          success: isSuccess
        };
        setTimeout(() => this.flashMessage.text = '', 3500);
      }
    };
  }

  document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
  });
</script>

<?php include('includes/footer.php'); ?>