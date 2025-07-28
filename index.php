<?php include('includes/header.php'); ?>
<?php include('includes/lang.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet" />

<style>
  body {
    font-family: 'Poppins', 'Noto Sans KR', sans-serif;
    /* No other styles changed */
  }
</style>


<!-- Hero Section -->
<section class="relative w-full h-screen overflow-hidden bg-center bg-cover">

  <!-- Background Video -->
  <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
    <source src="assets/videos/bg-index.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>

  <!-- Overlay -->
  <div class="absolute inset-0 bg-black/40 z-10"></div>

  <!-- Foreground Content -->
  <div class="relative z-20 flex flex-col items-center justify-center text-center text-white px-6 h-full max-w-3xl mx-auto" data-aos="fade-up">
    <h1 class="text-3xl md:text-6xl font-semibold leading-tight drop-shadow-sm">
      <?= $text['hero_line_1'] ?><br>
      for <span class="underline decoration-[#F94144]">Comfort</span>.
    </h1>
    <p class="mt-4 text-base md:text-lg text-gray-200 leading-relaxed">
      <?= $text['hero_sub'] ?>
    </p>
    <a href="book.php" class="mt-8 inline-block bg-[#F94144] hover:bg-[#D8322E] text-white px-6 py-2 rounded-full font-medium shadow-md transition duration-300 hover:scale-105">
      <?= $text['book_now'] ?>
    </a>
  </div>

</section>

<!-- Explore and Experience -->
<section class="py-20 bg-[#FFFCFB] text-black">
  <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">

<!-- Title and Subtitle -->
<div class="mb-10" data-aos="fade-up" x-data="{
  title: 'Explore and Experience',
  currentText: '',
  typingSpeed: 50,
  eraseSpeed: 40,
  isErasing: false,
  isTyping: true,
  typing() {
    if (this.isTyping && this.currentText.length < this.title.length) {
      // Typing effect
      this.currentText += this.title[this.currentText.length];
      setTimeout(() => this.typing(), this.typingSpeed);
    } else if (!this.isTyping && this.currentText.length > 0) {
      // Erasing effect
      this.currentText = this.currentText.slice(0, this.currentText.length - 1);
      setTimeout(() => this.typing(), this.eraseSpeed);
    } else if (this.isTyping && this.currentText.length === this.title.length) {
      // Switch to erasing after the full title is typed
      this.isTyping = false;
      setTimeout(() => this.typing(), 1000);  // Wait 1 second before starting to erase
    } else if (!this.isTyping && this.currentText.length === 0) {
      // After erasing, move to the next typing loop
      this.isErasing = false;
      this.isTyping = true;
      setTimeout(() => this.typing(), 200); // Short pause before starting to type again
    }
  },
  init() {
    this.typing();  // Start typing the title when component loads
  }
}">
  <!-- Auto-Typing Title -->
  <h2 class="text-4xl md:text-4xl mb-3" x-text="currentText"></h2>
  <p class="text-gray-600 max-w-2xl">
    A modern capsule-style stay in Subic, designed for comfort, convenience, and connection.
  </p>
