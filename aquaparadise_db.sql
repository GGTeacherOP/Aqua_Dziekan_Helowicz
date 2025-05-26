-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 26, 2025 at 08:28 PM
-- Wersja serwera: 10.4.28-MariaDB
-- Wersja PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aquaparadise_db`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `resource_type` enum('hotel_room','restaurant_table','spa_slot') NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `quantity_booked` int(11) NOT NULL DEFAULT 1,
  `booking_status` varchar(50) DEFAULT 'confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `order_item_id`, `product_id`, `user_id`, `guest_name`, `guest_email`, `resource_type`, `start_datetime`, `end_datetime`, `quantity_booked`, `booking_status`, `created_at`, `updated_at`) VALUES
(1, 19, 5, 6, NULL, NULL, 'hotel_room', '2025-05-20 14:00:00', '2025-05-21 12:00:00', 1, 'confirmed', '2025-05-26 17:14:20', '2025-05-26 17:14:20'),
(2, 20, 15, 6, NULL, NULL, 'spa_slot', '2025-05-28 15:00:00', '2025-05-28 16:00:00', 1, 'confirmed', '2025-05-26 17:14:20', '2025-05-26 17:14:20'),
(3, 21, 5, 11, NULL, NULL, 'hotel_room', '2025-06-02 14:00:00', '2025-06-03 12:00:00', 1, 'confirmed', '2025-05-26 18:00:13', '2025-05-26 18:00:13'),
(4, 22, 5, 11, NULL, NULL, 'hotel_room', '2025-06-02 14:00:00', '2025-06-03 12:00:00', 1, 'confirmed', '2025-05-26 18:01:10', '2025-05-26 18:01:10'),
(5, 23, 5, 11, NULL, NULL, 'hotel_room', '2025-06-02 14:00:00', '2025-06-03 12:00:00', 1, 'confirmed', '2025-05-26 18:02:05', '2025-05-26 18:02:05'),
(6, 24, 14, 11, NULL, NULL, 'spa_slot', '2025-05-26 13:30:00', '2025-05-26 15:30:00', 1, 'confirmed', '2025-05-26 18:05:01', '2025-05-26 18:05:01'),
(7, 25, 15, 11, NULL, NULL, 'spa_slot', '2025-05-26 13:30:00', '2025-05-26 15:00:00', 1, 'confirmed', '2025-05-26 18:05:46', '2025-05-26 18:05:46'),
(8, 26, 14, 11, NULL, NULL, 'spa_slot', '2025-05-26 13:30:00', '2025-05-26 15:30:00', 1, 'confirmed', '2025-05-26 18:06:55', '2025-05-26 18:06:55'),
(9, 27, 28, 11, NULL, NULL, 'restaurant_table', '2025-05-26 09:00:00', '2025-05-26 11:00:00', 1, 'confirmed', '2025-05-26 18:09:20', '2025-05-26 18:09:20'),
(10, 28, 28, 11, NULL, NULL, 'restaurant_table', '2025-05-26 09:00:00', '2025-05-26 11:00:00', 1, 'confirmed', '2025-05-26 18:09:42', '2025-05-26 18:09:42');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cartitems`
--

