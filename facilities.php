<?php include('includes/header.php'); ?>

<!-- External Resources -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<style>
  [x-cloak] { display: none !important; }
  img { loading: lazy; }
  /* Lightbox modal styles */
  .lightbox-bg { background: rgba(0,0,0,0.8); }
</style>

<script>
  document.addEventListener('alpine:init', () => {
    AOS.init({ duration: 800, once: true });
  });
</script>

<main class="space-y-32">

  <!-- FACILITIES Hero -->
  <section class="relative py-32 text-center bg-cover bg-center lightbox-bg"
           style="background-image:url('assets/images/facilities/lobby1.jpg');" data-aos="fade-down">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-white">
      <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Our Facilities & Amenities</h1>
      <p class="text-lg md:text-xl italic">Experience Subic Bay Hostel & Dormitory—premium comfort, convenience, and style in one place.</p>
    </div>
  </section>

  <!-- MERGED Content (Facilities + Amenities) -->
  <section class="bg-[#FFFCFB] py-20" 
           x-data="{ lightboxOpen: false, lightboxImage: '', modalOpen: false, active: {} }">

    <div class="max-w-6xl mx-auto space-y-24">
      <?php
      // Combined data sets
      $sections = [
        [
          'type' => 'amenity',
          'title' => 'Ultra‑Comfy Capsule Beds',
          'description' => 'Sleep soundly in our private, cozy capsules designed for ultimate rest and privacy.',
          'images' => ['capsule-bed.jpg']
        ],
        [
          'type' => 'facility',
          'title' => 'Laundry Services',
          'description' => 'Convenient on-site laundry facilities to keep your clothes fresh during your stay.',
          'images' => ['laundry1.png', 'laundry2.png', 'laundry3.png']
        ],
        [
          'type' => 'amenity',
          'title' => 'Free High‑Speed Wi‑Fi',
          'description' => 'Stay connected with fast, reliable internet throughout the property.',
          'images' => ['freewifi.jpg']
        ],
        [
          'type' => 'facility',
          'title' => 'Subic Bay Hawker',
          'description' => 'A vibrant food court with local & international cuisines to delight your taste buds.',
          'images' => ['hawker1.png', 'cafeteria.png', 'food.jpg']
        ],
        [
          'type' => 'facility',
          'title' => 'Lobby & Lounge Areas',
          'description' => 'Relax in our welcoming lobby and stylish lounge areas with comfortable seating and friendly staff.',
          'images' => ['lobby1.jpg', 'lounge1.jpg']
        ],
        [
          'type' => 'amenity',
          'title' => 'Hot & Cold Showers',
          'description' => 'Enjoy refreshing hot or cold showers anytime of day.',
          'images' => ['shower.jpg']
        ],
        [
          'type' => 'facility',
          'title' => 'Function Room',
          'description' => 'Ideal venue for events, meetings, and celebrations right here at the property.',
          'images' => ['function1.png']
        ],
        [
          'type' => 'amenity',
          'title' => 'Free Parking',
          'description' => 'Secure, hassle‑free parking for all guests.',
          'images' => ['parking.jpg']
        ],
        [
          'type' => 'amenity',
          'title' => 'Competent, Friendly Staff',
          'description' => 'Experience genuine Filipino hospitality with our courteous team.',
          'images' => ['staff.jpg']
        ],
      ];

      $i = 0;
      foreach ($sections as $sec) {
        $isAmenity = ($sec['type'] === 'amenity');
        $reverse = ($i % 2 !== 0) ? 'md:flex-row-reverse' : '';
        $fadeDir = ($i % 2 === 0) ? 'fade-right' : 'fade-left';
        ?>
        <div class="grid md:grid-cols-2 items-center gap-12 <?php echo $reverse; ?>">
          <!-- Text content -->
          <div data-aos="<?php echo $fadeDir; ?>" data-aos-delay="150">
            <h2 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($sec['title']); ?></h2>
            <p class="text-gray-700 leading-relaxed"><?php echo htmlspecialchars($sec['description']); ?></p>
          </div>
          <!-- Image gallery -->
          <div class="flex flex-wrap justify-center gap-6">
            <?php foreach ($sec['images'] as $idx => $img): 
              $delay = 300 + ($idx * 150);
              ?>
              <div class="w-40 h-40 overflow-hidden rounded-xl shadow-lg hover:scale-105 transform transition"
                   data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                <img src="assets/images/facilities/<?php echo $img; ?>" alt="<?php echo htmlspecialchars($sec['title']); ?> image <?php echo $idx+1; ?>"
                     @click="lightboxOpen = true; lightboxImage = 'assets/images/facilities/<?php echo $img; ?>'">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php $i++; 
      } ?>
    </div>

    <!-- Lightbox Modal -->
    <div x-show="lightboxOpen" x-transition.opacity class="fixed inset-0 lightbox-bg flex items-center justify-center z-50"
         @keydown.escape.window="lightboxOpen = false" x-cloak>
      <div @click.away="lightboxOpen = false" class="relative max-w-3xl mx-auto p-4">
        <img :src="lightboxImage" alt="Preview image" class="rounded-xl shadow-xl max-h-[90vh] mx-auto">
        <button @click="lightboxOpen = false" tabindex="0"
                class="absolute top-2 right-2 text-white bg-black bg-opacity-50 p-2 rounded-full hover:bg-opacity-75"
                aria-label="Close lightbox">✕</button>
      </div>
    </div>

  </section>

  <!-- Call-to-Action Footer -->
  <section class="py-20 bg-[#EEE9E6] text-center">
    <div class="max-w-4xl mx-auto px-6">
      <h2 class="text-3xl font-bold mb-4">Ready for Your Stay?</h2>
      <p class="text-lg mb-6">📞 0999-996-6852 | 0915-535-9844</p>
      <div class="flex justify-center gap-6 text-2xl mb-6">
        <a href="https://www.facebook.com/subicbay.hostelanddormitory.33" aria-label="Facebook" class="hover:text-[#DF5219]"><i class="fi fi-brands-facebook"></i></a>
        <a href="https://www.tiktok.com/@subicbayhostel" aria-label="TikTok" class="hover:text-[#DF5219]"><i class="fi fi-brands-tik-tok"></i></a>
        <a href="https://www.instagram.com/subicbayhostelanddormitory/" aria-label="Instagram" class="hover:text-[#DF5219]"><i class="fi fi-brands-instagram"></i></a>
      </div>
      <a href="book.php" class="inline-block bg-black text-white px-6 py-3 rounded-full font-semibold hover:scale-105 transition">Book Now</a>
    </div>
  </section>

</main>

<script>
  AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true
  });
</script>

<?php include('includes/footer.php'); ?>
