
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Booking Modals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .modal-enter {
            animation: modalEnter 0.3s ease-out;
        }
        
        .modal-backdrop {
            animation: backdropEnter 0.3s ease-out;
        }
        
        @keyframes modalEnter {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        @keyframes backdropEnter {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .gradient-bg {
  background: linear-gradient(135deg, #000000 0%, #000000 100%);
}

        
        .glass-effect {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .success-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .warning-gradient {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .error-gradient {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
    </style>
</head>
<body class="bg-gray-100 p-8">


    <!-- 📝 Booking Preview Modal -->
    <div id="previewModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop"
        style="background: rgba(0, 0, 0, 0.6);"
        role="dialog"
        aria-modal="true">
        <div class="glass-effect rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden modal-enter">
            <!-- Header -->
            <div class="gradient-bg text-white p-6 relative">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold">Booking Preview</h2>
                    </div>
                    <button onclick="hidePreviewModal()" class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-opacity-30 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Guest Information -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                Guest Information
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Primary Guest:</span>
                                    <span id="modalName" class="font-medium">John Doe</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span id="modalEmail" class="font-medium">john@example.com</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phone:</span>
                                    <span id="modalPhone" class="font-medium">+63 915 535 9844</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Guests:</span>
                                    <span id="modalTotalGuests" class="font-medium">3</span>
                                </div>
                            </div>
                        </div>

                        <!-- Guest Details -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                                Guest Details
                            </h3>
                            <div id="modalGuestList" class="space-y-2 text-sm">
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Guest 1:</span>
                                    <span class="font-medium">John Doe</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Guest 2:</span>
                                    <span class="font-medium">Jane Smith</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Guest 3:</span>
                                    <span class="font-medium">Bob Johnson</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Information -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                Booking Details
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Check-in:</span>
                                    <span id="modalCheckIn" class="font-medium">July 15, 2025</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Check-out:</span>
                                    <span id="modalCheckOut" class="font-medium">July 18, 2025</span>
                                </div>
                            </div>
                        </div>

                        <!-- Room Summary -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                Room Summary
                            </h3>
                            <div id="modalRoomSummary" class="text-sm">
                                <div class="space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Deluxe Room x2</span>
                                        <span class="font-medium">₱4,800</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Standard Room x1</span>
                                        <span class="font-medium">₱2,400</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Special Request -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Special Request
                            </h3>
                            <p id="modalRequest" class="text-sm text-gray-700">Late check-in, vegetarian meals requested</p>
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="mt-6 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-green-800">Total Amount:</span>
                        <span class="text-2xl font-bold text-green-700">₱<span id="modalTotal">7,200</span></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 border-t border-gray-200 p-6">
                <div class="flex justify-end gap-3">
                    <button id="cancelPreview" onclick="hidePreviewModal()"
                        class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition duration-200">
                        Cancel
                    </button>
                    <button type="button" id="confirmBookingBtn" onclick="showSuccessModal(); hidePreviewModal()"
                        class="px-6 py-2.5 success-gradient text-white font-semibold rounded-lg transition duration-200 hover:shadow-lg transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Confirm Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Booking Submission Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop"
        style="background: rgba(0, 0, 0, 0.6);"
        role="dialog" aria-modal="true">
        <div class="glass-effect rounded-2xl shadow-2xl max-w-lg w-full modal-enter">
            <!-- Header -->
            <div class="success-gradient text-white p-6 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Booking Submitted Successfully!</h1>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <p class="text-gray-700 mb-3">Thank you for choosing Subic Bay Hostel. Your booking request has been successfully submitted and is under review.</p>
                    <p class="text-sm text-gray-600">You will receive a notification via email and SMS once your booking is confirmed or declined.</p>
                </div>

                <div class="bg-gradient-to-r from-orange-50 to-orange-100 border border-orange-200 rounded-xl p-6">
                    <h2 class="text-lg font-bold text-orange-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Next Steps
                    </h2>
                    <div class="text-sm text-gray-700 space-y-2">
                        <p>Please await confirmation before making any travel plans. We'll notify you once our team reviews your request.</p>
                        <p>For questions or changes, contact us at:</p>
                        <div class="bg-white rounded-lg p-3 mt-2">
                            <div class="flex items-center space-x-2 text-orange-800">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                                <span class="font-semibold">0915-535-9844</span>
                            </div>
                            <div class="flex items-center space-x-2 text-orange-800 mt-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                                <span class="font-semibold">info@subichostel.com</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 border-t border-gray-200 p-6 text-center">
                <button onclick="hideSuccessModal()"
                    class="px-8 py-3 success-gradient text-white font-semibold rounded-lg transition duration-200 hover:shadow-lg transform hover:scale-105">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- ⚠️ Pending Booking Warning Modal -->
    <div id="warningModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop"
        style="background: rgba(0, 0, 0, 0.6);"
        role="dialog" aria-modal="true">
        <div class="glass-effect rounded-2xl shadow-2xl max-w-md w-full modal-enter">
            <!-- Header -->
            <div class="error-gradient text-white p-6 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold">Booking In Progress</h2>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <p class="text-gray-700 mb-3">A booking request with the email <strong class="text-red-600">john@example.com</strong> is currently being processed.</p>
                    <p class="text-sm text-gray-600">Please wait for approval or rejection of the current booking before submitting another.</p>
                </div>

                <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-red-800">Only one booking per email address can be processed at a time.</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 border-t border-gray-200 p-6 text-center">
                <button onclick="hideWarningModal()"
                    class="px-8 py-3 error-gradient text-white font-semibold rounded-lg transition duration-200 hover:shadow-lg transform hover:scale-105">
                    Understood
                </button>
            </div>
        </div>
    </div>

    <script>
        function showPreviewModal() {
            document.getElementById('previewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hidePreviewModal() {
            document.getElementById('previewModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showSuccessModal() {
            document.getElementById('successModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showWarningModal() {
            document.getElementById('warningModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideWarningModal() {
            document.getElementById('warningModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                hidePreviewModal();
                hideSuccessModal();
                hideWarningModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hidePreviewModal();
                hideSuccessModal();
                hideWarningModal();
            }
        });
    </script>
</body>
</html>