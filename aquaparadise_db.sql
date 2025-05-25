-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 18, 2025 at 07:54 PM
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
(1, 1, 1, 2, 90.00, NULL, '2025-05-18 14:36:59'),
(2, 1, 5, 1, 450.00, '{\"booking_email\":\"wielkimaniek@gmail.com\",\"booking_name\":\"Maniek Wielki\",\"booking_phone\":\"927957284\",\"check_in_date\":\"2025-05-20\",\"check_out_date\":\"2025-05-21\",\"notes\":\"Prosze czarn\\u0105 po\\u015bciel\",\"num_guests\":\"1\",\"reservation_type\":\"hotel_room\"}', '2025-05-18 14:41:24'),
(3, 1, 15, 1, 0.00, '{\"booking_email\":\"wielkimaniek@gmail.com\",\"booking_name\":\"Maniek Wielki\",\"booking_phone\":\"927957284\",\"notes\":\"Oby syn\",\"reservation_type\":\"spa_booking\",\"selected_treatments_ids_string\":\"12\",\"treatment_date\":\"2025-05-28\",\"treatment_time\":\"15:00\"}', '2025-05-18 15:54:53');

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
(1, 6, NULL, '2025-05-18 14:36:14', '2025-05-18 14:36:14'),
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
(13, NULL, 'p2aoj07qhdedvdreqhjb16u6l3', '2025-05-18 17:52:26', '2025-05-18 17:52:26');

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
(3, NULL, NULL, 17, 5, 'Pyszny łosoś, najlepszy jaki jadłem! Idealnie wypieczony, sos rewelacyjny. Obsługa w restauracji również na wysokim poziomie.', 'Oczekująca', '2025-05-18 14:07:58', NULL),
(4, 1, NULL, 1, 3, 'Aquapark jest duży, ale w weekendy straszne tłumy. Na niektóre zjeżdżalnie trzeba było długo czekać. Czystość OK.', 'Zaakceptowana', '2025-05-18 14:07:58', 'Dziękujemy za opinię. Staramy się zarządzać przepływem gości, szczególnie w popularne dni. Zapraszamy w tygodniu dla większego komfortu.');

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

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_name` varchar(200) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Oczekujące',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'Nieopłacone',
  `payment_transaction_id` varchar(255) DEFAULT NULL,
  `promo_code_used` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

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
(14, 'access_vip_employee_module_restaurant', 'Dostęp do modułu VIP pracownika restauracji');

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
(26, 5, 'Romantyczny Rytuał dla Dwojga', 'Wyjątkowy pakiet dla par, obejmujący wspólny masaż i czas w strefie relaksu.', 850.00, 'spa_assets/welness.jpg', '{\"type\": \"spa_package\", \"duration_minutes\": 120, \"for_two\": true}', 1);

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
(7, 14);

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
(7, 'SuperAdmin', 'Główny administrator systemu z pełnym dostępem.');

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
(2, 'Anna', 'Nowak', 'anna.nowak@example.com', 'Nowak456', '987654321', 1, 1, '2025-05-18 14:07:58', '2025-05-18 17:49:38'),
(3, 'Piotr', 'Zieliński', 'piotr.zielinski@aquaparadise.pl', 'Piotrek789', '555111222', 6, 0, '2025-05-18 14:07:58', '2025-05-18 17:50:15'),
(4, 'Ewa', 'Lewandowska', 'ewa.lewandowska@aquaparadise.pl', 'EwaHot012', '555333444', 3, 0, '2025-05-18 14:07:58', '2025-05-18 17:51:50'),
(5, 'Krzysztof', 'Adminowski', 'superadmin@aquaparadise.pl', 'SuperAdminHaslo', '555000111', 7, 0, '2025-05-18 14:07:58', '2025-05-18 17:52:18'),
(6, 'Maniek', 'Wielki', 'wielkimaniek@gmail.com', 'Maniek111', '927957284', 1, 0, '2025-05-18 14:36:00', '2025-05-18 15:47:26');

--
-- Indeksy dla zrzutów tabel
--

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
-- AUTO_INCREMENT for table `cartitems`
--
ALTER TABLE `cartitems`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `opinions`
--
ALTER TABLE `opinions`
  MODIFY `opinion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

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
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*TAK*/;
