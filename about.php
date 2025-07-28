<?php include('includes/header.php'); ?>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<!-- AOS CSS & JS -->
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    once: true,
    duration: 1000,
    easing: 'ease-in-out',
  });
</script>

<link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css">

<style>
  body {
    font-family: 'Poppins', 'Noto Sans KR', sans-serif;
  }
</style>

<!-- Hero Section -->
<section class="bg-[#FFFCFB] py-32 text-center" data-aos="fade-down">
  <div class="max-w-4xl mx-auto px-4">
    <h1 class="text-4xl md:text-5xl text-[#222222] mb-4">
      Get to Know Subic Bay Hostel & Dormitory
    </h1>
    <p class="text-lg text-[#666666] leading-relaxed italic">
      A modern capsule-style stay in Subic, designed for comfort, convenience, and connection.
    </p>
  </div>
</section>

<!-- Our Story Section (Full-width Banner Style like Hotel Seoul Clark) -->
<section class="bg-[#F5F5F5] text-[#666666]" data-aos="fade-up">

  <!-- Image Banner -->
  <div class="w-full h-[300px] md:h-[400px] overflow-hidden " data-aos="zoom-in" data-aos-delay="100">
    <img 
      src="assets/images/sbhd-logo.jpg" 
      alt="Subic Bay Hostel - Front Area" 
      class="w-full h-full object-cover "
    >
  </div>

  <!-- Content -->
  <div class="max-w-5xl mx-auto px-6 py-16 text-center">
    
    <!-- Title -->
    <h2 class="text-3xl md:text-4xl  text-[#222222] mb-10" data-aos="fade-up" data-aos-delay="200">
      Our Story
    </h2>

    <!-- Text Description -->
    <div class="space-y-4 text-lg leading-relaxed">
      <p data-aos="fade-up" data-aos-delay="300">
        <strong class="text-[#222222] font-semibold">Subic Bay Hostel &amp; Dormitory</strong> is the first capsule hotel in Subic Bay Freeport Zone. Built for solo travelers, groups, and students, our hostel offers 280 standard and 60 deluxe capsules.
      </p>
      <p data-aos="fade-up" data-aos-delay="400">
        Just minutes from Subic’s prime hotspots, our space fosters connection, convenience, and rest — all in one smart environment.
      </p>
      <p class="italic text-[#555555]" data-aos="fade-up" data-aos-delay="500">
        Experience vibrant culinary delights at <strong class="text-[#222222] font-semibold">Subic Hawker</strong> — where flavors and community meet.
      </p>
    </div>
  </div>
</section>



<!-- Alpine.js (required for lightbox) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
  .card-3d {
    perspective: 1000px;
  }
  .card-3d img {
    transform-style: preserve-3d;
    transition: transform 0.6s ease;
  }
  .card-3d-left:hover img {
    transform: rotateY(10deg) scale(1.05);
  }
  .card-3d-center:hover img {
    transform: rotateX(10deg) scale(1.05);
  }
  .card-3d-right:hover img {
    transform: rotateY(-10deg) scale(1.05);
  }
</style>