</div>



    <!-- Cards Grid -->
    <div class="grid gap-8 md:grid-cols-3">

      <!-- Card Template -->
      <a href="rooms.php"
        class="group relative block overflow-hidden shadow-lg hover:shadow-2xl transition duration-500"
        data-aos="fade-up" data-aos-delay="100">
        <img src="assets/images/rooms/deluxe1.png" alt="Rooms"
          class="w-full h-[300px] md:h-[360px] object-cover transform transition duration-700 group-hover:scale-110" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
        <div class="absolute bottom-6 left-6 right-6 text-white transition-opacity duration-500 group-hover:opacity-0">
          <h3 class="text-2xl font-semibold mb-1">Room</h3>
          <p class="text-sm leading-snug">
            Stay in our Japanese-style capsule rooms—affordable, comfy, and perfect for solo travelers or groups.
          </p>
        </div>
        <div
          class="absolute bottom-0 left-0 w-full h-1 bg-[red] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left">
        </div>
      </a>

      <a href="about.php"
        class="group relative block  overflow-hidden shadow-lg hover:shadow-2xl transition duration-500"
        data-aos="fade-up" data-aos-delay="200">
        <img src="assets/images/facilities/lounge1.jpg" alt="Facilities"
          class="w-full h-[300px] md:h-[360px] object-cover transform transition duration-700 group-hover:scale-110" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
        <div class="absolute bottom-6 left-6 right-6 text-white transition-opacity duration-500 group-hover:opacity-0">
          <h3 class="text-2xl font-semibold mb-1">About Us</h3>
          <p class="text-sm leading-snug">
            We’re the first capsule hotel in Subic Bay, offering a unique, budget-friendly stay inside the Freeport Zone.
          </p>
        </div>
        <div
          class="absolute bottom-0 left-0 w-full h-1 bg-[red] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left">
        </div>
      </a>

      <a href="hawker.php"
        class="group relative block  overflow-hidden shadow-lg hover:shadow-2xl transition duration-500"
        data-aos="fade-up" data-aos-delay="300">
        <img src="assets/images/hawker/food.jpg" alt="Food Court"
          class="w-full h-[300px] md:h-[360px] object-cover transform transition duration-700 group-hover:scale-110" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
        <div class="absolute bottom-6 left-6 right-6 text-white transition-opacity duration-500 group-hover:opacity-0">
          <h3 class="text-2xl font-semibold mb-1">Food Court</h3>
          <p class="text-sm leading-snug">
            Don't just scroll... satisfy your hunger at Subic Bay Hawker!
          </p>
        </div>
        <div
          class="absolute bottom-0 left-0 w-full h-1 bg-[red] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left">
        </div>
      </a>

    </div>
  </div>
</section>



<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<!-- AOS CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />

<section class="py-24 h-auto md:h-[400px] text-[black] bg-[#F5F5F5]">
  <div class="max-w-6xl mx-auto px-6 text-center">
    <h2 class="text-4xl mb-12" data-aos="fade-up">What Our Guests Say</h2>

    <div class="relative" data-aos="fade-up" data-aos-delay="100">
      <!-- Swiper -->
      <div class="swiper single-review-swiper">
        <div class="swiper-wrapper">
          <!-- Slide 1 -->
          <div class="swiper-slide">
            <div class="p-8 text-center">
              <p class="italic text-gray-600">"Very clean and organized. Perfect for students or backpackers. Highly recommended!"</p>
              <div class="mt-4">
                <h4 class="font-bold text-[#F94144]">Alyssa C.</h4>
                <p class="text-sm text-gray-400">February 2024</p>
              </div>
            </div>
          </div>
          <!-- Slide 2 -->
          <div class="swiper-slide">
            <div class="p-8 text-center">
              <p class="italic text-gray-600">"Staff are accommodating and friendly. Ganda ng ambiance, sulit ang bayad."</p>
              <div class="mt-4">
                <h4 class="font-bold text-[#F94144]">Jerome T.</h4>
                <p class="text-sm text-gray-400">March 2024</p>
              </div>
            </div>
          </div>
          <!-- Slide 3 -->
          <div class="swiper-slide">
            <div class="p-8 text-center">
              <p class="italic text-gray-600">"Malinis, safe, and affordable. Will definitely come back with friends."</p>
              <div class="mt-4">
                <h4 class="font-bold text-[#F94144]">Rina G.</h4>
                <p class="text-sm text-gray-400">May 2024</p>
              </div>
            </div>
          </div>
          <!-- Slide 4 -->
          <div class="swiper-slide">
            <div class="p-8 text-center">
              <p class="italic text-gray-600">"Relaxing atmosphere and very clean rooms. Excellent for long stays."</p>
              <div class="mt-4">
                <h4 class="font-bold text-[#F94144]">Kevin P.</h4>
                <p class="text-sm text-gray-400">June 2024</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination Dots -->
        <div class="swiper-pagination mt-8"></div>
      </div>

      <!-- Arrows - moved outside the swiper -->
      <div class="swiper-button-prev custom-arrow left-0 top-1/2 -translate-y-1/2 z-10"></div>
      <div class="swiper-button-next custom-arrow right-0 top-1/2 -translate-y-1/2 z-10"></div>

     <style>
  .custom-arrow::after {
    color: #000;
    font-size: 28px;
  }

  .custom-arrow {
    position: absolute;
    width: 40px;
    height: 40px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    background-color: transparent; /* Remove background */
    box-shadow: none; /* Remove shadow */
  }

  .custom-arrow:hover {
    background-color: transparent; /* Keep it transparent on hover */
  }

  .custom-arrow:hover::after {
    color: #F94144; /* Optional: change arrow color on hover */
  }
