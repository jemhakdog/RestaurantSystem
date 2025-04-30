-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 08:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

DROP DATABASE IF EXISTS `restaurant_db`;
CREATE DATABASE  `restaurant_db`;
USE  `restaurant_db`;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `user_id` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`user_id`, `address`) VALUES
('5', 'mangatarem pangasinan');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `user_id`, `menu_id`, `quantity`, `created_at`) VALUES
(1, 5, 2, 123, '2025-04-04 09:09:32'),
(2, 5, 3, 1, '2025-04-04 09:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `name`, `description`, `price`, `quantity`, `image`, `category`) VALUES
(1, 'Spaghetti Bolognese', 'Classic Italian pasta with rich meat sauce', 685.33, 0, 'Spaghetti Bolognese.jpg', 'Spaghetti'),
(2, 'Chicken Caesar Salad', 'Crispy romaine lettuce with grilled chicken and Caesar dressing', 570.53, 29, 'Chicken Caesar Salad.jpg', 'Salad'),
(3, 'Cheeseburger', 'Juicy beef patty with cheese, lettuce, and tomato', 513.42, 40, 'cheeseburger.jpg', 'burger'),
(4, 'Margherita Pizza', 'Tomato, mozzarella, and basil on a crispy crust', 627.64, 25, 'Margherita Pizza.jpg', 'Pizza'),
(5, 'Braised Pork in Sweet Soy Sauce', 'Tender pieced of pork braised in a flavorful sauce with a touch of heat', 684.75, 35, 'Braised Pork in Sweet Soy Sauce.jpg', 'Pork'),
(6, 'Vegetarian Pizza', 'Tomato, mozzarella, and various fresh vegetables', 541.98, 20, 'Vegetarian Pizza.jpg', 'Pizza'),
(7, 'Sticky Honey & Chilli Pork', 'Delicious marinated pork in a chilli honey glaze with ginger and garlic.\r\n', 599.09, 50, 'Sticky Honey & Chilli Pork.jpg', 'Pork'),
(8, 'Mexican Grilled Chicken Bowl', 'Grilled chicken, Mexican style, served over a bed of quinoa', 284.98, 50, 'Mexican Grilled Chicken Bowl.jpg', 'Chicken'),
(9, 'Bicol Express ', 'The ultimate comfort food! With pork cubes cooked in coconut milk and chili peppers, it\'s rich, creamy, spicy and delicious!', 342.09, 40, 'Bicol Express .jpg', 'Pork');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_type` enum('delivery','pickup','dine-in') NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `order_status` enum('pending','preparing','ready','delivered','completed','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `service_type`, `payment_method`, `payment_status`, `order_status`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 5, 'delivery', 'bank_transfer', 'pending', '', 9.99, '2025-03-17 02:20:00', '2025-03-17 02:20:00'),
(2, 5, 'delivery', 'paypal', 'pending', '', 9.99, '2025-03-17 02:23:14', '2025-03-17 02:23:14'),
(3, 5, 'delivery', 'gcash', '', '', 9.99, '2025-03-17 02:24:41', '2025-03-17 02:24:41'),
(4, 5, 'delivery', 'bank_transfer', '', '', 19.98, '2025-03-17 02:26:24', '2025-03-17 02:26:24'),
(5, 5, 'delivery', 'gcash', '', '', 9.99, '2025-03-17 02:28:11', '2025-03-17 02:28:11'),
(6, 5, 'delivery', 'bank_transfer', '', '', 8.99, '2025-03-17 02:28:56', '2025-03-17 02:28:56'),
(7, 5, 'delivery', 'bank_transfer', '', '', 9.99, '2025-03-17 02:30:13', '2025-03-17 02:30:13'),
(8, 5, 'delivery', 'bank_transfer', '', '', 9.99, '2025-03-17 02:31:18', '2025-03-17 02:31:18'),
(9, 5, 'delivery', 'bank_transfer', '', '', 9.99, '2025-03-17 02:32:33', '2025-03-17 02:32:33'),
(10, 5, 'delivery', 'gcash', '', '', 8.99, '2025-03-17 02:33:15', '2025-03-17 02:33:15'),
(11, 5, 'delivery', 'bank_transfer', '', 'preparing', 17.98, '2025-03-17 02:36:08', '2025-03-31 03:43:12'),
(12, 5, 'delivery', 'gcash', '', '', 8.99, '2025-03-17 02:37:18', '2025-03-17 02:37:18'),
(13, 5, 'delivery', 'gcash', 'pending', 'pending', 9.99, '2025-03-17 02:37:42', '2025-03-31 03:42:01'),
(14, 5, 'delivery', 'paypal', '', 'pending', 18.98, '2025-03-17 02:39:41', '2025-03-31 03:41:30'),
(15, 5, '', 'bank_transfer', '', 'ready', 9.99, '2025-03-17 02:47:36', '2025-03-17 02:47:36'),
(16, 5, 'delivery', 'paypal', '', 'ready', 17.98, '2025-03-17 02:48:13', '2025-03-17 02:48:13'),
(17, 5, 'delivery', 'gcash', '', 'ready', 8.99, '2025-03-17 02:49:16', '2025-03-17 02:49:16'),
(18, 5, 'delivery', 'paypal', 'paid', 'ready', 8.99, '2025-03-17 02:49:59', '2025-03-17 02:49:59'),
(19, 5, 'dine-in', 'paypal', 'paid', 'ready', 9.99, '2025-03-17 02:55:20', '2025-03-17 02:55:20'),
(20, 5, 'delivery', 'gcash', 'paid', 'ready', 9.99, '2025-03-17 02:57:08', '2025-03-17 02:57:08'),
(21, 5, 'delivery', 'bank_transfer', 'paid', 'ready', 9.99, '2025-03-17 02:58:34', '2025-03-17 02:58:34'),
(22, 5, 'dine-in', 'paypal', 'paid', 'ready', 9.99, '2025-03-17 03:00:26', '2025-03-17 03:00:26'),
(23, 5, 'pickup', 'bank_transfer', 'paid', 'ready', 9.99, '2025-03-17 03:06:30', '2025-03-17 03:06:30'),
(24, 5, 'pickup', 'bank_transfer', 'paid', 'ready', 9.99, '2025-03-17 03:06:41', '2025-03-17 03:06:41'),
(25, 5, 'pickup', 'bank_transfer', 'paid', 'ready', 9.99, '2025-03-17 03:06:43', '2025-03-17 03:06:43'),
(26, 5, 'pickup', 'bank_transfer', 'paid', 'ready', 9.99, '2025-03-17 03:07:16', '2025-03-17 03:07:16'),
(27, 5, 'pickup', 'bank_transfer', 'paid', 'ready', 9.99, '2025-03-17 03:08:07', '2025-03-17 03:08:07'),
(28, 5, 'pickup', 'bank_transfer', 'paid', 'delivered', 9.99, '2025-03-17 03:08:09', '2025-03-31 07:55:09'),
(29, 5, 'pickup', 'bank_transfer', 'paid', 'delivered', 9.99, '2025-03-17 03:08:44', '2025-03-31 07:53:57'),
(30, 5, 'delivery', 'gcash', 'paid', 'delivered', 9.99, '2025-03-17 03:12:05', '2025-03-31 07:53:47'),
(31, 5, 'delivery', 'gcash', 'paid', 'delivered', 9.99, '2025-03-17 03:13:50', '2025-03-31 07:53:20'),
(32, 5, 'delivery', 'gcash', 'paid', 'delivered', 9.99, '2025-03-17 03:15:00', '2025-03-31 07:52:36'),
(33, 5, 'pickup', 'paypal', 'paid', 'delivered', 9.99, '2025-03-17 03:15:42', '2025-03-31 08:12:28'),
(34, 5, 'pickup', 'gcash', 'paid', 'delivered', 9.99, '2025-03-31 03:37:00', '2025-03-31 08:11:21'),
(35, 5, 'pickup', 'paypal', 'paid', 'delivered', 9.99, '2025-03-31 03:46:28', '2025-03-31 08:10:32'),
(39, 5, 'delivery', 'bank_transfer', 'pending', 'delivered', 9.99, '2025-03-31 04:09:48', '2025-03-31 08:09:03'),
(40, 5, 'dine-in', 'paypal', 'pending', 'delivered', 19.98, '2025-03-31 04:10:04', '2025-03-31 07:51:30'),
(41, 5, 'delivery', 'gcash', 'pending', 'delivered', 31.97, '2025-03-31 08:17:13', '2025-03-31 09:34:09'),
(42, 5, 'delivery', 'gcash', 'pending', 'ready', 28.97, '2025-03-31 12:44:52', '2025-03-31 12:46:11'),
(43, 5, 'dine-in', 'paypal', 'pending', 'cancelled', 9.99, '2025-04-01 07:16:55', '2025-04-01 07:17:48'),
(44, 5, 'delivery', 'cod', 'pending', 'pending', 70688.61, '2025-04-04 10:06:11', '2025-04-04 10:06:11'),
(45, 5, 'delivery', 'cod', 'pending', 'pending', 70688.61, '2025-04-04 10:21:30', '2025-04-04 10:21:30'),
(46, 5, 'delivery', 'cod', 'pending', 'pending', 70688.61, '2025-04-04 10:23:00', '2025-04-04 10:23:00'),
(47, 5, 'delivery', 'cod', 'pending', 'pending', 70688.61, '2025-04-04 10:24:42', '2025-04-04 10:24:42'),
(48, 5, 'delivery', 'cod', 'pending', 'pending', 70688.61, '2025-04-04 10:26:37', '2025-04-04 10:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `order_history`
--

CREATE TABLE `order_history` (
  `history_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_history`
--

INSERT INTO `order_history` (`history_id`, `order_id`, `status`, `description`, `created_by`, `created_at`) VALUES
(1, 39, 'preparing', NULL, 5, '2025-03-31 07:47:41'),
(3, 39, 'delivered', NULL, 6, '2025-03-31 08:09:03'),
(4, 35, 'delivered', NULL, 6, '2025-03-31 08:10:32'),
(5, 34, 'delivered', NULL, 6, '2025-03-31 08:11:21'),
(6, 33, 'delivered', NULL, 6, '2025-03-31 08:12:28'),
(7, 41, 'preparing', NULL, 6, '2025-03-31 08:17:28'),
(8, 41, 'ready', NULL, 6, '2025-03-31 09:34:07'),
(9, 41, 'delivered', NULL, 6, '2025-03-31 09:34:09'),
(10, 42, 'preparing', NULL, 6, '2025-03-31 12:46:00'),
(11, 42, 'ready', NULL, 6, '2025-03-31 12:46:11'),
(12, 43, 'cancelled', NULL, 6, '2025-04-01 07:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `notes` varchar(5000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `quantity`, `unit_price`, `subtotal`, `price`, `notes`) VALUES
(11, 45, 2, 123, 570.53, 70175.19, 0.00, ''),
(12, 45, 3, 1, 513.42, 513.42, 0.00, ''),
(13, 46, 2, 123, 570.53, 70175.19, 0.00, ''),
(14, 46, 3, 1, 513.42, 513.42, 0.00, ''),
(15, 47, 2, 123, 570.53, 70175.19, 0.00, 'note test '),
(16, 47, 3, 1, 513.42, 513.42, 0.00, ''),
(17, 48, 2, 123, 570.53, 70175.19, 0.00, 'asdsad'),
(18, 48, 3, 1, 513.42, 513.42, 0.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `old_status` enum('pending','preparing','ready','delivered','completed','cancelled') DEFAULT NULL,
  `new_status` enum('pending','preparing','ready','delivered','completed','cancelled') NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_timeline`
--

CREATE TABLE `order_timeline` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `order_id` int(11) NOT NULL,
  `receipt_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `receipt_number` varchar(50) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `tax_amount` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `service_type` varchar(20) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`order_id`, `receipt_date`, `receipt_number`, `subtotal`, `tax_amount`, `total_amount`, `payment_method`, `service_type`, `customer_name`, `delivery_address`) VALUES
(32, '2025-03-17 03:15:00', 'RCP-20250317-32', 8.79, 1.20, 9.99, 'gcash', 'delivery', 'jem autria', 'mangatarem pangasinan'),
(33, '2025-03-17 03:15:42', 'RCP-20250317-33', 8.79, 1.20, 9.99, 'paypal', 'pickup', 'jem autria', NULL),
(34, '2025-03-31 03:37:00', 'RCP-20250331-34', 8.79, 1.20, 9.99, 'gcash', 'pickup', 'jem autria', NULL),
(35, '2025-03-31 03:46:28', 'RCP-20250331-35', 8.79, 1.20, 9.99, 'paypal', 'pickup', 'jem autria', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `reservation_date` datetime NOT NULL,
  `table_number` int(11) NOT NULL,
  `payment_status` varchar(50) DEFAULT 'Pending',
  `paypal_transaction_id` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `name`, `email`, `phone`, `reservation_date`, `table_number`, `payment_status`, `paypal_transaction_id`, `status`) VALUES
(22, 5, 'jem aus', 'jemcarlo46@gmail.com', '090909', '2025-03-27 11:27:00', 16, 'Paid', '123', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `available`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(25, 6, 1),
(26, 7, 1),
(27, 8, 1),
(28, 9, 1),
(29, 10, 1),
(30, 11, 1),
(31, 12, 1),
(32, 13, 1),
(33, 14, 1),
(34, 15, 1),
(35, 16, 1),
(36, 17, 1),
(37, 18, 1),
(38, 19, 1),
(39, 20, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `secret_key` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `password`, `role`, `created_at`, `secret_key`, `profile_picture`) VALUES
(5, 'jem', 'autria', 'jemcarlo46@gmail.com', '09207766194', '$2y$10$z3zc/QzwA4iZbcMDHVVRheUiL97GLNagpmcIBaYefy.VWR2ezczuq', NULL, '2025-03-17 01:31:15', '123', NULL),
(6, 'Admin', 'User', 'admin@admin.com', '1234567890', '$2y$10$AMcMKDWXSBKsdaNE3FfzXeWyR5IgbIpwoGP0rzHGZObaGpfrpD1Ge', 'admin', '2025-03-31 07:57:51', 'admin123', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `order_timeline`
--
ALTER TABLE `order_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_number` (`table_number`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `table_number` (`table_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `order_history`
--
ALTER TABLE `order_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_timeline`
--
ALTER TABLE `order_timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_history`
--
ALTER TABLE `order_history`
  ADD CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_history_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`);

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_timeline`
--
ALTER TABLE `order_timeline`
  ADD CONSTRAINT `order_timeline_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`table_number`) REFERENCES `tables` (`table_number`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