<!-- Mission, Vision & Core Values Section -->
<section class="bg-[#FFFCFB] py-20 text-[#666666]" x-data="{ lightboxOpen: false, lightboxImage: '' }">
  <div class="max-w-6xl mx-auto px-4 space-y-20">

    <!-- Mission -->
    <div class="grid md:grid-cols-2 gap-10 items-center">
      <!-- Text -->
      <div data-aos="fade-right" data-aos-delay="100">
        <h3 class="text-2xl md:text-3xl font-semibold text-[#222222] mb-4">Our Mission</h3>
        <p class="text-base leading-relaxed">
          At <span class="font-semibold text-[#FF3F33]">Subic Bay Hostel and Dormitory</span>, our mission is to provide a clean, safe, and comfortable environment where guests can enjoy quality accommodations at reasonable rates. <br><br>
          We strive to ensure excellent customer service, promote a strong sense of community among our guests, and maintain high operational standards in everything we do.
        </p>
      </div>

      <!-- Image Group -->
      <div class="flex justify-center gap-4">
        <div class="card-3d card-3d-left" data-aos="fade-right" data-aos-delay="200">
          <img src="assets/images/rooms/mission-1.jpg" alt="Standard"
               class="w-36 md:w-44 aspect-square object-cover rounded-lg shadow-md cursor-pointer"
               @click="lightboxOpen = true; lightboxImage = 'assets/images/rooms/skye.jpg'">
        </div>
        <div class="card-3d card-3d-center" data-aos="zoom-in" data-aos-delay="300">
          <img src="assets/images/rooms/mission-2.jpg" alt="Lounge"
               class="w-36 md:w-44 aspect-square object-cover rounded-lg shadow-md cursor-pointer"
               @click="lightboxOpen = true; lightboxImage = 'assets/images/rooms/mission-2.jpg'">
        </div>
        <div class="card-3d card-3d-right" data-aos="fade-left" data-aos-delay="400">
          <img src="assets/images/rooms/mission-3.jpg" alt="Deluxe"
               class="w-36 md:w-44 aspect-square object-cover rounded-lg shadow-md cursor-pointer"
               @click="lightboxOpen = true; lightboxImage = 'assets/images/rooms/mission-3.jpg'">
        </div>
      </div>
    </div>

    <!-- Vision -->
    <div class="grid md:grid-cols-2 gap-10 items-center">
      <!-- Image Group -->
      <div class="flex justify-center gap-4">
        <div class="card-3d card-3d-left" data-aos="fade-right" data-aos-delay="200">
          <img src="assets/images/rooms/vision-1.jpg" alt="Standard"
               class="w-36 md:w-44 aspect-square object-cover rounded-lg shadow-md cursor-pointer"
               @click="lightboxOpen = true; lightboxImage = 'assets/images/rooms/vision-1.jpg'">
        </div>
        <div class="card-3d card-3d-center" data-aos="zoom-in" data-aos-delay="300">
          <img src="assets/images/rooms/vision-2.jpg" alt="Lounge"
               class="w-36 md:w-44 aspect-square object-cover rounded-lg shadow-md cursor-pointer"
               @click="lightboxOpen = true; lightboxImage = 'assets/images/rooms/vision-2.jpg'">
        </div>
        <div class="card-3d card-3d-right" data-aos="fade-left" data-aos-delay="400">
          <img src="assets/images/rooms/vision-3.jpg" alt="Deluxe"
               class="w-36 md:w-44 aspect-square object-cover rounded-lg shadow-md cursor-pointer"
               @click="lightboxOpen = true; lightboxImage = 'assets/images/rooms/vision-3.jpg'">
        </div>
      </div>

      <!-- Text -->
      <div data-aos="fade-left" data-aos-delay="100">
        <h3 class="text-2xl md:text-3xl font-semibold text-[#222222] mb-4">Our Vision</h3>
        <p class="text-base leading-relaxed">
          To be the <span class="font-semibold text-[#FF3F33]">leading hostel and dormitory</span> in the <span class="font-semibold text-[#FF3F33]">Subic Bay Freeport Zone</span>, recognized for our outstanding service, affordability, and unwavering commitment to guest satisfaction.
        </p>
      </div>
    </div>

    <!-- Core Values -->
    <div>
      <div class="text-center mb-10" data-aos="fade-up" data-aos-delay="100">
        <h3 class="text-2xl md:text-3xl font-semibold text-[#222222]">Our Core Values</h3>
        <p class="text-base mt-4 max-w-2xl mx-auto">We take pride in the foundational values that shape our service and hospitality.</p>
      </div>

      <!-- Values Grid -->
      <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 px-4" data-aos="fade-up" data-aos-delay="500">
        <div class="bg-white hover:scale-105 rounded-xl shadow p-6 text-center">
          <h4 class="font-semibold text-lg text-[#222222] mb-2">Customer Satisfaction</h4>
          <p class="text-sm">We prioritize the needs and comfort of our guests in every interaction.</p>
        </div>
        <div class="bg-white hover:scale-105 rounded-xl shadow p-6 text-center">
          <h4 class="font-semibold text-lg text-[#222222] mb-2">Affordability</h4>
          <p class="text-sm">Offering competitive rates without compromising quality.</p>
        </div>
        <div class="bg-white hover:scale-105 rounded-xl shadow p-6 text-center">
          <h4 class="font-semibold text-lg text-[#222222] mb-2">Cleanliness & Safety</h4>
          <p class="text-sm">Maintaining a hygienic and secure environment for all guests.</p>
        </div>
        <div class="bg-white hover:scale-105 rounded-xl shadow p-6 text-center">
          <h4 class="font-semibold text-lg text-[#222222] mb-2">Community-Oriented</h4>
          <p class="text-sm">Creating a welcoming and inclusive atmosphere that feels like home.</p>
        </div>
        <div class="bg-white hover:scale-105 rounded-xl shadow p-6 text-center">
          <h4 class="font-semibold text-lg text-[#222222] mb-2">Integrity</h4>
          <p class="text-sm">Conducting business with honesty, transparency, and respect.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Lightbox Modal -->
  <div x-show="lightboxOpen" x-transition.opacity
       class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50"
       @keydown.escape.window="lightboxOpen = false"
       x-cloak>
    <div @click.away="lightboxOpen = false" class="relative max-w-3xl mx-auto">
      <img :src="lightboxImage" alt="Preview" class="rounded-xl max-h-[90vh] mx-auto shadow-lg">
      <button @click="lightboxOpen = false"
              class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full p-1 hover:bg-opacity-75">
        ✕
      </button>
    </div>
  </div>
