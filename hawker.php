<?php include('includes/header.php'); ?>

<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<style>
  [x-cloak] { display: none !important; }
  .flip-card-inner {
    transition: transform 0.8s;
    transform-style: preserve-3d;
  }
  .flip-card.flipped .flip-card-inner {
    transform: rotateY(180deg);
  }
  .flip-card-front, .flip-card-back {
    backface-visibility: hidden;
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    border-radius: 0.75rem; /* rounded-xl */
    overflow: hidden;
  }
  .flip-card-back {
    transform: rotateY(180deg);
  }
</style>

<!-- Hero -->
<section class="bg-cover bg-center h-[60vh] flex items-center justify-center text-center text-white relative" style="background-image: url('assets/images/hawker/food.jpg');" data-aos="fade-in">
  <div class="absolute inset-0 bg-gradient-to-b from-black/50 to-black/50"></div>
  <div class="relative z-10 px-4">
    <h1 class="text-4xl md:text-6xl font-semibold mb-4"></h1>
    <p class="text-lg md:text-xl"></p>
  </div>
</section>

<!-- Filipino Dishes Section -->
<section class="py-20 bg-[#FFFCFB] text-[#333]">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-semibold mb-10 inline-block relative after:block after:w-16 after:h-1 after:bg-[#F94144] after:mt-2"
    data-aos="fade-up" 
    x-data="{
      title: 'Our Featured Filipino Dishes',
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
          setTimeout(() => this.typing(), 1000);  // Wait before starting to erase
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
  <span x-text="currentText"></span>
</h2>


    <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-6 gap-6" data-aos="fade-up" data-aos-delay="100">
      <?php
      $dishes = [
        ['adobo.png', 'Adobo'],
        ['adobo-squid.png', 'Adobong Pusit'],
        ['ampalaya.png', 'Ginisang Ampalaya'],
        ['bagoong-pork.png', 'Pork with Bagoong'],
        ['beef-steak.png', 'Beef Steak'],
        ['Chicken.png', 'Fried Chicken'],
        ['chopsuey.png', 'Chopsuey'],
        ['curry.png', 'Chicken Curry'],
        ['daing.png', 'Daing na Bangus'],
        ['dinuguan.png', 'Dinuguan'],
        ['galunggong.png', 'Galunggong'],
        ['laing.png', 'Laing'],
        ['le-paksiw.png', 'Lechon Paksiw'],
        ['lumpia.png', 'Lumpiang Shanghai'],
        ['menudo.png', 'Menudo'],
        ['munggo.png', 'Ginisang Munggo'],
        ['pakbet.png', 'Pinakbet'],
        ['sinigang-bangus.png', 'Sinigang na Bangus'],
        ['sinigang-pork.png', 'Sinigang na Baboy'],
        ['sisig.png', 'Pork Sisig'],
        ['tilapia.png', 'Fried Tilapia'],
        ['tinola.png', 'Tinola'],
        ['torta.png', 'Tortang Talong'],
      ];

      $simpleIngredients = [
        'Adobo' => 'Chicken, coconut vinegar, soy sauce, garlic, bay leaves, black pepper',
        'Adobong Pusit' => 'Baby squid, squid ink, garlic, cane vinegar, small chili peppers',
        'Ginisang Ampalaya' => 'Bitter melon, eggs, tomatoes, shallots, garlic olive oil',
        'Pork with Bagoong' => 'Pork, shrimp paste, brown sugar, lime zest',
        'Beef Steak' => 'Beef steak, calamansi, onions, black pepper',
        'Fried Chicken' => 'Chicken thigh, bread crumbs, paprika, fresh greens',
        'Chopsuey' => 'Mixed vegetables, mushrooms, sesame ginger sauce',
        'Chicken Curry' => 'Chicken, curry powder, coconut milk, lime leaves, coriander',
        'Daing na Bangus' => 'Milkfish, garlic vinegar, peppercorns, calamansi',
        'Dinuguan' => 'Pork, pork blood, vinegar, green chili, crispy garlic',
        'Galunggong' => 'Galunggong fish, sea salt, pepper, calamansi',
        'Laing' => 'Taro leaves, coconut milk, chili, smoked fish, ginger',
        'Lechon Paksiw' => 'Roast pork, liver sauce, vinegar, caramelized sugar, star anise',
        'Lumpiang Shanghai' => 'Ground pork & shrimp, onions, carrots, garlic, crispy wrap',
        'Menudo' => 'Pork, liver, tomatoes, potatoes, peppers, peas',
        'Ginisang Munggo' => 'Mung beans, pork bits, spinach, garlic chips',
        'Pinakbet' => 'Eggplant, bitter melon, okra, squash, shrimp paste, pork crackling',
        'Sinigang na Bangus' => 'Milkfish belly, tamarind broth, radish, water spinach, beans',
        'Sinigang na Baboy' => 'Pork ribs, tamarind, taro, water spinach, radish',
        'Pork Sisig' => 'Pork face, calamansi, onions, chili, egg yolk',
        'Fried Tilapia' => 'Tilapia, sea salt, lime, chili vinegar dip',
        'Tinola' => 'Chicken, green papaya, chili leaves, ginger, lemongrass',
        'Tortang Talong' => 'Grilled eggplant, eggs, garlic, onions, tomato salsa'
      ];

      foreach ($dishes as $dish) {
        $name = $dish[1];
        $ingredients = $simpleIngredients[$name] ?? 'Fresh local ingredients carefully selected by our chef';

        echo '
        <div x-data="{ flipped: false }" 
             @click="flipped = !flipped"
             :class="{ \'flipped\' : flipped }" 
             class="flip-card relative w-full h-0 pb-[100%] cursor-pointer shadow-xl group rounded-xl overflow-hidden">

          <div class="flip-card-inner absolute inset-0">

            <!-- Front -->
            <div class="flip-card-front bg-cover bg-center" style="background-image: url(\'assets/images/hawker/'.$dish[0].'\')">
              <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-500">
                <h3 class="text-white text-center text-lg font-semibold px-2">'.$name.'</h3>
              </div>
            </div>

            <!-- Back -->
            <div class="flip-card-back bg-[#222831] text-white flex items-center justify-center text-center p-4">
              <div>
                <h4 class="text-md font-semibold mb-2">Ingredients:</h4>
                <p class="text-sm">'.$ingredients.'</p>
              </div>
            </div>

          </div>
        </div>';
      }
      ?>
    </div>
  </div>
</section>

<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>

<!-- Map Section -->
<section class="bg-[#F5F5F5] text-[black] py-24" data-aos="fade-up">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <h2 class="text-4xl mb-4">Give Us a Visit</h2>
    <p class="text-lg">We’re located right in the heart of Olongapo — accessible, convenient, and close to everything.</p>
    <p class="text-xl mt-2">📍Block 8, Lot 2B, Waterfront Road, Subic Bay Freeport Zone, Olongapo City, 2200</p>
    <div class="relative mt-12 overflow-hidden shadow-xl transition-transform hover:scale-105 duration-500">
      <iframe class="w-full h-[22rem] md:h-[32rem] border-2 border-[#F94144]"
        src="https://maps.google.com/maps?q=Subic%20Bay%20Hostel&t=&z=15&ie=UTF8&iwloc=&output=embed"
        loading="lazy" allowfullscreen frameborder="0"></iframe>
    </div>
  </div>
</section>

<?php include('includes/footer.php'); ?>
