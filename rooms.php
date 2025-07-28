<?php
include('includes/header.php');
include('includes/db.php');

// Helper to clean image paths (slashes)
function cleanPath($path) {
  return trim(str_replace('\\', '/', $path));
}

// Function to fetch room types by specific IDs (default: [1, 2])
function getRoomTypes($conn, $ids = [1, 2]) {
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $types = str_repeat('i', count($ids));
  $stmt = $conn->prepare("SELECT * FROM room_types WHERE id IN ($placeholders) ORDER BY id ASC");
  $stmt->bind_param($types, ...$ids);
  $stmt->execute();
  $result = $stmt->get_result();
  $room_types = [];
  while ($row = $result->fetch_assoc()) {
    $row['image'] = cleanPath($row['image'] ?? 'assets/images/rooms/default.png');
    $row['lightbox_image'] = cleanPath($row['lightbox_image'] ?? $row['image']);
    $row['title'] = $row['title'] ?: 'Capsule Room';
    $row['desc'] = $row['description'] ?: 'Description not available.';
    $row['price'] = $row['price'] ?? 0.00;
    $row['also_available'] = $row['also_available'] ?? '';
    $row['inclusions'] = $row['inclusions'] ?? '';
    $row['capacity'] = $row['capacity'] ?? '344 Capsules';
    $row['extra'] = true;

    $room_types[] = $row;
  }
  return $room_types;
}

// Fetch gallery images for a room type
function fetchRoomImages($conn, $room_type_id, $limit = 4) {
  $stmt = $conn->prepare("SELECT image_path FROM room_images WHERE room_type_id = ? LIMIT ?");
  $stmt->bind_param("ii", $room_type_id, $limit);
  $stmt->execute();
  $result = $stmt->get_result();
  $images = [];
  while ($row = $result->fetch_assoc()) {
    $images[] = cleanPath($row['image_path']);  // Ensure proper path cleaning
  }
  return $images;
}

// Fetch the room types (thumbnails) and gallery images
$room_types = getRoomTypes($conn, [1, 2]);

// Ensure images are fetched from room_images table
foreach ($room_types as &$rt) {
  $rt['images'] = fetchRoomImages($conn, $rt['id'], 4);
}

// Map to Alpine-friendly format
$standard = $room_types[0] ?? [];
$deluxe = $room_types[1] ?? [];
$rooms = array_map(function ($rt) {
  return [
    'img' => $rt['image'],
    'lightbox' => $rt['lightbox_image'],
    'title' => $rt['title'],
    'desc' => $rt['desc'],
    'price' => $rt['price'],
    'inclusions' => $rt['inclusions'],
    'also_available' => $rt['also_available'],
    'capacity' => $rt['capacity'],
    'extra' => $rt['extra'] ?? false,
  ];
}, $room_types);
?>

<!-- Styles & Libraries -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css" />
<style>
  [x-cloak] { display: none !important; }
  html { scroll-behavior: smooth; }
  body {
    font-family: 'Poppins', 'Noto Sans KR', sans-serif;
  }
</style>

<div x-data='{
 capsuleRooms: true, // Initialize capsuleRooms
  lightboxOpen: false,
  lightboxImage: "",
  modalOpenRoom: false,
  activeRoom: {},
  standard: <?= json_encode($standard, JSON_UNESCAPED_SLASHES) ?>,
  deluxe: <?= json_encode($deluxe, JSON_UNESCAPED_SLASHES) ?>,
  rooms: <?= json_encode($rooms, JSON_UNESCAPED_SLASHES) ?>
}'>

 <!-- Hero Section -->
<section class="relative w-full h-[90vh] flex items-center justify-center overflow-hidden">
  <!-- Background Image -->
  <img src="assets/images/bg-capsule.jpg"
       alt="Modern capsule hotel interiors"
       class="absolute inset-0 w-full h-full object-cover"
       loading="lazy" />

  <!-- Overlay for readability -->
  <div class="absolute inset-0 bg-black/40"></div>

  <!-- Content -->
  <div class="relative z-10 text-center px-4 max-w-full"
       data-aos="fade-up"
       data-aos-duration="1000"
       data-aos-easing="ease-out">
    <h1 class="text-4xl md:text-6xl font-light leading-tight text-white drop-shadow-lg mb-4">
      Our <span class="font-semibold text-[#FF3D3D]">Capsules</span>
    </h1>
    <h2 class="text-xl md:text-1xl text-white/90 max-w-2xl mx-auto">
      Choose from our clean, modern capsule rooms — designed for privacy, comfort, and affordability.
    </h2>
  </div>
</section>

  <!-- Featured Capsule Rooms -->
