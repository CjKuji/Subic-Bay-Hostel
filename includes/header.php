<?php include('lang.php'); ?>

<!DOCTYPE html>
<html lang="<?= $lang; ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Subic Bay Hostel & Dormitory</title>

  <!-- Tailwind & Alpine -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>



  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />


  <style>
    body {
      font-family: 'Inter', sans-serif;
      opacity: 0;
      transition: opacity 0.2s ease-in-out;
    }

    body.loaded {
      opacity: 1;
    }

    [x-cloak] {
      display: none !important;
    }
  </style>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      document.body.classList.add("loaded");
    });
  </script>
</head>

<body class="bg-white text-gray-800">

  <!-- Navbar -->
  <header x-data="{ scrolled: false, open: false, activeLink: window.location.pathname }"
    @scroll.window="scrolled = window.scrollY > 10"
    :class="scrolled ? 'bg-white shadow-md py-3' : 'bg-transparent py-5'"
    class="fixed top-0 inset-x-0 z-50 transition-all duration-500 ease-in-out">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">

      <!-- Logo -->
      <a href="index.php" class="flex items-center gap-3 transition-transform duration-300"
        :class="scrolled ? 'scale-90' : 'scale-100'">
        <img src="assets/images/sbhd-logo.png" alt="Subic Bay Hostel & Dormitory Logo" class="h-12 w-auto">
      </a>

      <!-- Nav -->
      <nav class="relative" @click.outside="open = false">

        <!-- ✅ Replaced Mobile Dropdown Button -->
        <button @click="open = !open"
          id="dropdownDefaultButton"
          class="md:hidden text-white bg-[#F94144] hover:bg-red-600 focus:ring-1 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-1 text-center inline-flex items-center"
          type="button">
          Menu
          <svg class="w-2.5 h-2.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
          </svg>
        </button>

        <!-- Menu Items -->
        <ul :class="{ 'hidden': !open, 'block': open }"
          class="absolute md:static right-2 top-full mt-2 md:mt-0 
           w-fit md:w-auto
           bg-white text-black 
           md:bg-transparent md:text-gray-800 
           md:flex md:space-x-6 space-y-2 md:space-y-0 
           shadow-xl md:shadow-none 
           rounded-lg md:rounded-none 
           px-4 py-3 md:p-0 
           z-40 
           transition-all duration-500 ease-in-out transform-gpu
           md:transition-none text-base font-medium">


          <!-- Menu Links -->
          <?php
          $links = [
            'index.php' => $text['home'],
            'about.php' => $text['about'],
            'rooms.php' => $text['rooms'],
            'book.php' => $text['book'],
            'contact.php' => $text['contact']
          ];
          foreach ($links as $file => $label):
          ?>
            <li>
              <a href="<?= $file ?>"
                :class="{ 'text-[#F94144] border-b-2 border-[#F94144]': activeLink.includes('<?= pathinfo($file, PATHINFO_FILENAME); ?>') }"
                class="font-medium pb-0 block hover:text-[#F94144] transition-all duration-200">
                <?= $label; ?>
              </a>
            </li>
          <?php endforeach; ?>

          <!-- Normal nav item: Facilities -->
          <li>
            <a href="facilities.php"
              class="block font-medium pb-0 hover:text-[#F94144] transition-all"
              :class="{ 'text-[#F94144]': activeLink.includes('facilities') }">
              <?= $text['facilities'] ?? 'Facilities'; ?>
            </a>
          </li>

          <!-- Normal nav item: Food Court -->
          <li>
            <a href="hawker.php"
              class="block font-medium pb-0 hover:text-[#F94144] transition-all"
              :class="{ 'text-[#F94144]': activeLink.includes('food') }">
              <?= $text['foodcourt'] ?? 'Food Court'; ?>
            </a>
          </li>




          <!-- Language Dropdown -->
          <li x-data="{ openLang: false }" class="relative">
            <button @click="openLang = !openLang"
              class="flex items-center space-x-1 font-medium pb-0 hover:text-[#F94144] focus:outline-none">
              <span><?= $text['language']; ?></span>
              <svg class="w-4 h-4 mt-[2px] transition-transform duration-300"
                :class="{ 'rotate-180': openLang }"
                fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M5.23 7.21a.75.75 0 011.06.02L10 11.084l3.71-3.854a.75.75 0 111.08 1.04l-4.25 4.41a.75.75 0 01-1.08 0l-4.25-4.41a.75.75 0 01.02-1.06z"
                  clip-rule="evenodd" />
              </svg>
            </button>

            <ul x-show="openLang"
              x-transition
              @click.outside="openLang = false"
              class="absolute mt-0 left-0 w-40 bg-white border border-gray-200 shadow-xl rounded-md py-0 text-sm text-gray-700 z-50">
              <li><a href="?lang=en" data-lang="en" class="block px-2 py-2 hover:bg-gray-100">🇬🇧 English</a></li>
              <li><a href="?lang=tl" data-lang="tl" class="block px-2 py-2 hover:bg-gray-100">🇵🇭 Tagalog</a></li>
              <li><a href="?lang=ja" data-lang="ja" class="block px-2 py-2 hover:bg-gray-100">🇯🇵 Japanese</a></li>
              <li><a href="?lang=zh" data-lang="zh" class="block px-2 py-2 hover:bg-gray-100">🇨🇳 Chinese</a></li>
            </ul>
          </li>

      </nav>

      <!-- CTA Book Button -->
      <a href="book.php"
        class="hidden md:inline-block bg-[#F94144] text-white px-5 py-1 rounded-full text-sm font-semibold hover:bg-red-600 transition duration-300 hover:scale-105 ml-4">
        <?= $text['book']; ?>
      </a>
    </div>
  </header>

  <!-- Language Persistence -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const savedLang = localStorage.getItem('sbhd-lang');
      const currentLang = new URLSearchParams(window.location.search).get('lang');
      if (!currentLang && savedLang) {
        window.location.href = '?lang=' + savedLang;
      }
      document.querySelectorAll('[data-lang]').forEach(link => {
        link.addEventListener('click', () => {
          localStorage.setItem('sbhd-lang', link.dataset.lang);
        });
      });
    });
  </script>