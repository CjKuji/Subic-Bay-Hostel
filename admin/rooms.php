<?php
session_start();
require '../includes/db.php';
require 'includes/auth.php';

// Fetch room types with thumbnail
$mainRoomTypes = [];
$result = $conn->query("SELECT id, title, price, description, capacity, inclusions, also_available, image FROM room_types ORDER BY title ASC");
while ($row = $result->fetch_assoc()) {
  $row['image'] = $row['image'] ? '/subic-bay-hostel/' . ltrim($row['image'], '/') : null;
  $mainRoomTypes[] = $row;
}

// Fetch titles only for modal selects
$roomTypes = [];
$result2 = $conn->query("SELECT id, title FROM room_types ORDER BY title ASC");
while ($result2 && $row = $result2->fetch_assoc()) {
  $roomTypes[] = $row;
}

include('includes/header.php');
?>

<style>
  .custom-scroll {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 transparent;
  }

  .custom-scroll::-webkit-scrollbar {
    width: 8px;
  }

  .custom-scroll::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 9999px;
    background-clip: content-box;
    border: 3px solid transparent;
  }

  .custom-scroll:hover::-webkit-scrollbar-thumb {
    background-color: #a0aec0;
  }

  .dark .custom-scroll::-webkit-scrollbar-thumb {
    background-color: #4a5568;
  }
</style>
<main
  x-data="roomsPage(<?= htmlspecialchars(json_encode($roomTypes), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($mainRoomTypes), ENT_QUOTES) ?>)"
  @keydown.escape.window="closeAllModals()"
  class="text-gray-900 dark:text-gray-100 overflow-x-hidden bg-gray-50 min-h-screen px-4 sm:px-8 py-8">

  <!-- Flash Message -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="mb-6 px-4 py-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm text-sm text-gray-800 dark:text-gray-100">
      <?= htmlspecialchars(is_array($_SESSION['flash']) ? json_encode($_SESSION['flash']) : $_SESSION['flash']) ?>
      <?php unset($_SESSION['flash']); ?>
    </div>
  <?php endif; ?>

  <!-- Room Cards Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    <template x-for="mainType in mainRooms" :key="mainType.id">
      <div
        class="group bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 shadow hover:shadow-xl overflow-hidden flex flex-col transition-transform duration-300 hover:-translate-y-2 focus-within:shadow-xl"
        :id="'room-type-' + mainType.id">

        <!-- Image -->
        <div class="relative aspect-[4/3] bg-gray-100 dark:bg-gray-900 flex items-center justify-center overflow-hidden">
          <img
            :src="mainType.image || 'https://via.placeholder.com/400x300?text=No+Image'"
            alt="Room Image"
            class="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-110">
          <div class="absolute bottom-3 left-3 bg-indigo-600 text-white text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-md shadow">
            Featured
          </div>
        </div>

        <!-- Content -->
        <div class="p-6 flex flex-col flex-grow space-y-4">
          <div>
            <h3 class="text-xl font-bold text-whitetruncate" x-text="mainType.title"></h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-3" x-text="truncateSentences(mainType.description)"></p>
          </div>

          <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
            <p><i data-lucide="wallet" class="inline w-4 h-4 mr-1 text-white"></i><span class="font-medium text-white" x-text="mainType.price ? '₱' + parseFloat(mainType.price).toFixed(2) : 'N/A'"></span></p>
            <p><i data-lucide="users" class="inline w-4 h-4 mr-1 text-white"></i><span class="font-medium" x-text="mainType.capacity || 'N/A'"></span> Guests</p>
            <p><i data-lucide="check-circle" class="inline w-4 h-4 mr-1 text-white"></i><span x-text="truncateArrayToThree(mainType.inclusions)"></span></p>
            <p><i data-lucide="plus-square" class="inline w-4 h-4 mr-1 text-white"></i><span x-text="truncateArrayToThree(mainType.also_available)"></span></p>
          </div>

          <button
            @click="openMainTypeEditModal(mainRooms.find(r => r.id === mainType.id))"
            class="mt-auto w-full inline-flex justify-center items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-white transition">
            Edit Room
          </button>
        </div>
      </div>
    </template>
  </div>

  <!-- Flash Popup -->
  <div
    x-show="flashMessage.text !== ''"
    x-transition.opacity
    x-text="flashMessage.text"
    class="fixed bottom-6 right-6 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-800 dark:text-gray-200 shadow-lg px-4 py-3 rounded-lg text-sm z-50 cursor-pointer max-w-xs"
    @click="flashMessage.text = ''">
  </div>

  <!-- Modals -->
  <?php include('modals/roomsModal.php'); ?>
  <?php include('modals/deleteModal.php'); ?>
</main>

