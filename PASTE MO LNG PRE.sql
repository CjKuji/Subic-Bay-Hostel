  -- DROP AND CREATE DATABASE
  DROP DATABASE IF EXISTS `subic_hostel_db`;
  CREATE DATABASE `subic_hostel_db`;
  USE `subic_hostel_db`;

  -- Disable foreign key checks for clean table drops
  SET foreign_key_checks = 0;

  -- DROP TABLES IF EXIST
  DROP TABLE IF EXISTS 
    `room_images`, `guest_lists`, `floors`, `contact_messages`,
    `bookings`, `rooms`, `room_types`, `admins`, `users`;

  -- Re-enable foreign key checks
  SET foreign_key_checks = 1;

  -- START TRANSACTION
  START TRANSACTION;
  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
  SET time_zone = "+00:00";
  /*!40101 SET NAMES utf8mb4 */;

  -- TABLE: admins
  CREATE TABLE `admin` (
    `id` int(11) NOT NULL,
    `username` varchar(255) DEFAULT NULL,
    `password` varchar(255) DEFAULT NULL,
    `profile_image` varchar(255) DEFAULT NULL,
    `email` varchar(150) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  INSERT INTO `admin` (`id`, `username`, `email`, `password`) VALUES
  (1, 'CjKuji', '', '$2y$10$Kg0iQ47EX19MNzTeRGM9rers8AFcuh6CuLS2VWCMmlWl.gbL0GxG6');

  -- TABLE: users
  CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `full_name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `gender` varchar(10) DEFAULT NULL,
    `password` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `gender`, `password`) VALUES
  (1, 'Carl James Bugero', 'cjbugero@gmail.com', '09911514767', 'male', NULL);

  -- TABLE: room_types
  CREATE TABLE `room_types` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(100) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `price` decimal(10,2) NOT NULL DEFAULT 0.00,
    `inclusions` text DEFAULT NULL,
    `also_available` text DEFAULT NULL,
    `capacity` varchar(50) DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  INSERT INTO `room_types` (`id`, `title`, `description`, `price`, `inclusions`, `also_available`, `capacity`, `image`, `created_at`) VALUES
  (1, 'Standard Room', 'Description for Standard Room', 600.00, 'Wi-Fi, Aircon, TV', 'Breakfast available', '2', 'standard-room.jpg', '2025-07-15 07:23:43'),
  (2, 'Deluxe Room', 'Description for Deluxe Room', 900.00, 'Wi-Fi, Aircon, TV, Mini Bar', 'Free parking', '2', 'deluxe-room.jpg', '2025-07-15 07:23:43');

  -- TABLE: floors
  CREATE TABLE `floors` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `floor_number` int(2) NOT NULL,
    `section_name` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `floor_section` (`floor_number`, `section_name`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  INSERT INTO `floors` (`id`, `floor_number`, `section_name`) VALUES
  (1, 2, 'A-2F-n'),
  (2, 2, 'A-2F-m'),
  (3, 2, 'A-2F-k'),
  (4, 2, 'A-2F-j'),
  (5, 2, 'A-2F-h'),
  (6, 2, 'A-2F-g'),
  (7, 2, 'A-2F-e'),
  (8, 2, 'A-2F-d'),
  (9, 2, 'A-2F-c'),
  (10, 2, 'A-2F-b'),
  (11, 2, 'A-2F-a'),
  (12, 3, 'A-3F-n'),
  (13, 3, 'A-3F-m'),
  (14, 3, 'A-3F-k'),
  (15, 3, 'A-3F-j'),
  (16, 3, 'A-3F-h'),
  (17, 3, 'A-3F-g'),
  (18, 3, 'A-3F-e'),
  (19, 3, 'A-3F-d'),
  (20, 3, 'A-3F-c'),
  (21, 3, 'A-3F-b'),
  (22, 3, 'A-3F-a');

  -- TABLE: rooms
  CREATE TABLE `rooms` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `room_number` varchar(10) NOT NULL,
    `floor_id` int(11) NOT NULL,
    `room_type_id` int(11) NOT NULL,
    `is_occupied` tinyint(1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `room_number` (`room_number`, `floor_id`),
    KEY `room_type_id` (`room_type_id`),
    KEY `floor_id` (`floor_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  -- TABLE: bookings
  CREATE TABLE `bookings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `room_type_id` int(11) NOT NULL,
    `check_in` date NOT NULL,
    `check_out` date NOT NULL,
    `No_of_guests` int(11) NOT NULL,
    `number_of_rooms` int(11) NOT NULL DEFAULT 1,
    `special_request` text DEFAULT NULL,
    `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
    `check_in_status` enum('pending','checked_in','checked_out') DEFAULT 'pending',
    `room_id` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `room_type_id` (`room_type_id`),
    KEY `room_id` (`room_id`),
    KEY `status` (`status`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  INSERT INTO `bookings` (`id`, `user_id`, `room_type_id`, `check_in`, `check_out`, `No_of_guests`, `number_of_rooms`, `special_request`, `status`, `check_in_status`, `room_id`, `created_at`, `deleted_at`) VALUES
  (1, 1, 2, '2025-07-25', '2025-07-26', 4, 4, '', 'pending', 'pending', NULL, '2025-07-25 07:39:19', NULL);

  -- TABLE: guest_lists
  CREATE TABLE `guest_lists` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `booking_id` int(11) NOT NULL,
    `guest_name` varchar(255) NOT NULL,
    `gender` enum('male','female') NOT NULL,
    `room_type_id` int(11) NOT NULL,
    `room_id` int(11) DEFAULT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `check_in` date DEFAULT NULL,
    `check_out` date DEFAULT NULL,
    `is_booker` TINYINT(1) DEFAULT 0,
    `check_in_status` enum('pending','checked_in','checked_out') DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`),
    KEY `room_type_id` (`room_type_id`),
    KEY `room_id` (`room_id`),
    KEY `check_in_status` (`check_in_status`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  INSERT INTO `guest_lists` (`id`, `booking_id`, `guest_name`, `gender`, `room_type_id`, `room_id`, `phone`, `email`, `check_in`, `check_out`, `is_booker`, `check_in_status`, `created_at`, `updated_at`) VALUES
  (1, 1, 'Carl James Bugero', 'male', 2, NULL, '09911514767', 'cjbugero@gmail.com', '2025-07-25', '2025-07-26', 1, 'pending', '2025-07-25 01:39:19', '2025-07-25 07:39:19'),
  (2, 1, 'John Paul Cabaltera', 'male', 1, NULL, NULL, NULL, '2025-07-25', '2025-07-26', 0, 'pending', '2025-07-25 01:39:19', '2025-07-25 07:39:19'),
  (3, 1, 'Zae', 'female', 2, NULL, NULL, NULL, '2025-07-25', '2025-07-26', 0, 'pending', '2025-07-25 01:39:19', '2025-07-25 07:39:19'),
  (4, 1, 'Jane', 'female', 1, NULL, NULL, NULL, '2025-07-25', '2025-07-26', 0, 'pending', '2025-07-25 01:39:19', '2025-07-25 07:39:19');

  -- TABLE: contact_messages
  CREATE TABLE `contact_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `contact` VARCHAR(30),
    `message` text NOT NULL,
    `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `read_at` DATETIME NULL,
    `responded_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `submitted_at` (`submitted_at`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  -- TABLE: room_images
  CREATE TABLE `room_images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `room_type_id` int(11) NOT NULL,
    `image_path` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `room_type_id` (`room_type_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  -- FOREIGN KEYS
  ALTER TABLE `bookings`
    ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL;

  ALTER TABLE `guest_lists`
    ADD CONSTRAINT `guest_lists_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
    ADD CONSTRAINT `guest_lists_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`),
    ADD CONSTRAINT `guest_lists_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL;

  ALTER TABLE `rooms`
    ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `rooms_ibfk_2` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE;

  ALTER TABLE `room_images`
    ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

  -- COMMIT CHANGES
  COMMIT;
