 <!-- Modal -->
 <div
   x-cloak
   x-show="isOpen"
   class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
   @click.outside="closeModal"
   role="dialog"
   aria-modal="true"
   aria-labelledby="modal-title">
   <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-2xl relative max-h-[90vh] overflow-y-auto">
     <div
       x-show="loading"
       x-transition.opacity
       class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-2xl z-50"
       aria-live="assertive"
       aria-busy="true">
       <div class="loader" aria-label="Loading"></div>
     </div>

     <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition" aria-label="Close modal">
       <i data-lucide="x" class="w-6 h-6"></i>
     </button>

     <h2 id="modal-title" class="text-2xl font-bold text-[#DF5219] mb-4 flex items-center gap-2">
       <i data-lucide="message-circle" class="w-6 h-6 text-[#DF5219]"></i>
       Message Details
     </h2>

     <div class="space-y-3 text-sm text-gray-700">
       <div><strong>Status:</strong>
         <span
           class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold"
           :class="{
              'text-red-700 bg-red-100': modalData.status === 'Unread',
              'text-orange-700 bg-orange-100': modalData.status === 'Read',
              'text-green-700 bg-green-100': modalData.status === 'Responded'
            }">
           <i :data-lucide="modalData.statusIcon" class="w-4 h-4"></i>
           <span x-text="modalData.status"></span>
         </span>
       </div>

       <div><strong>Name:</strong> <span x-text="modalData.name"></span></div>
       <div><strong>Email:</strong> <span x-text="modalData.email"></span></div>
       <div><strong>Submitted At:</strong> <span x-text="modalData.submitted_at"></span></div>
       <div>
         <strong>Message:</strong>
         <div class="bg-gray-100 p-4 rounded mt-2 text-sm text-gray-800 whitespace-pre-wrap" x-text="modalData.message"></div>
       </div>

       <form @submit.prevent="submitResponse" class="mt-6 space-y-3">
         <h3 class="text-sm font-semibold text-gray-700">Respond to Message</h3>
         <input type="text" readonly x-model="modalData.email" class="w-full bg-gray-100 border border-gray-300 rounded px-4 py-2 text-sm" />
         <textarea x-model="responseMessage" class="w-full border border-gray-300 rounded px-4 py-2 text-sm" rows="4" placeholder="Write your response here..." required></textarea>
         <button
           type="submit"
           class="bg-[#DF5219] hover:bg-[#c4440e] text-white px-4 py-2 rounded text-sm font-medium inline-flex items-center gap-1 transition"
           :disabled="loading">
           <template x-if="loading">
             <div class="loader w-5 h-5"></div>
           </template>
           <i data-lucide="send" class="w-4 h-4"></i> Send Response
         </button>
       </form>
     </div>
   </div>
 </div>