CREATE TABLE `cartitems` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price_at_addition` decimal(10,2) NOT NULL,
  `item_details` text DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `cartitems`
--

INSERT INTO `cartitems` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `price_at_addition`, `item_details`, `added_at`) VALUES
(5, 14, 3, 1, 220.00, NULL, '2025-05-21 18:37:03'),
(6, 18, 2, 1, 70.00, NULL, '2025-05-22 17:05:05'),
(17, 15, 1, 1, 90.00, NULL, '2025-05-23 10:29:32'),
(38, 12, 3, 1, 220.00, NULL, '2025-05-26 15:36:53');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `session_id`, `created_at`, `updated_at`) VALUES
(1, 6, '', '2025-05-18 14:36:14', '2025-05-18 14:36:14'),
(2, NULL, 'rs5qipm8e8rur2n9cpcnpa1qch', '2025-05-18 15:46:11', '2025-05-18 15:46:11'),
(3, NULL, 'o31qujudt2il4iarcvk0qlr992', '2025-05-18 17:37:57', '2025-05-18 17:37:57'),
(4, 2, NULL, '2025-05-18 17:49:38', '2025-05-18 17:49:38'),
(5, NULL, '458brtqe87husope9hce104985', '2025-05-18 17:49:42', '2025-05-18 17:49:42'),
(6, 3, NULL, '2025-05-18 17:50:51', '2025-05-18 17:50:51'),
(7, NULL, 'u6qbuckcrplj7m2c88qhl2hg1b', '2025-05-18 17:50:56', '2025-05-18 17:50:56'),
(8, 1, NULL, '2025-05-18 17:51:17', '2025-05-18 17:51:17'),
(9, NULL, '0u6kp4evbteemgr52vg7mc9shq', '2025-05-18 17:51:28', '2025-05-18 17:51:28'),
(10, 4, NULL, '2025-05-18 17:51:52', '2025-05-18 17:51:52'),
(11, NULL, 'ri5ji2ni1i1oht6k7gpooajk5h', '2025-05-18 17:51:58', '2025-05-18 17:51:58'),
(12, 5, NULL, '2025-05-18 17:52:19', '2025-05-18 17:52:19'),
(13, NULL, 'p2aoj07qhdedvdreqhjb16u6l3', '2025-05-18 17:52:26', '2025-05-18 17:52:26'),
(14, NULL, '9hs64jpocbguo2s60se7f0mueh', '2025-05-21 18:35:20', '2025-05-21 18:35:20'),
(15, 7, NULL, '2025-05-21 18:38:06', '2025-05-21 18:38:06'),
(16, NULL, 'kr4fcslcagr5831ilk0tah6dag', '2025-05-21 18:38:15', '2025-05-21 18:38:15'),
(17, NULL, '25htkcs38esej13k1lr18dm1rc', '2025-05-21 18:39:22', '2025-05-21 18:39:22'),
(18, NULL, '6ghu6f1dgtajc0a1c1p76vk60b', '2025-05-22 17:04:53', '2025-05-22 17:04:53'),
(19, NULL, 's0ivnp9v5pb65fkj5sb78tgllp', '2025-05-22 18:28:15', '2025-05-22 18:28:15'),
(20, NULL, '6mrmcmiloqj9ptcae9l8ud1j9q', '2025-05-23 07:52:41', '2025-05-23 07:52:41'),
(21, NULL, '3huj6b5kh9f8tcjjq2kuo6el1v', '2025-05-23 10:30:45', '2025-05-23 10:30:45'),
(22, NULL, 'f7l5u35bq1oudqnthtisjj9o31', '2025-05-23 10:31:10', '2025-05-23 10:31:10'),
(23, NULL, '7rn3cpe620i4mdcve5rkjkgpp9', '2025-05-25 11:19:52', '2025-05-25 11:19:52'),
(24, NULL, 'd8aqobksva5c9ns74etnr0s7au', '2025-05-25 14:27:40', '2025-05-25 14:27:40'),
(25, NULL, 'ngkstnrm7qmp5gmvsurenjej1l', '2025-05-25 15:55:40', '2025-05-25 15:55:40'),
(26, NULL, 'ere0qvd9krdv8i509u915l2hrd', '2025-05-25 18:28:16', '2025-05-25 18:28:16'),
(27, NULL, 'cung5oid1ruke8c8abnttud159', '2025-05-25 18:30:49', '2025-05-25 18:30:49'),
(28, NULL, '41pv3hus2qo4e9opk7lj5ra1uu', '2025-05-25 18:31:06', '2025-05-25 18:31:06'),
(29, NULL, '12tcl1dj8bl39b98gmodfampop', '2025-05-25 18:34:51', '2025-05-25 18:34:51'),
(30, NULL, 'igdfcs65kg28dgl7pebn8iv0p4', '2025-05-25 18:38:53', '2025-05-25 18:38:53'),
(31, NULL, '18mspesd1te2tmvtqr5hua01ot', '2025-05-26 15:02:01', '2025-05-26 15:02:01'),
(32, 11, NULL, '2025-05-26 15:05:33', '2025-05-26 15:31:06'),
(33, 12, NULL, '2025-05-26 15:31:13', '2025-05-26 15:34:18');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(1, 'Aquapark - Bilety', 'Bilety wstępu do aquaparku.'),
(2, 'Hotel - Pokoje', 'Pokoje hotelowe dostępne do rezerwacji.'),
(4, 'Restauracja - Menu', 'Dania i napoje dostępne w restauracji AquaTaste.'),
(5, 'SPA - Pakiety Wellness', 'Kompleksowe pakiety zabiegów SPA i wellness.'),
(6, 'SPA - Terapie Masażu', 'Odprężające masaże dostosowane do potrzeb, od klasycznych po egzotyczne rytuały.'),
(7, 'SPA - Zabiegi na Twarz', 'Profesjonalne zabiegi kosmetyczne przywracające skórze blask i młody wygląd.'),
(8, 'SPA - Zabiegi na Ciało', 'Peelingi, okłady i rytuały pielęgnacyjne, które odżywią skórę i zmysły.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `opinions`
--

CREATE TABLE `opinions` (
  `opinion_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(150) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text NOT NULL,
  `status` varchar(50) DEFAULT 'Oczekująca',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `opinions`
--

