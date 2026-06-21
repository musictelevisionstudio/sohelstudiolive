
-- ১. অ্যাডমিন টেবিল
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `country_code` varchar(10) DEFAULT '+880',
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `account_status` enum('active','inactive') DEFAULT 'active',
  `otp_verification` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ২. চ্যানেল টেবিল (আপডেটেড)
CREATE TABLE IF NOT EXISTS `channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `channel_name` varchar(255) NOT NULL,
  `channel_url` mediumtext NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `ads_status` tinyint(1) DEFAULT 0,
  `channel_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `ticker_text` text, 
  `ticker_enabled` tinyint(1) DEFAULT 1,
  `ad_url` varchar(255) DEFAULT NULL,
  `ad_enabled` tinyint(1) DEFAULT 0,
  `live_text` varchar(50) DEFAULT 'LIVE',
  `ticker_speed` int(11) DEFAULT 50,
  `ad_duration` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ৩. ডিভাইস টেবিল
CREATE TABLE IF NOT EXISTS `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `device_id` varchar(255) NOT NULL UNIQUE,
  `status` tinyint(1) DEFAULT 0,
  `last_visit` timestamp NULL DEFAULT current_timestamp(),
  `name` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `addr` mediumtext DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ৪. সাবস্ক্রিপশন টেবিল
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `full_name` varchar(255) NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `district` varchar(100) NOT NULL,
  `package` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `sender_number` varchar(20) NOT NULL,
  `trx_id` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ৫. সাইট সেটিংস টেবিল
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `force_ad_status` tinyint(1) DEFAULT 0,
  `app_notice` text DEFAULT NULL,
  `admin_whatsapp` varchar(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ৬. গ্রিন এপিআই টেবিল
CREATE TABLE IF NOT EXISTS `green_api_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `api_url` varchar(255) DEFAULT NULL,
  `instance_id` varchar(100) DEFAULT NULL,
  `api_token` varchar(255) DEFAULT NULL,
  `country_id` int(11) DEFAULT 5,
  `admin_whatsapp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;