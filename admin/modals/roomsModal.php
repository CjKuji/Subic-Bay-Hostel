<!-- Edit Room Type Modal -->
<div
  x-show="mainTypeEditModalOpen"
  x-transition.opacity.duration.300ms
  x-cloak
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
  @click.self="closeMainTypeEditModal()"
  x-trap.noscroll="mainTypeEditModalOpen"
  @keydown.escape.window="mainTypeEditModalOpen && closeMainTypeEditModal()"
  role="dialog"
  aria-modal="true"
  aria-labelledby="modal-title">
  <div
    class="relative w-full max-w-7xl bg-[#0f0f0f] text-white rounded-3xl shadow-2xl mx-4 md:mx-8 border border-neutral-800 transition-all"
    @click.stop
    tabindex="0">
    <div class="max-h-[90vh] overflow-y-auto custom-scroll rounded-3xl">
      <div class="container mx-auto px-6 sm:px-10 py-10 space-y-10">

        <!-- Modal Header -->
        <div class="flex items-start justify-between">
          <div>
            <h2 id="modal-title" class="text-3xl font-semibold flex items-center gap-3">
              <i data-lucide="bed-double" class="w-7 h-7 text-[#5409DA]"></i>
              Edit Room Type
            </h2>
            <p class="text-sm text-neutral-400 mt-1">Update details and manage images for this room type.</p>
          </div>
          <button
            @click="closeMainTypeEditModal()"
            class="rounded-full p-2 hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-[#5409DA] transition"
            aria-label="Close modal">
            <i data-lucide="x" class="w-5 h-5 text-white"></i>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="saveMainTypeEdit" class="space-y-10" enctype="multipart/form-data">

          <!-- Room Information Section -->
          <section class="bg-[#1a1a1a] border border-neutral-700 rounded-2xl p-8 space-y-6">
            <h3 class="text-xl font-semibold text-white">Room Information</h3>

            <div class="grid md:grid-cols-2 gap-6">

              <!-- Room Title -->
              <div class="space-y-2">
                <label for="roomTitle" class="block text-sm font-medium text-black dark:text-white">
                  Room Title <span class="ml-1 text-xs text-gray-500">(e.g. Deluxe Queen Room)</span>
                </label>
                <div class="relative">
                  <i data-lucide="type" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                  <input
                    id="roomTitle"
                    type="text"
                    x-model="activeMainType.title"
                    :disabled="loading"
                    required
                    placeholder="Deluxe Queen Room"
                    class="pl-11 pr-4 py-2.5 w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                           text-sm text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5409DA] transition" />
                  <span x-show="savedFeedback" x-transition.opacity class="absolute right-3 top-3 text-[#4ADE80] text-sm">Saved!</span>
                </div>
              </div>

              <!-- Price -->
              <div class="space-y-2">
                <label for="roomPrice" class="block text-sm font-medium text-black dark:text-white">
                  Price (₱) <span class="ml-1 text-xs text-gray-500">(Set base nightly rate)</span>
                </label>
                <div class="relative">
                  <i data-lucide="wallet" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                  <input
                    id="roomPrice"
                    type="number"
                    min="0"
                    step="0.01"
                    x-model.number="activeMainType.price"
                    :disabled="loading"
                    placeholder="1500"
                    class="pl-11 pr-4 py-2.5 w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                           text-sm text-black dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-[#5409DA] focus:outline-none transition" />
                </div>
              </div>

              <!-- Capacity -->
              <div class="space-y-2">
                <label for="roomCapacity" class="block text-sm font-medium text-black dark:text-white">
                  Capacity <span class="ml-1 text-xs text-gray-500">(e.g. 2 persons)</span>
                </label>
                <div class="relative">
                  <i data-lucide="users" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                  <input
                    id="roomCapacity"
                    type="text"
                    x-model="activeMainType.capacity"
                    :disabled="loading"
                    placeholder="e.g. 4 persons"
                    class="pl-11 pr-4 py-2.5 w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                           text-sm text-black dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-[#5409DA] focus:outline-none transition" />
                </div>
              </div>

              <!-- Description (full width) -->
              <div class="md:col-span-2 mt-6 space-y-2">
                <label for="roomDescription" class="block text-sm font-medium text-black dark:text-white">
                  Description <span class="ml-1 text-xs text-gray-500">(Max 500 characters)</span>
                </label>
                <div class="relative">
                  <i data-lucide="file-text" class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"></i>
                  <textarea
                    id="roomDescription"
                    x-model="activeMainType.description"
                    :disabled="loading"
                    maxlength="500"
                    rows="4"
                    placeholder="Spacious room with ocean view, queen-sized bed, and private bath."
                    class="pl-11 pr-4 py-2.5 w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                           text-sm text-black dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-[#5409DA] focus:outline-none resize-y transition"></textarea>
                  <p x-text="`${(activeMainType.description || '').length}/500 characters`" class="text-xs text-gray-400 mt-1"></p>
                </div>
              </div>

              <!-- Inclusions -->
              <div class="md:col-span-2 mt-8 space-y-4">
                <h4 class="text-base font-semibold text-black dark:text-white flex items-center gap-2">
                  <svg class="w-5 h-5 text-[#5409DA]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  Inclusions
                </h4>

                <template x-for="(inclusion, i) in activeMainType.inclusionsArray" :key="'inclusion-' + i">
                  <div class="flex items-center gap-3">
                    <input
                      type="text"
                      x-model="activeMainType.inclusionsArray[i]"
                      :disabled="loading"
                      placeholder="e.g. Complimentary breakfast"
                      class="flex-1 px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                             text-sm text-black dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-[#5409DA] focus:outline-none transition" />
                    <button
                      type="button"
                      @click="activeMainType.inclusionsArray.splice(i, 1)"
                      :disabled="loading"
                      class="text-red-500 hover:text-red-600 transition p-1.5 rounded-full"
                      aria-label="Remove inclusion">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6m0-6l6 6" />
                      </svg>
                    </button>
                  </div>
                </template>

                <button
                  type="button"
                  @click="activeMainType.inclusionsArray.push('')"
                  :disabled="loading"
                  class="inline-flex items-center gap-2 bg-[#5409DA] hover:bg-[#3c07a1] text-white font-medium text-sm px-4 py-2 rounded-full transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                  Add Inclusion
                </button>
              </div>

              <!-- Optional Add-ons -->
              <div class="md:col-span-2 mt-10 space-y-4">
                <h4 class="text-base font-semibold text-black dark:text-white flex items-center gap-2">
                  <svg class="w-5 h-5 text-[#5409DA]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6h.01M12 12h.01M12 18h.01" />
                  </svg>
                  Optional Add-ons
                </h4>

                <template x-for="(item, i) in activeMainType.alsoAvailableArray" :key="'also-' + i">
                  <div class="flex items-center gap-3">
                    <input
                      type="text"
                      x-model="activeMainType.alsoAvailableArray[i]"
                      :disabled="loading"
                      placeholder="e.g. Extra pillows, Baby crib"
                      class="flex-1 px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                             text-sm text-black dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-[#5409DA] focus:outline-none transition" />
                    <button
                      type="button"
                      @click="activeMainType.alsoAvailableArray.splice(i, 1)"
                      :disabled="loading"
                      class="text-red-500 hover:text-red-600 transition p-1.5 rounded-full"
                      aria-label="Remove option">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6m0-6l6 6" />
                      </svg>
                    </button>
                  </div>
                </template>

                <button
                  type="button"
                  @click="activeMainType.alsoAvailableArray.push('')"
                  :disabled="loading"
                  class="inline-flex items-center gap-2 bg-[#5409DA] hover:bg-[#3c07a1] text-white font-medium text-sm px-4 py-2 rounded-full transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                  Add Option
                </button>
              </div>
            </div>
          </section>

          <!-- Images Section -->
          <section class="bg-[#1a1a1a] border border-neutral-700 rounded-2xl p-8 space-y-6">
            <h3 class="text-xl font-semibold text-white">Image Management</h3>

            <section x-cloak>
              <h4 class="text-xl font-semibold text-black dark:text-white flex items-center gap-2">
                <i data-lucide="image" class="w-5 h-5 text-[#5409DA]"></i>
                Thumbnail Image
              </h4>

              <template x-if="existingImages.some(i => i.type === 'main')">
                <div class="flex gap-5 flex-wrap mt-4">
                  <template x-for="(img, idx) in existingImages.filter(i => i.type === 'main')" :key="'thumb-' + idx">
                    <div class="flex flex-col items-center space-y-3">
                      <div class="relative w-36 h-36 rounded-2xl overflow-hidden border border-gray-300 shadow group">
                        <img
                          :src="img.url"
                          alt="Thumbnail Image"
                          class="object-cover w-full h-full cursor-pointer transition hover:scale-105"
                          @click="openLightbox(img.url)" />

                        <div class="absolute bottom-0 w-full bg-black/60 text-white text-xs text-center py-1">Thumbnail</div>

                        <button
                          type="button"
                          @click.stop="openDeleteModal(img.id, idx, activeMainType.id)"
                          :disabled="loading"
                          aria-label="Delete thumbnail"
                          class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm opacity-0 group-hover:opacity-100 transition focus:outline-none focus:ring-2 focus:ring-red-400">
                          &times;
                        </button>
                      </div>

                      <label
                        class="inline-flex items-center bg-white border border-[#5409DA] text-[#5409DA] font-semibold px-4 py-1 rounded-lg cursor-pointer hover:bg-[#5409DA] hover:text-white transition">
                        Replace
                        <input
                          type="file"
                          accept="image/*"
                          class="hidden"
                          @change="replaceThumbnail($event, img.id, activeMainType.id)"
                          :disabled="loading" />
                      </label>
                    </div>
                  </template>
                </div>
              </template>

              <template x-if="!existingImages.some(i => i.type === 'main')">
                <div class="border-2 border-dashed border-gray-400 rounded-2xl p-8 max-w-md text-center mt-6">
                  <p class="text-sm text-gray-600 mb-4">No thumbnail found. Please upload one.</p>
                  <label
                    class="bg-[#5409DA] hover:bg-[#4320b8] text-white font-medium px-5 py-2 rounded-lg cursor-pointer transition">
                    Upload Thumbnail
                    <input
                      type="file"
                      accept="image/*"
                      class="hidden"
                      x-ref="thumbnailInput"
                      @change="replaceThumbnail($event, 0, activeMainType.id)"
                      :disabled="loading" />
                  </label>

                  <div x-show="thumbnailPreview" class="mt-4">
                    <p class="text-xs text-gray-500 mb-1">Preview:</p>
                    <img
                      :src="thumbnailPreview"
                      alt="Thumbnail Preview"
                      class="w-32 h-32 object-cover border rounded-xl mx-auto shadow" />
                  </div>
                </div>
              </template>
            </section>

            <!-- Gallery Images -->
            <section x-show="existingImages.length > 0" x-cloak>
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                  <i data-lucide="gallery-horizontal" class="w-5 h-5 text-[#5409DA]"></i>
                  <h4 class="text-xl font-semibold text-black dark:text-white">Gallery Images</h4>
                  <input
                    type="checkbox"
                    id="select_all_images"
                    name="select_all_images"
                    class="rounded text-[#5409DA] focus:ring-[#5409DA] ml-3"
                    :checked="existingImages.filter(i => i.type !== 'main').every(i => selectedImages.includes(i.id))"
                    @change="selectAllImages()"
                    title="Select/Deselect All" />
                </div>
              </div>

              <button
                type="button"
                @click="openDeleteModal()"
                x-show="selectedImages.length > 0"
                class="inline-flex items-center gap-2 px-5 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-400 disabled:opacity-50"
                :disabled="loading">
                <i data-lucide="trash" class="w-4 h-4"></i>
                Delete Selected (<span x-text="selectedImages.length"></span>)
              </button>

              <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-5 mt-5">
                <template x-for="(img, idx) in existingImages.filter(i => i.type !== 'main')" :key="'existing-' + idx">
                  <div class="relative group rounded-2xl overflow-hidden border border-gray-300 shadow-sm bg-white">
                    <img
                      :src="img.url"
                      alt="Gallery Image"
                      class="w-full h-28 object-cover cursor-pointer transition hover:scale-105"
                      @click="openLightbox(img.url)" />

                    <button
                      type="button"
                      @click.stop="openDeleteModal(img.id, idx, activeMainType.id)"
                      :disabled="loading"
                      aria-label="Delete image"
                      class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm opacity-0 group-hover:opacity-100 transition focus:outline-none focus:ring-2 focus:ring-red-400">
                      ×
                    </button>
                    <input
                      type="checkbox"
                      :value="img.id"
                      :checked="selectedImages.includes(img.id)"
                      @change="toggleSelectImage(img.id)"
                      class="absolute bottom-2 left-2 w-5 h-5 text-[#5409DA] bg-white border-gray-300 rounded focus:ring-[#5409DA] focus:outline-none"
                      title="Select" />
                  </div>
                </template>
              </div>
            </section>

          </section>

          <!-- Upload New Images -->
          <section>
            <h4 class="text-xl font-semibold text-black dark:text-white flex items-center gap-2 mb-4">
              <i data-lucide="upload" class="w-5 h-5 text-[#5409DA]"></i>
              Upload New Images
            </h4>

            <label class="inline-flex items-center bg-white border border-[#5409DA] text-[#5409DA] font-semibold px-5 py-2 rounded-lg cursor-pointer hover:bg-[#5409DA] hover:text-white transition">
              Add Images
              <input type="file"
                multiple
                accept="image/*"
                class="hidden"
                @change="handleImageSelection($event)"
                :disabled="loading" />
            </label>

            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-5 mt-6">
              <template x-for="(img, index) in imagePreviews" :key="index">
                <div class="relative rounded-2xl overflow-hidden border border-gray-300 shadow-sm bg-white">
                  <img :src="img"
                    alt="Preview Image"
                    class="object-cover w-full h-28 transition hover:scale-105" />
                  <button type="button"
                    @click="removeImage(index)"
                    class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm focus:outline-none focus:ring-2 focus:ring-red-400"
                    title="Remove"
                    :disabled="loading">
                    &times;
                  </button>
                </div>
              </template>
            </div>
          </section>

          <!-- Submit Button -->
          <div class="flex justify-end pt-6">
            <button
              type="submit"
              :disabled="loading"
              class="bg-[#5409DA] hover:bg-[#3c07a1] text-white px-6 py-2 rounded-xl text-sm font-medium transition disabled:opacity-50">
              Save Changes
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>