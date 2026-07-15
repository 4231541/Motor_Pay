-- phpMyAdmin SQL Dump
-- Generation Time: 2026-07-16 00:56:46
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `sayara_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sayara_db`;

DROP TABLE IF EXISTS `favorites`, `notifications`, `users`, `brands`, `models`, `cars`, `offers`, `requests`;

CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_ar` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `name_ar` varchar(150) NOT NULL,
  `name_en` varchar(150) NOT NULL,
  `year` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `min_installment` decimal(10,2) NOT NULL,
  `images` text,
  `type_ar` varchar(50) DEFAULT NULL,
  `type_en` varchar(50) DEFAULT NULL,
  `grade_ar` varchar(50) DEFAULT NULL,
  `grade_en` varchar(50) DEFAULT NULL,
  `fuel_ar` varchar(50) DEFAULT NULL,
  `fuel_en` varchar(50) DEFAULT NULL,
  `transmission_ar` varchar(50) DEFAULT NULL,
  `transmission_en` varchar(50) DEFAULT NULL,
  `drive_ar` varchar(50) DEFAULT NULL,
  `drive_en` varchar(50) DEFAULT NULL,
  `color_ar` varchar(50) DEFAULT NULL,
  `color_en` varchar(50) DEFAULT NULL,
  `color_inner_ar` varchar(50) DEFAULT NULL,
  `color_inner_en` varchar(50) DEFAULT NULL,
  `engine_size` varchar(50) DEFAULT NULL,
  `seats` int(11) DEFAULT 5,
  `doors` int(11) DEFAULT 4,
  `specs_safety` text,
  `specs_comfort` text,
  `specs_tech` text,
  `specs_exterior` text,
  `is_available` tinyint(1) DEFAULT 1,
  `views` int(11) DEFAULT 0,
  `orders_count` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_ar` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `description_ar` text NOT NULL,
  `description_en` text NOT NULL,
  `discount_pct` decimal(5,2) DEFAULT 0,
  `car_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `notes` text,
  `national_id` varchar(50) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `employer` varchar(150) DEFAULT NULL,
  `work_duration` int(11) DEFAULT NULL,
  `downpayment` decimal(10,2) DEFAULT NULL,
  `term_months` int(11) DEFAULT NULL,
  `monthly_installment` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'received',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title_ar` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `message_ar` text NOT NULL,
  `message_en` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `city`, `password`, `role`, `created_at`) VALUES ('1', 'مشرف النظام', 'admin@syarah.com', '0500000000', 'الرياض', '$2y$10$E8eC6RMQGyi82leeGetKRey1hkKIwdEJsu9Ym.bDdp1BaPSQCdmhC', 'admin', '2026-07-14 00:34:30');
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `city`, `password`, `role`, `created_at`) VALUES ('2', 'أحمد العتيبي', 'user@syarah.com', '0512345678', 'جدة', '$2y$10$E8eC6RMQGyi82leeGetKRey1hkKIwdEJsu9Ym.bDdp1BaPSQCdmhC', 'user', '2026-07-14 00:34:30');

-- Dumping data for table `brands`
INSERT INTO `brands` (`id`, `name_ar`, `name_en`, `logo`) VALUES ('1', 'تويوتا', 'Toyota', 'toyota.svg');
INSERT INTO `brands` (`id`, `name_ar`, `name_en`, `logo`) VALUES ('2', 'هيونداي', 'Hyundai', 'hyundai.svg');
INSERT INTO `brands` (`id`, `name_ar`, `name_en`, `logo`) VALUES ('3', 'كيا', 'Kia', 'kia.svg');
INSERT INTO `brands` (`id`, `name_ar`, `name_en`, `logo`) VALUES ('4', 'نيسان', 'Nissan', 'nissan.svg');
INSERT INTO `brands` (`id`, `name_ar`, `name_en`, `logo`) VALUES ('5', 'بي إم دبليو', 'BMW', 'bmw.svg');
INSERT INTO `brands` (`id`, `name_ar`, `name_en`, `logo`) VALUES ('6', 'مرسيدس بنز', 'Mercedes-Benz', 'mercedes.svg');
INSERT INTO `brands` (`id`, `name_ar`, `name_en`, `logo`) VALUES ('7', 'أودي', 'Audi', 'audi.svg');
INSERT INTO `brands` (`id`, `name_ar`, `name_en`, `logo`) VALUES ('8', 'هوندا', 'Honda', 'honda.svg');

