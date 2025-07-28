<div
  x-show="showModal"
  x-cloak
  x-transition:enter="transition ease-out duration-200"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
  @click.outside="showModal = false"
  @keydown.escape.window="showModal = false"
  role="dialog"
  aria-modal="true"
>
  <div class="bg-white p-6 rounded-2xl shadow-2xl w-full max-w-md relative">
    <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition" aria-label="Close modal">
      <i data-lucide="x" class="w-6 h-6"></i>
    </button>

    <h2 class="text-xl font-bold text-[#DF5219] mb-4 flex items-center gap-2">
      <i data-lucide="plus" class="w-5 h-5 text-[#DF5219]"></i> Add New Admin
    </h2>

    <form method="POST" class="space-y-4" novalidate>
      <input type="text" name="username" placeholder="Username" required
        class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-[#DF5219]" aria-label="Username" />
      <input type="email" name="email" placeholder="example@gmail.com" pattern=".+@gmail\.com" required
        class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-[#DF5219]" aria-label="Gmail address" />
      <input type="password" name="password" placeholder="Password" required
        class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-[#DF5219]" aria-label="Password" />
      <div class="flex justify-end gap-2">
        <button type="button" @click="showModal = false"
          class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 text-sm">Cancel</button>
        <button type="submit"
          class="bg-[#DF5219] hover:bg-[#c4440e] text-white px-4 py-2 rounded text-sm font-medium">Create</button>
      </div>
    </form>
  </div>
</div>

<?php if (isset($_GET['delete'])): ?>
  <div
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    @keydown.escape.window="show = false"
    role="dialog"
    aria-modal="true">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-sm relative">
      <button @click="show = false; window.location.href='admins.php'" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700" aria-label="Close">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>

      <div class="text-center">
        <h2 class="text-lg font-bold text-[#DF5219] mb-2">
          ⚠️ Confirm Deletion
        </h2>
        <p class="text-sm text-gray-600 mb-4">Are you sure you want to delete <strong><?= htmlspecialchars($_GET['delete']) ?></strong>?</p>
        <div class="flex justify-center gap-4">
          <a href="admins.php" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400 text-sm">Cancel</a>
          <a href="delete-admin.php?user=<?= urlencode($_GET['delete']) ?>" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Yes, Delete</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
