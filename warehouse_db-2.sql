-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 02, 2026 at 06:07 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warehouse_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `target_table` varchar(100) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `target_table`, `target_id`, `details`, `created_at`) VALUES
(1, 1, 'Global Settings Updated', 'settings', 0, 'Various system keys updated by Admin', '2026-01-28 10:54:20'),
(2, 1, 'Global Settings Updated', 'settings', 0, 'Various system keys updated by Admin', '2026-01-28 10:57:47'),
(3, 1, 'New Product Added', 'products', 16, 'chocolate (Stock: 10)', '2026-01-28 10:59:46'),
(4, 1, 'New Product Added', 'products', 17, 'Mouse Trackpad (Stock: 20)', '2026-01-28 12:49:21'),
(5, 1, 'New Product Added', 'products', 18, 'Service Charge (Stock: 100000)', '2026-01-28 12:50:08'),
(6, 1, 'New Sale Finalized', 'sales', 3, 'Total: ₹5310', '2026-01-28 12:55:33'),
(7, 1, 'Invoice Marked Paid', 'sales', 3, 'Ref: UPI-TRANS-', '2026-01-28 12:55:44'),
(8, 1, 'Product Updated', 'products', 17, 'Mouse Trackpad (Stock: 20)', '2026-01-28 12:59:25'),
(9, 1, 'Product Updated', 'products', 18, 'Service Charge (Stock: 100000)', '2026-01-28 12:59:58'),
(10, 1, 'Product Updated', 'products', 18, 'Service Charge (Stock: 100000)', '2026-01-28 13:00:31'),
(11, 1, 'Product Updated', 'products', 17, 'Mouse Trackpad (Stock: 20)', '2026-01-28 13:00:58'),
(12, 1, 'Product Updated', 'products', 17, 'Mouse Trackpad (Stock: 20)', '2026-01-28 13:01:57'),
(13, 1, 'New Sale Finalized', 'sales', 4, 'Total: ₹4531', '2026-01-28 13:02:19'),
(14, 1, 'Global Settings Updated', 'settings', 0, 'Various system keys updated by Admin', '2026-01-28 13:06:24'),
(15, 1, 'New Sale Finalized', 'sales', 5, 'Total: ₹4531', '2026-01-28 13:08:17'),
(16, 1, 'New Sale Finalized', 'sales', 6, 'Total: ₹4531', '2026-01-28 13:10:41'),
(17, 1, 'New Sale Finalized', 'sales', 7, 'Total: ₹4531', '2026-01-28 13:13:02'),
(18, 1, 'Product Updated', 'products', 5, 'Yoga Mat (Stock: 2)', '2026-01-28 17:10:17'),
(19, 1, 'Product Updated', 'products', 5, 'Yoga Mat (Stock: 6)', '2026-01-28 17:19:18'),
(20, 1, 'Global Settings Updated', 'settings', 0, 'Various system keys updated by Admin', '2026-01-30 12:18:48'),
(21, 1, 'New Sale Finalized', 'sales', 8, 'Total: ₹1.18 (+50 XP)', '2026-01-30 17:05:51'),
(22, 1, 'New Sale Finalized', 'sales', 9, 'Total: ₹1.18 (+50 XP)', '2026-01-30 17:06:36'),
(23, 1, 'New Sale Finalized', 'sales', 10, 'Total: ₹1.18 (+50 XP)', '2026-01-30 17:09:16'),
(24, 1, 'New Sale Finalized', 'sales', 11, 'Total: ₹1.18 (+50 XP)', '2026-01-30 17:18:04'),
(25, 1, 'New Sale Finalized', 'sales', 12, 'Total: ₹1.18 (+50 XP)', '2026-01-30 17:21:03'),
(26, 1, 'Invoice Marked Paid', 'sales', 12, 'Ref: UPI-TRANS-', '2026-01-30 17:38:00'),
(27, 1, 'External Payment Verified', 'sales', 4, 'Verified via Razorpay/Webhook Callback', '2026-01-30 17:45:34'),
(28, 1, 'Product Updated', 'products', 18, 'Service Charge (Stock: 99996)', '2026-01-30 18:29:37'),
(29, 1, 'Product Updated', 'products', 17, 'Mouse Trackpad (Stock: 16)', '2026-01-30 18:29:49'),
(30, 1, 'Product Updated', 'products', 16, 'chocolate (Stock: 6)', '2026-01-30 18:32:49'),
(31, 1, 'Global Settings Updated', 'settings', 0, 'Various system keys updated by Admin', '2026-02-02 17:37:06'),
(32, 1, 'Invoice Marked Paid', 'sales', 2, 'Ref: MOCK-REF-123', '2026-02-02 17:48:56'),
(33, 1, 'Invoice Marked Paid', 'sales', 2, 'Ref: MOCK-REF-123', '2026-02-02 17:49:19'),
(34, 1, 'Invoice Marked Paid', 'sales', 2, 'Ref: MOCK-REF-123', '2026-02-02 17:49:45'),
(35, 1, 'Invoice Marked Paid', 'sales', 1, 'Ref: MOCK-REF-123', '2026-02-02 17:50:19'),
(36, 1, 'New Sale Finalized', 'sales', 13, 'Total: ₹1.18 (+50 XP)', '2026-02-02 17:52:05'),
(37, 1, 'Invoice Marked Paid', 'sales', 13, 'Ref: FINAL-DEBUG-REF', '2026-02-02 17:53:47'),
(38, 1, 'Invoice Marked Paid', 'sales', 2, 'Ref: FINAL-DEBUG-REF', '2026-02-02 17:53:47'),
(39, 1, 'Product Updated', 'products', 16, 'chocolate (Stock: 15)', '2026-02-02 17:56:06'),
(40, 1, 'New Sale Finalized', 'sales', 14, 'Total: ₹1.18 (+50 XP)', '2026-02-02 17:56:24'),
(41, 1, 'New Sale Finalized', 'sales', 15, 'Total: ₹141.6 (+50 XP)', '2026-02-02 18:02:26'),
(42, 1, 'Invoice Marked Paid', 'sales', 15, 'Ref: TEST-REF-123', '2026-02-02 18:07:00'),
(43, 1, 'New Sale Finalized', 'sales', 16, 'Total: ₹1.18 (+50 XP)', '2026-02-02 18:11:09'),
(44, 1, 'Invoice Marked Paid', 'sales', 16, 'Ref: MANUAL-TEST-999', '2026-02-02 18:13:06'),
(45, 1, 'New Sale Finalized', 'sales', 17, 'Total: ₹141.6 (+50 XP)', '2026-02-02 18:17:16'),
(46, 1, 'Product Updated', 'products', 16, 'chocolate (Stock: 1)', '2026-02-14 16:08:41'),
(47, 1, 'Product Updated', 'products', 16, 'chocolate (Stock: 100)', '2026-02-14 16:09:18'),
(48, 1, 'New Product Added (+10 XP)', 'products', 19, 'Darshan Sing tel (Stock: 123)', '2026-02-27 04:32:40'),
(49, 1, 'New Product Added (+10 XP)', 'products', 60, 'SENTAS Sing Tel (Stock: 56)', '2026-02-27 04:47:59'),
(50, 1, 'New Product Added (+10 XP)', 'products', 61, 'RAVI cotton seed oil (Stock: 187)', '2026-02-27 04:49:31'),
(51, 1, 'New Product Added (+10 XP)', 'products', 62, 'VIMAL GHEE (Stock: 37)', '2026-02-27 04:51:32'),
(52, 1, 'New Product Added (+10 XP)', 'products', 63, 'AKSHAR GHEE (Stock: 240)', '2026-02-27 04:51:56'),
(53, 1, 'New Product Added (+10 XP)', 'products', 64, 'SHREE HARI GHEE (Stock: 82)', '2026-02-27 04:52:22'),
(54, 1, 'Product Updated', 'products', 63, 'OSCAR GHEE (Stock: 240)', '2026-02-27 04:54:19'),
(55, 1, 'New Product Added (+10 XP)', 'products', 65, 'ચણા નો લોટ કકરો  (Stock: 756)', '2026-02-27 04:56:08'),
(56, 1, 'New Product Added (+10 XP)', 'products', 66, 'ખમણ બેસન  (Stock: 250)', '2026-02-27 04:58:35'),
(57, 1, 'New Product Added (+10 XP)', 'products', 67, 'કઢી બેસન  (Stock: 150)', '2026-02-27 04:59:25'),
(58, 1, 'New Product Added (+10 XP)', 'products', 68, 'રોટલી લોટ  (Stock: 120)', '2026-02-27 05:00:01'),
(59, 1, 'New Product Added (+10 XP)', 'products', 69, 'ભાખરી લોટ  (Stock: 1600)', '2026-02-27 05:00:57'),
(60, 1, 'New Product Added (+10 XP)', 'products', 70, 'ઠાકોરજી નો રોટલી નો લોટ  (Stock: 720)', '2026-02-27 05:02:07'),
(61, 1, 'New Product Added (+10 XP)', 'products', 71, 'હાંડવા નો લોટ  (Stock: 30)', '2026-02-27 05:03:07'),
(62, 1, 'New Product Added (+10 XP)', 'products', 72, 'મેદો  (Stock: 1000)', '2026-02-27 05:04:24'),
(63, 1, 'New Product Added (+10 XP)', 'products', 73, 'આખી ખાંડ  (Stock: 6660)', '2026-02-27 05:16:37'),
(64, 1, 'New Product Added (+10 XP)', 'products', 74, 'દળેકી ખાંડ  (Stock: 240)', '2026-02-27 05:17:09'),
(65, 1, 'New Product Added (+10 XP)', 'products', 75, 'મધુર ખાંડ  (Stock: 75)', '2026-02-27 05:17:48'),
(66, 1, 'New Product Added (+10 XP)', 'products', 76, 'આખી સાકર  (Stock: 119)', '2026-02-27 05:18:28');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(6, 'OIL/GHEE', ''),
(7, 'LOTT', ''),
(8, 'SUGAR', '');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gstin` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `gstin`) VALUES
(1, 'Alice Wonderland', '555-1111', 'alice@example.com', NULL, NULL),
(2, 'Bob Builder', '555-2222', 'bob@example.com', NULL, NULL),
(3, 'Charlie Brown', '555-3333', 'charlie@example.com', NULL, NULL),
(4, 'Umang Gupta', '9104645080', '', '', NULL),
(5, 'yash kapatel', '7046819366', 'yashkapatel2411@gmail.com', 'Tekre, Navli, Anand, Gujarat - 388355', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_snapshots`
--

