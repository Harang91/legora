-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Jan 11. 23:17
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `legora`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `listing_id`, `quantity`, `added_at`) VALUES
(1, 8, 4, 1, '2025-12-15 09:10:00'),
(2, 12, 6, 3, '2025-12-15 10:22:00'),
(3, 5, 11, 1, '2025-12-15 11:05:00'),
(4, 14, 1, 1, '2025-12-15 12:40:00'),
(5, 21, 8, 2, '2025-12-15 13:55:00'),
(6, 9, 15, 1, '2025-12-15 14:30:00'),
(7, 11, 19, 1, '2025-12-15 15:12:00'),
(8, 13, 5, 1, '2025-12-15 16:00:00'),
(9, 17, 10, 4, '2025-12-15 16:45:00'),
(10, 22, 12, 1, '2025-12-15 17:20:00'),
(11, 7, 3, 1, '2025-12-15 18:05:00'),
(12, 18, 14, 1, '2025-12-15 18:40:00'),
(13, 23, 20, 2, '2025-12-15 19:10:00'),
(14, 27, 9, 1, '2025-12-15 19:55:00'),
(15, 31, 7, 1, '2025-12-15 20:30:00');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `colors`
--

CREATE TABLE `colors` (
  `id` int(4) NOT NULL,
  `name` varchar(40) NOT NULL,
  `rgb` char(6) NOT NULL,
  `is_trans` tinyint(1) NOT NULL,
  `num_parts` int(11) DEFAULT NULL,
  `num_sets` int(11) DEFAULT NULL,
  `y1` int(11) DEFAULT NULL,
  `y2` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `colors`
--

INSERT INTO `colors` (`id`, `name`, `rgb`, `is_trans`, `num_parts`, `num_sets`, `y1`, `y2`) VALUES
(1, 'White', 'FFFFFF', 0, 50000, 12000, 1950, 2026),
(2, 'Grey', '9BA19D', 0, 20000, 7000, 1950, 2003),
(4, 'Orange', 'F07F13', 0, 8000, 2000, 1998, 2026),
(5, 'Red', 'C91A09', 0, 42000, 11000, 1950, 2026),
(7, 'Blue', '0055BF', 0, 25000, 6000, 1950, 2026),
(11, 'Black', '000000', 0, 60000, 15000, 1950, 2026),
(14, 'Yellow', 'F2CD37', 0, 22000, 5000, 1950, 2026),
(15, 'Green', '237841', 0, 15000, 4000, 1950, 2026),
(33, 'Trans-Clear', 'FFFFFF', 1, 12000, 3000, 1950, 2026),
(34, 'Lime', 'BBE90B', 0, 9000, 2500, 2000, 2026),
(36, 'Bright Green', '4B9F4A', 0, 7000, 2000, 2003, 2026),
(44, 'Trans-Red', 'C91A09', 1, 8000, 2000, 1980, 2026),
(46, 'Trans-Light Blue', 'AEEFEC', 1, 9000, 2500, 1980, 2026),
(85, 'Dark Bluish Gray', '6D6E5C', 0, 30000, 8500, 2004, 2026),
(86, 'Light Bluish Gray', 'A0A5A9', 0, 35000, 9000, 2004, 2026);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `elements`
--

CREATE TABLE `elements` (
  `element_id` varchar(20) NOT NULL,
  `part_num` varchar(20) NOT NULL,
  `color_id` int(11) NOT NULL,
  `design_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `elements`
--

INSERT INTO `elements` (`element_id`, `part_num`, `color_id`, `design_id`) VALUES
('300101', '3001', 1, NULL),
('300105', '3001', 5, NULL),
('300111', '3001', 11, NULL),
('300185', '3001', 85, NULL),
('300186', '3001', 86, NULL),
('300207', '3002', 7, NULL),
('300211', '3002', 11, NULL),
('300214', '3002', 14, NULL),
('302301', '3023', 1, NULL),
('302305', '3023', 5, NULL),
('302311', '3023', 11, NULL),
('302386', '3023', 86, NULL),
('302444', '3024', 44, NULL),
('3069b46', '3069b', 46, NULL),
('3070b33', '3070b', 33, NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `inventories`
--

CREATE TABLE `inventories` (
  `id` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `set_num` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `inventories`
--

INSERT INTO `inventories` (`id`, `version`, `set_num`) VALUES
(1, 1, '21330'),
(2, 1, '10305'),
(3, 1, '10294'),
(4, 1, '75192'),
(5, 1, '75313'),
(6, 1, '42143'),
(7, 1, '42115'),
(8, 1, '71741'),
(9, 1, '70620'),
(10, 1, '10276'),
(11, 1, '21318'),
(12, 1, '21322'),
(13, 1, '10295'),
(14, 1, '10300'),
(15, 1, '43202');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `inventory_minifigs`
--

CREATE TABLE `inventory_minifigs` (
  `inventory_id` int(11) NOT NULL,
  `fig_num` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `inventory_minifigs`
--

INSERT INTO `inventory_minifigs` (`inventory_id`, `fig_num`, `quantity`) VALUES
(1, 'fig-014190', 1),
(1, 'fig-014199', 1),
(2, 'cty0001', 2),
(3, 'cty0002', 1),
(4, 'fig-014187', 4),
(5, 'fig-014188', 1),
(6, 'fig-012902', 1),
(7, 'fig-012900', 1),
(8, 'fig-003085', 1),
(9, 'fig-006167', 1),
(10, 'fig-006183', 1),
(11, 'fig-015285', 1),
(12, 'fig-014192', 1),
(13, 'fig-014202', 1),
(14, 'fig-014203', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `inventory_parts`
--

CREATE TABLE `inventory_parts` (
  `inventory_id` int(11) NOT NULL,
  `part_num` varchar(20) NOT NULL,
  `color_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `is_spare` tinyint(1) NOT NULL,
  `img_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `inventory_parts`
--

INSERT INTO `inventory_parts` (`inventory_id`, `part_num`, `color_id`, `quantity`, `is_spare`, `img_url`) VALUES
(1, '3001', 5, 12, 0, ''),
(1, '3023', 1, 20, 0, NULL),
(2, '3003', 11, 30, 0, NULL),
(3, '3004', 86, 50, 0, NULL),
(4, '3001', 85, 100, 0, NULL),
(5, '3020', 1, 40, 0, NULL),
(6, '3021', 7, 25, 0, NULL),
(7, '3022', 14, 18, 0, NULL),
(8, '3023', 33, 10, 0, NULL),
(9, '3024', 46, 15, 0, NULL),
(10, '3710', 1, 60, 0, NULL),
(11, '3622', 5, 22, 0, NULL),
(12, '3623', 11, 33, 0, NULL),
(13, '3069b', 1, 12, 0, 'https://cdn.rebrickable.com/media/parts/elements/306901.jpg'),
(14, '3070b', 86, 14, 0, NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `inventory_sets`
--

CREATE TABLE `inventory_sets` (
  `inventory_id` int(11) NOT NULL,
  `set_num` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `inventory_sets`
--

INSERT INTO `inventory_sets` (`inventory_id`, `set_num`, `quantity`) VALUES
(1, '21330', 1),
(2, '10305', 1),
(3, '10294', 1),
(4, '75192', 1),
(5, '75313', 1),
(6, '42143', 1),
(7, '42115', 1),
(8, '71741', 1),
(9, '70620', 1),
(10, '10276', 1),
(11, '21318', 1),
(12, '21322', 1),
(13, '10295', 1),
(14, '10300', 1),
(15, '43202', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `listings`
--

CREATE TABLE `listings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_type` enum('set','part','minifig') NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `item_condition` enum('new','used') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `custom_image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `listings`
--

INSERT INTO `listings` (`id`, `user_id`, `item_type`, `item_id`, `item_name`, `quantity`, `price`, `item_condition`, `description`, `created_at`, `deleted_at`, `custom_image_url`) VALUES
(1, 8, 'set', '21330', 'Home Alone – Reszkessetek, betörők!', 1, 89990.00, 'used', 'Hiánytalan, doboz nélkül. Egyszer összerakva, vitrinben tartva.', '2026-01-11 21:10:00', NULL, NULL),
(2, 12, 'set', '10305', 'Lion Knights’ Castle – Lovagvár', 1, 149990.00, 'new', 'Bontatlan, LEGO Store vásárlás. Gyűjtői állapot.', '2026-01-11 21:10:00', NULL, NULL),
(3, 5, 'set', '75313', 'AT-AT UCS', 1, 259990.00, 'used', 'Tökéletes állapot, összerakva egyszer. Minifigek megvannak.', '2026-01-11 21:10:00', NULL, NULL),
(4, 14, 'set', '71741', 'Ninjago City Gardens', 1, 129990.00, 'used', 'Hiánytalan, eredeti doboz és útmutató jár hozzá.', '2026-01-11 21:10:00', NULL, NULL),
(5, 21, 'set', '43202', 'The Madrigal House (Encanto)', 1, 17990.00, 'new', 'Bontatlan, ajándékba kaptuk, de duplán van.', '2026-01-11 21:10:00', NULL, NULL),
(6, 9, 'part', '3001', 'Brick 2 x 4 – piros', 50, 40.00, 'used', 'Vegyes állapot, de mind ép és tiszta.', '2026-01-11 21:10:00', NULL, NULL),
(7, 11, 'part', '3023', 'Plate 1 x 2 – fehér', 100, 25.00, 'new', 'Újak, válogatásból maradtak.', '2026-01-11 21:10:00', NULL, NULL),
(8, 13, 'part', '3069b', 'Tile 1 x 2 with Groove – fekete', 30, 55.00, 'used', 'Szépek, karcmentesek.', '2026-01-11 21:10:00', NULL, NULL),
(9, 17, 'part', '3710', 'Plate 1 x 4 – szürke', 40, 30.00, 'used', 'Normál használt állapot.', '2026-01-11 21:10:00', NULL, NULL),
(10, 22, 'part', '3024', 'Plate 1 x 1 – lime', 80, 20.00, 'new', 'Újak, sortingból maradtak.', '2026-01-11 21:10:00', NULL, NULL),
(11, 3, 'minifig', 'fig-014199', 'Luke Skywalker', 1, 2990.00, 'used', 'Szép állapot, eredeti fej és haj.', '2026-01-11 21:10:00', NULL, NULL),
(12, 4, 'minifig', 'fig-014187', 'Stormtrooper', 2, 2490.00, 'used', 'Két darab, mindkettő jó állapotú.', '2026-01-11 21:10:00', NULL, NULL),
(13, 7, 'minifig', 'fig-003085', 'Harry Potter', 1, 3490.00, 'new', 'Új, zacskós.', '2026-01-11 21:10:00', NULL, NULL),
(14, 10, 'minifig', 'fig-012902', 'Spider-Man', 1, 3990.00, 'used', 'Karcmentes, eredeti alkatrészekkel.', '2026-01-11 21:10:00', NULL, NULL),
(15, 16, 'minifig', 'fig-012900', 'Iron Man', 1, 4990.00, 'used', 'Sisak nyitható, festés szép.', '2026-01-11 21:10:00', NULL, NULL),
(16, 18, 'set', '10295', 'Porsche 911', 1, 59990.00, 'used', 'Hiánytalan, doboz nélkül. Nagyon szép állapot.', '2026-01-11 21:10:00', NULL, NULL),
(17, 19, 'part', '3003', 'Brick 2 x 2 – fekete', 60, 35.00, 'new', 'Újak, sortingból.', '2026-01-11 21:10:00', NULL, NULL),
(18, 23, 'minifig', 'fig-015285', 'Obi-Wan Kenobi', 1, 3290.00, 'used', 'Szép állapot, eredeti köpeny nélkül.', '2026-01-11 21:10:00', NULL, NULL),
(19, 27, 'set', '21322', 'Pirates of Barracuda Bay', 1, 99990.00, 'used', 'Hiánytalan, eredeti doboz nélkül.', '2026-01-11 21:10:00', NULL, NULL),
(20, 31, 'part', '3020', 'Plate 2 x 4 – kék', 25, 45.00, 'used', 'Jó állapotú, építésekből maradt.', '2026-01-11 21:10:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `minifigs`
--

CREATE TABLE `minifigs` (
  `fig_num` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `num_parts` int(11) DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `minifigs`
--

INSERT INTO `minifigs` (`fig_num`, `name`, `num_parts`, `img_url`) VALUES
('cty0001', 'City Male', 4, NULL),
('cty0002', 'City Female', 4, NULL),
('fig-003085', 'Harry Potter', 6, 'https://cdn.rebrickable.com/media/sets/fig-003085.jpg'),
('fig-006167', 'Hermione Granger', 6, 'https://cdn.rebrickable.com/media/sets/fig-006167.jpg'),
('fig-006183', 'Ron Weasley', 6, 'https://cdn.rebrickable.com/media/sets/fig-006183.jpg'),
('fig-012900', 'Iron Man', 8, 'https://cdn.rebrickable.com/media/sets/fig-012900.jpg'),
('fig-012902', 'Spider-Man', 7, 'https://cdn.rebrickable.com/media/sets/fig-012902.jpg'),
('fig-014187', 'Stormtrooper', 6, 'https://cdn.rebrickable.com/media/sets/fig-014187.jpg'),
('fig-014188', 'Boba Fett', 9, 'https://cdn.rebrickable.com/media/sets/fig-014188.jpg'),
('fig-014190', 'Darth Vader', 9, 'https://cdn.rebrickable.com/media/sets/fig-014190.jpg'),
('fig-014192', 'Yoda', 5, 'https://cdn.rebrickable.com/media/sets/fig-014192.jpg'),
('fig-014199', 'Luke Skywalker', 7, 'https://cdn.rebrickable.com/media/sets/fig-014199.jpg'),
('fig-014202', 'Han Solo', 7, 'https://cdn.rebrickable.com/media/sets/fig-014202.jpg'),
('fig-014203', 'Princess Leia', 8, 'https://cdn.rebrickable.com/media/sets/fig-014203.jpg'),
('fig-015285', 'Obi-Wan Kenobi', 7, 'https://cdn.rebrickable.com/media/sets/fig-015285.jpg');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','completed') DEFAULT 'pending',
  `ordered_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `orders`
--

INSERT INTO `orders` (`id`, `buyer_id`, `seller_id`, `total_price`, `status`, `ordered_at`) VALUES
(11, 9, 8, 17990.00, 'completed', '2025-12-10 09:20:00'),
(12, 12, 14, 40.00, 'completed', '2025-12-10 14:10:00'),
(13, 5, 21, 55.00, 'completed', '2025-12-11 10:05:00'),
(14, 17, 11, 3490.00, 'completed', '2025-12-11 16:30:00'),
(15, 22, 7, 4990.00, 'completed', '2025-12-12 11:45:00'),
(16, 13, 18, 99990.00, 'completed', '2025-12-12 15:00:00'),
(17, 31, 23, 25.00, 'completed', '2025-12-13 09:50:00'),
(18, 27, 19, 129990.00, 'completed', '2025-12-13 13:40:00'),
(19, 32, 10, 30.00, 'completed', '2025-12-14 08:55:00'),
(20, 16, 12, 45.00, 'completed', '2025-12-14 17:20:00');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_order` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `listing_id`, `quantity`, `price_at_order`) VALUES
(11, 11, 5, 1, 17990.00),
(12, 12, 6, 1, 40.00),
(13, 13, 8, 1, 55.00),
(14, 14, 13, 1, 3490.00),
(15, 15, 15, 1, 4990.00),
(16, 16, 19, 1, 99990.00),
(17, 17, 10, 1, 25.00),
(18, 18, 4, 1, 129990.00),
(19, 19, 9, 1, 30.00),
(20, 20, 20, 1, 45.00);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `old_status` enum('pending','paid','shipped','completed','cancelled','refunded') DEFAULT NULL,
  `new_status` enum('pending','paid','shipped','completed','cancelled','refunded') NOT NULL,
  `changed_by` int(11) NOT NULL,
  `changed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `old_status`, `new_status`, `changed_by`, `changed_at`) VALUES
(31, 11, 'pending', 'paid', 9, '2025-12-10 09:25:00'),
(32, 11, 'paid', 'shipped', 8, '2025-12-10 12:00:00'),
(33, 11, 'shipped', 'completed', 9, '2025-12-12 09:00:00'),
(34, 12, 'pending', 'paid', 12, '2025-12-10 14:15:00'),
(35, 12, 'paid', 'shipped', 14, '2025-12-10 16:00:00'),
(36, 12, 'shipped', 'completed', 12, '2025-12-11 10:00:00'),
(37, 13, 'pending', 'paid', 5, '2025-12-11 10:10:00'),
(38, 13, 'paid', 'shipped', 21, '2025-12-11 13:00:00'),
(39, 13, 'shipped', 'completed', 5, '2025-12-12 08:00:00'),
(40, 14, 'pending', 'paid', 17, '2025-12-11 16:35:00'),
(41, 14, 'paid', 'shipped', 11, '2025-12-11 18:00:00'),
(42, 14, 'shipped', 'completed', 17, '2025-12-12 12:00:00'),
(43, 15, 'pending', 'paid', 22, '2025-12-12 11:50:00'),
(44, 15, 'paid', 'shipped', 7, '2025-12-12 14:00:00'),
(45, 15, 'shipped', 'completed', 22, '2025-12-13 09:00:00'),
(46, 16, 'pending', 'paid', 13, '2025-12-12 15:05:00'),
(47, 16, 'paid', 'shipped', 18, '2025-12-12 17:00:00'),
(48, 16, 'shipped', 'completed', 13, '2025-12-14 10:00:00'),
(49, 17, 'pending', 'paid', 31, '2025-12-13 09:55:00'),
(50, 17, 'paid', 'shipped', 23, '2025-12-13 12:00:00'),
(51, 17, 'shipped', 'completed', 31, '2025-12-14 08:00:00'),
(52, 18, 'pending', 'paid', 27, '2025-12-13 13:45:00'),
(53, 18, 'paid', 'shipped', 19, '2025-12-13 16:00:00'),
(54, 18, 'shipped', 'completed', 27, '2025-12-15 09:00:00'),
(55, 19, 'pending', 'paid', 32, '2025-12-14 09:00:00'),
(56, 19, 'paid', 'shipped', 10, '2025-12-14 11:00:00'),
(57, 19, 'shipped', 'completed', 32, '2025-12-15 10:00:00'),
(58, 20, 'pending', 'paid', 16, '2025-12-14 17:25:00'),
(59, 20, 'paid', 'shipped', 12, '2025-12-14 19:00:00'),
(60, 20, 'shipped', 'completed', 16, '2025-12-16 09:00:00');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `parts`
--

CREATE TABLE `parts` (
  `part_num` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `part_cat_id` int(11) DEFAULT NULL,
  `part_material` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `parts`
--

INSERT INTO `parts` (`part_num`, `name`, `part_cat_id`, `part_material`) VALUES
('3001', 'Brick 2 x 4', 1, 'ABS'),
('3002', 'Brick 2 x 3', 1, 'ABS'),
('3003', 'Brick 2 x 2', 1, 'ABS'),
('3004', 'Brick 1 x 2', 1, 'ABS'),
('3005', 'Brick 1 x 1', 1, 'ABS'),
('3020', 'Plate 2 x 4', 2, 'ABS'),
('3021', 'Plate 2 x 3', 2, 'ABS'),
('3022', 'Plate 2 x 2', 2, 'ABS'),
('3023', 'Plate 1 x 2', 2, 'ABS'),
('3024', 'Plate 1 x 1', 2, 'ABS'),
('3069b', 'Tile 1 x 2 with Groove', 3, 'ABS'),
('3070b', 'Tile 1 x 1 with Groove', 3, 'ABS'),
('3622', 'Brick 1 x 3', 1, 'ABS'),
('3623', 'Plate 1 x 3', 2, 'ABS'),
('3710', 'Plate 1 x 4', 2, 'ABS');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `part_categories`
--

CREATE TABLE `part_categories` (
  `id` int(2) NOT NULL,
  `name` varchar(44) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- A tábla adatainak kiíratása `part_categories`
--

INSERT INTO `part_categories` (`id`, `name`) VALUES
(1, 'Bricks'),
(2, 'Plates'),
(3, 'Tiles'),
(4, 'Minifig Heads'),
(5, 'Minifig Torsos'),
(6, 'Minifig Legs'),
(7, 'Wheels'),
(8, 'Technic Pins'),
(9, 'Technic Beams'),
(10, 'Windows'),
(11, 'Doors'),
(12, 'Slopes'),
(13, 'Curved Slopes'),
(14, 'Animals'),
(15, 'Plants');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `part_relationships`
--

CREATE TABLE `part_relationships` (
  `rel_type` char(1) NOT NULL,
  `child_part_num` varchar(20) NOT NULL,
  `parent_part_num` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `part_relationships`
--

INSERT INTO `part_relationships` (`rel_type`, `child_part_num`, `parent_part_num`) VALUES
('M', '3001', '3001'),
('A', '3002', '3001'),
('A', '3003', '3001'),
('M', '3004', '3004'),
('A', '3005', '3004'),
('M', '3023', '3023'),
('A', '3024', '3023'),
('M', '3022', '3022'),
('A', '3021', '3022'),
('M', '3020', '3020'),
('A', '3069b', '3069b'),
('A', '3070b', '3069b'),
('M', '3622', '3622'),
('A', '3623', '3622'),
('A', '3710', '3023');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `rater_id` int(11) NOT NULL,
  `rated_user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `rated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `ratings`
--

INSERT INTO `ratings` (`id`, `rater_id`, `rated_user_id`, `rating`, `comment`, `rated_at`) VALUES
(1, 8, 12, 5, 'Gyors, pontos eladó. A készlet hibátlan volt.', '2025-11-20 10:12:00'),
(2, 12, 8, 5, 'Nagyon kedves vevő, minden rendben ment.', '2025-11-21 14:33:00'),
(3, 5, 14, 4, 'A termék jó állapotú volt, de a csomagolás lehetett volna jobb.', '2025-11-22 09:15:00'),
(4, 14, 5, 5, 'Korrekt, megbízható eladó. Ajánlom.', '2025-11-22 18:40:00'),
(5, 21, 9, 5, 'Villámgyors átvétel, minden rendben.', '2025-11-23 11:05:00'),
(6, 9, 21, 4, 'A kommunikáció jó volt, de a futár késett.', '2025-11-23 16:22:00'),
(7, 11, 3, 5, 'Szuper vevő, minden a megbeszéltek szerint zajlott.', '2025-11-24 08:50:00'),
(8, 3, 11, 5, 'Nagyon korrekt eladó, ajánlom mindenkinek.', '2025-11-24 12:10:00'),
(9, 13, 17, 4, 'A minifig jó állapotú volt, apró karcokkal.', '2025-11-25 09:44:00'),
(10, 17, 13, 5, 'Gyors és pontos, minden rendben.', '2025-11-25 15:30:00'),
(11, 22, 10, 5, 'Tökéletes állapotú alkatrészek, köszönöm!', '2025-11-26 10:05:00'),
(12, 10, 22, 4, 'Kicsit lassú válaszidő, de minden rendben volt.', '2025-11-26 17:12:00'),
(13, 7, 16, 5, 'Nagyon kedves eladó, minden szuper.', '2025-11-27 13:20:00'),
(14, 16, 7, 5, 'Gyors, pontos, megbízható.', '2025-11-27 19:45:00'),
(15, 18, 19, 3, 'A termék megfelelt, de a leírás lehetett volna részletesebb.', '2025-11-28 09:00:00'),
(16, 19, 18, 5, 'Nagyon korrekt vevő, ajánlom.', '2025-11-28 14:55:00'),
(17, 23, 27, 5, 'Gyors átvétel, minden rendben.', '2025-11-29 11:40:00'),
(18, 27, 23, 4, 'Kisebb karc volt a figurán, de összességében oké.', '2025-11-29 17:10:00'),
(19, 31, 32, 5, 'Kiváló kommunikáció, minden a megbeszéltek szerint.', '2025-11-30 10:22:00'),
(20, 32, 31, 5, 'Nagyon korrekt eladó, gyors postázás.', '2025-11-30 15:48:00');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `sets`
--

CREATE TABLE `sets` (
  `set_num` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `num_parts` int(11) DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `sets`
--

INSERT INTO `sets` (`set_num`, `name`, `year`, `theme_id`, `num_parts`, `img_url`) VALUES
('10276', 'Colosseum', 2020, 1, 9036, 'https://cdn.rebrickable.com/media/sets/10276-1.jpg'),
('10294', 'Titanic', 2021, 1, 9090, 'https://cdn.rebrickable.com/media/sets/10294-1.jpg'),
('10295', 'Porsche 911', 2021, 3, 1458, 'https://cdn.rebrickable.com/media/sets/10295-1.jpg'),
('10300', 'Back to the Future Time Machine', 2022, 1, 1872, 'https://cdn.rebrickable.com/media/sets/10300-1.jpg'),
('10305', 'Lion Knights’ Castle', 2022, 1, 4514, 'https://cdn.rebrickable.com/media/sets/10305-1.jpg'),
('21318', 'Tree House', 2019, 1, 3036, 'https://cdn.rebrickable.com/media/sets/21318-1.jpg'),
('21322', 'Pirates of Barracuda Bay', 2020, 1, 2545, 'https://cdn.rebrickable.com/media/sets/21322-1.jpg'),
('21330', 'Home Alone', 2021, 1, 3955, 'https://cdn.rebrickable.com/media/sets/21330-1.jpg'),
('42115', 'Lamborghini Sián FKP 37', 2020, 3, 3696, 'https://cdn.rebrickable.com/media/sets/42115-1.jpg'),
('42143', 'Ferrari Daytona SP3', 2022, 3, 3778, 'https://cdn.rebrickable.com/media/sets/42143-1.jpg'),
('43202', 'The Madrigal House', 2021, 5, 587, 'https://cdn.rebrickable.com/media/sets/43202-1.jpg'),
('70620', 'Ninjago City', 2017, 4, 4867, 'https://cdn.rebrickable.com/media/sets/70620-1.jpg'),
('71741', 'Ninjago City Gardens', 2021, 4, 5685, 'https://cdn.rebrickable.com/media/sets/71741-1.jpg'),
('75192', 'Millennium Falcon (UCS)', 2017, 2, 7541, 'https://cdn.rebrickable.com/media/sets/75192-1.jpg'),
('75313', 'AT-AT (UCS)', 2021, 2, 6785, 'https://cdn.rebrickable.com/media/sets/75313-1.jpg');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `themes`
--

INSERT INTO `themes` (`id`, `name`, `parent_id`) VALUES
(1, 'Icons', NULL),
(2, 'Star Wars', NULL),
(3, 'Technic', NULL),
(4, 'Ninjago', NULL),
(5, 'Disney', NULL),
(6, 'City', NULL),
(7, 'Harry Potter', NULL),
(8, 'Marvel Super Heroes', NULL),
(9, 'Creator Expert', NULL),
(10, 'Ideas', NULL),
(11, 'Architecture', NULL),
(12, 'Friends', NULL),
(13, 'Classic', NULL),
(14, 'Castle', NULL),
(15, 'Pirates', NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 0,
  `verify_token` varchar(64) DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`, `is_active`, `verify_token`, `role`, `address`, `phone`) VALUES
(1, 'tesztuser', 'tesztuser@example.com', '$2y$10$38h9diq8XPRwu2ryOxWOc.xEyfjaBQka10.gR7V7aVjOb8OUje2Lu', '2025-10-09 10:40:41', 1, NULL, 'user', NULL, NULL),
(2, '999_updated', 'newmail@example.com', '$2y$10$QF6cF0L2EbFBZC/XKnVNcOZayV.xAWldWG2KmMO3h.xoGE8DZyNcG', '2025-10-09 18:27:07', 1, NULL, 'user', NULL, NULL),
(3, 'tesztuser1', 'tesztuser1@example.com', '$2y$10$qgtDwH4h2AN74ckLFXsJJucmvMSBjU4tVBm7jPi8qfGNSO49lFQ9e', '2025-10-10 08:06:38', 0, 'b52d253f12b196cc10344cd09df6e5f9701d5eb870a3470a0ff47acde15112be', 'user', NULL, NULL),
(4, 'tesztuser2', 'tesztuser2@example.com', '$2y$10$qdUWtT6HJUInq/8ezAp26eL1sCQ7bpksVoPJp9V6pcHGUnbIQ9fkq', '2025-10-14 09:04:21', 0, 'a5d3174627866ffb8fd7dd0afaa4c616eb0e3dc44cccb8b6c7ea3243c7a1f452', 'user', NULL, NULL),
(5, 'tesztuser3', 'tesztuser3@example.com', '$2y$10$Ya1J3s7MKeEVIQiRQ.LQ4u2oDmaq0pm0XzOY/W6sPh7va5KuG1whO', '2025-10-14 09:04:37', 1, NULL, 'user', NULL, NULL),
(6, 'tesztuser4', 'tesztuser4@example.com', '$2y$10$7uYj/Xt0lXNiJnzKKxXdpu8x5gHj2RmLiEerqst1GaEXiLYgT0zIy', '2025-10-14 09:04:45', 1, NULL, 'user', NULL, NULL),
(7, 'tesztuser5', 'tesztuser5@example.com', '$2y$10$OLMHlJLk7f6BN6catDnZaunRspnRPY3LDVEpMor8spqQBDEqBBDpq', '2025-10-14 09:05:02', 1, NULL, 'user', NULL, NULL),
(8, 'user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:02:51', 1, NULL, 'user', '8200 Veszprém, Kossuth Lajos utca 3.', '+36 30 111 1001'),
(9, 'user2', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '1051 Budapest, Bajcsy-Zsilinszky út 12.', '+36 30 111 1002'),
(10, 'user3', 'user3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '4026 Debrecen, Piac utca 45.', '+36 30 111 1003'),
(11, 'user4', 'user4@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '6720 Szeged, Kárász utca 8.', '+36 30 111 1004'),
(12, 'user5', 'user5@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '9022 Győr, Baross Gábor út 15.', '+36 30 111 1005'),
(13, 'user6', 'user6@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '7400 Kaposvár, Fő utca 21.', '+36 30 111 1006'),
(14, 'user7', 'user7@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '8900 Zalaegerszeg, Kossuth utca 9.', '+36 30 111 1007'),
(15, 'user8', 'user8@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '6000 Kecskemét, Rákóczi út 33.', '+36 30 111 1008'),
(16, 'user9', 'user9@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '7100 Szekszárd, Arany János utca 4.', '+36 30 111 1009'),
(17, 'user10', 'user10@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '5000 Szolnok, Kossuth tér 1.', '+36 30 111 1010'),
(18, 'user11', 'user11@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '3300 Eger, Dobó István tér 5.', '+36 30 111 1011'),
(19, 'user12', 'user12@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '8000 Székesfehérvár, Fő utca 7.', '+36 30 111 1012'),
(20, 'user13', 'user13@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '4400 Nyíregyháza, Kossuth tér 2.', '+36 30 111 1013'),
(21, 'user14', 'user14@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '9700 Szombathely, Fő tér 10.', '+36 30 111 1014'),
(22, 'user15', 'user15@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '2600 Vác, Március 15. tér 1.', '+36 30 111 1015'),
(23, 'user16', 'user16@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '2800 Tatabánya, Fő tér 6.', '+36 30 111 1016'),
(24, 'user17', 'user17@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '3525 Miskolc, Széchenyi utca 99.', '+36 30 111 1017'),
(25, 'user18', 'user18@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '9700 Szombathely, Kossuth Lajos utca 11.', '+36 30 111 1018'),
(26, 'user19', 'user19@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '8800 Nagykanizsa, Erzsébet tér 2.', '+36 30 111 1019'),
(27, 'user20', 'user20@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '7400 Kaposvár, Ady Endre utca 14.', '+36 30 111 1020'),
(28, 'user21', 'user21@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '5600 Békéscsaba, Andrássy út 3.', '+36 30 111 1021'),
(29, 'user22', 'user22@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-12 11:04:48', 1, NULL, 'user', '6500 Baja, Dózsa György út 9.', '+36 30 111 1022'),
(30, 'fetest', 'fetest@gmail.com', '$2y$10$UlWJy93SYlq8IkyoYcKWeOzvfEL.57Ccp95p1NAfGkiSlACaTFLmW', '2025-11-15 18:24:58', 0, '4fb8eb9c9b5f771c5a08b1812bc8e00893bbafa12b355cd4daa788a855215aed', 'user', NULL, NULL),
(31, '12345test', '12345test@test.com', '$2y$10$nwz0TUTr/02IREJcCP6Oauk1p8cPgkTpMTcA3f0a6M54dr3.E.PYS', '2025-11-15 18:28:27', 1, NULL, 'user', NULL, NULL),
(32, 'dodo', 'dodo@legora.com', '$2y$10$PU9FXjnE8O9l6xHmmeGltui3AItAxJrASVC.B36HEk7fNn7Qi/3VO', '2025-11-15 19:00:37', 1, NULL, 'user', NULL, NULL),
(33, 'newtestuser1', 'newtestuser1@examlpe.com', '$2y$10$DfgnelDS7QWO3bFTVvWSNO9eLHr3lMKqy.7fngVQm3ZKwEO18Fpoy', '2025-12-05 12:21:50', 1, NULL, 'admin', 'Veszprém', '06301234567'),
(34, 'bela', 'bela@testuser.hu', '$2y$10$TJ86PIoo8Y2AVyHgnps41.7yt1ra1PJlcEpF2C9B7S490yZyN/NPS', '2025-12-07 21:10:43', 0, 'c8960df4a6fa7069cdd229c9b0922cae7e088aa709ec6bd7267f83e613d47d41', 'user', NULL, NULL),
(35, 'andras', 'andras@testuser.com', '$2y$10$aw77eUY5fIqmqeJi4sTV4OYCjIF8oluP0hUESws1ggDMSudDd6qKm', '2025-12-07 21:13:02', 0, '551cbcfb1e978fdfa16485a2cde48f3a0b75d2461b6aa983309194fc1d088ad0', 'user', NULL, NULL),
(36, 'anti', 'anti@testuser.com', '$2y$10$YCZaep7GdNfELICVBKeTFOHYtrxwVoGiN/jJkVGj5IpqE9qdea.3m', '2025-12-07 21:16:33', 0, '75d97ad21dc0923a4b720d36cff68a5b11205aaad62010796861f3ac2105f5d1', 'user', NULL, NULL),
(37, 'bela2', 'bela2@testuser.hu', '$2y$10$KXsgtI7XQtT29BB8C8pYT.LB3xpHiOTZcZBntYbpHZO.vzd3lJHay', '2025-12-07 21:17:54', 0, '86d17e63a2bd2f2d76ec84370178855ea67f15130b67e7d750c13217f70b9d36', 'user', NULL, NULL),
(38, 'anti2', 'anti@testuser.hu', '$2y$10$KZVXdQKVP3miIEbp3dDv9epgufZKyrZiCHx959kM3.inlrMDzeYT6', '2025-12-07 21:19:23', 1, '0a45cc361d9ccd6599163342d090b56aef9cdfec15743f92805e0f8ff101d5d6', 'user', 'kukutyinfalu', '+36201234256');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- A tábla indexei `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `elements`
--
ALTER TABLE `elements`
  ADD PRIMARY KEY (`element_id`),
  ADD KEY `fk_elements_part` (`part_num`),
  ADD KEY `fk_elements_color` (`color_id`);

--
-- A tábla indexei `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `inventory_minifigs`
--
ALTER TABLE `inventory_minifigs`
  ADD PRIMARY KEY (`inventory_id`,`fig_num`),
  ADD KEY `fk_invminifigs_fig` (`fig_num`);

--
-- A tábla indexei `inventory_parts`
--
ALTER TABLE `inventory_parts`
  ADD PRIMARY KEY (`inventory_id`,`part_num`,`color_id`),
  ADD KEY `fk_invparts_part` (`part_num`),
  ADD KEY `fk_invparts_color` (`color_id`);

--
-- A tábla indexei `inventory_sets`
--
ALTER TABLE `inventory_sets`
  ADD PRIMARY KEY (`inventory_id`,`set_num`),
  ADD KEY `fk_invsets_set` (`set_num`);

--
-- A tábla indexei `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `minifigs`
--
ALTER TABLE `minifigs`
  ADD PRIMARY KEY (`fig_num`);

--
-- A tábla indexei `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- A tábla indexei `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- A tábla indexei `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- A tábla indexei `parts`
--
ALTER TABLE `parts`
  ADD PRIMARY KEY (`part_num`),
  ADD KEY `fk_parts_category` (`part_cat_id`);

--
-- A tábla indexei `part_categories`
--
ALTER TABLE `part_categories`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `part_relationships`
--
ALTER TABLE `part_relationships`
  ADD KEY `fk_partrels_child` (`child_part_num`),
  ADD KEY `fk_partrels_parent` (`parent_part_num`);

--
-- A tábla indexei `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rater_id` (`rater_id`),
  ADD KEY `rated_user_id` (`rated_user_id`);

--
-- A tábla indexei `sets`
--
ALTER TABLE `sets`
  ADD PRIMARY KEY (`set_num`),
  ADD KEY `fk_sets_theme` (`theme_id`);

--
-- A tábla indexei `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT a táblához `listings`
--
ALTER TABLE `listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT a táblához `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT a táblához `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT a táblához `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT a táblához `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`);

--
-- Megkötések a táblához `elements`
--
ALTER TABLE `elements`
  ADD CONSTRAINT `fk_elements_color` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`),
  ADD CONSTRAINT `fk_elements_part` FOREIGN KEY (`part_num`) REFERENCES `parts` (`part_num`);

--
-- Megkötések a táblához `inventory_minifigs`
--
ALTER TABLE `inventory_minifigs`
  ADD CONSTRAINT `fk_invminifigs_fig` FOREIGN KEY (`fig_num`) REFERENCES `minifigs` (`fig_num`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invminifigs_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`);

--
-- Megkötések a táblához `inventory_parts`
--
ALTER TABLE `inventory_parts`
  ADD CONSTRAINT `fk_invparts_color` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`),
  ADD CONSTRAINT `fk_invparts_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`),
  ADD CONSTRAINT `fk_invparts_part` FOREIGN KEY (`part_num`) REFERENCES `parts` (`part_num`);

--
-- Megkötések a táblához `inventory_sets`
--
ALTER TABLE `inventory_sets`
  ADD CONSTRAINT `fk_invsets_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`),
  ADD CONSTRAINT `fk_invsets_set` FOREIGN KEY (`set_num`) REFERENCES `sets` (`set_num`);

--
-- Megkötések a táblához `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Megkötések a táblához `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Megkötések a táblához `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`);

--
-- Megkötések a táblához `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Megkötések a táblához `parts`
--
ALTER TABLE `parts`
  ADD CONSTRAINT `fk_parts_category` FOREIGN KEY (`part_cat_id`) REFERENCES `part_categories` (`id`);

--
-- Megkötések a táblához `part_relationships`
--
ALTER TABLE `part_relationships`
  ADD CONSTRAINT `fk_partrels_child` FOREIGN KEY (`child_part_num`) REFERENCES `parts` (`part_num`),
  ADD CONSTRAINT `fk_partrels_parent` FOREIGN KEY (`parent_part_num`) REFERENCES `parts` (`part_num`);

--
-- Megkötések a táblához `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`rater_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`rated_user_id`) REFERENCES `users` (`id`);

--
-- Megkötések a táblához `sets`
--
ALTER TABLE `sets`
  ADD CONSTRAINT `fk_sets_theme` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
