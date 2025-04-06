-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2025 at 07:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_info` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `context` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`id`, `user_id`, `pet_info`, `appointment_date`, `appointment_time`, `context`, `created_at`) VALUES
(7, 1, 'Bull', '2025-04-10', '09:00:00', 'Chán ăn', '2025-04-06 15:43:01');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `description`) VALUES
(1, 'Dog Food', 'Food products specifically for dogs, including dry food, wet food, and treats.'),
(2, 'Cat Food', 'Food products specifically for cats, including dry food, wet food, and treats.'),
(3, 'Bird Food', 'Food products for birds, including seeds, pellets, and treats.'),
(4, 'Dog Toys', 'Toys designed for dogs, including chew toys, fetch toys, and interactive toys.'),
(5, 'Cat Toys', 'Toys designed for cats, including scratching posts, balls, and interactive toys.'),
(6, 'Bird Toys', 'Toys designed for birds, including swings, ladders, and mirrors.'),
(7, 'Dog Grooming', 'Grooming products for dogs, including shampoos, brushes, and nail clippers.'),
(8, 'Cat Grooming', 'Grooming products for cats, including shampoos, brushes, and nail clippers.'),
(9, 'Bird Grooming', 'Grooming products for birds, including beak care and feather care products.'),
(10, 'Dog Health & Wellness', 'Health and wellness products for dogs, such as supplements and flea treatments.'),
(11, 'Cat Health & Wellness', 'Health and wellness products for cats, such as supplements and flea treatments.'),
(12, 'Bird Health & Wellness', 'Health and wellness products for birds, such as supplements and vitamins.'),
(13, 'Dog Leashes & Collars', 'Leashes, collars, and harnesses designed for dogs.'),
(14, 'Cat Leashes & Collars', 'Leashes, collars, and harnesses designed for cats.'),
(15, 'Bird Cages & Accessories', 'Cages and accessories for pet birds, including perches and food holders.'),
(16, 'Dog Beds & Furniture', 'Beds, cushions, and other furniture for dogs.'),
(17, 'Cat Beds & Furniture', 'Beds, cushions, and other furniture for cats.'),
(18, 'Bird Cages', 'Cages designed specifically for birds of all sizes.');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `price`, `image_path`, `category_id`) VALUES
(1, 'Dog Food - Chicken Flavor', 'Nutritious chicken-flavored food for adult dogs, rich in protein and vitamins.', 29.99, '/images/dog_food_chicken.png', 1),
(2, 'Cat Food - Salmon Delight', 'Delicious salmon-flavored food for adult cats, rich in Omega-3 fatty acids.', 24.99, '/images/cat_food_salmon.png', 2),
(3, 'Bird Food - Mixed Seeds', 'A variety of seeds suitable for all bird species, providing balanced nutrition.', 15.99, '/images/bird_food_seeds.png', 3),
(4, 'Dog Chew Toy - Bone', 'Durable chew toy shaped like a bone, perfect for dogs who love to chew.', 12.50, '/images/dog_chew_bone.png', 4),
(5, 'Cat Scratching Post', 'A sturdy scratching post to help your cat sharpen its claws and stretch.', 19.99, '/images/cat_scratching_post.png', 5),
(6, 'Bird Swing', 'A colorful swing designed for birds to enjoy while they perch and play.', 8.99, '/images/bird_swing.png', 6),
(7, 'Dog Shampoo - Lavender Scent', 'Gentle lavender-scented shampoo that cleans and soothes your dog\'s skin.', 14.99, '/images/dog_shampoo_lavender.png', 7),
(8, 'Cat Brush - Self Cleaning', 'A self-cleaning brush to remove loose fur from your cat without making a mess.', 18.99, '/images/cat_brush_self_cleaning.png', 8),
(9, 'Bird Feather Care', 'Specialized product to help maintain your bird\'s feathers and overall health.', 22.50, '/images/bird_feather_care.png', 9),
(10, 'Dog Flea & Tick Treatment', 'A monthly treatment to prevent fleas and ticks in dogs of all sizes.', 39.99, '/images/dog_flea_tick.png', 10),
(11, 'Cat Supplement - Hairball Control', 'A supplement designed to reduce hairball formation in cats.', 14.99, '/images/cat_supplement_hairball.png', 11),
(12, 'Bird Vitamin Drops', 'Essential vitamins and minerals for your bird to stay healthy and strong.', 10.99, '/images/bird_vitamin_drops.png', 12),
(13, 'Dog Leash - Reflective', 'A reflective dog leash for safe walks at night.', 16.50, '/images/dog_leash_reflective.png', 13),
(14, 'Cat Collar - Breakaway', 'A breakaway collar for cats with a cute bowtie design.', 8.99, '/images/cat_collar_breakaway.png', 14),
(15, 'Bird Cage - Large', 'A spacious bird cage for larger birds like parrots, with plenty of perches.', 89.99, '/images/bird_cage_large.png', 15),
(16, 'Dog Bed - Memory Foam', 'A comfortable memory foam dog bed that supports joints and provides ultimate comfort.', 49.99, '/images/dog_bed_memory_foam.png', 16),
(17, 'Cat Bed - Heated', 'A heated cat bed that keeps your pet warm and cozy during the winter months.', 39.99, '/images/cat_bed_heated.png', 17),
(18, 'Bird Cage - Small', 'A smaller bird cage perfect for smaller bird species or as a travel cage.', 39.99, '/images/bird_cage_small.png', 18);

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt`
--