<section class="bg-[#FFFCFB] py-24 px-4 text-[#222]">
  <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">
    
    <!-- Text Content -->
    <div data-aos="fade-right" data-aos-duration="800">
      <p class="text-sm uppercase tracking-wide mb-3">
        Recharge Your Soul: Tranquility Starts Here
      </p>
      <h2 class="text-4xl md:text-4xl font-light leading-tight mb-6">
        <span class="">Capsule Comfort:</span><br>
        <span class="uppercase text-[#FF3D3D]">We redefine the way you stay!</span>
      </h2>
      <p class="text-base text-gray-600 max-w-xl">
        The First Ever Capsule Hotel inside Subic Bay Freeport Zone, giving great value for your hard-earned money with comfortable accommodations and premium guest perks.
      </p>
    </div>

    <!-- Room Cards -->
    <div class="flex justify-center gap-6 flex-wrap mt-8 md:mt-0">
      <template x-for="(room, i) in rooms" :key="i">
        <div class="text-center"
             :data-aos="i % 2 === 0 ? 'fade-up-right' : 'fade-up-left'"
             :data-aos-delay="(i + 2) * 100">
          <img loading="lazy"
               :src="room.img"
               :alt="room.title + ' image'"
               class="w-48 md:w-60 aspect-square rounded-2xl object-cover hover:scale-105 transition-transform duration-300 ease-in-out shadow-xl cursor-pointer"
               @click.stop="lightboxOpen = true; lightboxImage = room.img" />
          <p class="mt-3 text-sm text-gray-700 font-medium tracking-wide" x-text="room.title"></p>
        </div>
      </template>
    </div>

  </div>
</section>

<!-- STANDARD CAPSULE SECTION -->
<section 
  class="bg-[#FFFCFB] py-10 px-4 sm:px-6 lg:px-8 text-[#222] overflow-hidden"
  x-data="capsuleRooms"
>
  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-start" data-aos="fade-up">

    <!-- LEFT COLUMN: Text Content -->
    <div class="flex flex-col w-full space-y-4">

      <!-- Title -->
      <div class="sticky top-0 bg-[#FFFCFB] z-10 py-4 text-center">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-light tracking-tight leading-snug">
          <span class="relative inline-block">
            <span class="text-[#F94144]">Standard</span> Capsule
            <div class="absolute bottom-0 left-0 w-1/2 h-[3px] bg-black"></div>
            <div class="flex bottom-0 items-center w-20 h-[3px] bg-[#F94144]"></div>
          </span>
        </h2>
        <p class="max-w-2xl mx-auto mt-3 text-sm sm:text-base text-gray-600 leading-relaxed">
          Our competitively priced capsule rooms are designed for comfort and value—perfect for every type of traveler.
        </p>
      </div>

      <!-- Sliding Review Carousel -->
      <div 
        x-data="{
          currentIndex: 0,
          reviews: [
            { text: '“Very clean and organized. Perfect for students or backpackers. Highly recommended!”', author: 'Alyssa C.', date: 'Feb 2024' },
            { text: '“Staff are accommodating and friendly. Ganda ng ambiance, sulit ang bayad.”', author: 'Jerome T.', date: 'Mar 2024' },
            { text: '“Malinis, safe, and affordable. Will definitely come back with friends.”', author: 'Rina G.', date: 'May 2024' },
            { text: '“Relaxing atmosphere and very clean rooms. Excellent for long stays.”', author: 'Kevin P.', date: 'Jun 2024' }
          ],
          start() {
            setInterval(() => {
              this.currentIndex = (this.currentIndex + 1) % this.reviews.length;
            }, 4000);
          }
        }"
        x-init="start"
        class="w-full mt-4 relative overflow-hidden max-w-3xl mx-auto"
      >
        <div class="w-full overflow-hidden">
          <div 
            class="flex transition-transform duration-700 ease-in-out"
            :style="'transform: translateX(-' + (currentIndex * 100) + '%)'"
            style="width: 400%;"
          >
            <template x-for="(review, index) in reviews" :key="index">
              <div class="w-full flex-shrink-0 px-2">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 sm:p-6 flex flex-col justify-between min-h-[180px]">
                  <p class="text-sm sm:text-base lg:text-lg text-gray-700 italic leading-relaxed" x-text="review.text"></p>

                  <div class="mt-4 flex justify-between items-end">
                    <div class="flex items-center gap-1 text-xs sm:text-sm text-neutral-600">
                      <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.112 3.417a1 1 0 00.95.69h3.6c.969 0 1.371 1.24.588 1.81l-2.917 2.12a1 1 0 00-.364 1.118l1.112 3.417c.3.921-.755 1.688-1.538 1.118l-2.917-2.12a1 1 0 00-1.176 0l-2.917 2.12c-.783.57-1.838-.197-1.538-1.118l1.112-3.417a1 1 0 00-.364-1.118l-2.917-2.12c-.783-.57-.38-1.81.588-1.81h3.6a1 1 0 00.95-.69l1.112-3.417z"/>
                      </svg>
                      <span>4.8 / 5.0</span>
                    </div>
                    <div class="text-right text-xs sm:text-sm">
                      <p class="font-semibold text-[#F94144]" x-text="review.author"></p>
                      <p class="text-gray-400" x-text="review.date"></p>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- Dots -->
        <div class="flex justify-center mt-4 space-x-1">
          <template x-for="(dot, dotIndex) in reviews" :key="'dot' + dotIndex">
            <div 
              @click="currentIndex = dotIndex"
              :class="{
                'bg-[#F94144] scale-110': currentIndex === dotIndex,
                'bg-gray-300': currentIndex !== dotIndex
              }"
              class="w-2.5 h-2.5 rounded-full cursor-pointer transition-all duration-300"
            ></div>
          </template>
        </div>
      </div>

      <!-- Buttons -->
      <div class="mt-6 flex flex-col sm:flex-row justify-center items-center gap-4">
        <button 
          @click="modalOpenRoom = true; activeRoom = standard"
          class="group inline-flex items-center justify-center gap-2 bg-neutral-900 hover:bg-neutral-800 text-white text-sm font-medium px-6 py-2 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:ring-offset-2 active:scale-[0.98] w-full sm:w-auto"
        >
          <span>View Details</span>
          <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <a 
          href="book.php?room=standard" 
          class="inline-flex items-center justify-center gap-2 bg-[#F94144] hover:bg-[#e33131] text-white text-sm font-medium px-10 py-2 rounded-lg shadow-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-400/50 focus:ring-offset-2 active:scale-[0.98] w-full sm:w-auto"
        >
          Book Now
        </a>
      </div>

      <!-- Price -->
      <!-- Price -->
