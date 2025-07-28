<?php
// Detect environment (from Render or fallback to development)
define('ENVIRONMENT', getenv('APP_ENV') ?: 'development');

// Configuration for different environments
$config = [
  'development' => [
    'db' => [
      'host' => 'localhost',
      'user' => 'root',
      'pass' => '',
      'name' => 'subic_hostel_db',
    ],
    'smtp' => [
      'host' => 'smtp.gmail.com',
      'port' => 587,
      'user' => 'penixkujs@gmail.com',
      'pass' => 'mjrw tuub khwg osdk',
      'from_email' => 'penixkujs@gmail.com',
      'from_name'  => 'SBHD Dev',
    ]
  ],
  'production' => [
    'db' => [
      'host' => 'your-prod-host',   // e.g. from Render’s PostgreSQL or MySQL
      'user' => 'your-prod-user',
      'pass' => 'your-prod-pass',
      'name' => 'your-prod-db',
    ],
    'smtp' => [
      'host' => 'smtp.gmail.com',
      'port' => 587,
      'user' => 'your-prod-email@gmail.com',
      'pass' => 'your-app-password',
      'from_email' => 'your-prod-email@gmail.com',
      'from_name'  => 'SBHD Production',
    ]
  ]
];

// Load current config
$current = $config[ENVIRONMENT];

// === Database Connection ===
$conn = new mysqli(
  $current['db']['host'],
  $current['db']['user'],
  $current['db']['pass'],
  $current['db']['name']
);

if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// === SMTP Config ===
if (!defined('SMTP_HOST')) define('SMTP_HOST', $current['smtp']['host']);
if (!defined('SMTP_PORT')) define('SMTP_PORT', $current['smtp']['port']);
if (!defined('SMTP_USER')) define('SMTP_USER', $current['smtp']['user']);
if (!defined('SMTP_PASS')) define('SMTP_PASS', $current['smtp']['pass']);
if (!defined('FROM_EMAIL')) define('FROM_EMAIL', $current['smtp']['from_email']);
if (!defined('FROM_NAME')) define('FROM_NAME', $current['smtp']['from_name']);