INSERT INTO `receipt` (`id`, `user_id`, `total_price`, `purchase_date`, `payment_status`) VALUES
(1, 1, 75.97, '2025-04-06 16:13:21', 'pending'),
(2, 1, 24.99, '2025-04-06 16:18:50', 'pending'),
(3, 1, 54.98, '2025-04-06 16:19:17', 'pending'),
(4, 1, 70.97, '2025-04-06 16:25:20', 'pending'),
(5, 1, 70.97, '2025-04-06 16:31:22', 'pending'),
(6, 1, 70.97, '2025-04-06 16:40:00', 'pending'),
(7, 1, 70.97, '2025-04-06 16:42:12', 'pending'),
(8, 1, 40.98, '2025-04-06 16:44:42', 'pending'),
(9, 1, 40.98, '2025-04-06 16:45:27', 'pending'),
(10, 1, 140.95, '2025-04-06 16:51:04', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `receipt_items`
--

CREATE TABLE `receipt_items` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt_items`
--

INSERT INTO `receipt_items` (`id`, `receipt_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 2, 29.99),
(2, 1, 3, 1, 15.99),
(3, 2, 2, 1, 24.99),
(4, 3, 2, 1, 24.99),
(5, 3, 1, 1, 29.99),
(6, 4, 2, 1, 24.99),
(7, 4, 1, 1, 29.99),
(8, 4, 3, 1, 15.99),
(9, 5, 2, 1, 24.99),
(10, 5, 1, 1, 29.99),
(11, 5, 3, 1, 15.99),
(12, 6, 2, 1, 24.99),
(13, 6, 1, 1, 29.99),
(14, 6, 3, 1, 15.99),
(15, 7, 1, 1, 29.99),
(16, 7, 2, 1, 24.99),
(17, 7, 3, 1, 15.99),
(18, 8, 2, 1, 24.99),
(19, 8, 3, 1, 15.99),
(20, 9, 2, 1, 24.99),
(21, 9, 3, 1, 15.99),
(22, 10, 1, 2, 29.99),
(23, 10, 2, 1, 24.99),
(24, 10, 3, 1, 15.99),
(25, 10, 10, 1, 39.99);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator with full access to the system'),
(2, 'user', 'Standard user with access to basic features'),
(3, 'mod', 'Moderator with privileges to manage content');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `birth_year` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `address` text DEFAULT NULL,
  `phone` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `fullname`, `gender`, `birth_year`, `role`, `status`, `created_at`, `updated_at`, `address`, `phone`) VALUES
(1, 'khanh@gmail.com', '$2y$10$jgpDxGPrZTvQ6iv5JRfBDusshERowMF7XbN9X3iqVy.ujrJZSzWOy', 'Gia Khanh', 'male', 0, 1, 'active', '2025-04-06 14:26:14', '2025-04-06 14:26:14', 'Ho Chi Minh', '1234567890');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `receipt_items`
--
ALTER TABLE `receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `receipt_items`
--
ALTER TABLE `receipt_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `receipt_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `receipt_items`
--
ALTER TABLE `receipt_items`
  ADD CONSTRAINT `receipt_items_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `receipt` (`id`),
  ADD CONSTRAINT `receipt_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
