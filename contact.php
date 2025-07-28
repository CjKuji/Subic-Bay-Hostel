<?php
session_start();
include('includes/db.php'); // adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Honeypot check (basic spam prevention)
    if (!empty($_POST['website'])) {
        exit; // likely a bot
    }

    // Sanitize input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $message = trim($_POST['message']);

    // Simple validation
    if ($name && $email && $contact && $message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, contact, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $contact, $message);

        if ($stmt->execute()) {
            $stmt->close();
            $_SESSION['success'] = "Thank you! Your message has been sent.";
            header("Location: contact.php");
            exit;
        } else {
            $error = "Something went wrong. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<?php include('includes/header.php'); ?>

<!-- Google Fonts and AOS -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 1000, easing: 'ease-in-out' });
</script>

<link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css">
<style>body { font-family: 'Poppins', 'Noto Sans KR', sans-serif; }</style>

<!-- Hero Section -->
<section class="relative py-32 text-center bg-[url('assets/images/contact.jpg')] bg-center bg-cover bg-no-repeat" data-aos="fade-down">
  <div class="absolute inset-0 bg-black/50"></div>
  <div class="relative max-w-4xl mx-auto px-4">
    <h1 class="text-4xl md:text-5xl text-white mb-4">Contact Us</h1>
    <p class="text-lg text-gray-100 leading-relaxed italic">We're here to help — reach out anytime!</p>
  </div>
</section>

<!-- Contact Section -->
<section class="bg-[#F5F5F5] py-20 text-[#666666]">
  <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-start">

    <!-- Contact Info -->
    <div class="space-y-6" data-aos="fade-right" data-aos-delay="100">
      <h2 class="text-3xl font-semibold text-[#222222] mb-6">Get In Touch</h2>
      <div>
        <h4 class="text-lg font-semibold text-[#222222] mb-2">Address:</h4>
        <p>Subic Bay Hostel & Dormitory,<br> Subic Bay Freeport Zone, Philippines</p>
      </div>
      <div>
        <h4 class="text-lg font-semibold text-[#222222] mb-2">Phone:</h4>
        <p>📞 0999-996-6852 &nbsp;|&nbsp; 0915-535-9844</p>
      </div>
      <div>
        <h4 class="text-lg font-semibold text-[#222222] mb-2">Email:</h4>
        <p>info@subicbayhostel.com</p>
      </div>
      <div class="flex gap-6 text-2xl mt-4">
        <a href="https://www.facebook.com/subicbay.hostelanddormitory.33" target="_blank" class="hover:scale-125 transition-transform">
          <i class="fi fi-brands-facebook"></i>
        </a>
        <a href="https://www.tiktok.com/@subicbayhostel" target="_blank" class="hover:scale-125 transition-transform">
          <i class="fi fi-brands-tik-tok"></i>
        </a>
        <a href="https://www.instagram.com/subicbayhostelanddormitory/" target="_blank" class="hover:scale-125 transition-transform">
          <i class="fi fi-brands-instagram"></i>
        </a>
      </div>
    </div>

    <!-- Contact Form -->
    <div data-aos="fade-left" data-aos-delay="200">
      <?php if (!empty($_SESSION['success'])): ?>
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
          <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          <?= $error; ?>
        </div>
      <?php endif; ?>

      <form action="contact.php" method="POST" class="space-y-6 bg-white p-8 rounded-2xl shadow-md">
        <!-- Honeypot field (invisible to users) -->
        <input type="text" name="website" style="display:none">

        <div>
          <label class="block mb-1 font-semibold text-[#222222]">Full Name</label>
          <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#F94144]">
        </div>
        <div>
          <label class="block mb-1 font-semibold text-[#222222]">Email</label>
          <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#F94144]">
        </div>
        <div>
          <label class="block mb-1 font-semibold text-[#222222]">Contact Number</label>
          <input type="text" name="contact" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#F94144]">
        </div>
        <div>
          <label class="block mb-1 font-semibold text-[#222222]">Message</label>
          <textarea name="message" rows="4" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#F94144]"></textarea>
        </div>
        <button type="submit" class="w-full bg-[#F94144] text-white py-3 rounded-lg font-semibold hover:scale-110 transition-transform">
          Send Message
        </button>
      </form>
    </div>
  </div>
</section>

<!-- Map Section -->
<section class="bg-[#FFFCFB] text-[black] py-24" data-aos="fade-up">
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

<!-- Call to Action -->
<section class="py-16 bg-[#F5F5F5] text-center" data-aos="fade-up">
  <div class="max-w-xl mx-auto px-4">
    <h3 class="text-2xl md:text-3xl font-semibold mb-4">Ready to Book?</h3>
    <p class="mb-4">📞 0999-996-6852 &nbsp;|&nbsp; 0915-535-9844</p>
    <a href="book.php" class="inline-block text-white bg-[black] hover:bg-[black] hover:text-white px-4 py-2 rounded-full font-semibold transition-transform hover:scale-125">
      Book Now
    </a>
  </div>
</section>

<!-- Scripts -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 800, once: true });
</script>

<?php include('includes/footer.php'); ?>