INSERT INTO `opinions` (`opinion_id`, `user_id`, `guest_name`, `product_id`, `rating`, `comment`, `status`, `created_at`, `admin_comment`) VALUES
(1, 1, NULL, 6, 5, 'Wspaniały pobyt, pokój z pięknym widokiem! Czysto i komfortowo. Obsługa hotelowa bardzo miła i pomocna. Na pewno wrócimy!', 'Zaakceptowana', '2025-05-18 14:07:58', 'Dziękujemy za miłe słowa i zapraszamy ponownie! Cieszymy się, że pobyt się udał.'),
(2, 2, NULL, 9, 4, 'Bardzo przyjemny masaż, terapeutka profesjonalna. Czułam się odprężona. Może trochę za głośna muzyka w tle, ale ogólnie polecam.', 'Zaakceptowana', '2025-05-18 14:07:58', NULL),
(3, NULL, NULL, 17, 5, 'Pyszny łosoś, najlepszy jaki jadłem! Idealnie wypieczony, sos rewelacyjny. Obsługa w restauracji również na wysokim poziomie.', 'Zaakceptowana', '2025-05-18 14:07:58', 'zajebiscie dzieki'),
(4, 1, NULL, 1, 3, 'Aquapark jest duży, ale w weekendy straszne tłumy. Na niektóre zjeżdżalnie trzeba było długo czekać. Czystość OK.', 'Zaakceptowana', '2025-05-18 14:07:58', 'Dziękujemy za opinię. Staramy się zarządzać przepływem gości, szczególnie w popularne dni. Zapraszamy w tygodniu dla większego komfortu.'),
(5, NULL, 'Janusz', NULL, 4, 'spoko', 'Zaakceptowana', '2025-05-25 14:31:07', 'cos wiecej bys dodal'),
(6, 5, NULL, NULL, 4, 'okej', 'Zaakceptowana', '2025-05-25 15:47:19', 'git'),
(7, NULL, 'Anonimowy', NULL, 3, 'slabo ogolnie drogo posilki zimne', 'Zaakceptowana', '2025-05-25 15:57:26', 'a chcesz w ryj');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orderitems`
--

CREATE TABLE `orderitems` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL,
  `item_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price_per_item`, `item_details`) VALUES
(1, 1, 1, 1, 90.00, NULL),
(2, 2, 4, 1, 270.00, NULL),
(3, 3, 1, 1, 90.00, NULL),
(4, 4, 2, 1, 70.00, '0'),
(5, 5, 1, 1, 90.00, '0'),
(6, 5, 7, 1, 1200.00, '{\"booking_email\":\"superadmin@aquaparadise.pl\",\"booking_name\":\"Krzysztof Adminowski\",\"booking_phone\":\"555000111\",\"check_in_date\":\"2025-05-28\",\"check_out_date\":\"2025-05-31\",\"notes\":\"\",\"num_guests\":\"1\",\"reservation_type\":\"hotel_room\"}'),
(7, 5, 28, 1, 0.00, '{\"reservation_type\":\"restaurant_table\",\"booking_name\":\"Krzysztof Adminowski\",\"booking_phone\":\"555000111\",\"booking_email\":\"superadmin@aquaparadise.pl\",\"reservation_date\":\"2025-06-03\",\"reservation_time\":\"00:25\",\"num_guests\":\"1\",\"notes\":\"\"}'),
(8, 5, 15, 1, 930.00, '{\"reservation_type\":\"spa_booking\",\"placeholder_spa_product_id\":\"15\",\"booking_name\":\"Krzysztof Adminowski\",\"booking_email\":\"superadmin@aquaparadise.pl\",\"booking_phone\":\"555000111\",\"treatment_date\":\"2025-05-29\",\"treatment_time\":\"20:31\",\"selected_treatments_ids_string\":\"10,13,21\",\"total_price_for_selected_spa\":\"930.00\",\"notes\":\"\"}'),
(9, 6, 2, 1, 70.00, '0'),
(10, 7, 4, 1, 270.00, '0'),
(11, 7, 8, 1, 750.00, '{\"reservation_type\":\"hotel_room\",\"booking_name\":\"Bogdan Wielki\",\"booking_email\":\"bogdanwielki@gmail.com\",\"booking_phone\":\"101101101\",\"check_in_date\":\"2025-05-29\",\"check_out_date\":\"2025-06-08\",\"num_guests\":\"4\",\"notes\":\"\"}'),
(12, 7, 26, 1, 850.00, '{\"reservation_type\":\"spa_booking\",\"placeholder_spa_product_id\":\"15\",\"booking_name\":\"Bogdan Wielki\",\"booking_email\":\"bogdanwielki@gmail.com\",\"booking_phone\":\"101101101\",\"treatment_date\":\"2025-06-01\",\"treatment_time\":\"20:30\",\"selected_treatments_ids_string\":\"26\",\"total_price_for_selected_spa\":\"850.00\",\"notes\":\"\"}'),
(13, 7, 28, 1, 0.00, '{\"reservation_type\":\"restaurant_table\",\"booking_name\":\"Bogdan Wielki\",\"booking_phone\":\"101101101\",\"booking_email\":\"bogdanwielki@gmail.com\",\"reservation_date\":\"2025-05-31\",\"reservation_time\":\"21:00\",\"num_guests\":\"4\",\"notes\":\"\"}'),
(14, 8, 1, 1, 90.00, '0'),
(15, 8, 5, 1, 450.00, '{\"reservation_type\":\"hotel_room\",\"booking_name\":\"Grzegorz Wilk\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"booking_phone\":\"555123004\",\"check_in_date\":\"2025-05-27\",\"check_out_date\":\"2025-06-14\",\"num_guests\":\"1\",\"notes\":\"\"}'),
(16, 8, 25, 1, 680.00, '{\"reservation_type\":\"spa_booking\",\"placeholder_spa_product_id\":\"15\",\"booking_name\":\"Grzegorz Wilk\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"booking_phone\":\"555123004\",\"treatment_date\":\"2025-06-05\",\"treatment_time\":\"21:00\",\"selected_treatments_ids_string\":\"25\",\"total_price_for_selected_spa\":\"680.00\",\"notes\":\"Prosze panią\"}'),
(17, 8, 28, 1, 0.00, '{\"reservation_type\":\"restaurant_table\",\"booking_name\":\"Grzegorz Wilk\",\"booking_phone\":\"555000111\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"reservation_date\":\"2025-05-30\",\"reservation_time\":\"19:30\",\"num_guests\":\"1\",\"notes\":\"\"}'),
(18, 9, 1, 2, 90.00, ''),
(19, 9, 5, 1, 450.00, '{\"booking_email\":\"wielkimaniek@gmail.com\",\"booking_name\":\"Maniek Wielki\",\"booking_phone\":\"927957284\",\"check_in_date\":\"2025-05-20\",\"check_out_date\":\"2025-05-21\",\"notes\":\"Prosze czarn\\u0105 po\\u015bciel\",\"num_guests\":\"1\",\"reservation_type\":\"hotel_room\"}'),
(20, 9, 15, 1, 0.00, '{\"booking_email\":\"wielkimaniek@gmail.com\",\"booking_name\":\"Maniek Wielki\",\"booking_phone\":\"927957284\",\"notes\":\"Oby syn\",\"reservation_type\":\"spa_booking\",\"selected_treatments_ids_string\":\"12\",\"treatment_date\":\"2025-05-28\",\"treatment_time\":\"15:00\"}'),
(21, 10, 5, 1, 450.00, '{\"reservation_type\":\"hotel_room\",\"booking_name\":\"Grzegorz Wilk\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"booking_phone\":\"555000111\",\"check_in_date\":\"2025-06-02\",\"check_out_date\":\"2025-06-03\",\"num_guests\":\"1\",\"notes\":\"\"}'),
(22, 11, 5, 1, 450.00, '{\"reservation_type\":\"hotel_room\",\"booking_name\":\"Grzegorz Wilk\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"booking_phone\":\"555000111\",\"check_in_date\":\"2025-06-02\",\"check_out_date\":\"2025-06-03\",\"num_guests\":\"1\",\"notes\":\"\"}'),
(23, 12, 5, 1, 450.00, '{\"reservation_type\":\"hotel_room\",\"booking_name\":\"Grzegorz Wilk\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"booking_phone\":\"555000111\",\"check_in_date\":\"2025-06-02\",\"check_out_date\":\"2025-06-03\",\"num_guests\":\"1\",\"notes\":\"\"}'),
(24, 13, 14, 1, 500.00, '{\"reservation_type\":\"spa_booking\",\"placeholder_spa_product_id\":\"15\",\"booking_name\":\"Grzegorz Wilk\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"booking_phone\":\"555000111\",\"treatment_date\":\"2025-05-26\",\"treatment_time\":\"13:30\",\"selected_treatments_ids_string\":\"14\",\"total_price_for_selected_spa\":\"500.00\",\"notes\":\"\"}'),
(25, 14, 15, 1, 280.00, '{\"reservation_type\":\"spa_booking\",\"placeholder_spa_product_id\":\"15\",\"booking_name\":\"Grzegorz Wilk\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"booking_phone\":\"555000111\",\"treatment_date\":\"2025-05-26\",\"treatment_time\":\"13:30\",\"selected_treatments_ids_string\":\"12\",\"total_price_for_selected_spa\":\"280.00\",\"notes\":\"\"}'),
(26, 15, 14, 1, 500.00, '{\"reservation_type\":\"spa_booking\",\"placeholder_spa_product_id\":\"15\",\"booking_name\":\"Grzegorz Wilk\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"booking_phone\":\"555000111\",\"treatment_date\":\"2025-05-26\",\"treatment_time\":\"13:30\",\"selected_treatments_ids_string\":\"14\",\"total_price_for_selected_spa\":\"500.00\",\"notes\":\"\"}'),
(27, 16, 28, 1, 0.00, '{\"reservation_type\":\"restaurant_table\",\"booking_name\":\"Grzegorz Wilk\",\"booking_phone\":\"555000111\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"reservation_date\":\"2025-05-26\",\"reservation_time\":\"09:00\",\"num_guests\":\"1\",\"notes\":\"\"}'),
(28, 17, 28, 1, 0.00, '{\"reservation_type\":\"restaurant_table\",\"booking_name\":\"Grzegorz Wilk\",\"booking_phone\":\"555000111\",\"booking_email\":\"grzegorz.wilk@aquaparadise.pl\",\"reservation_date\":\"2025-05-26\",\"reservation_time\":\"09:00\",\"num_guests\":\"1\",\"notes\":\"\"}');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_name` varchar(200) DEFAULT NULL,
  `billing_name` varchar(255) DEFAULT NULL,
  `billing_email` varchar(255) DEFAULT NULL,
  `billing_address_street` varchar(255) DEFAULT NULL,
  `billing_address_city` varchar(100) DEFAULT NULL,
  `billing_address_postal_code` varchar(20) DEFAULT NULL,
  `billing_address_country` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Oczekujące na płatność',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'np. Karta, Blik, Przelew',
  `payment_status` varchar(50) DEFAULT 'Oczekuje' COMMENT 'np. Oczekuje, Zakończona, Nieudana, Zwrócona',
  `payment_transaction_id` varchar(255) DEFAULT NULL,
  `promo_code_used` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `guest_email`, `guest_name`, `billing_name`, `billing_email`, `billing_address_street`, `billing_address_city`, `billing_address_postal_code`, `billing_address_country`, `total_amount`, `order_status`, `payment_method`, `payment_status`, `payment_transaction_id`, `promo_code_used`, `notes`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, 'Gość', NULL, NULL, NULL, NULL, NULL, 90.00, 'Zrealizowane', 'Blik', 'Zakończona', NULL, NULL, NULL, '2025-05-23 08:33:51', '2025-05-23 08:33:51'),