<p class="mt-2 text-xs sm:text-sm text-center text-neutral-500">
  Starting from 
  <span class="font-semibold text-neutral-700" x-text="'PHP' + Number(standard.price).toLocaleString() + '/night'"></span>
</p>
    </div>

    <!-- RIGHT COLUMN: Image Grid -->
    <div class="w-full">
      <div class="grid grid-cols-2 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
        <template x-for="(img, idx) in standard.images" :key="idx">
          <div class="relative overflow-hidden rounded-md shadow-md group cursor-pointer">
            <img
              loading="lazy"
              :src="img"
              :alt="`Standard Capsule image ${idx + 1}`"
              class="w-full h-40 sm:h-48 md:h-56 object-cover object-center transition-transform duration-500 group-hover:scale-105 group-hover:brightness-90"
              @click="lightboxOpen = true; lightboxImage = img"
            />
                    </div>
        </template>
      </div>
    </div>

  </div>
</section>

<!-- DELUXE CAPSULE SECTION -->
<section 
  class="bg-[#FFFCFB] py-10 px-4 sm:px-6 lg:px-8 text-[#222] overflow-hidden"
  x-data="capsuleRooms"
>
  <div class="max-w-7xl mx-auto w-full grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-start" data-aos="fade-up">

    <!-- LEFT COLUMN: Image Grid -->
    <div class="space-y-4 w-full order-2 md:order-none">
      <div class="grid grid-cols-2 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
        <template x-for="(img, idx) in deluxe.images" :key="idx">
          <div class="relative overflow-hidden rounded-md shadow-md group cursor-pointer">
            <img
              loading="lazy"
              :src="img"
              :alt="`Deluxe Capsule image ${idx + 1}`"
              class="w-full h-40 sm:h-48 md:h-56 object-cover object-center transition-transform duration-500 group-hover:scale-105 group-hover:brightness-90"
              @click="lightboxOpen = true; lightboxImage = img"
            />
                      </div>
        </template>
      </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="flex flex-col w-full order-1 md:order-none">

      <!-- Title -->
      <div class="sticky top-0 bg-[#FFFCFB] z-10 py-4 text-center">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-light tracking-tight leading-snug">
          <span class="relative inline-block">
            <span class="text-[#F94144]">Deluxe</span> Capsule
            <div class="absolute bottom-0 left-0 w-1/2 h-[3px] bg-black"></div>
            <div class="flex bottom-0 items-center w-20 h-[3px] bg-[#F94144]"></div>
          </span>
        </h2>
        <p class="max-w-2xl mx-auto mt-3 text-sm sm:text-base text-gray-600 leading-relaxed">
          Great for budget travelers and groups. Discounts available for big groups, school trips, team buildings, and sports activities.
        </p>
      </div>

      <!-- SLIDING REVIEW CAROUSEL -->
      <div 
        x-data="{
          currentIndex: 0,
          reviews: [
            { text: '“Spacious and comfortable. Best for barkada trips and long stays.”', author: 'Maria L.', date: 'Jan 2024' },
            { text: '“Good value for money. Rooms are cozy and staff are nice.”', author: 'Enzo D.', date: 'Feb 2024' },
            { text: '“Perfect for our group retreat. Malinis at maayos!”', author: 'Rachelle B.', date: 'Apr 2024' },
            { text: '“The deluxe capsule felt like a hotel at hostel prices.”', author: 'Leo V.', date: 'Jun 2024' }
          ],
          start() {
            setInterval(() => {
              this.currentIndex = (this.currentIndex + 1) % this.reviews.length;
            }, 4000);
          }
        }"
        x-init="start"
        class="w-full mt-4 relative overflow-hidden max-w-3xl mx-auto"
      >
        <div class="w-full overflow-hidden">
          <div 
            class="flex transition-transform duration-700 ease-in-out"
            :style="'transform: translateX(-' + (currentIndex * 100) + '%)'"
            style="width: 400%;"
          >
            <template x-for="(review, index) in reviews" :key="index">
              <div class="w-full flex-shrink-0 px-2">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 sm:p-6 flex flex-col justify-between min-h-[180px]">
                  <p class="text-sm sm:text-base lg:text-lg text-gray-700 italic leading-relaxed" x-text="review.text"></p>

                  <div class="mt-4 flex justify-between items-end">
                    <div class="flex items-center gap-1 text-xs sm:text-sm text-neutral-600">
                      <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.112 3.417a1 1 0 00.95.69h3.6c.969 0 1.371 1.24.588 1.81l-2.917 2.12a1 1 0 00-.364 1.118l1.112 3.417c.3.921-.755 1.688-1.538 1.118l-2.917-2.12a1 1 0 00-1.176 0l-2.917 2.12c-.783.57-1.838-.197-1.538-1.118l1.112-3.417a1 1 0 00-.364-1.118l-2.917-2.12c-.783-.57-.38-1.81.588-1.81h3.6a1 1 0 00.95-.69l1.112-3.417z"/>
                      </svg>
                      <span>4.9 / 5.0</span>
                    </div>
                    <div class="text-right text-xs sm:text-sm">
                      <p class="font-semibold text-[#F94144]" x-text="review.author"></p>
                      <p class="text-gray-400" x-text="review.date"></p>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- Dots -->
        <div class="flex justify-center mt-4 space-x-1">
          <template x-for="(dot, dotIndex) in reviews" :key="'dot' + dotIndex">
            <div 
              @click="currentIndex = dotIndex"
              :class="{
                'bg-[#F94144] scale-110': currentIndex === dotIndex,
                'bg-gray-300': currentIndex !== dotIndex
              }"
              class="w-2.5 h-2.5 rounded-full cursor-pointer transition-all duration-300"
            ></div>
          </template>
        </div>
      </div>

      <!-- Buttons -->
      <div class="mt-6 flex flex-col sm:flex-row justify-center items-center gap-4">
        <button 
          @click="modalOpenRoom = true; activeRoom = deluxe"
          class="group inline-flex items-center justify-center gap-2 bg-neutral-900 hover:bg-neutral-800 text-white text-sm font-medium px-6 py-2 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:ring-offset-2 active:scale-[0.98] w-full sm:w-auto"
        >
          <span>View Details</span>
          <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <a 
          href="book.php?room=deluxe" 
          class="inline-flex items-center justify-center gap-2 bg-[#F94144] hover:bg-[#e33131] text-white text-sm font-medium px-10 py-2 rounded-lg shadow-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-400/50 focus:ring-offset-2 active:scale-[0.98] w-full sm:w-auto"
        >
          Book Now
        </a>
      </div>

      <!-- Price -->
      <!-- Price -->
<p class="mt-2 text-xs sm:text-sm text-center text-neutral-500">
  Starting from 
  <span class="font-semibold text-neutral-700" x-text="'PHP' + Number(deluxe.price).toLocaleString() + '/night'"></span>
</p>
    </div>

  </div>
</section>

<?php include 'modals/rooms-modal.php'; ?>
<?php include 'includes/cta.php'; ?>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
      once: true,
      duration: 800,
    });
  });
</script>

<?php include('includes/footer.php'); ?>
