<!-- Room Modal -->
<div
    x-show="modalOpenRoom"
    x-transition
    x-cloak
    @click.self="modalOpenRoom = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-live="polite"
>
    <div
        @click.stop
        class="relative bg-white rounded-3xl shadow-2xl p-6 sm:p-8 max-w-2xl w-full mx-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
    >
        <!-- Close Button -->
        <button
            @click="modalOpenRoom = false"
            class="absolute top-4 right-4 text-gray-400 hover:text-[#F94144] transition"
            aria-label="Close modal"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Title -->
        <h3 class="text-3xl font-bold mb-1 text-[#F94144]" x-text="activeRoom.title"></h3>

        <!-- Price -->
        <p
            class="text-xl font-semibold mb-4 text-[#F94144]"
            x-text="activeRoom.price ? 'Price starts at ₱' + parseFloat(activeRoom.price).toLocaleString() : ''"
        ></p>

        <!-- Description -->
        <p class="text-gray-600 text-lg mb-6" x-text="activeRoom.desc"></p>

        <!-- Room Details -->
        <template x-if="activeRoom.extra">
            <div class="space-y-6">
                <!-- Also Available -->
                <div x-show="activeRoom.also_available && activeRoom.also_available.trim() !== ''">
                    <h4 class="text-lg font-semibold mb-2">Also available:</h4>
                    <div class="flex flex-wrap gap-3">
                        <template x-for="label in activeRoom.also_available.split(',')" :key="label">
                            <span class="bg-gray-100 rounded-full px-4 py-1 text-sm text-gray-700" x-text="label.trim()"></span>
                        </template>
                    </div>
                </div>

                <!-- Capacity -->
                <div>
                    <h4 class="text-lg font-semibold mb-2">Maximum Capacity:</h4>
                    <p class="text-gray-600" x-text="activeRoom.capacity || '344 Capsules'"></p>
                </div>

                <!-- Inclusions -->
                <div>
                    <h4 class="text-lg font-semibold mb-2">Inclusions:</h4>
                    <ul class="grid grid-cols-2 gap-3 text-gray-600">
                        <template
                            x-for="item in (activeRoom.inclusions
                                ? activeRoom.inclusions.split(',')
                                : [
                                    'Lockable space',
                                    'Free unlimited Wi-Fi',
                                    'Airconditioned capsule wing',
                                    'Water & electricity',
                                    'Laundry services',
                                    'Purified water',
                                    'Spacious parking space',
                                    'Cafeteria'
                                  ])"
                            :key="item"
                        >
                            <li class="flex items-center gap-2">
                                <span class="h-2 w-2 bg-[#F94144] rounded-full"></span>
                                <span x-text="item.trim()"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </template>

        <!-- CTA Buttons -->
        <div class="mt-8 flex justify-end gap-3 flex-wrap">
            <!-- Book Now -->
            <a
                href="book.php"
                class="inline-flex items-center bg-[#F94144] hover:bg-[#D8322E] text-white py-2 px-6 rounded-full transition"
            >
                Book Now
            </a>

            <!-- Close -->
            <button
                @click="modalOpenRoom = false"
                class="inline-flex items-center bg-gray-300 hover:bg-gray-400 text-black py-2 px-6 rounded-full transition"
            >
                Close
            </button>
        </div>
    </div>
</div>

<!-- Lightbox -->
<div
    x-show="lightboxOpen"
    x-transition
    x-cloak
    @click="lightboxOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
>
    <img
        :src="lightboxImage"
        :alt="activeRoom.title ? activeRoom.title + ' image preview' : 'Room image preview'"
        class="max-h-[90vh] max-w-[90vw] rounded-lg shadow-lg transition-transform transform hover:scale-105"
    />
</div>