CREATE TABLE `inventory_snapshots` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `stock_qty` int(11) DEFAULT NULL,
  `recorded_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_quantity` decimal(15,3) DEFAULT 0.000,
  `unit` varchar(20) DEFAULT 'unit(no)',
  `reorder_level` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `batch_number` varchar(50) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT 1,
  `hsn_code` varchar(20) DEFAULT NULL,
  `rack_location` varchar(50) DEFAULT 'A1',
  `bin_location` varchar(50) DEFAULT '001',
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `image_path`, `category_id`, `sub_category_id`, `serial_number`, `barcode`, `price`, `purchase_price`, `stock_quantity`, `unit`, `reorder_level`, `created_at`, `batch_number`, `expiry_date`, `warehouse_id`, `hsn_code`, `rack_location`, `bin_location`, `is_deleted`) VALUES
(1, 'Smartphone X', NULL, NULL, NULL, NULL, 'SN-001', 'BAR-1001', 799.99, 0.00, 100.000, 'unit(no)', 5, '2026-01-27 17:29:26', NULL, NULL, 1, NULL, 'A1', '001', 1),
(2, 'Laptop Pro', NULL, NULL, NULL, NULL, 'SN-002', 'BAR-1002', 1299.99, 0.00, 100.000, 'unit(no)', 5, '2026-01-27 17:29:26', NULL, NULL, 1, NULL, 'A1', '001', 1),
(3, 'Coffee Maker', NULL, NULL, NULL, NULL, 'SN-003', 'BAR-2001', 89.50, 0.00, 100.000, 'unit(no)', 5, '2026-01-27 17:29:26', NULL, NULL, 1, NULL, 'A1', '001', 1),
(4, 'Car Battery', NULL, NULL, NULL, NULL, 'SN-004', 'BAR-3001', 120.00, 0.00, 100.000, 'unit(no)', 5, '2026-01-27 17:29:26', NULL, NULL, 1, NULL, 'A1', '001', 1),
(5, 'Yoga Mat', '', NULL, NULL, NULL, 'SN-005', 'BAR-5001', 25.00, 25.00, 100.000, 'unit(no)', 5, '2026-01-27 17:29:26', '', NULL, 1, '', 'A1', '001', 1),
(15, 'bike', '', NULL, NULL, NULL, 'SN-1234', 'BAR-4567829', 1600000.00, 1500000.00, 100.000, 'unit(no)', 5, '2026-01-28 09:01:10', NULL, NULL, 1, NULL, 'A1', '001', 1),
(16, 'chocolate', '', NULL, NULL, NULL, 'SN-10101', 'BAR-888999', 1.00, 1.00, 100.000, 'unit(no)', 5, '2026-01-28 10:59:46', '0', NULL, 1, '', 'A1', '001', 1),
(17, 'Mouse Trackpad', '', NULL, NULL, NULL, 'SN-2233898', 'BAR-62199912', 3389.83, 3389.83, 100.000, 'unit(no)', 5, '2026-01-28 12:49:21', '0', NULL, 1, '', 'A2', '001', 1),
(18, 'Service Charge', '', NULL, NULL, NULL, 'SN-00001', 'BAR-010101', 450.00, 450.00, 100.000, 'unit(no)', 5, '2026-01-28 12:50:08', '0', NULL, 1, '', 'A1', '001', 1),
(19, 'Darshan Sing tel', '', NULL, 6, NULL, 'OIL-1', NULL, 166.00, 166.00, 123.000, 'unit(no)', 5, '2026-02-27 04:32:40', '0', NULL, 1, '', '', '', 0),
(60, 'SENTAS Sing Tel', '', NULL, 6, NULL, 'OIL-2', NULL, 166.00, 166.00, 56.000, 'unit(no)', 5, '2026-02-27 04:47:59', '0', NULL, 1, '', '', '', 0),
(61, 'RAVI cotton seed oil', '', NULL, 6, NULL, NULL, NULL, 103.00, 103.00, 187.000, 'unit(no)', 5, '2026-02-27 04:49:31', '0', NULL, 1, '', '', '', 0),
(62, 'VIMAL GHEE', '', NULL, 6, NULL, NULL, NULL, 550.00, 550.00, 37.000, 'unit(no)', 5, '2026-02-27 04:51:32', '0', NULL, 1, '', '', '', 0),
(63, 'OSCAR GHEE', '', NULL, 6, NULL, NULL, NULL, 550.00, 550.00, 240.000, 'unit(no)', 5, '2026-02-27 04:51:56', '0', NULL, 1, '', 'A1', '001', 0),
(64, 'SHREE HARI GHEE', '', NULL, 6, NULL, NULL, NULL, 550.00, 550.00, 82.000, 'unit(no)', 5, '2026-02-27 04:52:22', '0', NULL, 1, '', '', '', 0),
(65, 'ચણા નો લોટ કકરો ', '', NULL, 7, NULL, NULL, NULL, 75.00, 75.00, 756.000, 'unit(no)', 5, '2026-02-27 04:56:08', '0', NULL, 1, '', '', '', 0),
(66, 'ખમણ બેસન ', '', NULL, 7, NULL, NULL, NULL, 80.00, 80.00, 250.000, 'unit(no)', 5, '2026-02-27 04:58:35', '0', NULL, 1, '', '', '', 0),
(67, 'કઢી બેસન ', '', NULL, 7, NULL, NULL, NULL, 80.00, 80.00, 150.000, 'unit(no)', 5, '2026-02-27 04:59:25', '0', NULL, 1, '', '', '', 0),
(68, 'રોટલી લોટ ', '', NULL, 7, NULL, NULL, NULL, 30.00, 30.00, 120.000, 'unit(no)', 5, '2026-02-27 05:00:01', '0', NULL, 1, '', '', '', 0),
(69, 'ભાખરી લોટ ', '', NULL, 7, NULL, NULL, NULL, 30.00, 30.00, 1600.000, 'unit(no)', 5, '2026-02-27 05:00:57', '0', NULL, 1, '', '', '', 0),
(70, 'ઠાકોરજી નો રોટલી નો લોટ ', '', NULL, 7, NULL, NULL, NULL, 30.00, 30.00, 720.000, 'unit(no)', 5, '2026-02-27 05:02:07', '0', NULL, 1, '', '', '', 0),
(71, 'હાંડવા નો લોટ ', '', NULL, 7, NULL, NULL, NULL, 30.00, 30.00, 30.000, 'unit(no)', 5, '2026-02-27 05:03:07', '0', NULL, 1, '', '', '', 0),
(72, 'મેદો ', '', NULL, 7, NULL, NULL, NULL, 0.00, 0.00, 1000.000, 'unit(no)', 5, '2026-02-27 05:04:24', '0', NULL, 1, '', '', '', 0),
(73, 'આખી ખાંડ ', '', NULL, 8, NULL, NULL, NULL, 38.00, 38.00, 6660.000, 'unit(no)', 5, '2026-02-27 05:16:37', '0', NULL, 1, '', '', '', 0),
(74, 'દળેકી ખાંડ ', '', NULL, 8, NULL, NULL, NULL, 40.00, 40.00, 240.000, 'unit(no)', 5, '2026-02-27 05:17:09', '0', NULL, 1, '', '', '', 0),
(75, 'મધુર ખાંડ ', '', NULL, 8, NULL, NULL, NULL, 40.00, 40.00, 75.000, 'unit(no)', 5, '2026-02-27 05:17:48', '0', NULL, 1, '', '', '', 0),
(76, 'આખી સાકર ', '', NULL, 8, NULL, NULL, NULL, 40.00, 40.00, 119.000, 'unit(no)', 5, '2026-02-27 05:18:28', '0', NULL, 1, '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `purchase_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `vendor_id`, `total_amount`, `purchase_date`, `created_at`) VALUES
(1, 3, 600.00, '2026-01-27', '2026-01-27 17:31:40'),
(2, 2, 179.00, '2026-01-28', '2026-01-28 08:40:31'),
(3, 2, 1.00, '2026-01-28', '2026-01-28 11:00:08'),
(4, 1, 4000.00, '2026-01-28', '2026-01-28 12:50:50'),
(5, 1, 500.00, '2026-01-28', '2026-01-28 12:51:01');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 4, 5.000, 120.00),
(2, 2, 3, 2.000, 89.50),
(3, 3, 16, 1.000, 1.00),
(4, 4, 17, 1.000, 4000.00),
(5, 5, 18, 1.000, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `sale_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cgst_amount` decimal(15,2) DEFAULT 0.00,
  `sgst_amount` decimal(15,2) DEFAULT 0.00,
  `igst_amount` decimal(15,2) DEFAULT 0.00,
  `tax_summary` text DEFAULT NULL,
  `payment_status` enum('Unpaid','Paid','Partial') DEFAULT 'Unpaid',
  `transaction_ref` varchar(255) DEFAULT NULL,
  `payment_method` enum('Cash','UPI','Card','Credit') DEFAULT 'Cash',
  `invoice_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `total_amount`, `sale_date`, `created_at`, `cgst_amount`, `sgst_amount`, `igst_amount`, `tax_summary`, `payment_status`, `transaction_ref`, `payment_method`, `invoice_no`) VALUES
(1, NULL, 120.00, '2026-01-27', '2026-01-27 17:32:22', 0.00, 0.00, 0.00, NULL, 'Paid', 'MOCK-REF-123', 'Cash', 'INV-00001'),
(2, NULL, 89.50, '2026-01-28', '2026-01-28 08:41:13', 0.00, 0.00, 0.00, NULL, 'Paid', 'FINAL-DEBUG-REF', 'Cash', 'INV-00002'),
(3, 4, 5310.00, '2026-01-28', '2026-01-28 12:55:33', 405.00, 405.00, 0.00, NULL, 'Paid', 'UPI-TRANS-', 'Cash', 'INV-00003'),
(4, 4, 4531.00, '2026-01-28', '2026-01-28 13:02:19', 345.58, 345.58, 0.00, NULL, 'Paid', 'SIM-XPSOVA5AP', 'UPI', 'INV-00004'),
(5, 4, 4531.00, '2026-01-28', '2026-01-28 13:08:17', 345.58, 345.58, 0.00, NULL, 'Paid', 'MOCK-API-TEST-99', 'UPI', 'INV-00005'),
(6, 4, 4531.00, '2026-01-28', '2026-01-28 13:10:41', 345.58, 345.58, 0.00, NULL, 'Paid', NULL, 'Cash', 'INV-00006'),
(7, 4, 4531.00, '2026-01-28', '2026-01-28 13:13:02', 345.58, 345.58, 0.00, NULL, 'Paid', NULL, 'Cash', 'INV-00001'),
(8, 2, 1.18, '2026-01-29', '2026-01-30 17:05:51', 0.09, 0.09, 0.00, NULL, 'Paid', NULL, 'Cash', 'INV-00001'),
(9, 1, 1.18, '2026-01-30', '2026-01-30 17:06:36', 0.09, 0.09, 0.00, NULL, 'Paid', NULL, 'UPI', 'INV-00002'),
(10, 3, 1.18, '2026-01-30', '2026-01-30 17:09:16', 0.09, 0.09, 0.00, NULL, 'Paid', NULL, 'UPI', 'INV-00003'),
(11, 5, 1.18, '2026-01-30', '2026-01-30 17:18:04', 0.09, 0.09, 0.00, NULL, 'Paid', NULL, 'UPI', 'INV-00004'),
(12, NULL, 1.18, '2026-01-30', '2026-01-30 17:21:03', 0.09, 0.09, 0.00, NULL, 'Paid', 'UPI-TRANS-', 'UPI', 'INV-00005'),
(13, NULL, 1.18, '2026-02-02', '2026-02-02 17:52:05', 0.09, 0.09, 0.00, NULL, 'Paid', 'FINAL-DEBUG-REF', 'Credit', 'INV-00006'),
(14, 2, 1.18, '2026-02-02', '2026-02-02 17:56:24', 0.09, 0.09, 0.00, NULL, 'Paid', NULL, 'Cash', 'INV-00007'),
(15, NULL, 141.60, '2026-02-02', '2026-02-02 18:02:26', 10.80, 10.80, 0.00, NULL, 'Paid', 'TEST-REF-123', 'Cash', 'INV-00008'),
(16, NULL, 1.18, '2026-02-02', '2026-02-02 18:11:09', 0.09, 0.09, 0.00, NULL, 'Paid', 'MANUAL-TEST-999', 'Cash', 'INV-00009'),
(17, NULL, 141.60, '2026-02-02', '2026-02-02 18:17:16', 10.80, 10.80, 0.00, NULL, 'Unpaid', NULL, 'Cash', 'INV-00010');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 4, 1.000, 120.00),
(2, 2, 3, 1.000, 89.50),
(3, 3, 17, 1.000, 4000.00),
(4, 3, 18, 1.000, 500.00),
(5, 4, 17, 1.000, 3389.83),
(6, 4, 18, 1.000, 450.00),
(7, 5, 17, 1.000, 3389.83),
(8, 5, 18, 1.000, 450.00),
(9, 6, 17, 1.000, 3389.83),
(10, 6, 18, 1.000, 450.00),
(11, 7, 17, 1.000, 3389.83),
(12, 7, 18, 1.000, 450.00),
(13, 8, 16, 1.000, 1.00),
(14, 9, 16, 1.000, 1.00),
(15, 10, 16, 1.000, 1.00),
(16, 11, 16, 1.000, 1.00),
(17, 12, 16, 1.000, 1.00),
(18, 13, 16, 1.000, 1.00),
(19, 14, 16, 1.000, 1.00),
(20, 15, 4, 1.000, 120.00),
(21, 16, 16, 1.000, 1.00),
(22, 17, 4, 1.000, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'company_name', 'Warehouse Pro ', '2026-01-28 13:06:24'),
(2, 'company_email', 'warehousepro01@gmail.com', '2026-02-02 17:37:06'),
(3, 'company_phone', '+91 98765 43210', '2026-01-28 09:10:40'),
(4, 'company_address', 'C.G.Road , Ahmedabad, 380009', '2026-01-28 13:06:24'),
(5, 'system_currency', '₹', '2026-01-28 09:10:40'),
(8, 'company_gstin', '27AAACP1234A1Z5', '2026-01-28 09:51:45'),
(9, 'cgst_rate', '9', '2026-01-28 09:51:45'),
(10, 'sgst_rate', '9', '2026-01-28 09:51:45'),
(11, 'igst_rate', '18', '2026-01-28 09:51:45'),
(12, 'smtp_host', 'smtp.gmail.com', '2026-01-28 09:51:45'),
(13, 'smtp_port', '587', '2026-01-28 09:51:45'),
(14, 'smtp_user', 'warehousepro01@gmail.com', '2026-01-30 12:18:48'),
(15, 'smtp_pass', 'duxobhribxhkdhfq', '2026-01-30 12:18:48'),
(16, 'smtp_from_name', 'Warehouse Pro Invoicing', '2026-01-28 09:51:45'),
(53, 'merchant_upi', 'devpatel2705@okaxis', '2026-01-28 10:57:47'),
(72, 'invoice_prefix', 'INV-', '2026-01-28 17:05:37'),
(73, 'next_invoice_no', '11', '2026-02-02 18:17:16'),
(76, 'app_version', '1.5.0', '2026-01-30 11:47:32'),
(77, 'xp_per_sale', '50', '2026-01-30 11:47:32'),
(78, 'xp_per_product', '10', '2026-01-30 11:47:32'),
(95, 'rzp_key_id', 'rzp_test_dwX91example', '2026-01-30 17:30:48'),
(96, 'rzp_key_secret', 'secret_example_123', '2026-01-30 17:30:48');

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` enum('Pending','In-Progress','Completed') DEFAULT 'Pending',
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `due_date` date DEFAULT NULL,
  `xp_reward` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','product_dept','purchase_dept','sell_dept','inventory_dept') NOT NULL,
  `xp` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `xp`, `created_at`, `avatar_path`) VALUES
