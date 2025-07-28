<?php

// Detect language from query
if (isset($_GET['lang'])) {
  $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'en';


// Translation array
$translations = [
  'en' => [
    'home' => 'Home',
    'about' => 'About Us',
    'rooms' => 'Rooms',
    'book' => 'Book',
    'contact' => 'Contact',
    'language' => 'Language',
    'book_now' => 'Book Now',

    // Hero
    'hero_line_1' => 'Your Capsule Partner',
    'hero_line_2' => 'for Comfort.',
    'hero_sub' => 'The first and only Japanese capsule hotel in Subic Bay!',

    // About
    'about_title' => 'Comfort Meets Affordability',
    'about_desc' => 'Whether you\'re training, working, or just visiting Subic, we’ve got your pod ready. Enjoy capsule-style living designed for peace, privacy, and your productivity.',

    // Reels
    'whats_happening' => 'What’s Happening?',
    'reel_1' => 'Your Capsule, Your Comfort — Waiting for You!',
    'reel_2' => 'Don’t let the rain ruin your day — chill at SBHD',
    'reel_3' => 'Live, Laugh, Lounge at Subic Bay Hostel',

    // Room Types
    'choose_capsule' => 'Choose Your <span class="text-[#F94144]">Capsule</span>',
    'standard_capsule' => 'Standard Capsule',
    'standard_desc' => 'Ideal for solo travelers who value privacy and peace.',
    'deluxe_capsule' => 'Deluxe Capsule',
    'deluxe_desc' => 'More space, a study desk, and soft lighting. Comfort upgraded.',
    'per_night' => 'night',

    // CTA
    'cta_title' => 'Reserve Your Capsule Now',
    'cta_desc' => 'Modern hostel living in the heart of Subic Bay.',
  ],

  'tl' => [
    'home' => 'Bahay',
    'about' => 'Tungkol sa Amin',
    'rooms' => 'Mga Kuwarto',
    'book' => 'Magpareserba',
    'contact' => 'Makipag-ugnayan',
    'language' => 'Wika',
    'book_now' => 'Magpareserba',

    // Hero
    'hero_line_1' => 'Ang Iyong Ka-Capsule',
    'hero_line_2' => 'para sa Ginhawa.',
    'hero_sub' => 'Manatiling tuyo at chill sa Subic Bay Hostel & Dormitory — kung saan nagtatagpo ang capsule comfort at affordability.',

    // About
    'about_title' => 'Ginhawa at Abot-Kayang Presyo',
    'about_desc' => 'Kung ikaw man ay nagsasanay, nagtatrabaho, o bumibisita sa Subic, handa na ang iyong pod. Tamasa ang capsule-style living para sa katahimikan at produktibidad.',

    // Reels
    'whats_happening' => 'Ano ang Nagaganap?',
    'reel_1' => 'Ang Iyong Capsule, Iyong Ginhawa — Naghihintay na!',
    'reel_2' => 'Huwag hayaang masira ng ulan ang iyong araw — chill sa SBHD',
    'reel_3' => 'Mamuhay, Tumawa, Magpahinga sa Subic Bay Hostel',

    // Room Types
    'choose_capsule' => 'Pumili ng Iyong <span class="text-[#F94144]">Capsule</span>',
    'standard_capsule' => 'Standard Capsule',
    'standard_desc' => 'Mainam para sa mga solo traveler na nais ng katahimikan.',
    'deluxe_capsule' => 'Deluxe Capsule',
    'deluxe_desc' => 'Mas malaking espasyo, may study desk at malambot na ilaw. Mas komportableng karanasan.',
    'per_night' => 'gabi',

    // CTA
    'cta_title' => 'Ipareserba na ang Iyong Capsule',
    'cta_desc' => 'Makabagong hostel living sa puso ng Subic Bay.',
  ],

  'ja' => [
    'home' => 'ホーム',
    'about' => '私たちについて',
    'rooms' => '部屋',
    'book' => '予約する',
    'contact' => 'お問い合わせ',
    'language' => '言語',
    'book_now' => '今すぐ予約',

    // Hero
    'hero_line_1' => 'あなたのカプセルパートナー',
    'hero_line_2' => '快適さのために。',
    'hero_sub' => '雨の日も安心！快適さと手頃さを兼ね備えたSubic Bay Hostel & Dormitoryへ。',

    // About
    'about_title' => '快適さと手頃な価格の融合',
    'about_desc' => 'トレーニング、仕事、観光など、どんな目的でもあなたのカプセルが待っています。静かで生産的な滞在をお届けします。',

    // Reels
    'whats_happening' => '最新情報',
    'reel_1' => 'あなたのカプセル、あなたの快適さ — すぐそこに！',
    'reel_2' => '雨の日も心配なし — SBHDでくつろごう',
    'reel_3' => '楽しく、リラックスして、Subic Bay Hostelへ',

    // Room Types
    'choose_capsule' => 'カプセルを<span class="text-[#F94144]">選ぶ</span>',
    'standard_capsule' => 'スタンダードカプセル',
    'standard_desc' => 'プライバシーと静けさを重視する一人旅に最適です。',
    'deluxe_capsule' => 'デラックスカプセル',
    'deluxe_desc' => '広めの空間、デスク、柔らかな照明で快適さアップ。',
    'per_night' => '泊',

    // CTA
    'cta_title' => '今すぐカプセルを予約しましょう',
    'cta_desc' => 'スービック湾の中心でのモダンなホステルライフ。',
  ],

  'zh' => [
    'home' => '主页',
    'about' => '关于我们',
    'rooms' => '房间',
    'book' => '预订',
    'contact' => '联系我们',
    'language' => '语言',
    'book_now' => '立即预订',

    // Hero
    'hero_line_1' => '您的胶囊伙伴',
    'hero_line_2' => '为舒适而生。',
    'hero_sub' => '在Subic Bay Hostel & Dormitory放松身心，享受经济实惠的胶囊舒适体验。',

    // About
    'about_title' => '舒适与实惠兼具',
    'about_desc' => '无论是培训、工作还是旅游，我们都为您准备好了舒适的胶囊。享受安静与私密并存的生活方式。',

    // Reels
    'whats_happening' => '正在发生什么？',
    'reel_1' => '您的胶囊，您的舒适 — 等您来享！',
    'reel_2' => '别让雨天坏了心情 — 来SBHD放松一下',
    'reel_3' => '生活、欢笑、休闲尽在Subic Bay Hostel',

    // Room Types
    'choose_capsule' => '选择您的<span class="text-[#F94144]">胶囊</span>',
    'standard_capsule' => '标准胶囊',
    'standard_desc' => '适合重视隐私与安静的单人旅客。',
    'deluxe_capsule' => '豪华胶囊',
    'deluxe_desc' => '更大空间，配有书桌与柔光照明，升级舒适体验。',
    'per_night' => '晚',

    // CTA
    'cta_title' => '立即预订您的胶囊',
    'cta_desc' => '苏比克湾中心的现代宿舍生活。',
  ]
];


$text = $translations[$lang] ?? $translations['en'];