<script>
  function roomsPage(roomTypes = [], mainRooms = []) {
    return {
      roomTypes,
      mainRooms,
      savedFeedback: false,

      flashMessage: {
        text: '',
        type: 'success'
      },
      loading: false,

      mainTypeEditModalOpen: false,
      deleteModalOpen: false,

      lightboxOpen: false,
      lightboxImage: '',
      thumbnailPreview: null,

      activeMainType: {},
      existingImages: [],
      imageFiles: [],
      imagePreviews: [],
      selectedImages: [],
      deleteTarget: {
        id: null,
        index: null,
        roomTypeId: null
      },

      // UTILITIES
      splitCommaString(str) {
        return (str || '').split(',').map(s => s.trim()).filter(Boolean);
      },
      joinArray(arr) {
        return Array.isArray(arr) ? arr.join(', ') : '';
      },
      sanitizeString(str) {
        return (str || '').toString().trim();
      },
      truncateArrayToThree(value) {
        const items = (value || '').split(',').map(i => i.trim()).filter(Boolean);
        if (items.length <= 3) return items.join(', ');
        return items.slice(0, 3).join(', ') + ', ...';
      },
      truncateSentences(text) {
        if (!text) return '';
        const sentences = text.match(/[^.!?]+[.!?]?/g) || [];
        const result = sentences.slice(0, 3).join(' ').trim();
        return sentences.length > 3 ? result + '...' : result;
      },

      // FLASH
      showFlash(text, type = 'success') {
        this.flashMessage = {
          text,
          type
        };
        setTimeout(() => {
          this.flashMessage = {
            text: '',
            type: ''
          };
        }, 3000);
      },

      // MODALS
      closeAllModals() {
        this.mainTypeEditModalOpen = false;
        this.closeLightbox();
        this.loading = false;
      },
      openDeleteModal(id = null, index = null, roomTypeId = null) {
        this.deleteTarget = {
          id,
          index,
          roomTypeId
        };
        this.deleteModalOpen = true;
      },
      closeDeleteModal() {
        this.deleteModalOpen = false;
        this.deleteTarget = {
          id: null,
          index: null,
          roomTypeId: null
        };
      },
      openLightbox(img) {
        this.lightboxImage = img;
        this.lightboxOpen = true;
      },
      closeLightbox() {
        if (this.lightboxImage.startsWith('blob:')) {
          URL.revokeObjectURL(this.lightboxImage);
        }
        this.lightboxOpen = false;
        this.lightboxImage = '';
      },

      // EDIT MODAL
      openMainTypeEditModal(mainType) {
        const latest = this.mainRooms.find(r => r.id === mainType.id) || mainType;
        this.activeMainType = {
          ...latest,
          inclusionsArray: this.splitCommaString(latest.inclusions),
          alsoAvailableArray: this.splitCommaString(latest.also_available),
        };
        this.clearImagePreviews();
        this.thumbnailPreview = null;

        fetch(`images-handler.php?room_type_id=${latest.id}`)
          .then(res => res.ok ? res.json() : Promise.reject('Failed to load images.'))
          .then(data => this.existingImages = data.images || [])
          .catch(() => {
            this.existingImages = [];
            this.showFlash('Error loading images.', 'error');
          });

        this.mainTypeEditModalOpen = true;
      },
      closeMainTypeEditModal() {
        this.mainTypeEditModalOpen = false;
        this.resetModalState();
      },
      resetModalState() {
        this.activeMainType = {};
        this.clearImagePreviews();
        this.existingImages = [];
        this.thumbnailPreview = null;
        this.loading = false;
      },
      previewThumbnail(event) {
        const file = event.target.files[0];
        if (!file || !file.type.startsWith('image/')) {
          this.thumbnailPreview = null;
          return;
        }
        if (this.thumbnailPreview?.startsWith('blob:')) {
          URL.revokeObjectURL(this.thumbnailPreview);
        }
        this.thumbnailPreview = URL.createObjectURL(file);
        event.target.value = null;
      },
      clearImagePreviews() {
        this.imagePreviews.forEach(url => URL.revokeObjectURL(url));
        this.imagePreviews = [];
        this.imageFiles = [];
      },
      handleImageSelection(event) {
        const files = Array.from(event.target.files);
        for (const file of files) {
          if (!file.type.startsWith('image/')) continue;
          const exists = this.imageFiles.some(existing =>
            existing.name === file.name && existing.size === file.size
          );
          if (!exists) {
            this.imageFiles.push(file);
            this.imagePreviews.push(URL.createObjectURL(file));
          }
        }
        event.target.value = null;
      },
      removeImage(index) {
        if (index >= 0 && index < this.imageFiles.length) {
          URL.revokeObjectURL(this.imagePreviews[index]);
          this.imagePreviews.splice(index, 1);
          this.imageFiles.splice(index, 1);
        }
      },
      async replaceThumbnail(event, imageId, roomTypeId) {
        const file = event.target.files[0];
        if (!file || this.loading) return;

        this.loading = true;
        const formData = new FormData();
        formData.append('image', file);
        formData.append('image_id', imageId);
        formData.append('room_type_id', roomTypeId);

        try {
          const res = await fetch('images-handler.php', {
            method: 'POST',
            body: formData
          });
          const data = await res.json();
          if (!res.ok || !data.success) throw new Error(data.message || 'Replace failed.');
          this.showFlash('Thumbnail replaced successfully.', 'success');

          const refreshRes = await fetch(`images-handler.php?room_type_id=${roomTypeId}`);
          const json = await refreshRes.json();
          this.existingImages = json.images || [];
        } catch (err) {
          this.showFlash(err.message || 'Thumbnail update failed.', 'error');
        } finally {
          this.loading = false;
          event.target.value = '';
        }
      },
      async saveMainTypeEdit() {
        if (this.loading) return;
        this.loading = true;

        const inclusions = this.joinArray(this.activeMainType.inclusionsArray);
        const alsoAvailable = this.joinArray(this.activeMainType.alsoAvailableArray);

        const formData = new FormData();
        formData.append('id', this.activeMainType.id);
        formData.append('title', this.sanitizeString(this.activeMainType.title));
        formData.append('description', this.sanitizeString(this.activeMainType.description || ''));
        formData.append('price', this.activeMainType.price);
        formData.append('capacity', this.sanitizeString(this.activeMainType.capacity || ''));
        formData.append('inclusions', this.sanitizeString(inclusions));
        formData.append('also_available', this.sanitizeString(alsoAvailable));

        const thumbnailInput = document.querySelector('#thumbnailInput');
        if (thumbnailInput?.files?.[0]) {
          formData.append('thumbnail', thumbnailInput.files[0]);
        }

        this.imageFiles.forEach(file => formData.append('images[]', file));

        try {
          const res = await fetch('update-room-type.php', {
            method: 'POST',
            body: formData
          });

          const data = await res.json();
          if (!res.ok || !data.success) throw new Error(data.error || 'Update failed');

          const imgRes = await fetch(`images-handler.php?room_type_id=${this.activeMainType.id}`);
          const imgData = await imgRes.json();
          this.existingImages = imgData.images || [];

          const newThumb = this.existingImages.find(i => i.type === 'main')?.url;

          const updated = {
            id: this.activeMainType.id,
            title: this.activeMainType.title,
            description: this.activeMainType.description,
            price: this.activeMainType.price,
            capacity: this.activeMainType.capacity,
            inclusions,
            also_available: alsoAvailable,
            image: (this.thumbnailPreview || newThumb || '') + '?v=' + Date.now()
          };

          const index = this.mainRooms.findIndex(r => r.id === updated.id);
          if (index !== -1) {
            this.mainRooms.splice(index, 1, updated);
          }

          this.showFlash(data.message || 'Room updated.', 'success');
          this.closeMainTypeEditModal();
        } catch (e) {
          console.error(e);
          this.showFlash(e.message || 'Something went wrong.', 'error');
        } finally {
          this.loading = false;
          this.thumbnailPreview = null;
        }
      },

      // IMAGE SELECTION
      selectAllImages() {
        const galleryImages = this.existingImages.filter(i => i.type !== 'main').map(i => i.id);
        if (
          this.selectedImages.length === galleryImages.length &&
          galleryImages.every(id => this.selectedImages.includes(id))
        ) {
          this.selectedImages = [];
        } else {
          this.selectedImages = galleryImages;
        }
      },

      // DELETE IMAGE LOGIC
      toggleSelectImage(id) {
        if (this.selectedImages.includes(id)) {
          this.selectedImages = this.selectedImages.filter(i => i !== id);
        } else {
          this.selectedImages.push(id);
        }
      },

      async confirmDeleteImage() {
        if (this.loading) return;
        this.loading = true;

        try {
          if (this.selectedImages.length > 0) {
            // Bulk delete selected images
            const res = await fetch('delete-room-image.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                image_ids: this.selectedImages,
                room_type_id: this.activeMainType.id
              })
            });
            const data = await res.json();
            if (!res.ok || !data.success) throw new Error(data.message || 'Delete failed.');

            // Remove deleted images from existingImages
            this.existingImages = this.existingImages.filter(img => !this.selectedImages.includes(img.id));
            this.selectedImages = [];
            this.showFlash('Selected images deleted successfully.', 'success');

          } else {
            // Delete single image via deleteTarget
            const {
              id,
              index,
              roomTypeId
            } = this.deleteTarget;
            const payload = {
              id
            };
            if (id === 0 && roomTypeId) payload.room_type_id = roomTypeId;

            const res = await fetch('images-handler.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(payload),
            });

            const data = await res.json();
            if (!res.ok || !data.success) throw new Error(data.message || 'Delete failed.');
            if (id > 0) this.existingImages.splice(index, 1);

            this.showFlash(data.message || 'Image deleted successfully.', 'success');
          }
        } catch (err) {
          this.showFlash(err.message || 'Failed to delete image.', 'error');
        } finally {
          this.closeDeleteModal();
          this.loading = false;
        }
      }
    };
  }
</script>

<?php include('includes/footer.php'); ?>