</style>

    </div>
  </div>
</section>


<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    new Swiper(".single-review-swiper", {
      loop: true,
      slidesPerView: 1,
      spaceBetween: 20,
      grabCursor: true,
      autoplay: {
        delay: 7000,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });

    AOS.init({
      duration: 800,
      once: true,
    });
  });
</script>

<!-- Our Clients -->
<section class="py-24 bg-[#FFFCFB] text-black">
  <div class="max-w-7xl mx-auto px-6">
    <h3 class="text-4xl mb-12 text-center">Our <span class="underline decoration-[#F94144]">Clients</span></h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center">

      <div class="flex justify-center items-center" data-aos="fade-up">
        <img src="assets/images/cl/unilab.jpg" alt="Unilab" class="w-28 h-auto transition hover:scale-125 duration-500 ">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="50">
        <img src="assets/images/cl/pharex.jpg" alt="Pharex" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="100">
        <img src="assets/images/cl/pmma.jpg" alt="PMMA" class="w-28 h-aut transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="150">
        <img src="assets/images/cl/bpsu.jpg" alt="BPSU" class="w-28 h-aut transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="200">
        <img src="assets/images/cl/arp.jpg" alt="ARP" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="250">
        <img src="assets/images/cl/gigamare.jpg" alt="Gigamare" class="w-28 h-aut transition hover:scale-125 duration-500">
      </div>

      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="300">
        <img src="assets/images/cl/nvsu.jpg" alt="NVSU" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="350">
        <img src="assets/images/cl/baypointe.jpg" alt="Baypointe" class="w-28 h-aut transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="400">
        <img src="assets/images/cl/lighthouse.jpg" alt="Lighthouse" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="450">
        <img src="assets/images/cl/abs-cbn.jpg" alt="ABS-CBN" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="500">
        <img src="assets/images/cl/gma.jpg" alt="GMA" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="550">
        <img src="assets/images/cl/sdc.jpg" alt="SDC" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>

      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="600">
        <img src="assets/images/cl/nu.jpg" alt="NU" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="650">
        <img src="assets/images/cl/redcross.jpg" alt="Red Cross" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="700">
        <img src="assets/images/cl/planate.jpg" alt="Planate" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="750">
        <img src="assets/images/cl/themanila.jpg" alt="The Manila" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="800">
        <img src="assets/images/cl/buwelo.jpg" alt="Buwelo" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>
      <div class="flex justify-center items-center" data-aos="fade-up" data-aos-delay="850">
        <img src="assets/images/cl/centralluzon.jpg" alt="Central Luzon" class="w-28 h-auto transition hover:scale-125 duration-500">
      </div>

    </div>
  </div>
</section>

