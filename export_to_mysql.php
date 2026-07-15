<?php
$sqliteDb = new PDO('sqlite:' . __DIR__ . '/database/syarah.db');
$sqliteDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = ['users', 'brands', 'models', 'cars', 'offers', 'requests'];

$sql = "-- phpMyAdmin SQL Dump\n";
$sql .= "-- Generation Time: " . date('Y-m-d H:i:s') . "\n";
$sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
$sql .= "START TRANSACTION;\n";
$sql .= "SET time_zone = \"+00:00\";\n\n";

$sql .= "CREATE DATABASE IF NOT EXISTS `sayara_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
$sql .= "USE `sayara_db`;\n\n";

// Table Structures
$sql .= "DROP TABLE IF EXISTS `favorites`, `notifications`, `users`, `brands`, `models`, `cars`, `offers`, `requests`;\n\n";

$sql .= "CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_ar` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sql .= "CREATE TABLE `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sql .= "CREATE TABLE `cars` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sql .= "CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sql .= "CREATE TABLE `offers` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sql .= "CREATE TABLE `requests` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sql .= "CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title_ar` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `message_ar` text NOT NULL,
  `message_en` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sql .= "CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";


// Dump Data
foreach ($tables as $table) {
    $stmt = $sqliteDb->query("SELECT * FROM $table");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($rows) > 0) {
        $sql .= "-- Dumping data for table `$table`\n";
        foreach ($rows as $row) {
            $cols = [];
            $vals = [];
            foreach ($row as $k => $v) {
                $cols[] = "`" . $k . "`";
                if ($v === null) {
                    $vals[] = "NULL";
                } else {
                    $vals[] = "'" . addslashes((string)$v) . "'";
                }
            }
            $sql .= "INSERT INTO `$table` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
        }
        $sql .= "\n";
    }
}

$sql .= "COMMIT;\n";

file_put_contents(__DIR__ . '/database_mysql.sql', $sql);
echo "Exported database_mysql.sql successfully!";