-- Dumping data for table `models`
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('1', '1', 'كامري', 'Camry');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('2', '1', 'لاند كروزر', 'Land Cruiser');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('3', '1', 'كورولا', 'Corolla');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('4', '2', 'توسان', 'Tucson');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('5', '2', 'إلنترا', 'Elantra');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('6', '2', 'سوناتا', 'Sonata');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('7', '3', 'K5', 'K5');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('8', '3', 'سبورتج', 'Sportage');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('9', '3', 'سورينتو', 'Sorento');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('10', '4', 'باترول', 'Patrol');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('11', '4', 'ألتيما', 'Altima');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('12', '4', 'اكس تريل', 'X-Trail');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('13', '5', 'الفئة الخامسة', '5 Series');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('14', '5', 'X5', 'X5');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('15', '6', 'الفئة C', 'C-Class');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('16', '6', 'الفئة S', 'S-Class');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('17', '7', 'Q7', 'Q7');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('18', '7', 'A6', 'A6');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('19', '8', 'أكورد', 'Accord');
INSERT INTO `models` (`id`, `brand_id`, `name_ar`, `name_en`) VALUES ('20', '8', 'سيفيك', 'Civic');

-- Dumping data for table `cars`
INSERT INTO `cars` (`id`, `brand_id`, `model_id`, `name_ar`, `name_en`, `year`, `price`, `min_installment`, `images`, `type_ar`, `type_en`, `grade_ar`, `grade_en`, `fuel_ar`, `fuel_en`, `transmission_ar`, `transmission_en`, `drive_ar`, `drive_en`, `color_ar`, `color_en`, `color_inner_ar`, `color_inner_en`, `engine_size`, `seats`, `doors`, `specs_safety`, `specs_comfort`, `specs_tech`, `specs_exterior`, `is_available`, `views`, `orders_count`, `created_at`) VALUES ('1', '1', '1', 'تويوتا كامري GLE 2026', 'Toyota Camry GLE 2026', '2026', '112000', '1650', '[\"camry_1.jpg\",\"camry_2.jpg\",\"camry_3.jpg\"]', 'سيدان', 'Sedan', 'فل كامل GLE', 'Full GLE', 'بنزين', 'Petrol', 'أوتوماتيك', 'Automatic', 'دفع أمامي', 'FWD', 'أبيض لؤلؤي', 'Pearl White', 'بيج جلد', 'Beige Leather', '2.5L', '5', '4', '[\"ABS\",\"ESP\",\"\\u0648\\u0633\\u0627\\u0626\\u062f \\u0647\\u0648\\u0627\\u0626\\u064a\\u0629 \\u0623\\u0645\\u0627\\u0645\\u064a\\u0629 \\u0648\\u062c\\u0627\\u0646\\u0628\\u064a\\u0629\",\"\\u0643\\u0627\\u0645\\u064a\\u0631\\u0627 \\u062e\\u0644\\u0641\\u064a\\u0629\",\"\\u062d\\u0633\\u0627\\u0633\\u0627\\u062a \\u0623\\u0645\\u0627\\u0645\\u064a\\u0629 \\u0648\\u062e\\u0644\\u0641\\u064a\\u0629\",\"\\u0645\\u062b\\u0628\\u062a \\u0633\\u0631\\u0639\\u0629 \\u0645\\u062a\\u0643\\u064a\\u0641\",\"\\u0645\\u0631\\u0627\\u0642\\u0628\\u0629 \\u0636\\u063a\\u0637 \\u0627\\u0644\\u0625\\u0637\\u0627\\u0631\\u0627\\u062a\",\"\\u0627\\u0644\\u0645\\u062d\\u0627\\u0641\\u0638\\u0629 \\u0639\\u0644\\u0649 \\u0627\\u0644\\u0645\\u0633\\u0627\\u0631\"]', '[\"\\u062f\\u062e\\u0648\\u0644 \\u0630\\u0643\\u064a\",\"\\u062a\\u0634\\u063a\\u064a\\u0644 \\u0628\\u0635\\u0645\\u0629\",\"\\u0645\\u0642\\u0627\\u0639\\u062f \\u062c\\u0644\\u062f\",\"\\u0645\\u0642\\u0627\\u0639\\u062f \\u0643\\u0647\\u0631\\u0628\\u0627\\u0626\\u064a\\u0629\",\"\\u0645\\u0643\\u064a\\u0641 \\u0623\\u0648\\u062a\\u0648\\u0645\\u0627\\u062a\\u064a\\u0643 \\u062b\\u0646\\u0627\\u0626\\u064a \\u0627\\u0644\\u0645\\u0646\\u0627\\u0637\\u0642\",\"\\u0641\\u062a\\u062d\\u0629 \\u0633\\u0642\\u0641\"]', '[\"\\u0634\\u0627\\u0634\\u0629 \\u0644\\u0645\\u0633 9 \\u0628\\u0648\\u0635\\u0629\",\"Apple CarPlay\",\"Android Auto\",\"\\u0628\\u0644\\u0648\\u062a\\u0648\\u062b\",\"\\u0634\\u0627\\u062d\\u0646 \\u0644\\u0627\\u0633\\u0644\\u0643\\u064a\",\"\\u0646\\u0638\\u0627\\u0645 \\u0635\\u0648\\u062a\\u064a JBL 9 \\u0633\\u0645\\u0627\\u0639\\u0627\\u062a\"]', '[\"\\u062c\\u0646\\u0648\\u0637 \\u0623\\u0644\\u0645\\u0646\\u064a\\u0648\\u0645 18 \\u0628\\u0648\\u0635\\u0629\",\"\\u0645\\u0635\\u0627\\u0628\\u064a\\u062d LED\",\"\\u0625\\u0636\\u0627\\u0621\\u0629 \\u0646\\u0647\\u0627\\u0631\\u064a\\u0629 LED\",\"\\u0645\\u0631\\u0627\\u064a\\u0627 \\u0643\\u0647\\u0631\\u0628\\u0627\\u0626\\u064a\\u0629 \\u0642\\u0627\\u0628\\u0644\\u0629 \\u0644\\u0644\\u0637\\u064a\"]', '1', '9', '0', '2026-07-14 00:34:30');
INSERT INTO `cars` (`id`, `brand_id`, `model_id`, `name_ar`, `name_en`, `year`, `price`, `min_installment`, `images`, `type_ar`, `type_en`, `grade_ar`, `grade_en`, `fuel_ar`, `fuel_en`, `transmission_ar`, `transmission_en`, `drive_ar`, `drive_en`, `color_ar`, `color_en`, `color_inner_ar`, `color_inner_en`, `engine_size`, `seats`, `doors`, `specs_safety`, `specs_comfort`, `specs_tech`, `specs_exterior`, `is_available`, `views`, `orders_count`, `created_at`) VALUES ('2', '2', '4', 'هيونداي توسان سمارت 2026', 'Hyundai Tucson Smart 2026', '2026', '104000', '1490', '[\"tucson_1.jpg\",\"tucson_2.jpg\"]', 'عائلية / SUV', 'SUV', 'نصف فل سمارت', 'Smart Mid', 'بنزين', 'Petrol', 'أوتوماتيك', 'Automatic', 'دفع رباعي مستمر', 'AWD', 'رمادي معدني', 'Metallic Gray', 'مخمل رمادي', 'Gray Cloth', '2.0L', '5', '5', '[\"ABS\",\"ESP\",\"\\u0648\\u0633\\u0627\\u0626\\u062f \\u0647\\u0648\\u0627\\u0626\\u064a\\u0629 \\u0623\\u0645\\u0627\\u0645\\u064a\\u0629\",\"\\u0643\\u0627\\u0645\\u064a\\u0631\\u0627 \\u062e\\u0644\\u0641\\u064a\\u0629\",\"\\u062d\\u0633\\u0627\\u0633\\u0627\\u062a \\u062e\\u0644\\u0641\\u064a\\u0629\",\"\\u0645\\u062b\\u0628\\u062a \\u0633\\u0631\\u0639\\u0629\"]', '[\"\\u062f\\u062e\\u0648\\u0644 \\u0630\\u0643\\u064a\",\"\\u062a\\u0634\\u063a\\u064a\\u0644 \\u0628\\u0635\\u0645\\u0629\",\"\\u0645\\u0643\\u064a\\u0641 \\u062e\\u0644\\u0641\\u064a\",\"\\u0641\\u062a\\u062d\\u0629 \\u0633\\u0642\\u0641 \\u0628\\u0627\\u0646\\u0648\\u0631\\u0627\\u0645\\u0627\"]', '[\"\\u0634\\u0627\\u0634\\u0629 \\u0644\\u0645\\u0633 8 \\u0628\\u0648\\u0635\\u0629\",\"Apple CarPlay\",\"Android Auto\",\"\\u0628\\u0644\\u0648\\u062a\\u0648\\u062b\",\"USB\"]', '[\"\\u062c\\u0646\\u0648\\u0637 17 \\u0628\\u0648\\u0635\\u0629\",\"\\u0645\\u0635\\u0627\\u0628\\u064a\\u062d LED \\u0623\\u0645\\u0627\\u0645\\u064a\\u0629\",\"\\u0625\\u0636\\u0627\\u0621\\u0629 \\u0646\\u0647\\u0627\\u0631\\u064a\\u0629\",\"\\u0645\\u0631\\u0627\\u064a\\u0627 \\u0643\\u0647\\u0631\\u0628\\u0627\\u0626\\u064a\\u0629\"]', '1', '2', '0', '2026-07-14 00:34:30');
INSERT INTO `cars` (`id`, `brand_id`, `model_id`, `name_ar`, `name_en`, `year`, `price`, `min_installment`, `images`, `type_ar`, `type_en`, `grade_ar`, `grade_en`, `fuel_ar`, `fuel_en`, `transmission_ar`, `transmission_en`, `drive_ar`, `drive_en`, `color_ar`, `color_en`, `color_inner_ar`, `color_inner_en`, `engine_size`, `seats`, `doors`, `specs_safety`, `specs_comfort`, `specs_tech`, `specs_exterior`, `is_available`, `views`, `orders_count`, `created_at`) VALUES ('3', '3', '7', 'كيا K5 LX 2026', 'Kia K5 LX 2026', '2026', '96000', '1380', '[\"k5_1.jpg\",\"k5_2.jpg\"]', 'سيدان', 'Sedan', 'ستاندرد LX', 'Standard LX', 'بنزين', 'Petrol', 'أوتوماتيك', 'Automatic', 'دفع أمامي', 'FWD', 'فضي', 'Silver', 'أسود مخمل', 'Black Cloth', '2.0L', '5', '4', '[\"ABS\",\"ESP\",\"\\u0648\\u0633\\u0627\\u0626\\u062f \\u0647\\u0648\\u0627\\u0626\\u064a\\u0629 \\u0623\\u0645\\u0627\\u0645\\u064a\\u0629\",\"\\u062d\\u0633\\u0627\\u0633\\u0627\\u062a \\u062e\\u0644\\u0641\\u064a\\u0629\",\"\\u0643\\u0627\\u0645\\u064a\\u0631\\u0627 \\u062e\\u0644\\u0641\\u064a\\u0629\",\"\\u0645\\u0627\\u0646\\u0639 \\u062a\\u0634\\u063a\\u064a\\u0644 \\u0636\\u062f \\u0627\\u0644\\u0633\\u0631\\u0642\\u0629\"]', '[\"\\u0645\\u0643\\u064a\\u0641 \\u0623\\u0648\\u062a\\u0648\\u0645\\u0627\\u062a\\u064a\\u0643\",\"\\u0645\\u0631\\u0627\\u064a\\u0627 \\u0643\\u0647\\u0631\\u0628\\u0627\\u0626\\u064a\\u0629\",\"\\u0645\\u062b\\u0628\\u062a \\u0633\\u0631\\u0639\\u0629\"]', '[\"\\u0634\\u0627\\u0634\\u0629 \\u0644\\u0645\\u0633 8 \\u0628\\u0648\\u0635\\u0629\",\"Apple CarPlay\",\"Android Auto\",\"\\u0628\\u0644\\u0648\\u062a\\u0648\\u062b\"]', '[\"\\u062c\\u0646\\u0648\\u0637 \\u0623\\u0644\\u0645\\u0646\\u064a\\u0648\\u0645 17 \\u0628\\u0648\\u0635\\u0629\",\"\\u0625\\u0636\\u0627\\u0621\\u0629 \\u0646\\u0647\\u0627\\u0631\\u064a\\u0629 LED\"]', '1', '0', '0', '2026-07-14 00:34:30');
INSERT INTO `cars` (`id`, `brand_id`, `model_id`, `name_ar`, `name_en`, `year`, `price`, `min_installment`, `images`, `type_ar`, `type_en`, `grade_ar`, `grade_en`, `fuel_ar`, `fuel_en`, `transmission_ar`, `transmission_en`, `drive_ar`, `drive_en`, `color_ar`, `color_en`, `color_inner_ar`, `color_inner_en`, `engine_size`, `seats`, `doors`, `specs_safety`, `specs_comfort`, `specs_tech`, `specs_exterior`, `is_available`, `views`, `orders_count`, `created_at`) VALUES ('4', '4', '10', 'نيسان باترول بلاتينيوم 2026', 'Nissan Patrol Platinum 2026', '2026', '285000', '3950', '[\"patrol_1.jpg\",\"patrol_2.jpg\",\"patrol_3.jpg\"]', 'عائلية / SUV', 'SUV', 'بلاتينيوم فل كامل', 'Platinum Full Option', 'بنزين', 'Petrol', 'أوتوماتيك', 'Automatic', 'دفع رباعي 4x4', '4WD', 'أسود ملكي', 'Royal Black', 'جلد جملي ذو جودة عالية', 'Tan Premium Leather', '4.0L V6', '8', '5', '[\"ABS\",\"ESP\",\"\\u0648\\u0633\\u0627\\u0626\\u062f \\u0647\\u0648\\u0627\\u0626\\u064a\\u0629 \\u0623\\u0645\\u0627\\u0645\\u064a\\u0629 \\u0648\\u062c\\u0627\\u0646\\u0628\\u064a\\u0629 \\u0648\\u0633\\u062a\\u0627\\u0626\\u0631\\u064a\\u0629\",\"\\u0643\\u0627\\u0645\\u064a\\u0631\\u0627 360 \\u062f\\u0631\\u062c\\u0629\",\"\\u062d\\u0633\\u0627\\u0633\\u0627\\u062a \\u0623\\u0645\\u0627\\u0645\\u064a\\u0629 \\u0648\\u062e\\u0644\\u0641\\u064a\\u0629\",\"\\u0645\\u062b\\u0628\\u062a \\u0633\\u0631\\u0639\\u0629 \\u0631\\u0627\\u062f\\u0627\\u0631\\u064a\",\"\\u0645\\u0631\\u0627\\u0642\\u0628\\u0629 \\u0636\\u063a\\u0637 \\u0627\\u0644\\u0625\\u0637\\u0627\\u0631\\u0627\\u062a\",\"\\u0627\\u0644\\u0645\\u062d\\u0627\\u0641\\u0638\\u0629 \\u0639\\u0644\\u0649 \\u0627\\u0644\\u0645\\u0633\\u0627\\u0631\",\"\\u0631\\u0627\\u062f\\u0627\\u0631 \\u0645\\u0646\\u0639 \\u0627\\u0644\\u062a\\u0635\\u0627\\u062f\\u0645\"]', '[\"\\u062f\\u062e\\u0648\\u0644 \\u0630\\u0643\\u064a\",\"\\u062a\\u0634\\u063a\\u064a\\u0644 \\u0628\\u0635\\u0645\\u0629\",\"\\u0645\\u0642\\u0627\\u0639\\u062f \\u062c\\u0644\\u062f \\u0641\\u0627\\u062e\\u0631\\u0629\",\"\\u062a\\u0628\\u0631\\u064a\\u062f \\u0648\\u062a\\u062f\\u0641\\u0626\\u0629 \\u0627\\u0644\\u0645\\u0642\\u0627\\u0639\\u062f\",\"\\u0645\\u0643\\u064a\\u0641 \\u0623\\u0648\\u062a\\u0648\\u0645\\u0627\\u062a\\u064a\\u0643 \\u0645\\u062a\\u0639\\u062f\\u062f \\u0627\\u0644\\u0645\\u0646\\u0627\\u0637\\u0642\",\"\\u0641\\u062a\\u062d\\u0629 \\u0633\\u0642\\u0641 \\u0628\\u0627\\u0646\\u0648\\u0631\\u0627\\u0645\\u0627\",\"\\u0645\\u0642\\u0627\\u0639\\u062f \\u0643\\u0647\\u0631\\u0628\\u0627\\u0626\\u064a\\u0629 \\u0645\\u0639 \\u0630\\u0627\\u0643\\u0631\\u0629\"]', '[\"\\u0634\\u0627\\u0634\\u0629 \\u0644\\u0645\\u0633 12.3 \\u0628\\u0648\\u0635\\u0629\",\"\\u0634\\u0627\\u0634\\u0629 \\u0639\\u062f\\u0627\\u062f\\u0627\\u062a \\u0631\\u0642\\u0645\\u064a\\u0629\",\"Apple CarPlay \\u0644\\u0627\\u0633\\u0644\\u0643\\u064a\",\"Android Auto\",\"\\u0628\\u0644\\u0648\\u062a\\u0648\\u062b\",\"\\u0646\\u0638\\u0627\\u0645 \\u0645\\u0644\\u0627\\u062d\\u0629 GPS\",\"\\u0646\\u0638\\u0627\\u0645 \\u0635\\u0648\\u062a\\u064a BOSE 13 \\u0633\\u0645\\u0627\\u0639\\u0629\"]', '[\"\\u062c\\u0646\\u0648\\u0637 \\u0623\\u0644\\u0645\\u0646\\u064a\\u0648\\u0645 20 \\u0628\\u0648\\u0635\\u0629\",\"\\u0645\\u0635\\u0627\\u0628\\u064a\\u062d LED \\u0645\\u062a\\u0643\\u064a\\u0641\\u0629\",\"\\u0645\\u0631\\u0627\\u064a\\u0627 \\u0642\\u0627\\u0628\\u0644\\u0629 \\u0644\\u0644\\u0637\\u064a \\u0643\\u0647\\u0631\\u0628\\u0627\\u0626\\u064a\\u0627\\u064b \\u0645\\u0639 \\u0630\\u0627\\u0643\\u0631\\u0629\",\"\\u0639\\u062a\\u0628\\u0627\\u062a \\u062c\\u0627\\u0646\\u064a\\u0629 \\u0645\\u0636\\u064a\\u0626\\u0629\",\"\\u0628\\u0627\\u0628 \\u0634\\u0646\\u0637\\u0629 \\u0643\\u0647\\u0631\\u0628\\u0627\\u0626\\u064a\"]', '1', '1', '0', '2026-07-14 00:34:30');
INSERT INTO `cars` (`id`, `brand_id`, `model_id`, `name_ar`, `name_en`, `year`, `price`, `min_installment`, `images`, `type_ar`, `type_en`, `grade_ar`, `grade_en`, `fuel_ar`, `fuel_en`, `transmission_ar`, `transmission_en`, `drive_ar`, `drive_en`, `color_ar`, `color_en`, `color_inner_ar`, `color_inner_en`, `engine_size`, `seats`, `doors`, `specs_safety`, `specs_comfort`, `specs_tech`, `specs_exterior`, `is_available`, `views`, `orders_count`, `created_at`) VALUES ('5', '5', '13', 'بي إم دبليو الفئة الخامسة 520i 2026', 'BMW 5 Series 520i 2026', '2026', '310000', '4300', '[\"bmw5_1.jpg\",\"bmw5_2.jpg\"]', 'سيدان فخم', 'Luxury Sedan', 'M Sport', 'M Sport Package', 'هايبرد (بنزين/كهرباء)', 'Hybrid', 'أوتوماتيك', 'Automatic', 'دفع خلفي', 'RWD', 'أزرق داكن', 'Dark Blue', 'جلد كونياك بني', 'Cognac Brown Leather', '2.0L Turbo', '5', '4', '[\"ABS\",\"ESP\",\"\\u0648\\u0633\\u0627\\u0626\\u062f \\u0647\\u0648\\u0627\\u0626\\u064a\\u0629 \\u0645\\u062a\\u0643\\u0627\\u0645\\u0644\\u0629\",\"\\u0645\\u0633\\u0627\\u0639\\u062f \\u0627\\u0644\\u0642\\u064a\\u0627\\u062f\\u0629 \\u0627\\u0644\\u0627\\u062d\\u062a\\u0631\\u0627\\u0641\\u064a\",\"\\u0643\\u0627\\u0645\\u064a\\u0631\\u0627 360 \\u062f\\u0631\\u062c\\u0629\",\"\\u062d\\u0633\\u0627\\u0633\\u0627\\u062a \\u0645\\u062d\\u064a\\u0637\\u064a\\u0629\",\"\\u0627\\u0644\\u0645\\u062d\\u0627\\u0641\\u0638\\u0629 \\u0639\\u0644\\u0649 \\u0627\\u0644\\u0645\\u0633\\u0627\\u0631 \\u0627\\u0644\\u0646\\u0634\\u0637\",\"\\u0646\\u0638\\u0627\\u0645 \\u0627\\u0644\\u062d\\u0645\\u0627\\u064a\\u0629 \\u0627\\u0644\\u0648\\u0642\\u0627\\u0626\\u064a\\u0629\"]', '[\"\\u062f\\u062e\\u0648\\u0644 \\u0630\\u0643\\u064a \\u0645\\u0631\\u064a\\u062d\",\"\\u062a\\u0634\\u063a\\u064a\\u0644 \\u0628\\u0635\\u0645\\u0629\\/\\u0645\\u0641\\u062a\\u0627\\u062d \\u0631\\u0642\\u0645\\u064a\",\"\\u0645\\u0642\\u0627\\u0639\\u062f \\u0631\\u064a\\u0627\\u0636\\u064a\\u0629 \\u0643\\u0647\\u0631\\u0628\\u0627\\u0626\\u064a\\u0629 \\u0645\\u0639 \\u062a\\u062f\\u0641\\u0626\\u0629\",\"\\u0625\\u0646\\u0627\\u0631\\u0629 \\u062f\\u0627\\u062e\\u0644\\u064a\\u0629 \\u062a\\u0641\\u0627\\u0639\\u0644\\u064a\\u0629\",\"\\u0633\\u0642\\u0641 \\u0628\\u0627\\u0646\\u0648\\u0631\\u0627\\u0645\\u064a\"]', '[\"\\u0634\\u0627\\u0634\\u0629 \\u0645\\u0646\\u062d\\u0646\\u064a\\u0629 BMW Curved Display\",\"\\u0646\\u0638\\u0627\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0627\\u062d\\u0629 \\u0627\\u0644\\u0645\\u062a\\u0642\\u062f\\u0645\",\"Apple CarPlay \\/ Android Auto\",\"\\u0634\\u0627\\u062d\\u0646 \\u0644\\u0627\\u0633\\u0644\\u0643\\u064a \\u0648\\u0633\\u0631\\u064a\\u0639\",\"\\u0634\\u0627\\u0634\\u0629 \\u0639\\u0631\\u0636 \\u0639\\u0644\\u0649 \\u0627\\u0644\\u0632\\u062c\\u0627\\u062c (HUD)\",\"\\u0646\\u0638\\u0627\\u0645 \\u0635\\u0648\\u062a\\u064a Harman Kardon\"]', '[\"\\u062c\\u0646\\u0648\\u0637 M \\u0645\\u0642\\u0627\\u0633 19 \\u0628\\u0648\\u0635\\u0629\",\"\\u062d\\u0632\\u0645\\u0629 M \\u0627\\u0644\\u0631\\u064a\\u0627\\u0636\\u064a\\u0629 \\u0627\\u0644\\u062e\\u0627\\u0631\\u062c\\u064a\\u0629\",\"\\u0625\\u0636\\u0627\\u0621\\u0629 \\u062a\\u0631\\u062d\\u064a\\u0628\\u064a\\u0629 \\u062e\\u0627\\u0631\\u062c\\u064a\\u0629 LED\"]', '1', '0', '0', '2026-07-14 00:34:30');
INSERT INTO `cars` (`id`, `brand_id`, `model_id`, `name_ar`, `name_en`, `year`, `price`, `min_installment`, `images`, `type_ar`, `type_en`, `grade_ar`, `grade_en`, `fuel_ar`, `fuel_en`, `transmission_ar`, `transmission_en`, `drive_ar`, `drive_en`, `color_ar`, `color_en`, `color_inner_ar`, `color_inner_en`, `engine_size`, `seats`, `doors`, `specs_safety`, `specs_comfort`, `specs_tech`, `specs_exterior`, `is_available`, `views`, `orders_count`, `created_at`) VALUES ('6', '6', '15', 'مرسيدس C200 2026', 'Mercedes C200 2026', '2026', '275000', '3800', '[\"c200_1.jpg\",\"c200_2.jpg\"]', 'سيدان فخم', 'Luxury Sedan', 'AMG Line', 'AMG Line Package', 'بنزين', 'Petrol', 'أوتوماتيك 9 سرعات', '9-Speed Automatic', 'دفع خلفي', 'RWD', 'رمادي سيلينيت', 'Selenite Gray', 'جلد أحمر مع أسود الماني', 'Red/Black Nappa Leather', '1.5L Turbo EQ Boost', '5', '4', '[\"ABS\",\"ESP\",\"\\u0648\\u0633\\u0627\\u0626\\u062f \\u0647\\u0648\\u0627\\u0626\\u064a\\u0629 \\u0645\\u062d\\u064a\\u0637\\u0629\",\"\\u0641\\u0631\\u0645\\u0644\\u0629 \\u0627\\u0644\\u0637\\u0648\\u0627\\u0631\\u0626 \\u0627\\u0644\\u0646\\u0634\\u0637\\u0629\",\"\\u0645\\u0633\\u0627\\u0639\\u062f \\u0627\\u0644\\u0646\\u0642\\u0637\\u0629 \\u0627\\u0644\\u0639\\u0645\\u064a\\u0627\\u0621\",\"\\u0645\\u062b\\u0628\\u062a \\u0633\\u0631\\u0639\\u0629 \\u062a\\u0641\\u0627\\u0639\\u0644\\u064a DISTRONIC\",\"\\u0643\\u0627\\u0645\\u064a\\u0631\\u0627 \\u062e\\u0644\\u0641\\u064a\\u0629 \\u0639\\u0627\\u0644\\u064a\\u0629 \\u0627\\u0644\\u062f\\u0642\\u0629\"]', '[\"\\u0645\\u0641\\u062a\\u0627\\u062d \\u0630\\u0643\\u064a KEYLESS-GO\",\"\\u062a\\u0634\\u063a\\u064a\\u0644 \\u0628\\u0635\\u0645\\u0629\",\"\\u0645\\u0642\\u0627\\u0639\\u062f AMG \\u062c\\u0644\\u062f\\u064a\\u0629 \\u0643\\u0647\\u0631\\u0628\\u0627\\u0626\\u064a\\u0629 \\u0628\\u0627\\u0644\\u0643\\u0627\\u0645\\u0644\",\"\\u062a\\u0643\\u064a\\u064a\\u0641 \\u0647\\u0648\\u0627\\u0621 \\u0623\\u0648\\u062a\\u0648\\u0645\\u0627\\u062a\\u064a\\u0643\\u064a \\u0645\\u062a\\u0637\\u0648\\u0631 THERMATIC\",\"\\u0625\\u0636\\u0627\\u0621\\u0629 \\u0645\\u062d\\u064a\\u0637\\u064a\\u0629 64 \\u0644\\u0648\\u0646\\u0627\\u064b\"]', '[\"\\u0646\\u0638\\u0627\\u0645 MBUX \\u0627\\u0644\\u0645\\u062d\\u062f\\u062b \\u0628\\u0634\\u0627\\u0634\\u0629 11.9 \\u0628\\u0648\\u0635\\u0629\",\"\\u0634\\u0627\\u0634\\u0629 \\u0639\\u062f\\u0627\\u062f\\u0627\\u062a 12.3 \\u0628\\u0648\\u0635\\u0629\",\"\\u0634\\u0627\\u062d\\u0646 \\u0644\\u0627\\u0633\\u0644\\u0643\\u064a\",\"\\u062a\\u0643\\u0627\\u0645\\u0644 \\u0627\\u0644\\u0647\\u0627\\u062a\\u0641 \\u0627\\u0644\\u0630\\u0643\\u064a\"]', '[\"\\u062c\\u0646\\u0648\\u0637 \\u0631\\u064a\\u0627\\u0636\\u064a\\u0629 AMG \\u0642\\u064a\\u0627\\u0633 18 \\u0628\\u0648\\u0635\\u0629\",\"\\u0634\\u0628\\u0643 AMG \\u0627\\u0644\\u0631\\u064a\\u0627\\u0636\\u064a \\u0628\\u0641\\u062a\\u062d\\u0627\\u062a \\u0645\\u0627\\u0633\\u064a\\u0629\",\"\\u0645\\u0631\\u0627\\u064a\\u0627 \\u0642\\u0627\\u0628\\u0644\\u0629 \\u0644\\u0644\\u0637\\u064a \\u0648\\u0627\\u0644\\u062a\\u0639\\u062a\\u064a\\u0645 \\u062a\\u0644\\u0642\\u0627\\u0626\\u064a\\u0627\\u064b\"]', '1', '0', '0', '2026-07-14 00:34:30');

