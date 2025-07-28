<!-- Delete Confirmation Modal -->
<div
    x-show="deleteModalOpen"
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    @click.self="closeDeleteModal"
    style="display: none"
    x-trap.noscroll="deleteModalOpen"
    aria-modal="true"
    role="dialog">
    <div
        class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-xl border border-gray-300 dark:border-gray-700 max-w-sm w-full mx-4"
        @click.stop>
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
            Confirm Deletion
        </h2>
        <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
            Are you sure you want to delete this image? This action cannot be undone.
        </p>
        <div class="flex justify-end gap-4">
            <button
                @click="closeDeleteModal"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg focus:outline-none">
                Cancel
            </button>
            <button
                @click="confirmDeleteImage"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg focus:outline-none">
                Delete
            </button>
        </div>
    </div>
</div>