(2, 7, NULL, NULL, 'Wikor Tak', 'wiktor@gmail.com', NULL, NULL, NULL, NULL, 270.00, 'Zrealizowane', 'Blik', 'Zakończona', NULL, NULL, NULL, '2025-05-23 08:34:50', '2025-05-23 08:34:50'),
(3, 7, NULL, NULL, 'Wikor Tak', 'wiktor@gmail.com', NULL, NULL, NULL, NULL, 90.00, 'Zrealizowane', 'Blik', 'Zakończona', NULL, NULL, NULL, '2025-05-23 10:17:58', '2025-05-23 10:17:58'),
(4, 7, NULL, NULL, 'Wikor Tak', 'wiktor@gmail.com', NULL, NULL, NULL, NULL, 70.00, 'Zrealizowane', 'Blik', 'Zakończona', NULL, NULL, NULL, '2025-05-23 10:24:19', '2025-05-23 10:24:19'),
(5, 5, NULL, NULL, 'Krzysztof Adminowski', 'superadmin@aquaparadise.pl', NULL, NULL, NULL, NULL, 2220.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748199280', NULL, NULL, '2025-05-25 18:54:40', '2025-05-25 18:54:40'),
(6, 5, NULL, NULL, 'Krzysztof Adminowski', 'superadmin@aquaparadise.pl', NULL, NULL, NULL, NULL, 70.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748199416', NULL, NULL, '2025-05-25 18:56:56', '2025-05-25 18:56:56'),
(7, 12, NULL, NULL, 'Bogdan Wielki', 'bogdanwielki@gmail.com', NULL, NULL, NULL, NULL, 1870.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748273669', NULL, NULL, '2025-05-26 15:34:29', '2025-05-26 15:34:29'),
(8, 11, NULL, NULL, 'Grzegorz Wilk', 'grzegorz.wilk@aquaparadise.pl', NULL, NULL, NULL, NULL, 1220.00, 'Zrealizowane', 'Karta', 'Zakończona', 'SIMULATED_KARTA_1748273763', NULL, NULL, '2025-05-26 15:36:03', '2025-05-26 15:36:03'),
(9, 6, NULL, NULL, 'Maniek Wielki', 'wielkimaniek@gmail.com', NULL, NULL, NULL, NULL, 630.00, 'Zrealizowane', 'Karta', 'Zakończona', 'SIMULATED_KARTA_1748279660', NULL, NULL, '2025-05-26 17:14:20', '2025-05-26 17:14:20'),
(10, 11, NULL, NULL, 'Grzegorz Wilk', 'grzegorz.wilk@aquaparadise.pl', NULL, NULL, NULL, NULL, 450.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748282413', NULL, NULL, '2025-05-26 18:00:13', '2025-05-26 18:00:13'),
(11, 11, NULL, NULL, 'Grzegorz Wilk', 'grzegorz.wilk@aquaparadise.pl', NULL, NULL, NULL, NULL, 450.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748282470', NULL, NULL, '2025-05-26 18:01:10', '2025-05-26 18:01:10'),
(12, 11, NULL, NULL, 'Grzegorz Wilk', 'grzegorz.wilk@aquaparadise.pl', NULL, NULL, NULL, NULL, 450.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748282525', NULL, NULL, '2025-05-26 18:02:05', '2025-05-26 18:02:05'),
(13, 11, NULL, NULL, 'Grzegorz Wilk', 'grzegorz.wilk@aquaparadise.pl', NULL, NULL, NULL, NULL, 500.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748282701', NULL, NULL, '2025-05-26 18:05:01', '2025-05-26 18:05:01'),
(14, 11, NULL, NULL, 'Grzegorz Wilk', 'grzegorz.wilk@aquaparadise.pl', NULL, NULL, NULL, NULL, 280.00, 'Zrealizowane', 'Karta', 'Zakończona', 'SIMULATED_KARTA_1748282746', NULL, NULL, '2025-05-26 18:05:46', '2025-05-26 18:05:46'),
(15, 11, NULL, NULL, 'Grzegorz Wilk', 'grzegorz.wilk@aquaparadise.pl', NULL, NULL, NULL, NULL, 500.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748282815', NULL, NULL, '2025-05-26 18:06:55', '2025-05-26 18:06:55'),
(16, 11, NULL, NULL, 'Grzegorz Wilk', 'grzegorz.wilk@aquaparadise.pl', NULL, NULL, NULL, NULL, 0.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748282960', NULL, NULL, '2025-05-26 18:09:20', '2025-05-26 18:09:20'),
(17, 11, NULL, NULL, 'Grzegorz Wilk', 'grzegorz.wilk@aquaparadise.pl', NULL, NULL, NULL, NULL, 0.00, 'Zrealizowane', 'Blik', 'Zakończona', 'SIMULATED_BLIK_1748282982', NULL, NULL, '2025-05-26 18:09:42', '2025-05-26 18:09:42');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(1, 'wiktor@gmail.com', '$2y$10$J7f6Q4QchQos0tgYuisMzOdLtKpeZPCX6OwalhdvqAfYC9XuYQA2O', '2025-05-25 21:35:24', '2025-05-25 18:35:24');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`, `description`) VALUES
(1, 'view_general_admin_dashboard', 'Dostęp do ogólnego panelu admina'),
(2, 'manage_users', 'Zarządzanie użytkownikami (dodawanie, edycja, usuwanie)'),
(3, 'manage_roles_permissions', 'Zarządzanie rolami i ich uprawnieniami'),
(4, 'manage_hotel_bookings', 'Zarządzanie rezerwacjami hotelowymi'),
(5, 'manage_spa_appointments', 'Zarządzanie rezerwacjami SPA'),
(6, 'manage_restaurant_reservations', 'Zarządzanie rezerwacjami w restauracji'),
(7, 'manage_aquapark_tickets_settings', 'Zarządzanie ustawieniami biletów do aquaparku'),
(8, 'manage_products_services', 'Zarządzanie wszystkimi produktami i usługami'),
(9, 'view_financial_reports', 'Dostęp do raportów finansowych (moduł \"Płacę\")'),
(10, 'manage_opinions', 'Moderowanie opinii klientów'),
(11, 'access_vip_employee_module_hotel', 'Dostęp do modułu VIP pracownika hotelu'),
(12, 'access_vip_employee_module_spa', 'Dostęp do modułu VIP pracownika SPA'),
(13, 'access_vip_employee_module_aquapark', 'Dostęp do modułu VIP pracownika aquaparku'),
(14, 'access_vip_employee_module_restaurant', 'Dostęp do modułu VIP pracownika restauracji'),
(15, 'manage_salaries', 'Zarządzanie wynagrodzeniami pracowników');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `availability_details` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `price`, `image_url`, `availability_details`, `is_active`) VALUES
(1, 1, 'Bilet Normalny Całodniowy', 'Całodniowy wstęp do wszystkich stref Aquaparku dla osoby dorosłej.', 90.00, 'aquapark_assets/bilet_normalny.jpg', '{\"type\": \"daily_ticket\", \"valid_for\": \"adult\"}', 1),
(2, 1, 'Bilet Ulgowy Całodniowy', 'Całodniowy wstęp dla dzieci (3-18 lat), studentów (do 26 lat), seniorów (60+). Wymagana legitymacja.', 70.00, 'aquapark_assets/bilet_ulgowy.jpg', '{\"type\": \"daily_ticket\", \"valid_for\": \"concession\"}', 1),
(3, 1, 'Bilet Rodzinny (2+1)', 'Całodniowy wstęp dla 2 osób dorosłych i 1 dziecka (3-18 lat).', 220.00, 'aquapark_assets/bilet_rodzinny1.jpg', '{\"type\": \"family_ticket\", \"adults\": 2, \"children\": 1}', 1),
(4, 1, 'Bilet Rodzinny (2+2)', 'Całodniowy wstęp dla 2 osób dorosłych i 2 dzieci (3-18 lat).', 270.00, 'aquapark_assets/bilet_rodzinny2.jpg', '{\"type\": \"family_ticket\", \"adults\": 2, \"children\": 2}', 1),
(5, 2, 'Pokój Standard', 'Elegancki i funkcjonalny pokój (20m²) z łóżkiem małżeńskim lub dwoma pojedynczymi, zapewniający komfortowy wypoczynek.', 450.00, 'hotel_assets/standard.webp', '{\"type\": \"room_per_night\", \"size_sqm\": 20, \"beds\": \"double_or_twin\", \"features\": [\"Widok na Ogród\", \"Klimatyzacja\", \"TV SAT\", \"Wi-Fi\"]}', 1),
(6, 2, 'Pokój Deluxe z Balkonem', 'Przestronny pokój (30m²) z prywatnym balkonem i zachwycającym widokiem, idealny dla wymagających gości.', 650.00, 'hotel_assets/deluxe.webp', '{\"type\": \"room_per_night\", \"size_sqm\": 30, \"beds\": \"king\", \"features\": [\"Balkon z Widokiem\", \"Większa Przestrzeń\", \"Minibar\", \"Dostęp do Strefy Spa\", \"Klimatyzacja\", \"TV SAT\", \"Wi-Fi\"]}', 1),
(7, 2, 'Apartament Luksusowy', 'Najwyższy standard komfortu (50m²): oddzielna sypialnia, przestronny salon oraz ekskluzywne wyposażenie.', 1200.00, 'hotel_assets/apartament.webp', '{\"type\": \"room_per_night\", \"size_sqm\": 50, \"beds\": \"king_plus_sofa\", \"features\": [\"Oddzielny Salon\", \"Wanna z Hydromasażem\", \"Serwis Kawowy\", \"Pełen Dostęp VIP\", \"Balkon\", \"Klimatyzacja\", \"TV SAT\", \"Wi-Fi\"]}', 1),
(8, 2, 'Pokój Rodzinny Superior', 'Dużo miejsca dla całej rodziny (35m²), z dodatkowymi udogodnieniami zapewniającymi komfortowy pobyt z dziećmi.', 750.00, 'hotel_assets/rodzinny.jpg', '{\"type\": \"room_per_night\", \"size_sqm\": 35, \"beds\": \"double_and_twin\", \"features\": [\"Dwa Duże Łóżka\", \"Kącik dla Dzieci\", \"Możliwość Dostawki\", \"Gry Planszowe\", \"Klimatyzacja\", \"TV SAT\", \"Wi-Fi\"]}', 1),
(9, 6, 'Masaż Relaksacyjny Klasyczny', 'Odprężający masaż całego ciała technikami klasycznymi, redukujący napięcie mięśniowe.', 250.00, 'spa_assets/terapie_masazu.jpeg', '{\"type\": \"spa_treatment\", \"duration_minutes\": 60, \"focus_area\": \"full_body\"}', 1),
(10, 6, 'Masaż Lomi Lomi Nui', 'Tradycyjny masaż hawajski wykonywany przedramionami, zapewniający głębokie odprężenie i harmonię.', 320.00, 'spa_assets/terapie_masazu.jpeg', '{\"type\": \"spa_treatment\", \"duration_minutes\": 75, \"focus_area\": \"full_body\"}', 1),
(11, 6, 'Masaż Gorącymi Kamieniami Wulkanicznymi', 'Głęboko relaksujący masaż z użyciem podgrzanych kamieni bazaltowych, rozluźniający mięśnie i kojący zmysły.', 350.00, 'spa_assets/terapie_masazu.jpeg', '{\"type\": \"spa_treatment\", \"duration_minutes\": 90, \"focus_area\": \"full_body\"}', 1),
(12, 7, 'Intensywnie Nawilżający Zabieg HydraBoost', 'Zabieg na twarz przywracający skórze optymalny poziom nawilżenia, blask i elastyczność.', 280.00, 'spa_assets/zabiegi_na_twarz.webp', '{\"type\": \"spa_treatment\", \"duration_minutes\": 60, \"focus_area\": \"face\"}', 1),
(13, 7, 'Liftingujący Zabieg Anti-Aging Gold Therapy', 'Luksusowy, odmładzający zabieg na twarz z wykorzystaniem płatków złota, poprawiający jędrność i redukujący zmarszczki.', 350.00, 'spa_assets/zabiegi_na_twarz.webp', '{\"type\": \"spa_treatment\", \"duration_minutes\": 75, \"focus_area\": \"face\"}', 1),
(14, 5, 'Pakiet \"Harmonia Zmysłów\"', 'Kompleksowy pakiet obejmujący 60-minutowy Masaż Relaksacyjny Klasyczny oraz Intensywnie Nawilżający Zabieg HydraBoost na twarz.', 500.00, 'spa_assets/welness.jpg', '{\"type\": \"spa_package\", \"duration_minutes\": 120, \"includes_treatments_ids\": [9, 12]}', 1),
(15, 5, 'Pakiet SPA Indywidualny', 'Placeholder dla indywidualnie komponowanych pakietów SPA przez klienta.', 30.00, 'spa_assets/welness.jpg', '{\"type\": \"custom_spa_package\", \"is_configurable\": true}', 1),
(16, 4, 'Bruschetta z pomidorami concasse i bazylią', 'Chrupiące pieczywo z aromatycznymi, świeżymi pomidorami, czosnkiem i bazylią.', 28.00, 'restaurant_assets/przystawki.jfif', '{\"type\": \"restaurant_dish\", \"course\": \"Przystawka\", \"allergens\": [\"gluten\"]}', 1),
(17, 4, 'Filet z łososia na szpinaku z sosem cytrynowym', 'Delikatny, pieczony filet z łososia norweskiego, podany na świeżym szpinaku z sosem maślano-cytrynowym i pieczonymi ziemniakami.', 65.00, 'restaurant_assets/dania_glowne.jfif', '{\"type\": \"restaurant_dish\", \"course\": \"Danie Główne\", \"allergens\": [\"ryby\", \"mleko\"]}', 1),
(18, 4, 'Curry warzywne z mlekiem kokosowym i ryżem jaśminowym (VEGAN)', 'Aromatyczne, wegańskie curry z sezonowymi warzywami, mlekiem kokosowym, trawą cytrynową i imbirem, podane z ryżem jaśminowym.', 48.00, 'restaurant_assets/vegan_vege.jfif', '{\"type\": \"restaurant_dish\", \"course\": \"Danie Główne\", \"dietary_info\": [\"Vegan\", \"Gluten-Free\"] }', 1),
(19, 4, 'Klasyczne włoskie Tiramisu', 'Pyszny, domowy deser na bazie biszkoptów nasączonych kawą, kremu mascarpone i kakao.', 28.00, 'restaurant_assets/desery.jfif', '{\"type\": \"restaurant_dish\", \"course\": \"Deser\", \"allergens\": [\"gluten\", \"jaja\", \"mleko\"]}', 1),
(20, 4, 'Koktajl \"AquaBlue Paradise\"', 'Autorski, orzeźwiający koktajl bezalkoholowy na bazie syropu blue curacao, soku ananasowego i limonki.', 18.00, 'restaurant_assets/napoje.jfif', '{\"type\": \"restaurant_drink\", \"category\": \"non_alcoholic_cocktail\"}', 1),
(21, 7, 'Oczyszczający Zabieg dla Skóry Problematycznej', 'Głęboko oczyszczający i normalizujący zabieg dla cery tłustej, mieszanej i trądzikowej.', 260.00, 'spa_assets/zabiegi_na_twarz.webp', '{\"type\": \"spa_treatment\", \"duration_minutes\": 60, \"focus_area\": \"face\"}', 1),
(22, 8, 'Aromatyczny Peeling Cukrowy Całego Ciała', 'Intensywnie wygładzający peeling cukrowy z naturalnymi olejkami, pozostawiający skórę jedwabiście gładką i nawilżoną.', 190.00, 'spa_assets/zabiegi_na_cialo.jpg', '{\"type\": \"spa_treatment\", \"duration_minutes\": 45, \"focus_area\": \"full_body\"}', 1),
(23, 8, 'Odżywczy Okład Czekoladowy', 'Luksusowy okład na ciało na bazie prawdziwej czekolady, który głęboko nawilża, odżywia i poprawia nastrój.', 330.00, 'spa_assets/zabiegi_na_cialo.jpg', '{\"type\": \"spa_treatment\", \"duration_minutes\": 75, \"focus_area\": \"full_body\"}', 1),
(24, 8, 'Detoksykujący Rytuał z Zieloną Herbatą', 'Kompleksowy rytuał oczyszczający z peelingiem, maską i masażem na bazie zielonej herbaty, wspomagający usuwanie toksyn.', 380.00, 'spa_assets/zabiegi_na_cialo.jpg', '{\"type\": \"spa_treatment\", \"duration_minutes\": 90, \"focus_area\": \"full_body\"}', 1),
(25, 5, 'Pakiet \"Królewski Relaks\" (Peeling + Okład + Masaż)', 'Kompletna regeneracja: peeling, odżywczy okład na ciało i relaksujący masaż.', 680.00, 'spa_assets/welness.jpg', '{\"type\": \"spa_package\", \"duration_minutes\": 150, \"includes_treatments_description\": \"Peeling, Okład, Masaż\"}', 1),
(26, 5, 'Romantyczny Rytuał dla Dwojga', 'Wyjątkowy pakiet dla par, obejmujący wspólny masaż i czas w strefie relaksu.', 850.00, 'spa_assets/welness.jpg', '{\"type\": \"spa_package\", \"duration_minutes\": 120, \"for_two\": true}', 1),
(28, 1, 'Rezerwacja Stolika w Restauracji', 'Rezerwacja stolika. Szczegóły w item_details zamówienia.', 0.00, NULL, '{\"type\": \"reservation_service\", \"area\": \"restaurant\"}', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rolepermissions`
--

CREATE TABLE `rolepermissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `rolepermissions`
--

INSERT INTO `rolepermissions` (`role_id`, `permission_id`) VALUES
(2, 7),
(2, 13),
(3, 4),
(3, 11),
(4, 5),
(4, 12),
(5, 6),
(5, 14),
(6, 1),
(6, 2),
(6, 4),
(6, 5),
(6, 6),
(6, 7),
(6, 8),
(6, 10),
(6, 15),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(7, 5),
(7, 6),
(7, 7),
(7, 8),
(7, 9),
(7, 10),
(7, 11),
(7, 12),
(7, 13),
(7, 14),
(7, 15);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Klient', 'Standardowy użytkownik dokonujący rezerwacji.'),
(2, 'Pracownik Aquapark', 'Pracownik obsługujący moduł Aquaparku.'),
(3, 'Pracownik Hotel', 'Pracownik obsługujący moduł Hotelu.'),
(4, 'Pracownik SPA', 'Pracownik obsługujący moduł SPA.'),
(5, 'Pracownik Restauracja', 'Pracownik obsługujący moduł Restauracji.'),
(6, 'Administrator', 'Pracownik z rozszerzonymi uprawnieniami zarządzania.'),
(7, 'SuperAdmin', 'Główny administrator systemu z pełnym dostępem.'),
(8, 'Recepcjonista', 'Pracownik obsługujący recepcję główną i hotelową.'),
(9, 'Kierownik Zmiany Aquapark', 'Osoba odpowiedzialna za nadzór nad pracą Aquaparku na danej zmianie.'),
(10, 'Specjalista ds. Marketingu', 'Osoba odpowiedzialna za działania marketingowe kompleksu.'),
(11, 'Pracownik Techniczny', 'Osoba odpowiedzialna za utrzymanie techniczne obiektów.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `salaries`
--

CREATE TABLE `salaries` (
  `salary_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'ID pracownika (z tabeli users)',
  `amount` decimal(10,2) NOT NULL COMMENT 'Kwota wynagrodzenia',
  `pay_frequency` enum('miesięcznie','godzinowo','projektowo','jednorazowo') NOT NULL DEFAULT 'miesięcznie' COMMENT 'Częstotliwość wypłaty',
  `effective_date` date NOT NULL COMMENT 'Data, od której obowiązuje wynagrodzenie',
  `contract_type` varchar(100) DEFAULT NULL COMMENT 'Typ umowy (np. UoP, B2B, Zlecenie)',
  `notes` text DEFAULT NULL COMMENT 'Dodatkowe uwagi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci COMMENT='Tabela przechowująca informacje o wynagrodzeniach pracowników';

--
-- Dumping data for table `salaries`
--

INSERT INTO `salaries` (`salary_id`, `user_id`, `amount`, `pay_frequency`, `effective_date`, `contract_type`, `notes`, `created_at`, `updated_at`) VALUES
(1, 8, 4500.00, 'miesięcznie', '2025-06-01', 'Umowa o pracę', 'Wynagrodzenie zasadnicze', '2025-05-25 16:39:51', '2025-05-25 16:39:51'),
(2, 9, 6200.00, 'miesięcznie', '2025-06-01', 'Umowa o pracę', 'Wynagrodzenie kierownicze', '2025-05-25 16:39:51', '2025-05-25 16:39:51'),
(3, 10, 5800.00, 'miesięcznie', '2025-06-01', 'Umowa o pracę', 'Specjalista ds. marketingu, dział promocji', '2025-05-25 16:39:51', '2025-05-25 16:39:51'),
(4, 11, 5100.00, 'miesięcznie', '2025-05-20', 'Umowa zlecenie', 'Stawka początkowa, dział techniczny', '2025-05-25 16:39:51', '2025-05-25 16:39:51'),
(5, 8, 4500.00, 'miesięcznie', '2025-06-01', 'Umowa o pracę', 'Wynagrodzenie zasadnicze dla Recepcjonistki', '2025-05-25 16:40:49', '2025-05-25 16:40:49'),
(6, 9, 6200.00, 'miesięcznie', '2025-06-01', 'Umowa o pracę', 'Wynagrodzenie dla Kierownika Zmiany Aquapark', '2025-05-25 16:40:49', '2025-05-25 16:40:49'),
(7, 10, 5800.00, 'miesięcznie', '2025-06-01', 'Umowa o pracę', 'Wynagrodzenie dla Specjalisty ds. Marketingu', '2025-05-25 16:40:49', '2025-05-25 16:40:49'),
(8, 11, 5100.00, 'miesięcznie', '2025-05-20', 'Umowa zlecenie', 'Stawka początkowa dla Pracownika Technicznego', '2025-05-25 16:40:49', '2025-05-25 16:40:49');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `is_vip_customer` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `role_id`, `is_vip_customer`, `created_at`, `last_login`) VALUES
(1, 'Jan', 'Kowalski', 'jan.kowalski@example.com', 'Kowalski123', '123456789', 1, 0, '2025-05-18 14:07:58', '2025-05-18 17:51:17'),
(2, 'Anna', 'Nowak', 'anna.nowak@example.com', 'Nowak456', '987654321', 1, 1, '2025-05-18 14:07:58', '2025-05-21 18:39:16'),
(3, 'Piotr', 'Zieliński', 'piotr.zielinski@aquaparadise.pl', 'Piotrek789', '555111222', 6, 0, '2025-05-18 14:07:58', '2025-05-18 17:50:15'),
(4, 'Ewa', 'Lewandowska', 'ewa.lewandowska@aquaparadise.pl', 'EwaHot012', '555333444', 3, 0, '2025-05-18 14:07:58', '2025-05-18 17:51:50'),
(5, 'Krzysztof', 'Adminowski', 'superadmin@aquaparadise.pl', 'SuperAdminHaslo', '555000111', 7, 0, '2025-05-18 14:07:58', '2025-05-26 15:52:40'),
(6, 'Maniek', 'Wielki', 'wielkimaniek@gmail.com', 'Maniek111', '927957284', 1, 0, '2025-05-18 14:36:00', '2025-05-26 17:09:40'),
(7, 'Wikor', 'Tak', 'wiktor@gmail.com', 'balwan123', NULL, 1, 0, '2025-05-21 18:38:06', '2025-05-23 10:31:09'),
(8, 'Zofia', 'Bąk', 'zofia.bak@aquaparadise.pl', 'Zofia123', '555123001', 8, 0, '2025-05-25 16:39:51', NULL),
(9, 'Marek', 'Cichy', 'marek.cichy@aquaparadise.pl', 'Marek123', '555123002', 9, 0, '2025-05-25 16:39:51', NULL),
(10, 'Patrycja', 'Duda', 'patrycja.duda@aquaparadise.pl', 'Patrycja123', '555123003', 10, 0, '2025-05-25 16:39:51', NULL),
(11, 'Grzegorz', 'Wilk', 'grzegorz.wilk@aquaparadise.pl', 'Grzes123', '555123004', 11, 0, '2025-05-25 16:39:51', '2025-05-26 17:16:28'),
(12, 'Bogdan', 'Wielki', 'bogdanwielki@gmail.com', 'bogus123', '101101101', 1, 0, '2025-05-26 15:34:18', NULL);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `order_item_id` (`order_item_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `cartitems`
--
ALTER TABLE `cartitems`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeksy dla tabeli `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `user_id_unique` (`user_id`),
  ADD UNIQUE KEY `session_id_unique` (`session_id`);

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeksy dla tabeli `opinions`
--
ALTER TABLE `opinions`
  ADD PRIMARY KEY (`opinion_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeksy dla tabeli `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_resets_token` (`token`),
  ADD KEY `idx_password_resets_email` (`email`);

--
-- Indeksy dla tabeli `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeksy dla tabeli `rolepermissions`
--
ALTER TABLE `rolepermissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indeksy dla tabeli `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indeksy dla tabeli `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`salary_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cartitems`
--
ALTER TABLE `cartitems`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `opinions`
--
ALTER TABLE `opinions`
  MODIFY `opinion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `salary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `orderitems` (`order_item_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `cartitems`
--
ALTER TABLE `cartitems`
  ADD CONSTRAINT `cartitems_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cartitems_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `opinions`
--
ALTER TABLE `opinions`
  ADD CONSTRAINT `opinions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `opinions_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE SET NULL;

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `rolepermissions`
--
ALTER TABLE `rolepermissions`
  ADD CONSTRAINT `rolepermissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rolepermissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `salaries`
--
ALTER TABLE `salaries`
  ADD CONSTRAINT `salaries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