<!-- FAQs Section -->
<section class="py-24 bg-[#FFFCFB] text-[black]">
  <div class="max-w-5xl mx-auto px-6">
    <h2 class="text-4xl text-center mb-12">Frequently Asked Questions</h2>
    <div class="space-y-4">
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6" data-aos="fade-right">
        <h3 class="text-lg md:text-xl text-[#F94144] font-medium mb-2">What are the check-in and check-out times?</h3>
        <p class="text-gray-600 leading-relaxed">
          Check-in: 2:00 PM Check-out: 12:00 NN Early check-in and late check-out incur additional fees.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6" data-aos="fade-left" data-aos-delay="100">
        <h3 class="text-lg md:text-xl text-[#F94144] font-medium mb-2">Do you have parking facilities?</h3>
        <p class="text-gray-600 leading-relaxed">
          Yes, we provide secure and complimentary parking for all our guests.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6" data-aos="fade-right" data-aos-delay="200">
        <h3 class="text-lg md:text-xl text-[#F94144] font-medium mb-2">Is Wi-Fi available in all rooms?</h3>
        <p class="text-gray-600 leading-relaxed">
          Absolutely! High-speed Wi-Fi is accessible throughout the property including guest rooms and common areas.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6" data-aos="fade-left" data-aos-delay="300">
        <h3 class="text-lg md:text-xl text-[#F94144] font-medium mb-2">Do you accept walk-in bookings?</h3>
        <p class="text-gray-600 leading-relaxed">
          Yes, but we recommend booking in advance to ensure availability.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6" data-aos="fade-right" data-aos-delay="400">
        <h3 class="text-lg md:text-xl text-[#F94144] font-medium mb-2">Can I request a late check-out?</h3>
        <p class="text-gray-600 leading-relaxed">
          Late check-out is subject to availability and may incur additional charges.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6" data-aos="fade-left" data-aos-delay="500">
        <h3 class="text-lg md:text-xl text-[#F94144] font-medium mb-2">Do you offer airport shuttle service?</h3>
        <p class="text-gray-600 leading-relaxed">
          Yes, we offer airport transfers upon request. Please coordinate with our front desk in advance.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Map Section -->
<section class="bg-[#F5F5F5] text-[black] py-24" data-aos="fade-up">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <h2 class="text-4xl mb-4">Give Us a Visit</h2>
    <p class="text-lg">We’re located right in the heart of Olongapo — accessible, convenient, and close to everything.</p>
    <p class="text-xl mt-2">📍Block 8, Lot 2B, Waterfront Road, Subic Bay Freeport Zone, Olongapo City, 2200</p>
    <div class="relative mt-12 overflow-hidden shadow-xl transition-transform hover:scale-105 duration-500">
      <iframe class="w-full h-[22rem] md:h-[32rem] border-2 border-[#F94144] l" 
        src="https://maps.google.com/maps?q=Subic%20Bay%20Hostel&t=&z=15&ie=UTF8&iwloc=&output=embed" 
        loading="lazy" allowfullscreen frameborder="0"></iframe>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-20 bg-[#FFFCFB] text-black text-center">
  <div class="max-w-4xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-bold mb-4"><?= $text['cta_title'] ?></h2>
    <p class="mb-4 text-lg">📞 0999-996-6852 &nbsp;&nbsp; | &nbsp;&nbsp; 0915-535-9844</p>
    <div class="flex justify-center gap-6 text-2xl my-4">
      <a href="https://www.facebook.com/subicbay.hostelanddormitory.33" class="hover:text-red hover:scale-125 transition" target="_blank"><i class="fi fi-brands-facebook"></i></a>
      <a href="https://www.tiktok.com/@subicbayhostel" class="hover:text-red hover:scale-125 transition" target="_blank"><i class="fi fi-brands-tik-tok"></i></a>
      <a href="https://www.instagram.com/subicbayhostelanddormitory/" class="hover:text-red hover:scale-125 transition" target="_blank"><i class="fi fi-brands-instagram"></i></a>
    </div>
    <p class="mb-6 text-base"><?= $text['cta_desc'] ?></p>
    <a href="book.php" class="inline-block text-white bg-[black] hover:bg-[black] hover:text-white px-4 py-2 rounded-full font-semibold transition-transform hover:scale-125">
      <?= $text['book_now'] ?>
    </a>
  </div>
</section>

<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ duration: 900, once: true });</script>
<script src="https://unpkg.com/alpinejs" defer></script>
<?php include('includes/footer.php'); ?>