(1, 'admin', '$2y$10$xmgISWoFf.Ntl1OpKOhGm.IlZ0OayHKm/LBZaDs1cBBbk0MiXTKGK', 'admin@warehouse.com', 'admin', 680, '2026-01-27 17:04:06', NULL),
(2, 'product_mgr', '$2y$10$gUuZzqHiZgD9PptQR5un9eoA7L5.WbMLZZkJteRNihZLF.CHMKHPe', 'product@warehouse.com', 'product_dept', 0, '2026-01-27 18:11:36', NULL),
(3, 'purchase_mgr', '$2y$10$epBQYvYtEKAq61Nwp0GQSOYLeWAaQRivuYjyQADB/Oq9/XwtKGbh6', 'purchase@warehouse.com', 'purchase_dept', 0, '2026-01-27 18:11:36', NULL),
(4, 'sales_pro', '$2y$10$m8gVF1Rzo.IRwUlgwWDC1.6ZBa7DRwC.LldI8LSNjOWrDHSaHmLbS', 'sales@warehouse.com', 'sell_dept', 0, '2026-01-27 18:11:36', NULL),
(5, 'stock_keeper', '$2y$10$6vu39xeEN2Jh8AGpELnbO.ehEAfRoE.5/UAA94yKIsHsIlmq1qBiW', 'stock@warehouse.com', 'inventory_dept', 0, '2026-01-27 18:11:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gstin` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `gstin`) VALUES
(1, 'Tech Supply Co', 'John Doe', '555-0101', 'info@techsupply.com', NULL, NULL),
(2, 'Kitchen Masters', 'Jane Smith', '555-0202', 'sales@kitchen.com', NULL, NULL),
(3, 'Auto Parts Pro', 'Mike Ross', '555-0303', 'mike@autoparts.com', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `location`, `contact_person`, `phone`, `created_at`) VALUES
(1, 'Warehouse PRO', 'C.G.Road, Ahmedabad', '', '', '2026-01-28 09:51:45'),
(2, 'Warehouse Pro', 'C.G.Road, Ahmedabad', '', '', '2026-01-28 12:47:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_snapshots`
--
ALTER TABLE `inventory_snapshots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_snapshots_ibfk_1` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `sub_category_id` (`sub_category_id`),
  ADD KEY `products_ibfk_1` (`category_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `purchase_items_ibfk_2` (`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `sale_items_ibfk_2` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inventory_snapshots`
--
ALTER TABLE `inventory_snapshots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `inventory_snapshots`
--
ALTER TABLE `inventory_snapshots`
  ADD CONSTRAINT `inventory_snapshots_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories` (`id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD CONSTRAINT `sub_categories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