</section>

<!-- Prime Location & Tourist Attractions -->
<section class="py-24 bg-[#F5F5F5] text-black">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid md:grid-cols-2 gap-12">

      <!-- Tourist Attractions (Left Column) -->
      <div>
        <h3 class="text-2xl font-semibold mb-8 text-center">Tourist Attractions</h3>
        <div class="space-y-6">
          
          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-right">
            <img src="assets/images/cl/inflatable.jpg" alt="Inflatable Island" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">6 mins drive to <span class="font-semibold text-[#F94144]">INFLATABLE ISLAND</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-right" data-aos-delay="100">
            <img src="assets/images/cl/historical-center.jpg" alt="Subic Bay Historical Center" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">8 mins drive to <span class="font-semibold text-[#F94144]">SUBIC BAY HISTORICAL CENTER</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-right" data-aos-delay="200">
            <img src="assets/images/cl/pamulaklakin.jpg" alt="Pamulaklakin Trail" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">25 mins drive to <span class="font-semibold text-[#F94144]">PAMULAKLAKIN FOREST TRAIL</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-right" data-aos-delay="300">
            <img src="assets/images/cl/waterpark.jpg" alt="Adventure Beach Water Park" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">25 mins drive to <span class="font-semibold text-[#F94144]">ADVENTURE BEACH WATER PARK</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-right" data-aos-delay="400">
            <img src="assets/images/cl/holy-land.jpg" alt="Holy Land Subic" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">25 mins drive to <span class="font-semibold text-[#F94144]">HOLY LAND SUBIC SANCTUARY</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-right" data-aos-delay="500">
            <img src="assets/images/cl/ocean-ad.jpg" alt="Ocean Adventure" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">30 mins drive to <span class="font-semibold text-[#F94144]">OCEAN ADVENTURE</span></p>
            </div>
          </div>

        </div>
      </div>

      <!-- Prime Location (Right Column) -->
      <div>
        <h3 class="text-2xl font-semibold mb-8 text-center">Prime Location</h3>
        <div class="space-y-6">

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-left">
            <img src="assets/images/cl/harbor.jpg" alt="Ayala Malls" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">12 mins walk to <span class="font-semibold text-[#F94144]">AYALA MALLS</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-left" data-aos-delay="100">
            <img src="assets/images/cl/smd.jpg" alt="SM Downtown" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">7 mins drive to <span class="font-semibold text-[#F94144]">SM DOWNTOWN</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-left" data-aos-delay="200">
            <img src="assets/images/cl/pierone.jpg" alt="Pier One Bar & Grill" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">5 mins walk to <span class="font-semibold text-[#F94144]">PIER ONE BAR & GRILL</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-left" data-aos-delay="300">
            <img src="assets/images/cl/chapel.JPG" alt="San Roque Chapel" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">10 mins walk to <span class="font-semibold text-[#F94144]">SAN ROQUE CHAPEL</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-left" data-aos-delay="400">
            <img src="assets/images/cl/baypoint.jpg" alt="Baypointe Hospital" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">1 min walk to <span class="font-semibold text-[#F94144]">BAYPOINTE HOSPITAL</span></p>
            </div>
          </div>

          <div class="bg-white border border-gray-200 shadow-sm overflow-hidden" data-aos="fade-left" data-aos-delay="500">
            <img src="assets/images/cl/le.jpg" alt="Le Soleil" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">1 min walk to <span class="font-semibold text-[#F94144]">LE SOLEIL</span></p>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>

<!-- Our Services -->
<section class="bg-[#F5F5F5] py-24 text-[#666666]" data-aos="fade-up">
  <div class="max-w-6xl mx-auto px-6 text-center">
    <h2 class="text-3xl md:text-4xl font-semibold text-[#222222] mb-16">Our Services</h2>
    <div class="grid md:grid-cols-2 gap-6 text-left">
      <?php
      $services = [
        ["icon" => "sparkles", "title" => "Great Service", "desc" => "We ensure a clean, secure, and convenient stay, catering to both short-term visitors and long-term tenants."],
        ["icon" => "users", "title" => "Best Teamwork", "desc" => "All departments collaborate efficiently to ensure cleanliness, comfort, and convenience for every guest."],
        ["icon" => "map-pin", "title" => "Accessibility", "desc" => "Near key establishments, transportation hubs, dining, sports, and business centers for ease of movement."],
        ["icon" => "user-group", "title" => "Target Market", "desc" => "Appeals to individuals and groups who prioritize affordability, accessibility, and essential amenities over luxury."],
        ["icon" => "phone", "title" => "24/7 Customer Assistance", "desc" => "Friendly and helpful staff ready to assist guests anytime."],
        ["icon" => "shield-check", "title" => "Security & Safety", "desc" => "Safe and secure environment for all guests through 24/7 internal security and CCTV cameras."],
        ["icon" => "home", "title" => "Hospitality", "desc" => "We create a homey and comfortable atmosphere for guests."],
        ["icon" => "calendar-days", "title" => "Flexible Reservation", "desc" => "Options for short- and long-term stays with personalized arrangements."],
      ];

      // Heroicons SVG paths
      $icons = [
        "sparkles" => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 8.5V4m0 0l-1.5 2M16 4l1.5 2M8 16v4m0 0l-1.5-2M8 20l1.5-2M4 8h4m0 0L6 6.5M8 8L6 9.5M20 16h-4m0 0l2 1.5M16 16l2-1.5" />',
        "users" => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-4-4h-1m-6 6H2v-2a4 4 0 014-4h1m5-4a4 4 0 110-8 4 4 0 010 8zm6 4a4 4 0 100-8 4 4 0 000 8z" />',
        "map-pin" => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11.5a2 2 0 100-4 2 2 0 000 4zM12 21c4.97-4.197 7.455-7.64 7.455-10.5a7.455 7.455 0 10-14.91 0C4.545 13.36 7.03 16.803 12 21z" />',
        "user-group" => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8zm6 2a4 4 0 00-3-3.87m-6 0A4 4 0 006 14" />',
        "phone" => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75a.75.75 0 01.75-.75h2.086a.75.75 0 01.53.22l1.372 1.372a.75.75 0 01.22.53v2.086a.75.75 0 01-.75.75h-.486a11.025 11.025 0 005.385 5.385v-.486a.75.75 0 01.75-.75h2.086a.75.75 0 01.53.22l1.372 1.372a.75.75 0 01.22.53v2.086a.75.75 0 01-.75.75h-.75A13.5 13.5 0 012.25 7.5v-.75z" />',
        "shield-check" => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m1.113-6.364L12 2.25 7.887 3.636a2.25 2.25 0 00-1.51 2.14v3.113c0 5.293 3.015 10.146 7.623 12.26.622.297 1.29.297 1.912 0 4.608-2.114 7.623-6.967 7.623-12.26V5.777a2.25 2.25 0 00-1.51-2.14z" />',
        "home" => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955a.5.5 0 01.707 0L20.25 12M4.5 9.75v9.75a.75.75 0 00.75.75h5.25v-6h3v6h5.25a.75.75 0 00.75-.75V9.75" />',
        "calendar-days" => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v12.75m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V10.5a.75.75 0 01.75-.75h14.5a.75.75 0 01.75.75v8.25" />',
      ];

      $delay = 0;
      $i = 0;

      foreach ($services as $service) {
        $animation = $i % 2 === 0 ? 'fade-right' : 'fade-left';
        $iconPath = $icons[$service['icon']] ?? '';
        echo "
        <div data-aos='$animation' data-aos-delay='$delay' class='bg-white rounded-2xl border border-gray-200 shadow-sm p-6'>
          <div class='flex items-center gap-4 mb-4'>
            <div class='bg-[#F94144] text-white p-3 rounded-xl'>
              <svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" class=\"w-6 h-6\">
                $iconPath
              </svg>
            </div>
            <h4 class='text-lg md:text-xl font-semibold text-[black]'>{$service['title']}</h4>
          </div>
          <p class='text-gray-600 leading-relaxed'>{$service['desc']}</p>
        </div>";
        $delay += 100;
        $i++;
      }
      ?>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="py-16 bg-[#FFFCFB] text-black text-center" data-aos="fade-up">
  <div class="max-w-xl mx-auto px-4">
    <h3 class="text-2xl md:text-3xl font-semibold mb-4">Ready to Book?</h3>
    <p class="mb-4">📞 0999-996-6852 &nbsp;|&nbsp; 0915-535-9844</p>
     <a href="book.php" class="inline-block text-white bg-[black] hover:bg-[black] hover:text-white px-4 py-2 rounded-full font-semibold transition-transform hover:scale-125">
      <?= $text['book_now'] ?>
    </a>
    <div class="flex justify-center gap-6 text-2xl mt-8">
      <a href="https://www.facebook.com/subicbay.hostelanddormitory.33" target="_blank" class="hover:scale-125 transition-transform">
        <i class="fi fi-brands-facebook"></i>
      </a>
      <a href="https://www.tiktok.com/@subicbayhostel" target="_blank" class=" hover:scale-125 transition-transform">
        <i class="fi fi-brands-tik-tok"></i>
      </a>
      <a href="https://www.instagram.com/subicbayhostelanddormitory/" target="_blank" class=" hover:scale-125 transition-transform">
        <i class="fi fi-brands-instagram"></i>
      </a>
    </div>
  </div>
</section>

<!-- AOS Init -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 800, once: true });
</script>

<?php include('includes/footer.php'); ?>