-- Dumping data for table `offers`
INSERT INTO `offers` (`id`, `title_ar`, `title_en`, `description_ar`, `description_en`, `discount_pct`, `car_id`, `image`, `valid_until`, `created_at`) VALUES ('1', 'عرض الصيف المميز على كامري 2026', 'Summer Deal on Camry 2026', 'احصل على خصم 5% ودعم للدفعة الأولى مع فترة سداد مرنة تصل لـ 60 شهراً بدون رسوم إدارية.', 'Get 5% discount and downpayment assistance with flexible term options up to 60 months and 0 admin fees.', '5', '1', 'offer_camry.jpg', '2026-09-30', '2026-07-14 00:34:30');
INSERT INTO `offers` (`id`, `title_ar`, `title_en`, `description_ar`, `description_en`, `discount_pct`, `car_id`, `image`, `valid_until`, `created_at`) VALUES ('2', 'قسطها بسعر الكاش! توسان 2026', 'Installments at Cash Price! Tucson 2026', 'عروض تمويلية مميزة بالتعاون مع البنك الأهلي بقسط شهري يبدأ من 1,490 ريال وهامش ربح 0%.', 'Exclusive financing program with SNB, monthly installment starting from 1,490 SAR and 0% profit margin.', '0', '2', 'offer_tucson.jpg', '2026-08-31', '2026-07-14 00:34:30');

-- Dumping data for table `requests`
INSERT INTO `requests` (`id`, `user_id`, `car_id`, `type`, `name`, `phone`, `email`, `city`, `payment_method`, `notes`, `national_id`, `salary`, `employer`, `work_duration`, `downpayment`, `term_months`, `monthly_installment`, `status`, `created_at`) VALUES ('1', '2', '1', 'installment', 'أحمد العتيبي', '0512345678', 'user@syarah.com', 'جدة', 'installment', 'أرغب في الاستلام بجدة، والتواصل عبر واتساب.', '1023456789', '12500', 'وزارة التعليم', '5', '20000', '60', '1650', 'received', '2026-07-10 14:32:00');
INSERT INTO `requests` (`id`, `user_id`, `car_id`, `type`, `name`, `phone`, `email`, `city`, `payment_method`, `notes`, `national_id`, `salary`, `employer`, `work_duration`, `downpayment`, `term_months`, `monthly_installment`, `status`, `created_at`) VALUES ('2', NULL, '4', 'booking', 'سلطان المطيري', '0544444444', 'sultan@example.com', 'الرياض', 'card', 'تم دفع قيمة الحجز المبدئي عبر بوابة مدى أونلاين.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booked', '2026-07-12 09:15:00');
INSERT INTO `requests` (`id`, `user_id`, `car_id`, `type`, `name`, `phone`, `email`, `city`, `payment_method`, `notes`, `national_id`, `salary`, `employer`, `work_duration`, `downpayment`, `term_months`, `monthly_installment`, `status`, `created_at`) VALUES ('3', NULL, '5', 'installment', 'خالد الحربي', '0533333333', 'khaled@example.com', 'الدمام', 'installment', 'يرجى مراجعة الطلب بأسرع وقت.', '1098765432', '19500', 'أرامكو السعودية', '8', '50000', '48', '5800', 'booked', '2026-07-13 18:22:00');

COMMIT;
