-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 30, 2024 at 05:52 AM
-- Server version: 5.7.39
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mentor_db_fresh`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mentee_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `limit` varchar(255) NOT NULL,
  `short_details` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assaign_days`
--

CREATE TABLE `assaign_days` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL DEFAULT '0',
  `day` int(11) NOT NULL,
  `start` varchar(255) DEFAULT NULL,
  `end` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assign_time`
--

CREATE TABLE `assign_time` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL DEFAULT '0',
  `day_id` int(11) NOT NULL,
  `time` varchar(255) NOT NULL,
  `start` varchar(255) NOT NULL,
  `end` varchar(255) NOT NULL,
  `person_per_slot` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `lang_id` varchar(155) DEFAULT NULL,
  `business_id` varchar(155) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `details` longtext,
  `image` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `meta_tags` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `total_views` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_category`
--

CREATE TABLE `blog_category` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `details` text,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `hit` int(11) DEFAULT NULL,
  `is_featured` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `business`
--

CREATE TABLE `business` (
  `id` int(11) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `details` text,
  `keywords` text,
  `description` text,
  `type` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `address` mediumtext,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `business_type` int(11) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `image` text,
  `thumb` text,
  `breadcrumb_banner` varchar(255) DEFAULT NULL,
  `hero_img` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `enable_gallery` varchar(255) DEFAULT '1',
  `enable_staff` varchar(255) NOT NULL DEFAULT '1',
  `is_primary` int(11) DEFAULT NULL,
  `template_style` int(11) NOT NULL DEFAULT '1',
  `curr_locate` varchar(155) DEFAULT '0',
  `num_format` varchar(155) DEFAULT '0',
  `color` varchar(255) NOT NULL DEFAULT '#546af1',
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `time_zone` int(11) DEFAULT '1',
  `date_format` varchar(255) DEFAULT 'd M Y',
  `time_format` varchar(255) DEFAULT 'hh',
  `time_interval` varchar(255) DEFAULT '30',
  `interval_type` varchar(255) DEFAULT 'minute',
  `interval_settings` varchar(155) DEFAULT '2',
  `enable_category` varchar(155) DEFAULT '0',
  `enable_rating` varchar(155) DEFAULT '0',
  `enable_location` varchar(155) DEFAULT '0',
  `enable_group` varchar(155) DEFAULT '0',
  `enable_guest` varchar(155) DEFAULT '0',
  `total_person` varchar(155) DEFAULT '5',
  `enable_payment` varchar(155) DEFAULT '1',
  `enable_onsite` varchar(155) DEFAULT '1',
  `enable_blog` varchar(155) DEFAULT NULL,
  `enable_portfolio` varchar(155) DEFAULT NULL,
  `enable_product` varchar(155) DEFAULT NULL,
  `enable_team` varchar(155) DEFAULT NULL,
  `enable_counter` varchar(155) DEFAULT NULL,
  `enable_career` varchar(155) DEFAULT NULL,
  `enable_service` varchar(155) DEFAULT NULL,
  `enable_event` varchar(155) DEFAULT NULL,
  `enable_quote` varchar(155) DEFAULT NULL,
  `enable_faq` varchar(155) DEFAULT NULL,
  `enable_testimonial` varchar(155) DEFAULT NULL,
  `enable_appointment` varchar(155) DEFAULT NULL,
  `holidays` longtext,
  `about_title` varchar(255) DEFAULT NULL,
  `about_details` text,
  `about_vedio_url` varchar(255) DEFAULT NULL,
  `home_style` varchar(155) DEFAULT NULL,
  `pagination_limit` varchar(255) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `details` text,
  `direction` text,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `icon`, `name`, `slug`, `details`, `direction`, `status`) VALUES
(1, 'bi bi-gear-wide-connected', 'Engineering ', 'engineering', 'Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. ', NULL, 1),
(2, 'bi bi-bag-check', 'Marketing', 'marketing', '', NULL, 1),
(3, 'bi bi-box', 'Product', 'product', '', NULL, 1),
(4, 'bi bi-layers-half', 'Design', 'design', '', NULL, 1),
(5, 'bi bi-server', 'Data Science', 'data-science', '', NULL, 1),
(6, 'bi bi-pencil-square', 'Content Writing', 'content-writing', '', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `business_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--

CREATE TABLE `counters` (
  `id` int(11) NOT NULL,
  `lang_id` varchar(155) DEFAULT NULL,
  `business_id` varchar(155) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `code` varchar(2) NOT NULL,
  `dial_code` varchar(5) NOT NULL,
  `currency_name` varchar(20) NOT NULL,
  `currency_symbol` varchar(20) NOT NULL,
  `currency_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `name`, `code`, `dial_code`, `currency_name`, `currency_symbol`, `currency_code`) VALUES
(1, 'Andorra', 'AD', '+376', 'Euro', '€', 'EUR'),
(2, 'United Arab Emirates', 'AE', '+971', 'United Arab Emirates', 'د.إ', 'AED'),
(3, 'Afghanistan', 'AF', '+93', 'Afghan afghani', '؋', 'AFN'),
(4, 'Antigua and Barbuda', 'AG', '+1268', 'East Caribbean dolla', '$', 'XCD'),
(5, 'Anguilla', 'AI', '+1264', 'East Caribbean dolla', '$', 'XCD'),
(6, 'Albania', 'AL', '+355', 'Albanian lek', 'L', 'ALL'),
(7, 'Armenia', 'AM', '+374', 'Armenian dram', '', 'AMD'),
(8, 'Angola', 'AO', '+244', 'Angolan kwanza', 'Kz', 'AOA'),
(9, 'Argentina', 'AR', '+54', 'Argentine peso', '$', 'ARS'),
(10, 'Austria', 'AT', '+43', 'Euro', '€', 'EUR'),
(11, 'Australia', 'AU', '+61', 'Australian dollar', '$', 'AUD'),
(12, 'Aruba', 'AW', '+297', 'Aruban florin', 'ƒ', 'AWG'),
(13, 'Azerbaijan', 'AZ', '+994', 'Azerbaijani manat', '', 'AZN'),
(14, 'Barbados', 'BB', '+1246', 'Barbadian dollar', '$', 'BBD'),
(15, 'Bangladesh', 'BD', '+880', 'Bangladeshi taka', '৳', 'BDT'),
(16, 'Belgium', 'BE', '+32', 'Euro', '€', 'EUR'),
(17, 'Burkina Faso', 'BF', '+226', 'West African CFA fra', 'Fr', 'XOF'),
(18, 'Bulgaria', 'BG', '+359', 'Bulgarian lev', 'лв', 'BGN'),
(19, 'Bahrain', 'BH', '+973', 'Bahraini dinar', '.د.ب', 'BHD'),
(20, 'Burundi', 'BI', '+257', 'Burundian franc', 'Fr', 'BIF'),
(21, 'Benin', 'BJ', '+229', 'West African CFA fra', 'Fr', 'XOF'),
(22, 'Bermuda', 'BM', '+1441', 'Bermudian dollar', '$', 'BMD'),
(23, 'Brazil', 'BR', '+55', 'Brazilian real', 'R$', 'BRL'),
(24, 'Bhutan', 'BT', '+975', 'Bhutanese ngultrum', 'Nu.', 'BTN'),
(25, 'Botswana', 'BW', '+267', 'Botswana pula', 'P', 'BWP'),
(26, 'Belarus', 'BY', '+375', 'Belarusian ruble', 'Br', 'BYR'),
(27, 'Belize', 'BZ', '+501', 'Belize dollar', '$', 'BZD'),
(28, 'Canada', 'CA', '+1', 'Canadian dollar', '$', 'CAD'),
(29, 'Switzerland', 'CH', '+41', 'Swiss franc', 'Fr', 'CHF'),
(30, 'Cote d\'Ivoire', 'CI', '+225', 'West African CFA fra', 'Fr', 'XOF'),
(31, 'Cook Islands', 'CK', '+682', 'New Zealand dollar', '$', 'NZD'),
(32, 'Chile', 'CL', '+56', 'Chilean peso', '$', 'CLP'),
(33, 'Cameroon', 'CM', '+237', 'Central African CFA ', 'Fr', 'XAF'),
(34, 'China', 'CN', '+86', 'Chinese yuan', '¥ or 元', 'CNY'),
(35, 'Colombia', 'CO', '+57', 'Colombian peso', '$', 'COP'),
(36, 'Costa Rica', 'CR', '+506', 'Costa Rican colón', '₡', 'CRC'),
(37, 'Cuba', 'CU', '+53', 'Cuban convertible pe', '$', 'CUC'),
(38, 'Cape Verde', 'CV', '+238', 'Cape Verdean escudo', 'Esc or $', 'CVE'),
(39, 'Cyprus', 'CY', '+357', 'Euro', '€', 'EUR'),
(40, 'Czech Republic', 'CZ', '+420', 'Czech koruna', 'Kč', 'CZK'),
(41, 'Germany', 'DE', '+49', 'Euro', '€', 'EUR'),
(42, 'Djibouti', 'DJ', '+253', 'Djiboutian franc', 'Fr', 'DJF'),
(43, 'Denmark', 'DK', '+45', 'Danish krone', 'kr', 'DKK'),
(44, 'Dominica', 'DM', '+1767', 'East Caribbean dolla', '$', 'XCD'),
(45, 'Dominican Republic', 'DO', '+1849', 'Dominican peso', '$', 'DOP'),
(46, 'Algeria', 'DZ', '+213', 'Algerian dinar', 'د.ج', 'DZD'),
(47, 'Ecuador', 'EC', '+593', 'United States dollar', '$', 'USD'),
(48, 'Estonia', 'EE', '+372', 'Euro', '€', 'EUR'),
(49, 'Egypt', 'EG', '+20', 'Egyptian pound', '£ or ج.م', 'EGP'),
(50, 'Eritrea', 'ER', '+291', 'Eritrean nakfa', 'Nfk', 'ERN'),
(51, 'Spain', 'ES', '+34', 'Euro', '€', 'EUR'),
(52, 'Ethiopia', 'ET', '+251', 'Ethiopian birr', 'Br', 'ETB'),
(53, 'Finland', 'FI', '+358', 'Euro', '€', 'EUR'),
(54, 'Fiji', 'FJ', '+679', 'Fijian dollar', '$', 'FJD'),
(55, 'Faroe Islands', 'FO', '+298', 'Danish krone', 'kr', 'DKK'),
(56, 'France', 'FR', '+33', 'Euro', '€', 'EUR'),
(57, 'Gabon', 'GA', '+241', 'Central African CFA ', 'Fr', 'XAF'),
(58, 'United Kingdom', 'GB', '+44', 'British pound', '£', 'GBP'),
(59, 'Grenada', 'GD', '+1473', 'East Caribbean dolla', '$', 'XCD'),
(60, 'Georgia', 'GE', '+995', 'Georgian lari', 'ლ', 'GEL'),
(61, 'Guernsey', 'GG', '+44', 'British pound', '£', 'GBP'),
(62, 'Ghana', 'GH', '+233', 'Ghana cedi', '₵', 'GHS'),
(63, 'Gibraltar', 'GI', '+350', 'Gibraltar pound', '£', 'GIP'),
(64, 'Guinea', 'GN', '+224', 'Guinean franc', 'Fr', 'GNF'),
(65, 'Equatorial Guinea', 'GQ', '+240', 'Central African CFA ', 'Fr', 'XAF'),
(66, 'Greece', 'GR', '+30', 'Euro', '€', 'EUR'),
(67, 'Guatemala', 'GT', '+502', 'Guatemalan quetzal', 'Q', 'GTQ'),
(68, 'Guinea-Bissau', 'GW', '+245', 'West African CFA fra', 'Fr', 'XOF'),
(69, 'Guyana', 'GY', '+595', 'Guyanese dollar', '$', 'GYD'),
(70, 'Hong Kong', 'HK', '+852', 'Hong Kong dollar', '$', 'HKD'),
(71, 'Honduras', 'HN', '+504', 'Honduran lempira', 'L', 'HNL'),
(72, 'Croatia', 'HR', '+385', 'Croatian kuna', 'kn', 'HRK'),
(73, 'Haiti', 'HT', '+509', 'Haitian gourde', 'G', 'HTG'),
(74, 'Hungary', 'HU', '+36', 'Hungarian forint', 'Ft', 'HUF'),
(75, 'Indonesia', 'ID', '+62', 'Indonesian rupiah', 'Rp', 'IDR'),
(76, 'Ireland', 'IE', '+353', 'Euro', '€', 'EUR'),
(77, 'Israel', 'IL', '+972', 'Israeli new shekel', '₪', 'ILS'),
(78, 'Isle of Man', 'IM', '+44', 'British pound', '£', 'GBP'),
(79, 'India', 'IN', '+91', 'Indian rupee', '₹', 'INR'),
(80, 'Iraq', 'IQ', '+964', 'Iraqi dinar', 'ع.د', 'IQD'),
(81, 'Iceland', 'IS', '+354', 'Icelandic króna', 'kr', 'ISK'),
(82, 'Italy', 'IT', '+39', 'Euro', '€', 'EUR'),
(83, 'Jersey', 'JE', '+44', 'British pound', '£', 'GBP'),
(84, 'Jamaica', 'JM', '+1876', 'Jamaican dollar', '$', 'JMD'),
(85, 'Jordan', 'JO', '+962', 'Jordanian dinar', 'د.ا', 'JOD'),
(86, 'Japan', 'JP', '+81', 'Japanese yen', '¥', 'JPY'),
(87, 'Kenya', 'KE', '+254', 'Kenyan shilling', 'Sh', 'KES'),
(88, 'Kyrgyzstan', 'KG', '+996', 'Kyrgyzstani som', 'лв', 'KGS'),
(89, 'Cambodia', 'KH', '+855', 'Cambodian riel', '៛', 'KHR'),
(90, 'Kiribati', 'KI', '+686', 'Australian dollar', '$', 'AUD'),
(91, 'Comoros', 'KM', '+269', 'Comorian franc', 'Fr', 'KMF'),
(92, 'Kuwait', 'KW', '+965', 'Kuwaiti dinar', 'د.ك', 'KWD'),
(93, 'Cayman Islands', 'KY', '+ 345', 'Cayman Islands dolla', '$', 'KYD'),
(94, 'Kazakhstan', 'KZ', '+7 7', 'Kazakhstani tenge', '', 'KZT'),
(95, 'Laos', 'LA', '+856', 'Lao kip', '₭', 'LAK'),
(96, 'Lebanon', 'LB', '+961', 'Lebanese pound', 'ل.ل', 'LBP'),
(97, 'Saint Lucia', 'LC', '+1758', 'East Caribbean dolla', '$', 'XCD'),
(98, 'Liechtenstein', 'LI', '+423', 'Swiss franc', 'Fr', 'CHF'),
(99, 'Sri Lanka', 'LK', '+94', 'Sri Lankan rupee', 'Rs or රු', 'LKR'),
(100, 'Liberia', 'LR', '+231', 'Liberian dollar', '$', 'LRD'),
(101, 'Lesotho', 'LS', '+266', 'Lesotho loti', 'L', 'LSL'),
(102, 'Lithuania', 'LT', '+370', 'Euro', '€', 'EUR'),
(103, 'Luxembourg', 'LU', '+352', 'Euro', '€', 'EUR'),
(104, 'Latvia', 'LV', '+371', 'Euro', '€', 'EUR'),
(105, 'Morocco', 'MA', '+212', 'Moroccan dirham', 'د.م.', 'MAD'),
(106, 'Monaco', 'MC', '+377', 'Euro', '€', 'EUR'),
(107, 'Moldova', 'MD', '+373', 'Moldovan leu', 'L', 'MDL'),
(108, 'Montenegro', 'ME', '+382', 'Euro', '€', 'EUR'),
(109, 'Madagascar', 'MG', '+261', 'Malagasy ariary', 'Ar', 'MGA'),
(110, 'Marshall Islands', 'MH', '+692', 'United States dollar', '$', 'USD'),
(111, 'Mali', 'ML', '+223', 'West African CFA fra', 'Fr', 'XOF'),
(112, 'Myanmar', 'MM', '+95', 'Burmese kyat', 'Ks', 'MMK'),
(113, 'Mongolia', 'MN', '+976', 'Mongolian tögrög', '₮', 'MNT'),
(114, 'Mauritania', 'MR', '+222', 'Mauritanian ouguiya', 'UM', 'MRO'),
(115, 'Montserrat', 'MS', '+1664', 'East Caribbean dolla', '$', 'XCD'),
(116, 'Malta', 'MT', '+356', 'Euro', '€', 'EUR'),
(117, 'Mauritius', 'MU', '+230', 'Mauritian rupee', '₨', 'MUR'),
(118, 'Maldives', 'MV', '+960', 'Maldivian rufiyaa', '.ރ', 'MVR'),
(119, 'Malawi', 'MW', '+265', 'Malawian kwacha', 'MK', 'MWK'),
(120, 'Mexico', 'MX', '+52', 'Mexican peso', '$', 'MXN'),
(121, 'Malaysia', 'MY', '+60', 'Malaysian ringgit', 'RM', 'MYR'),
(122, 'Mozambique', 'MZ', '+258', 'Mozambican metical', 'MT', 'MZN'),
(123, 'Namibia', 'NA', '+264', 'Namibian dollar', '$', 'NAD'),
(124, 'New Caledonia', 'NC', '+687', 'CFP franc', 'Fr', 'XPF'),
(125, 'Niger', 'NE', '+227', 'West African CFA fra', 'Fr', 'XOF'),
(126, 'Nigeria', 'NG', '+234', 'Nigerian naira', '₦', 'NGN'),
(127, 'Nicaragua', 'NI', '+505', 'Nicaraguan córdoba', 'C$', 'NIO'),
(128, 'Netherlands', 'NL', '+31', 'Euro', '€', 'EUR'),
(129, 'Norway', 'NO', '+47', 'Norwegian krone', 'kr', 'NOK'),
(130, 'Nepal', 'NP', '+977', 'Nepalese rupee', '₨', 'NPR'),
(131, 'Nauru', 'NR', '+674', 'Australian dollar', '$', 'AUD'),
(132, 'Niue', 'NU', '+683', 'New Zealand dollar', '$', 'NZD'),
(133, 'New Zealand', 'NZ', '+64', 'New Zealand dollar', '$', 'NZD'),
(134, 'Oman', 'OM', '+968', 'Omani rial', 'ر.ع.', 'OMR'),
(135, 'Panama', 'PA', '+507', 'Panamanian balboa', 'B/.', 'PAB'),
(136, 'Peru', 'PE', '+51', 'Peruvian nuevo sol', 'S/.', 'PEN'),
(137, 'French Polynesia', 'PF', '+689', 'CFP franc', 'Fr', 'XPF'),
(138, 'Papua New Guinea', 'PG', '+675', 'Papua New Guinean ki', 'K', 'PGK'),
(139, 'Philippines', 'PH', '+63', 'Philippine peso', '₱', 'PHP'),
(140, 'Pakistan', 'PK', '+92', 'Pakistani rupee', '₨', 'PKR'),
(141, 'Poland', 'PL', '+48', 'Polish z?oty', 'zł', 'PLN'),
(142, 'Portugal', 'PT', '+351', 'Euro', '€', 'EUR'),
(143, 'Palau', 'PW', '+680', 'Palauan dollar', '$', ''),
(144, 'Paraguay', 'PY', '+595', 'Paraguayan guaraní', '₲', 'PYG'),
(145, 'Qatar', 'QA', '+974', 'Qatari riyal', 'ر.ق', 'QAR'),
(146, 'Romania', 'RO', '+40', 'Romanian leu', 'lei', 'RON'),
(147, 'Serbia', 'RS', '+381', 'Serbian dinar', 'дин. or din.', 'RSD'),
(148, 'Russia', 'RU', '+7', 'Russian ruble', '', 'RUB'),
(149, 'Rwanda', 'RW', '+250', 'Rwandan franc', 'Fr', 'RWF'),
(150, 'Saudi Arabia', 'SA', '+966', 'Saudi riyal', 'ر.س', 'SAR'),
(151, 'Solomon Islands', 'SB', '+677', 'Solomon Islands doll', '$', 'SBD'),
(152, 'Seychelles', 'SC', '+248', 'Seychellois rupee', '₨', 'SCR'),
(153, 'Sudan', 'SD', '+249', 'Sudanese pound', 'ج.س.', 'SDG'),
(154, 'Sweden', 'SE', '+46', 'Swedish krona', 'kr', 'SEK'),
(155, 'Singapore', 'SG', '+65', 'Singapore dollar', '$', 'SGD'),
(156, 'Slovenia', 'SI', '+386', 'Euro', '€', 'EUR'),
(157, 'Slovakia', 'SK', '+421', 'Euro', '€', 'EUR'),
(158, 'Sierra Leone', 'SL', '+232', 'Sierra Leonean leone', 'Le', 'SLL'),
(159, 'San Marino', 'SM', '+378', 'Euro', '€', 'EUR'),
(160, 'Senegal', 'SN', '+221', 'West African CFA fra', 'Fr', 'XOF'),
(161, 'Somalia', 'SO', '+252', 'Somali shilling', 'Sh', 'SOS'),
(162, 'Suriname', 'SR', '+597', 'Surinamese dollar', '$', 'SRD'),
(163, 'El Salvador', 'SV', '+503', 'United States dollar', '$', 'USD'),
(164, 'Swaziland', 'SZ', '+268', 'Swazi lilangeni', 'L', 'SZL'),
(165, 'Chad', 'TD', '+235', 'Central African CFA ', 'Fr', 'XAF'),
(166, 'Togo', 'TG', '+228', 'West African CFA fra', 'Fr', 'XOF'),
(167, 'Thailand', 'TH', '+66', 'Thai baht', '฿', 'THB'),
(168, 'Tajikistan', 'TJ', '+992', 'Tajikistani somoni', 'ЅМ', 'TJS'),
(169, 'Turkmenistan', 'TM', '+993', 'Turkmenistan manat', 'm', 'TMT'),
(170, 'Tunisia', 'TN', '+216', 'Tunisian dinar', 'د.ت', 'TND'),
(171, 'Tonga', 'TO', '+676', 'Tongan pa?anga', 'T$', 'TOP'),
(172, 'Turkey', 'TR', '+90', 'Turkish lira', '', 'TRY'),
(173, 'Trinidad and Tobago', 'TT', '+1868', 'Trinidad and Tobago ', '$', 'TTD'),
(174, 'Tuvalu', 'TV', '+688', 'Australian dollar', '$', 'AUD'),
(175, 'Taiwan', 'TW', '+886', 'New Taiwan dollar', '$', 'TWD'),
(176, 'Ukraine', 'UA', '+380', 'Ukrainian hryvnia', '₴', 'UAH'),
(177, 'Uganda', 'UG', '+256', 'Ugandan shilling', 'Sh', 'UGX'),
(178, 'United States', 'US', '+1', 'United States dollar', '$', 'USD'),
(179, 'Uruguay', 'UY', '+598', 'Uruguayan peso', '$', 'UYU'),
(180, 'Uzbekistan', 'UZ', '+998', 'Uzbekistani som', '', 'UZS'),
(181, 'Vietnam', 'VN', '+84', 'Vietnamese ??ng', '₫', 'VND'),
(182, 'Vanuatu', 'VU', '+678', 'Vanuatu vatu', 'Vt', 'VUV'),
(183, 'Wallis and Futuna', 'WF', '+681', 'CFP franc', 'Fr', 'XPF'),
(184, 'Samoa', 'WS', '+685', 'Samoan t?l?', 'T', 'WST'),
(185, 'Yemen', 'YE', '+967', 'Yemeni rial', '﷼', 'YER'),
(186, 'South Africa', 'ZA', '+27', 'South African rand', 'R', 'ZAR'),
(187, 'Zambia', 'ZM', '+260', 'Zambian kwacha', 'ZK', 'ZMW'),
(188, 'Zimbabwe', 'ZW', '+263', 'Botswana pula', 'P', 'BWP');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `discount` varchar(255) NOT NULL,
  `start_date` varchar(255) DEFAULT NULL,
  `end_date` varchar(255) DEFAULT NULL,
  `once_per_mentee` int(11) DEFAULT '0',
  `usages_limit` varchar(255) DEFAULT NULL,
  `used` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_apply`
--

CREATE TABLE `coupon_apply` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `mentee_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `discount` int(11) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dialing_codes`
--

CREATE TABLE `dialing_codes` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `iso_code` varchar(2) NOT NULL,
  `isd_code` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dialing_codes`
--

INSERT INTO `dialing_codes` (`id`, `name`, `iso_code`, `isd_code`) VALUES
(1, 'Afghanistan', 'AF', '93'),
(2, 'Albania', 'AL', '355'),
(3, 'Algeria', 'DZ', '213'),
(4, 'American Samoa', 'AS', '1-684'),
(5, 'Andorra', 'AD', '376'),
(6, 'Angola', 'AO', '244'),
(7, 'Anguilla', 'AI', '1-264'),
(8, 'Antarctica', 'AQ', '672'),
(9, 'Antigua and Barbuda', 'AG', '1-268'),
(10, 'Argentina', 'AR', '54'),
(11, 'Armenia', 'AM', '374'),
(12, 'Aruba', 'AW', '297'),
(13, 'Australia', 'AU', '61'),
(14, 'Austria', 'AT', '43'),
(15, 'Azerbaijan', 'AZ', '994'),
(16, 'Bahamas', 'BS', '1-242'),
(17, 'Bahrain', 'BH', '973'),
(18, 'Bangladesh', 'BD', '880'),
(19, 'Barbados', 'BB', '1-246'),
(20, 'Belarus', 'BY', '375'),
(21, 'Belgium', 'BE', '32'),
(22, 'Belize', 'BZ', '501'),
(23, 'Benin', 'BJ', '229'),
(24, 'Bermuda', 'BM', '1-441'),
(25, 'Bhutan', 'BT', '975'),
(26, 'Bolivia', 'BO', '591'),
(27, 'Bosnia and Herzegowina', 'BA', '387'),
(28, 'Botswana', 'BW', '267'),
(29, 'Bouvet Island', 'BV', '47'),
(30, 'Brazil', 'BR', '55'),
(31, 'British Indian Ocean Territory', 'IO', '246'),
(32, 'Brunei Darussalam', 'BN', '673'),
(33, 'Bulgaria', 'BG', '359'),
(34, 'Burkina Faso', 'BF', '226'),
(35, 'Burundi', 'BI', '257'),
(36, 'Cambodia', 'KH', '855'),
(37, 'Cameroon', 'CM', '237'),
(38, 'Canada', 'CA', '1'),
(39, 'Cape Verde', 'CV', '238'),
(40, 'Cayman Islands', 'KY', '1-345'),
(41, 'Central African Republic', 'CF', '236'),
(42, 'Chad', 'TD', '235'),
(43, 'Chile', 'CL', '56'),
(44, 'China', 'CN', '86'),
(45, 'Christmas Island', 'CX', '61'),
(46, 'Cocos (Keeling) Islands', 'CC', '61'),
(47, 'Colombia', 'CO', '57'),
(48, 'Comoros', 'KM', '269'),
(49, 'Congo Democratic Republic of', 'CG', '242'),
(50, 'Cook Islands', 'CK', '682'),
(51, 'Costa Rica', 'CR', '506'),
(52, 'Cote D\'Ivoire', 'CI', '225'),
(53, 'Croatia', 'HR', '385'),
(54, 'Cuba', 'CU', '53'),
(55, 'Cyprus', 'CY', '357'),
(56, 'Czech Republic', 'CZ', '420'),
(57, 'Denmark', 'DK', '45'),
(58, 'Djibouti', 'DJ', '253'),
(59, 'Dominica', 'DM', '1-767'),
(60, 'Dominican Republic', 'DO', '1-809'),
(61, 'Timor-Leste', 'TL', '670'),
(62, 'Ecuador', 'EC', '593'),
(63, 'Egypt', 'EG', '20'),
(64, 'El Salvador', 'SV', '503'),
(65, 'Equatorial Guinea', 'GQ', '240'),
(66, 'Eritrea', 'ER', '291'),
(67, 'Estonia', 'EE', '372'),
(68, 'Ethiopia', 'ET', '251'),
(69, 'Falkland Islands (Malvinas)', 'FK', '500'),
(70, 'Faroe Islands', 'FO', '298'),
(71, 'Fiji', 'FJ', '679'),
(72, 'Finland', 'FI', '358'),
(73, 'France', 'FR', '33'),
(75, 'French Guiana', 'GF', '594'),
(76, 'French Polynesia', 'PF', '689'),
(77, 'French Southern Territories', 'TF', NULL),
(78, 'Gabon', 'GA', '241'),
(79, 'Gambia', 'GM', '220'),
(80, 'Georgia', 'GE', '995'),
(81, 'Germany', 'DE', '49'),
(82, 'Ghana', 'GH', '233'),
(83, 'Gibraltar', 'GI', '350'),
(84, 'Greece', 'GR', '30'),
(85, 'Greenland', 'GL', '299'),
(86, 'Grenada', 'GD', '1-473'),
(87, 'Guadeloupe', 'GP', '590'),
(88, 'Guam', 'GU', '1-671'),
(89, 'Guatemala', 'GT', '502'),
(90, 'Guinea', 'GN', '224'),
(91, 'Guinea-bissau', 'GW', '245'),
(92, 'Guyana', 'GY', '592'),
(93, 'Haiti', 'HT', '509'),
(94, 'Heard Island and McDonald Islands', 'HM', '011'),
(95, 'Honduras', 'HN', '504'),
(96, 'Hong Kong', 'HK', '852'),
(97, 'Hungary', 'HU', '36'),
(98, 'Iceland', 'IS', '354'),
(99, 'India', 'IN', '91'),
(100, 'Indonesia', 'ID', '62'),
(101, 'Iran (Islamic Republic of)', 'IR', '98'),
(102, 'Iraq', 'IQ', '964'),
(103, 'Ireland', 'IE', '353'),
(104, 'Israel', 'IL', '972'),
(105, 'Italy', 'IT', '39'),
(106, 'Jamaica', 'JM', '1-876'),
(107, 'Japan', 'JP', '81'),
(108, 'Jordan', 'JO', '962'),
(109, 'Kazakhstan', 'KZ', '7'),
(110, 'Kenya', 'KE', '254'),
(111, 'Kiribati', 'KI', '686'),
(112, 'Korea, Democratic People\'s Republic of', 'KP', '850'),
(113, 'South Korea', 'KR', '82'),
(114, 'Kuwait', 'KW', '965'),
(115, 'Kyrgyzstan', 'KG', '996'),
(116, 'Lao People\'s Democratic Republic', 'LA', '856'),
(117, 'Latvia', 'LV', '371'),
(118, 'Lebanon', 'LB', '961'),
(119, 'Lesotho', 'LS', '266'),
(120, 'Liberia', 'LR', '231'),
(121, 'Libya', 'LY', '218'),
(122, 'Liechtenstein', 'LI', '423'),
(123, 'Lithuania', 'LT', '370'),
(124, 'Luxembourg', 'LU', '352'),
(125, 'Macao', 'MO', '853'),
(126, 'Macedonia, The Former Yugoslav Republic of', 'MK', '389'),
(127, 'Madagascar', 'MG', '261'),
(128, 'Malawi', 'MW', '265'),
(129, 'Malaysia', 'MY', '60'),
(130, 'Maldives', 'MV', '960'),
(131, 'Mali', 'ML', '223'),
(132, 'Malta', 'MT', '356'),
(133, 'Marshall Islands', 'MH', '692'),
(134, 'Martinique', 'MQ', '596'),
(135, 'Mauritania', 'MR', '222'),
(136, 'Mauritius', 'MU', '230'),
(137, 'Mayotte', 'YT', '262'),
(138, 'Mexico', 'MX', '52'),
(139, 'Micronesia, Federated States of', 'FM', '691'),
(140, 'Moldova', 'MD', '373'),
(141, 'Monaco', 'MC', '377'),
(142, 'Mongolia', 'MN', '976'),
(143, 'Montserrat', 'MS', '1-664'),
(144, 'Morocco', 'MA', '212'),
(145, 'Mozambique', 'MZ', '258'),
(146, 'Myanmar', 'MM', '95'),
(147, 'Namibia', 'NA', '264'),
(148, 'Nauru', 'NR', '674'),
(149, 'Nepal', 'NP', '977'),
(150, 'Netherlands', 'NL', '31'),
(151, 'Netherlands Antilles', 'AN', '599'),
(152, 'New Caledonia', 'NC', '687	'),
(153, 'New Zealand', 'NZ', '64'),
(154, 'Nicaragua', 'NI', '505'),
(155, 'Niger', 'NE', '227'),
(156, 'Nigeria', 'NG', '234'),
(157, 'Niue', 'NU', '683'),
(158, 'Norfolk Island', 'NF', '672'),
(159, 'Northern Mariana Islands', 'MP', '1-670'),
(160, 'Norway', 'NO', '47'),
(161, 'Oman', 'OM', '968'),
(162, 'Pakistan', 'PK', '92'),
(163, 'Palau', 'PW', '680'),
(164, 'Panama', 'PA', '507'),
(165, 'Papua New Guinea', 'PG', '675'),
(166, 'Paraguay', 'PY', '595'),
(167, 'Peru', 'PE', '51'),
(168, 'Philippines', 'PH', '63'),
(169, 'Pitcairn', 'PN', '64'),
(170, 'Poland', 'PL', '48'),
(171, 'Portugal', 'PT', '351'),
(172, 'Puerto Rico', 'PR', '1-787'),
(173, 'Qatar', 'QA', '974'),
(174, 'Reunion', 'RE', '262'),
(175, 'Romania', 'RO', '40'),
(176, 'Russian Federation', 'RU', '7'),
(177, 'Rwanda', 'RW', '250'),
(178, 'Saint Kitts and Nevis', 'KN', '1-869'),
(179, 'Saint Lucia', 'LC', '1-758'),
(180, 'Saint Vincent and the Grenadines', 'VC', '1-784'),
(181, 'Samoa', 'WS', '685'),
(182, 'San Marino', 'SM', '378'),
(183, 'Sao Tome and Principe', 'ST', '239'),
(184, 'Saudi Arabia', 'SA', '966'),
(185, 'Senegal', 'SN', '221'),
(186, 'Seychelles', 'SC', '248'),
(187, 'Sierra Leone', 'SL', '232'),
(188, 'Singapore', 'SG', '65'),
(189, 'Slovakia (Slovak Republic)', 'SK', '421'),
(190, 'Slovenia', 'SI', '386'),
(191, 'Solomon Islands', 'SB', '677'),
(192, 'Somalia', 'SO', '252'),
(193, 'South Africa', 'ZA', '27'),
(194, 'South Georgia and the South Sandwich Islands', 'GS', '500'),
(195, 'Spain', 'ES', '34'),
(196, 'Sri Lanka', 'LK', '94'),
(197, 'Saint Helena, Ascension and Tristan da Cunha', 'SH', '290'),
(198, 'St. Pierre and Miquelon', 'PM', '508'),
(199, 'Sudan', 'SD', '249'),
(200, 'Suriname', 'SR', '597'),
(201, 'Svalbard and Jan Mayen Islands', 'SJ', '47'),
(202, 'Swaziland', 'SZ', '268'),
(203, 'Sweden', 'SE', '46'),
(204, 'Switzerland', 'CH', '41'),
(205, 'Syrian Arab Republic', 'SY', '963'),
(206, 'Taiwan', 'TW', '886'),
(207, 'Tajikistan', 'TJ', '992'),
(208, 'Tanzania, United Republic of', 'TZ', '255'),
(209, 'Thailand', 'TH', '66'),
(210, 'Togo', 'TG', '228'),
(211, 'Tokelau', 'TK', '690'),
(212, 'Tonga', 'TO', '676'),
(213, 'Trinidad and Tobago', 'TT', '1-868'),
(214, 'Tunisia', 'TN', '216'),
(215, 'Turkey', 'TR', '90'),
(216, 'Turkmenistan', 'TM', '993'),
(217, 'Turks and Caicos Islands', 'TC', '1-649'),
(218, 'Tuvalu', 'TV', '688'),
(219, 'Uganda', 'UG', '256'),
(220, 'Ukraine', 'UA', '380'),
(221, 'United Arab Emirates', 'AE', '971'),
(222, 'United Kingdom', 'GB', '44'),
(223, 'United States', 'US', '1'),
(224, 'United States Minor Outlying Islands', 'UM', '246'),
(225, 'Uruguay', 'UY', '598'),
(226, 'Uzbekistan', 'UZ', '998'),
(227, 'Vanuatu', 'VU', '678'),
(228, 'Vatican City State (Holy See)', 'VA', '379'),
(229, 'Venezuela', 'VE', '58'),
(230, 'Vietnam', 'VN', '84'),
(231, 'Virgin Islands (British)', 'VG', '1-284'),
(232, 'Virgin Islands (U.S.)', 'VI', '1-340'),
(233, 'Wallis and Futuna Islands', 'WF', '681'),
(234, 'Western Sahara', 'EH', '212'),
(235, 'Yemen', 'YE', '967'),
(236, 'Serbia', 'RS', '381'),
(238, 'Zambia', 'ZM', '260'),
(239, 'Zimbabwe', 'ZW', '263'),
(240, 'Aaland Islands', 'AX', '358'),
(241, 'Palestine', 'PS', '970'),
(242, 'Montenegro', 'ME', '382'),
(243, 'Guernsey', 'GG', '44-1481'),
(244, 'Isle of Man', 'IM', '44-1624'),
(245, 'Jersey', 'JE', '44-1534'),
(247, 'CuraÃ§ao', 'CW', '599'),
(248, 'Ivory Coast', 'CI', '225'),
(249, 'Kosovo', 'XK', '383');

-- --------------------------------------------------------

--
-- Table structure for table `educations`
--

CREATE TABLE `educations` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `institute` varchar(255) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `start_year` varchar(255) NOT NULL,
  `end_year` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `experiences`
--

CREATE TABLE `experiences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `start_date` varchar(255) DEFAULT NULL,
  `end_date` varchar(255) DEFAULT NULL,
  `is_present` varchar(255) NOT NULL DEFAULT '0',
  `contribution` text,
  `status` varchar(255) NOT NULL,
  `created_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `lang_id` varchar(155) NOT NULL DEFAULT '1',
  `business_id` varchar(255) DEFAULT NULL,
  `type` varchar(155) NOT NULL DEFAULT 'admin',
  `title` varchar(255) DEFAULT NULL,
  `details` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `lang_id`, `business_id`, `type`, `title`, `details`) VALUES
(1, '1', '', 'admin', 'How does the free trial work?', 'Our 14 day trial is 100% free and does not require any credit card information to start. If at the end of your trial you would like to upgrade, great. If not, your plan will automatically be downgraded to the free plan.'),
(2, '1', '', 'admin', 'Do I need to choose a plan now ?', 'No. You get the full featured, unlimited version of our service completely free for 14 days. Once you\'re ready to upgrade, you may choose a plan which suits your needs.'),
(4, '1', '', 'admin', 'Can I get a demo of the product?', 'Sure We are currently running live demos a week. You can sign up and register for our next one here.'),
(6, '1', '489263197772', 'user', 'How can I booking an appointment ?', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English.'),
(7, '1', '489263197772', 'user', 'How can I make a site of e-commerce ?', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English.'),
(8, '1', '489263197772', 'user', 'Can I booking a ticket for any kind of event with this site ?', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English.');

-- --------------------------------------------------------

--
-- Table structure for table `favourite`
--

CREATE TABLE `favourite` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `favourite_id` int(11) NOT NULL,
  `created_at` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `is_limit` int(11) NOT NULL,
  `basic` int(11) DEFAULT NULL,
  `standared` int(11) DEFAULT NULL,
  `premium` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `feature_assaign`
--

CREATE TABLE `feature_assaign` (
  `id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fonts`
--

CREATE TABLE `fonts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fonts`
--

INSERT INTO `fonts` (`id`, `user_id`, `name`, `slug`) VALUES
(1, 0, 'Lato', 'lato'),
(2, 0, 'Smith', 'smith'),
(3, 0, 'Bree Serif', 'bree-serif'),
(4, 0, 'Cabin', 'cabin'),
(5, 0, 'Cookie', 'cookie'),
(6, 0, 'Montserrat', 'montserrat'),
(7, 0, 'Raleway', 'raleway'),
(8, 0, 'Roboto', 'roboto'),
(9, 0, 'Nunito', 'nunito'),
(10, 0, 'Molengo', 'molengo'),
(11, 0, 'Sarabun', 'sarabun'),
(12, 0, 'Open Sans', 'open-sans'),
(13, 0, 'Source Sans Pro', 'source-sans-pro'),
(14, 0, 'PT Sans', 'pt-sans'),
(15, 0, 'Noto Sans', 'noto-sans'),
(16, 0, 'Roboto Mono', 'roboto-mono'),
(17, 0, 'Muli', 'muli'),
(18, 0, 'Arimo', 'arimo'),
(19, 0, 'Fira Sans', 'fira-sans'),
(20, 0, 'Noto Serif', 'noto-serif'),
(21, 0, 'Work Sans', 'work-sans'),
(22, 0, 'Quicksand', 'quicksand'),
(23, 0, 'Dosis', 'dosis'),
(24, 0, 'Rubik', 'rubik'),
(25, 0, 'Oxygen', 'oxygen'),
(26, 0, 'Hind', 'hind'),
(27, 0, 'Josefin Sans', 'josefin-sans'),
(28, 0, 'Merriweather Sans', 'merriweather-sans'),
(29, 0, 'Kanit', 'kanit'),
(30, 0, 'Comfortaa', 'comfortaa'),
(31, 0, 'Bebas Neue', 'bebas-neue'),
(32, 0, 'Short', 'short'),
(33, 0, 'Iransans', 'iransans'),
(34, 0, 'Noto Sans KR', 'noto-sans-kr'),
(35, 0, 'Arial', 'arial'),
(36, 0, 'Alata', 'alata'),
(37, 0, 'Sriracha', 'sriracha'),
(38, 0, 'Tajawal', 'tajawal'),
(39, 0, 'Ubuntu', 'ubuntu');

-- --------------------------------------------------------

--
-- Table structure for table `kyc_verifications`
--

CREATE TABLE `kyc_verifications` (
  `id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `doc_id_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `birth_date` varchar(255) NOT NULL,
  `address` text,
  `front_side_doc` varchar(255) DEFAULT NULL,
  `back_side_doc` varchar(255) DEFAULT NULL,
  `selfiee_with_doc` varchar(255) DEFAULT NULL,
  `passport` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT '0',
  `reject_reason` text,
  `is_preview` int(11) DEFAULT '0',
  `created_at` varchar(255) DEFAULT NULL,
  `resub_date` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `text_direction` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `name`, `slug`, `short_name`, `code`, `text_direction`, `status`) VALUES
(1, 'English', 'english', 'en', '', 'ltr', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lang_values`
--

CREATE TABLE `lang_values` (
  `id` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `english` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lang_values`
--

INSERT INTO `lang_values` (`id`, `type`, `label`, `keyword`, `english`) VALUES
(1, 'admin', 'Language', 'language', 'Language'),
(2, 'admin', 'Edit frontend values', 'edit-frontend-values', 'Translate Frontend'),
(3, 'admin', 'Edit admin values', 'edit-admin-values', 'Translate Admin Panel'),
(4, 'admin', 'Edit user values', 'edit-user-values', 'Translate User Panel'),
(5, 'admin', 'Update language for', 'update-language-for', 'Update language for'),
(6, 'admin', 'Frontend', 'frontend', 'Frontend'),
(7, 'admin', 'Admin', 'admin', 'Admin'),
(8, 'admin', 'User', 'user', 'User'),
(9, 'admin', 'Add New Language ', 'add-new-language', 'Add New Language '),
(10, 'admin', 'Manage Language', 'manage-language', 'Manage Language'),
(11, 'admin', 'Update values', 'update-values', 'Update values'),
(12, 'admin', 'Your Password has been changed Successfully', 'password-reset-success-msg', 'Your Password has been changed Successfully'),
(13, 'admin', 'Oops', 'oops', 'Oops'),
(14, 'admin', 'Your Confirm Password doesn\'t Match', 'confirm-pass-not-match-msg', 'Your Confirm Password doesn\'t Match'),
(15, 'admin', 'Your Old Password doesn\'t Match', 'old-password-doesnt-match', 'Your Old Password doesn\'t Match'),
(16, 'admin', 'Sorry', 'sorry', 'Sorry!'),
(17, 'admin', 'Something wrong', 'something-wrong', 'Something wrong'),
(18, 'admin', 'Success', 'success', 'Success!'),
(19, 'admin', 'Setup successfully', 'setup-successfully', 'Setup successfully'),
(20, 'admin', 'Send successfully', 'send-successfully', 'Send successfully'),
(21, 'admin', 'Are you sure', 'are-you-sure', 'Are you sure?'),
(22, 'admin', 'Converted successfully', 'converted-successfully', 'Converted successfully'),
(23, 'admin', 'Data limit has been over', 'data-limit-over', 'Data limit has been over'),
(24, 'admin', 'Sending failed', 'sending-failed', 'Sending failed'),
(25, 'admin', 'Approved Successfully', 'approved-successfully', 'Approved Successfully'),
(26, 'admin', 'You will not be able to recover this file', 'not-recover-file', 'You will not be able to recover this file'),
(27, 'admin', 'Deleted successfully', 'deleted-successfully', 'Deleted successfully'),
(28, 'admin', 'Approve this invoice', 'approve-this-invoice', 'Approve this invoice'),
(29, 'admin', 'Set as your primary chamber', 'set-as-your-primary-chamber', 'Set as your primary chamber'),
(30, 'admin', 'Want to set', 'want-to-set', 'Want to set'),
(31, 'admin', 'You have made some changes and it\'s not saved?', 'made-changes-not-saved', 'You have made some changes and it\'s not saved?'),
(32, 'admin', 'Your account has been suspended', 'account-suspend-msg', 'Your account has been suspended!'),
(33, 'front', 'This email already exist, try another one', 'email-exist', 'This email already exist, try another one'),
(34, 'front', 'Your account is not active', 'account-not-active', 'Your account is not active'),
(35, 'front', 'Sorry your username or password is not correct!', 'wrong-username-password', 'Sorry your username or password is not correct!'),
(36, 'front', 'Your email is not verified, Please verify your email', 'email-not-verified', 'Your email is not verified, Please verify your email'),
(37, 'front', 'We\'ve sent a password to your email address. Please check your inbox', 'password-sent-to-email', 'We\'ve sent a password to your email address. Please check your inbox'),
(38, 'front', 'Password Reset Successfully !', 'password-reset-successfully', 'Password Reset Successfully !'),
(39, 'front', 'You are not a valid user', 'not-a-valid-user', 'You are not a valid user'),
(40, 'admin', 'Set default language', 'set-default-language', 'Set default language'),
(41, 'admin', 'Short Form', 'short-form', 'Short Form'),
(42, 'admin', 'Text direction', 'text-direction', 'Text direction'),
(44, 'admin', 'Set Trial Days', 'set-trial-days', 'Set trial days'),
(45, 'front', 'Start', 'start', 'Start'),
(46, 'front', 'days trial', 'days-trial', 'days trial'),
(47, 'admin', 'Delete', 'delete', 'Delete'),
(48, 'admin', 'Activate', 'activate', 'Activate'),
(49, 'admin', 'Deactivate', 'deactivate', 'Deactivate'),
(50, 'admin', 'Dashboard', 'dashboard', 'Dashboard'),
(51, 'admin', 'Save', 'save', 'Save'),
(52, 'front', 'Home', 'home', 'Home'),
(53, 'front', 'Pricing', 'pricing', 'Pricing'),
(54, 'front', 'Blogs', 'blogs', 'Blogs'),
(55, 'front', 'Faqs', 'faqs', 'FAQs'),
(56, 'front', 'Contact', 'contact', 'Contact'),
(57, 'front', 'Pages', 'pages', 'Pages'),
(58, 'front', 'Sign In', 'sign-in', 'Sign In'),
(59, 'front', 'Sign Out', 'sign-out', 'Sign Out'),
(60, 'front', 'Get Started', 'get-started', 'Get Started'),
(61, 'front', 'Workflow', 'workflow', 'Workflow'),
(62, 'front', 'Look at a glance how our system works', 'workflow-title', 'Look at a glance how our system works'),
(63, 'front', 'Choose Plan', 'choose-plan', 'Choose Plan'),
(64, 'front', 'Choose your confortable plan', 'choose-your-confortable-plan', 'Choose your confortable plan'),
(65, 'front', 'Get Paid', 'get-paid', 'Get Paid'),
(66, 'front', 'Get Paid title', 'get-paid-title', 'Paid via paypal/stripe payment method'),
(67, 'front', 'Start Working', 'start-working', 'Start Working'),
(68, 'front', 'Start Working title', 'start-working-title', 'Start Using and explore the featuers'),
(69, 'front', 'Start using', 'start-using', 'Start using'),
(70, 'front', 'account', 'account', 'account'),
(71, 'front', 'Our online registration process makes it easy for you to sign up for an free or pro account.', 'home-intro-desc', 'Our online registration process makes it easy for you to sign up for an free or pro account.'),
(72, 'front', 'Services', 'services', 'Services'),
(73, 'front', 'All rights reserved', 'all-rights-reserved', 'All rights reserved'),
(74, 'front', 'Small Business — friendly Pricing', 'pricing-title', 'Small Business — friendly Pricing'),
(75, 'front', 'We\'re offering a generous Free Plan and affordable Standard & Premium pricing plans that will help you to grow with', 'pricing-desc', 'We\'re offering a generous Free Plan and affordable Standard & Premium pricing plans that will help you to grow with'),
(76, 'front', 'Monthly', 'monthly', 'Monthly'),
(77, 'front', 'Yearly', 'yearly', 'Yearly'),
(78, 'front', 'Per Year', 'per-year', 'Per Year'),
(79, 'front', 'Per Month', 'per-month', 'Per Month'),
(80, 'front', 'Select Plan', 'select-plan', 'Select Plan'),
(81, 'front', 'Experts', 'experts', 'Experts'),
(82, 'front', 'Meet our experienced experts and book your appoinment in online.', 'expert-title', 'Meet our experienced experts and book your appoinment in online.'),
(83, 'front', 'Select Departments', 'select-departments', 'Select Departments'),
(84, 'front', 'Select Experiences', 'select-experiences', 'Select Experiences'),
(85, 'front', 'Search by name', 'search-by-name', 'Search by name'),
(86, 'front', 'Book Appointment', 'book-appointment', 'Book Appointment'),
(87, 'front', 'No data found!', 'no-data-found', 'No data found!'),
(88, 'front', 'Blog & News', 'blog-news', 'Blog & News'),
(89, 'front', 'Learn More & Empower Yourself', 'learn-more-empower-yourself', 'Learn More & Empower Yourself'),
(90, 'front', 'Search blog posts', 'search-blog-posts', 'Search blog posts'),
(91, 'front', 'Tags', 'tags', 'Tags'),
(92, 'front', 'Leave a reply', 'leave-a-reply', 'Leave a reply'),
(93, 'front', 'Name', 'name', 'Name'),
(94, 'front', 'Address', 'address', 'Address'),
(95, 'front', 'Comment', 'comment', 'Comment'),
(96, 'front', 'Submit', 'submit', 'Submit'),
(97, 'front', 'Frequently Asked Questions', 'frequently-asked-questions', 'Frequently Asked Questions'),
(98, 'front', 'Get In Touch', 'get-in-touch', 'Get In Touch'),
(99, 'front', 'Message', 'message', 'Message'),
(100, 'front', 'Sign in to your', 'sign-in-to-your', 'Sign in to your'),
(101, 'front', 'Username', 'username', 'Username'),
(102, 'front', 'Password', 'password', 'Password'),
(103, 'front', 'Forgot password?', 'forgot-password', 'Forgot password?'),
(104, 'front', 'Don\'t have an account yet?', 'an-account-yet', 'Don\'t have an account yet?'),
(105, 'front', 'Select Your Role', 'select-your-role', 'Select Your Role'),
(106, 'front', 'Enter your email', 'enter-your-email', 'Enter your email'),
(107, 'front', ' Back', 'back', ' Back'),
(108, 'front', 'Email', 'email', 'Email'),
(109, 'front', 'Your full name', 'your-full-name', 'Your full name'),
(110, 'front', 'Your email address', 'your-email-address', 'Your email'),
(111, 'front', 'Your password', 'your-password', 'Your password'),
(112, 'front', 'I have read and understood the', 'i-have-read-and-understood-the', 'I have read and understood the'),
(113, 'front', 'Terms and Conditions', 'terms-and-conditions', 'Terms and Conditions'),
(114, 'front', 'Privacy Policy', 'privacy-policy', 'Privacy Policy'),
(115, 'front', 'and', 'and', 'and'),
(116, 'front', 'of this site', 'of-this-site', 'of this site'),
(117, 'front', 'Already have an account?', 'already-have-an-account', 'Already have an account?'),
(118, 'front', 'Register', 'register', 'Register'),
(119, 'front', 'Privacy', 'privacy', 'Privacy'),
(120, 'front', 'Terms', 'terms', 'Terms'),
(121, 'front', 'Error', 'error', 'Error'),
(122, 'front', 'Warning', 'warning', 'Warning'),
(123, 'front', 'Appointment type is required', 'appointment-type-is-required', 'Appointment type is required'),
(124, 'front', 'Booking date is required', 'booking-date-is-required', 'Booking date is required'),
(125, 'front', 'Booking time is required', 'booking-time-is-required', 'Booking time is required'),
(126, 'front', 'Processing', 'processing', 'Processing'),
(127, 'front', 'Appointment booked successfully', 'appointment-booked-successfully', 'Appointment booked successfully'),
(128, 'front', 'Incorrect username or password', 'incorrect-username-or-password', 'Incorrect username or password'),
(129, 'front', 'Please enter a valid date', 'please-enter-a-valid-date', 'Please enter a valid date'),
(130, 'front', 'Recaptcha is required', 'recaptcha-is-required', 'Recaptcha is required'),
(131, 'front', 'Signing In', 'signing-in', 'Signing In'),
(132, 'front', 'Your account is not active', 'your-account-is-not-active', 'Your account is not active'),
(133, 'front', 'Your account has been suspended', 'your-account-has-been-suspended', 'Your account has been suspended'),
(134, 'front', 'Your email is not verified, Please verify your email', 'your-email-is-not-verified-please-verify-your-email', 'Your email is not verified, Please verify your email'),
(135, 'front', 'Registared successfully!', 'registared-successfully', 'Registered successfully!'),
(136, 'front', 'Please wait we are preparing environment for you...', 'preparing-environment', 'Please wait we are preparing environment for you...'),
(137, 'front', 'This email already used, please try another one', 'email-exitsts', 'This email already used, please try another one'),
(138, 'front', 'Something wrong !, Failed to send code in your email.', 'something-wrong', 'Something wrong !, Failed to send code in your email.'),
(139, 'front', 'We\'ve sent a password to your email address. Please check your inbox', 'email-send-notify', 'We\'ve sent a password to your email address. Please check your inbox'),
(140, 'front', 'You are not a valid user', 'you-are-not-a-valid-user', 'You are not a valid user'),
(141, 'front', 'Try Again', 'try-again', 'Try Again'),
(142, 'front', 'Your account verified successfully!', 'your-account-verified-successfully', 'Your account verified successfully!'),
(143, 'front', 'Verify code is not matched', 'verify-code-is-not-matched', 'Verify code is not matched'),
(144, 'front', 'Experience Years', 'experience-years', 'Experience Years'),
(145, 'front', 'Patients', 'patients', 'Patients'),
(146, 'front', 'Visited Patient\'s', 'visited-patients', 'Visited Patient\'s'),
(147, 'front', 'Before booked an appointment check the availability', 'booking-availability', 'Before booked an appointment check the availability'),
(148, 'front', 'Appointment Type', 'appointment-type', 'Appointment Type'),
(149, 'front', 'Select Type', 'select-type', 'Select Type'),
(150, 'front', 'Date ', 'date', 'Date '),
(151, 'front', 'Time', 'time', 'Time'),
(152, 'front', 'Consultation Fee', 'consultation-fee', 'Consultation Fee'),
(153, 'front', 'Continue', 'continue', 'Continue'),
(154, 'front', ' New Registration', 'new-registration', ' New Registration'),
(155, 'front', ' Already have account?', 'already-have-account', ' Already have account?'),
(156, 'front', 'Close', 'close', 'Close'),
(157, 'front', 'Powered by', 'powered-by', 'Powered by'),
(158, 'admin', 'Settings', 'settings', 'Settings'),
(159, 'admin', 'Payment Settings', 'payment-settings', 'Payment Settings'),
(160, 'admin', 'Plans', 'plans', 'Plans'),
(161, 'admin', 'Departments', 'departments', 'Departments'),
(162, 'admin', 'Add Category', 'add-category', 'Add Category'),
(163, 'admin', 'Blog Posts', 'blog-posts', 'Blog Posts'),
(164, 'admin', 'Change Password', 'change-password', 'Change Password'),
(165, 'admin', 'Logout', 'logout', 'Logout'),
(166, 'admin', 'Verified', 'verified', 'Verified'),
(167, 'admin', 'Pending', 'pending', 'Pending'),
(168, 'admin', 'Expired', 'expired', 'Expired'),
(169, 'admin', 'Last 12 months Income', 'last-12-months-income', 'Last 12 months Income'),
(170, 'admin', 'Income', 'income', 'Income'),
(171, 'admin', 'Recently joined Users', 'recently-joined-users', 'Recently joined Users'),
(172, 'admin', 'a week ago', 'a-week-ago', 'a week ago'),
(173, 'admin', 'Net Income', 'net-income', 'Net Income'),
(174, 'admin', 'Fiscal year', 'fiscal-year', 'Fiscal year'),
(175, 'admin', 'Fiscal year start is January 01', 'fiscal-year-title', 'Fiscal year start is January 01'),
(176, 'admin', 'Version', 'version', 'Version'),
(177, 'admin', 'Plans by user', 'plans-by-user', 'Plans by user'),
(178, 'admin', 'Manage Settings', 'manage-settings', 'Manage Settings'),
(179, 'admin', ' Website Settings', 'website-settings', ' Website Settings'),
(180, 'admin', 'Upload Favicon', 'upload-favicon', 'Upload Favicon'),
(181, 'admin', 'Upload Logo', 'upload-logo', 'Upload Logo'),
(182, 'admin', 'Upload home image', 'upload-home-image', 'Upload home image'),
(183, 'admin', 'Application Name', 'application-name', 'Application Name'),
(184, 'admin', 'Application Title', 'application-title', 'Application Title'),
(185, 'admin', 'Keywords', 'keywords', 'Keywords'),
(186, 'admin', 'Description', 'description', 'Description'),
(187, 'admin', 'Footer About', 'footer-about', 'Footer About'),
(188, 'admin', 'Admin Email', 'admin-email', 'Admin Email'),
(189, 'admin', 'Copyright', 'copyright', 'Copyright'),
(190, 'admin', 'This email will used for your site from mail', 'settings-email-info', 'This email will used for your site from mail'),
(191, 'admin', 'Zoom Settings', 'zoom-settings', 'Zoom Settings'),
(192, 'admin', 'Preferences', 'preferences', 'Preferences'),
(193, 'admin', 'Registration System', 'registration-system', 'Registration System'),
(194, 'admin', 'Enable to allow sign up users to your site.', 'registration-title', 'Enable to allow sign up users to your site.'),
(195, 'admin', 'Enable reCaptcha for all open froms (Sign up, contact, comments)', 'recaptcha-title', 'Enable reCaptcha for all open froms (Sign up, contact, comments)'),
(196, 'admin', 'Email Verification', 'email-verification', 'Email Verification'),
(197, 'admin', 'Enable to allow email verification for registered users.', 'email-verify-title', 'Enable to allow email verification for registered users.'),
(198, 'admin', 'Enable to show users option in frontend', 'users-title', 'Enable to show users option in frontend'),
(199, 'admin', 'Enable to show blog option in frontend', 'blogs-title', 'Enable to show blog option in frontend'),
(200, 'admin', 'Enable to show faqs option in frontend', 'faq-title', 'Enable to show faqs option in frontend'),
(201, 'admin', 'Enable to show home page workflow section in frontend', 'workflow-enable', 'Enable to show home page workflow section in frontend'),
(202, 'admin', 'Email Settings', 'email-settings', 'Email Settings'),
(203, 'admin', 'If you are using gmail smtp please make sure you have set below settings before sending mail', 'mail-info-title', 'If you are using gmail smtp please make sure you have set below settings before sending mail'),
(204, 'admin', 'Two factor authentication off', 'two-factor-off', 'Two factor authentication off'),
(205, 'admin', 'Less secure app on', 'less-secure-app-on', 'Less secure app on'),
(206, 'admin', 'Mail Type', 'mail-type', 'Mail Type'),
(207, 'admin', 'Mail Title', 'mail-title', 'Mail Title'),
(208, 'admin', 'Mail Host', 'mail-host', 'Mail Host'),
(209, 'admin', 'Mail Port', 'mail-port', 'Mail Port'),
(210, 'admin', 'Mail Username', 'mail-username', 'Mail Username'),
(211, 'admin', 'Mail Password', 'mail-password', 'Mail Password'),
(212, 'admin', 'Mail Encryption', 'mail-encryption', 'Mail Encryption'),
(213, 'admin', '  SSL is used for port 465/25, TLS is used for port 587', 'mail-port-help', '  SSL is used for port 465/25, TLS is used for port 587'),
(214, 'admin', 'Send Test Mail', 'send-test-mail', 'Send Test Mail'),
(215, 'admin', 'Social Settings', 'social-settings', 'Social Settings'),
(216, 'admin', 'Set default', 'set-default', 'Set default'),
(217, 'admin', 'Update', 'update', 'Update'),
(218, 'admin', 'Direction', 'direction', 'Direction'),
(219, 'admin', 'Status', 'status', 'Status'),
(220, 'admin', 'Action', 'action', 'Action'),
(221, 'admin', 'Currency', 'currency', 'Currency'),
(222, 'admin', 'Paypal mode', 'paypal-mode', 'Paypal mode'),
(223, 'admin', 'Paypal Account', 'paypal-account', 'Paypal Account'),
(224, 'admin', 'Publish key', 'publish-key', 'Public key'),
(225, 'admin', 'Secret key', 'secret-key', 'Secret key'),
(226, 'admin', 'Add Offline Payment', 'add-offline-payment', 'Add Offline Payment'),
(227, 'admin', 'Select User', 'select-user', 'Select User'),
(228, 'admin', 'Subscription type', 'subscription-type', 'Subscription type'),
(229, 'admin', 'Payment Status', 'payment-status', 'Payment Status'),
(230, 'admin', 'Manage Plans', 'manage-plans', 'Manage Plans'),
(231, 'admin', 'Show', 'show', 'Show'),
(232, 'admin', 'Hide', 'hide', 'Hide'),
(233, 'admin', 'Disable to hide this plan', 'disable-to-hide-this-plan', 'Disable to hide this plan'),
(234, 'admin', 'Active', 'active', 'Active'),
(235, 'admin', 'Edit Plan', 'edit-plan', 'Edit Plan'),
(236, 'admin', 'Update plan', 'update-plan', 'Update plan'),
(237, 'admin', 'Manage your plan settings here', 'manage-your-plan', 'Manage your plan settings here'),
(238, 'admin', 'Plan Settings', 'plan-settings', 'Plan Settings'),
(239, 'admin', 'Plan', 'plan', 'Plan'),
(240, 'admin', 'Choose which features you want to add in this plan', 'choose-which-features', 'Choose which features you want to add in this plan'),
(241, 'admin', 'Plan Name', 'plan-name', 'Plan Name'),
(242, 'admin', 'Monthly Price', 'monthly-price', 'Monthly Price'),
(243, 'admin', 'Yearly Price', 'yearly-price', 'Yearly Price'),
(244, 'admin', 'Set 0 price for free package', 'set-0-price-for-free-package', 'Set 0 price for free package'),
(245, 'admin', 'Online Consultation & get payments', 'online-consultation-get-payments', 'Online Consultation & get payments'),
(260, 'admin', 'Set limit -1 for unlimited', 'set-limit-1-for-unlimited', 'Set limit -1 for unlimited'),
(261, 'admin', 'Add New Department', 'add-new-department', 'Add New Department'),
(262, 'admin', 'All Users', 'all-users', 'All Users'),
(263, 'admin', 'Sort by Packages', 'sort-by-packages', 'Sort by Packages'),
(264, 'admin', 'Sort by Status', 'sort-by-status', 'Sort by Status'),
(265, 'admin', 'Avatar', 'avatar', 'Avatar'),
(266, 'admin', 'Account Status', 'account-status', 'Account Status'),
(267, 'admin', 'Joined', 'joined', 'Joined'),
(268, 'admin', 'All category', 'all-category', 'All category'),
(269, 'admin', ' Add new Category', 'add-new-category', ' Add new Category'),
(270, 'admin', 'Category Name', 'category-name', 'Category Name'),
(271, 'admin', 'Edit', 'edit', 'Edit'),
(272, 'admin', 'All Blog posts', 'all-blog-posts', 'All Blog posts'),
(273, 'admin', 'Thumb', 'thumb', 'Thumb'),
(274, 'admin', 'Title', 'title', 'Title'),
(275, 'admin', 'Details', 'details', 'Details'),
(276, 'admin', 'Add new blog', 'add-new-blog', 'Add new blog'),
(277, 'admin', 'Category ', 'category', 'Category '),
(278, 'admin', 'Slug', 'slug', 'Slug'),
(279, 'admin', 'Inactive', 'inactive', 'Inactive'),
(280, 'admin', 'All Services', 'all-services', 'All Services'),
(281, 'admin', 'Add new Services', 'add-new-services', 'Add new Services'),
(282, 'admin', 'Upload Image', 'upload-image', 'Upload Image'),
(283, 'admin', 'Order', 'order', 'Order'),
(284, 'admin', 'Add New service', 'add-new-service', 'Add New service'),
(285, 'admin', 'Add new page', 'add-new-page', 'Add new page'),
(286, 'admin', 'Page title', 'page-title', 'Page title'),
(287, 'admin', 'Page slug', 'page-slug', 'Page slug'),
(288, 'admin', 'Page description', 'page-description', 'Page description'),
(289, 'admin', 'All Faqs', 'all-faqs', 'All Faqs'),
(290, 'admin', 'Add New FAQ', 'add-new-faq', 'Add New FAQ'),
(291, 'admin', 'Old Password', 'old-password', 'Old Password'),
(292, 'admin', 'New Password', 'new-password', 'New Password'),
(293, 'admin', 'Confirm New Password', 'confirm-new-password', 'Confirm New Password'),
(294, 'front', 'Resources', 'resources', 'Resources'),
(295, 'front', 'Users', 'users', 'Users'),
(296, 'front', 'The better way to manage your chambers, prescriptions, appointments & patients', 'feature-home-title', 'The better way to manage your chambers, prescriptions, appointments & patients'),
(297, 'front', 'account you can easily manage chamber wise prescriptions, patients, appointments and many more features.', 'feature-home-desc', 'account you can easily manage chamber wise prescriptions, patients, appointments and many more features.'),
(298, 'front', 'Using', 'using', 'Using'),
(299, 'front', 'Features not selected !', 'features-not-selected', 'Features not selected !'),
(300, 'front', 'years experience', 'years-experience', 'years experience'),
(301, 'front', 'View Profile', 'view-profile', 'View Profile'),
(302, 'front', 'Explore our features', 'explore-our-features', 'Explore our features'),
(303, 'front', 'Read More', 'read-more', 'Read More'),
(304, 'front', 'Appointment schedule is not setted.', 'schedule-is-not-setted', 'Appointment schedule is not setted.'),
(305, 'front', 'Online Consultation', 'online-consultation', 'Online Consultation'),
(306, 'front', 'Offline', 'offline', 'Offline'),
(307, 'front', 'Email or Mobile', 'email-or-mobile', 'Email or Mobile'),
(308, 'front', 'Phone', 'phone', 'Phone'),
(309, 'front', 'Educations', 'educations', 'Educations'),
(310, 'front', 'Experiences', 'experiences', 'Experiences'),
(311, 'front', 'This profile is currently not available', 'profile-not-available', 'This profile is currently not available'),
(312, 'front', 'Upgrade your plan', 'upgrade-your-plan', 'Upgrade your plan'),
(313, 'front', 'Back to Home', 'back-to-home', 'Back to Home'),
(314, 'front', 'The resource requested could not be found on this site!', 'error-404', 'The resource requested could not be found on this site!'),
(315, 'front', 'Verify Account', 'verify-account', 'Verify Account'),
(316, 'front', 'We have sent a link to your registered email address, please click this link to verify your account', 'verify-email-sent-link', 'We have sent a link to your registered email address, please click this link to verify your account'),
(317, 'front', 'Email verification failed!', 'email-verification-failed', 'Email verification failed!'),
(318, 'front', 'Congratulation\'s', 'congratulations', 'Congratulation\'s'),
(319, 'front', 'Your account successfully verified', 'your-account-successfully-verified', 'Your account successfully verified'),
(320, 'front', 'Logout Successfully !', 'logout-successfully-', 'Logout Successfully !'),
(321, 'front', 'Recover password', 'recover-password', 'Recover password'),
(325, 'front', 'Enter username', 'enter-username', 'Enter username'),
(326, 'front', 'Enter password', 'enter-password', 'Enter password'),
(327, 'front', 'Registration system is disabled !', 'registration-system-is-disabled-', 'Registration system is disabled !'),
(328, 'front', 'Contact Admin', 'contact-admin', 'Contact Admin'),
(329, 'front', 'Get started with a', 'get-started-with-a', 'Get started with a'),
(335, 'admin', 'Create New', 'create-new', 'Create New'),
(336, 'admin', 'Lists', 'lists', 'Lists'),
(337, 'admin', 'Set Schedule', 'set-schedule', 'Set Schedule'),
(339, 'admin', 'Personal Info', 'personal-info', 'Personal Info'),
(340, 'admin', 'Manage Education', 'manage-education', 'Manage Education'),
(341, 'admin', 'Manage Experiences', 'manage-experiences', 'Manage Experiences'),
(342, 'admin', 'Profile', 'profile', 'Profile'),
(343, 'admin', 'Blog', 'blog', 'Blog'),
(344, 'admin', 'Today\'s Appointment', 'todays-appointment', 'Today\'s Appointment'),
(345, 'admin', 'Serial No', 'serial-no', 'Serial No'),
(349, 'admin', 'Schedule Info', 'schedule-info', 'Schedule Info'),
(351, 'admin', 'Online', 'online', 'Online'),
(353, 'admin', 'See all Users', 'see-all-users', 'See all Users'),
(354, 'admin', 'Save Changes', 'save-changes', 'Save Changes'),
(355, 'admin', 'mode', 'mode', 'mode'),
(356, 'admin', 'Add Payment', 'add-payment', 'Add Payment'),
(357, 'admin', 'Select Plans', 'select-plans', 'Select Plans'),
(358, 'admin', 'Enable to active this plan', 'enable-to-active-this-plan', 'Enable to active this plan'),
(359, 'admin', 'Hidden', 'hidden', 'Hidden'),
(360, 'admin', 'Enable access to', 'enable-access-to', 'Enable access to'),
(361, 'admin', 'feature in this plan', 'feature-in-this-plan', 'feature in this plan'),
(363, 'admin', 'Package', 'package', 'Package'),
(364, 'admin', 'Days left', 'days-left', 'Days left'),
(365, 'admin', 'Disabled', 'disabled', 'Disabled'),
(366, 'admin', 'All Categories', 'all-categories', 'All Categories'),
(367, 'admin', 'Add New Post', 'add-new-post', 'Add New Post'),
(368, 'admin', 'Enter your tags', 'enter-your-tags', 'Enter your tags'),
(369, 'admin', 'Accounts', 'accounts', 'Accounts'),
(486, 'admin', 'Insert your translate value here', 'insert-your-translate-value-here', 'Insert your translate value here'),
(487, 'front', 'Code resend successfully', 'email-resend-successfully', 'Code resend successfully'),
(490, 'front', 'Verify Code', 'verify-code', 'Verify Code'),
(493, 'admin', 'Yes, Start It', 'yes-start', 'Yes, Start It'),
(494, 'admin', 'Set this chamber as a default', 'set-this-chamber-default', 'Set this chamber as a default'),
(495, 'admin', 'Cancel this user serial', 'cancel-this-user-serial', 'Cancel this user serial'),
(496, 'admin', 'Serial cancel successfully', 'serial-cancel-success', 'Serial cancel successfully'),
(498, 'admin', 'Inserted Successfully !', 'inserted-successfully', 'Inserted Successfully !'),
(499, 'admin', 'Updated Successfully !', 'updated-successfully', 'Updated Successfully !'),
(509, 'front', 'days free trial', 'days-free-trial', 'days free trial'),
(510, 'admin', 'Multilingual System', 'multilingual-system', 'Multilingual System'),
(511, 'admin', 'Enable to allow multilingual system', 'enable-multilingual', 'Enable to allow multilingual system'),
(527, 'admin', 'Set 0 to hide trial option', 'set-trial-info', 'Set 0 to disable trial option'),
(528, 'admin', 'Label', 'label', 'Label'),
(529, 'admin', 'keyword', 'keyword', 'keyword'),
(530, 'admin', 'Type', 'type', 'Type'),
(531, 'admin', 'Value', 'value', 'Value'),
(532, 'front', 'Companies', 'companies', 'Companies'),
(533, 'front', 'Company Lists', 'company-lists', 'Company Lists'),
(534, 'front', 'All Countries', 'all-countries', 'All Countries'),
(535, 'front', 'View Page', 'view-page', 'View Page'),
(536, 'front', 'Customers', 'customers', 'Customers'),
(539, 'front', 'Gallery', 'gallery', 'Gallery'),
(540, 'front', 'Get Online Payments', 'get-online-payments', 'Get Online Payments'),
(541, 'front', 'Zoom Meeting', 'zoom-meeting', 'Virtual Meeting(Zoom, Google Meet)'),
(542, 'front', 'Book Now', 'book-now', 'Book Now'),
(543, 'front', 'About', 'about', 'About'),
(544, 'front', 'Business Days', 'business-days', 'Business Days'),
(546, 'front', 'Duration', 'duration', 'Duration'),
(547, 'front', 'Price', 'price', 'Price'),
(548, 'front', 'No image found!', 'no-image-found', 'No image found!'),
(549, 'front', 'Select Service', 'select-service', 'Select Service'),
(551, 'front', 'Select Date & Time', 'select-date-time', 'Select Date & Time'),
(552, 'front', 'Schedule not available', 'schedule-not-available', 'Schedule not available'),
(553, 'front', 'Pick Appointment Time For', 'pick-time-for', 'Pick Appointment Time For'),
(554, 'front', 'Easy step by step appointment booking', 'easy-step-booking-title', 'Easy step by step appointment booking'),
(555, 'front', 'Choose staff, schedule date & time to booking your services.', 'easy-step-booking-details', 'Choose staff, schedule date & time to booking your services.'),
(556, 'front', 'Phone Number', 'phone-number', 'Phone Number'),
(557, 'front', 'Select Country', 'select-country', 'Select Country'),
(558, 'front', 'Confirm Booking Details', 'confirm-booking-details', 'Confirm Booking Details'),
(559, 'front', 'You are almost done!', 'you-are-almost-done', 'You are almost done!'),
(560, 'front', 'Booking Number', 'booking-number', 'Booking Number'),
(561, 'front', 'Booking Info', 'booking-info', 'Booking Info'),
(562, 'front', 'Customer Info', 'customer-info', 'Customer Info'),
(563, 'front', 'Payment Info', 'payment-info', 'Payment Info'),
(564, 'front', 'Discount', 'discount', 'Discount'),
(565, 'front', 'Total Cost', 'total-cost', 'Total Cost'),
(566, 'front', 'Add Coupon', 'add-coupon', 'Add Coupon'),
(567, 'front', 'Code here', 'code-here', 'Code here'),
(568, 'front', 'Apply', 'apply', 'Apply'),
(569, 'front', 'Pay Now', 'pay-now', 'Pay Now'),
(570, 'front', 'Pay On Site', 'pay-on-site', 'Pay On Site'),
(571, 'front', 'All transactions are secure and encrypted. Credit card information is never stored.', 'secure-and-encrypted', 'All transactions are secure and encrypted. Credit card information is never stored.'),
(572, 'front', 'Confirm Booking', 'confirm-booking', 'Confirm Booking'),
(575, 'front', 'Approved', 'approved', 'Approved'),
(576, 'front', 'Completed', 'completed', 'Completed'),
(577, 'front', 'Register your company', 'register-your-company', 'Register your company'),
(578, 'front', 'Basic information, You can add more later', 'basic-information-you-can-add-more-later', 'Basic information, You can add more later'),
(579, 'front', 'Company Slug (Related to url & cannot be changed)', 'company-slug-restrict', 'Company Slug (Related to url & cannot be changed)'),
(580, 'front', 'Company Name', 'company-name', 'Company Name'),
(581, 'front', 'Categories', 'categories', 'Categories'),
(582, 'front', 'Country', 'country', 'Country'),
(583, 'front', 'Select Code', 'select-code', 'Select Code'),
(584, 'front', 'This site uses cookies. By continuing to browse the site you are agreeing to our use of cookies', 'accept_cookies', 'This site uses cookies. By continuing to browse the site you are agreeing to our use of cookies'),
(585, 'front', 'Accept', 'accept', 'Accept'),
(587, 'front', 'Card Details', 'card-details', 'Card Details'),
(588, 'front', 'Card Number', 'card-number', 'Card Number'),
(589, 'front', 'Cardholder\'s Name', 'cardholders-name', 'Cardholder\'s Name'),
(590, 'front', 'Loading', 'loading', 'Loading'),
(591, 'front', 'One Platform For any Business', 'one-platform-for-any-business', 'One Platform For any Business'),
(592, 'front', 'Smart booking tool to grow your online business', 'smart-booking-tool-to-grow-your-online-business', 'Smart booking tool to grow your online business'),
(593, 'front', 'The best solution to start your online business <br> with powerful features', 'the-best-solution-to-start', 'The best solution to start your online business <br> with powerful features'),
(594, 'front', 'Booking Website', 'booking-website', 'Booking Website'),
(595, 'front', 'You will get a ready to use booking site after signup in', 'booking-website-title', 'You will get a ready to use booking site after signup in'),
(596, 'front', 'Accept online bookings', 'accept-online-bookings', 'Accept online bookings'),
(597, 'front', 'Accept bookings from your clients using your own booking site', 'accept-online-bookings-title', 'Accept bookings from your clients using your own booking site'),
(600, 'front', 'Accept Payments', 'accept-payments', 'Accept Payments'),
(601, 'front', 'Accept Online / Offline payments from your clients', 'accept-payments-title', 'Accept Online / Offline payments from your clients'),
(602, 'front', 'Customize your appointment schedule and booking page', 'customize-your-appointment-schedule-and-booking-page', 'Customize your appointment schedule and booking page'),
(605, 'front', 'Sign up for our 14-day trial with all features. No credit card required', 'sign-up-for-our-14-day-trial-with-all-features', 'Sign up for our 14-day trial with all features. No credit card required'),
(606, 'front', 'Write more details', 'write-more-details', 'Write more details'),
(607, 'front', 'Features not selected !', 'features-not-selected-', 'Features not selected !'),
(608, 'front', 'year', 'year', 'year'),
(609, 'front', 'month', 'month', 'month'),
(610, 'front', 'Admin/User', 'adminuser', 'Admin/User'),
(611, 'front', 'Customer', 'customer', 'Customer'),
(612, 'front', 'Enter email or username', 'enter-email-or-username', 'Enter email or username'),
(613, 'front', 'Name of your company', 'name-of-your-company', 'Name of your company'),
(614, 'front', 'Select', 'select', 'Select'),
(615, 'front', 'This name is already taken, try another one', 'name-is-already-taken', 'This name is already taken, try another one'),
(616, 'front', 'Name is available', 'name-is-available', 'Name is available'),
(617, 'front', 'We have send a verification code in your', 'we-have-send-a-verification-code-in-your', 'We have send a verification code in your'),
(618, 'front', 'Enter Code here', 'enter-code-here', 'Enter Code here'),
(619, 'front', 'Resend', 'resend', 'Resend'),
(620, 'front', 'Open', 'open', 'Open'),
(621, 'front', 'Free', 'free', 'Free'),
(622, 'front', 'Booking is temporary unavailable!', 'booking-is-temporary-unavailable', 'Booking is temporary unavailable!'),
(623, 'front', 'Capacity', 'capacity', 'Capacity'),
(625, 'front', 'Invalid code', 'invalid-code', 'Invalid code'),
(626, 'front', 'You have already applied this code', 'already-applied-code', 'You have already applied this code'),
(627, 'front', 'Coupon applied successfully!', 'coupon-applied-successfully', 'Coupon applied successfully!'),
(628, 'front', 'off', 'off', 'off'),
(630, 'front', 'Rejected', 'rejected', 'Rejected'),
(631, 'front', 'Start Time', 'start-time', 'Start Time'),
(632, 'front', 'End Time', 'end-time', 'End Time'),
(633, 'front', 'Cancelled', 'cancelled', 'Cancelled'),
(634, 'front', 'Complete your payment', 'complete-your-payment', 'Complete your payment'),
(636, 'front', 'Zoom Meeting Link', 'zoom-meeting-link', 'Zoom Meeting Link'),
(637, 'front', 'Booking Confirmation', 'booking-confirmation', 'Booking Confirmation'),
(638, 'front', 'Please complete your payment to confirm the booking', 'confirm-the-booking', 'Please complete your payment to confirm the booking'),
(639, 'admin', 'View Site', 'view-site', 'View Site'),
(640, 'admin', 'Manage Profile', 'manage-profile', 'Manage Profile'),
(641, 'admin', 'License', 'license', 'License'),
(642, 'admin', 'Transactions', 'transactions', 'Transactions'),
(643, 'admin', 'Features', 'features', 'Features'),
(644, 'admin', 'Contacts', 'contacts', 'Contacts'),
(645, 'admin', 'Info', 'info', 'Info'),
(646, 'user', 'Company Settings', 'company-settings', 'Company Settings'),
(647, 'user', 'Working Hours', 'working-hours', 'Working Hours'),
(648, 'admin', 'Latest Users', 'latest-users', 'Latest Users'),
(649, 'admin', 'Joining date', 'joining-date', 'Joining date'),
(651, 'admin', 'Charts', 'charts', 'Charts'),
(652, 'admin', 'SMS Settings', 'sms-settings', 'SMS Settings'),
(654, 'admin', 'For better view please use logo size 300 ✕ 150', 'logo-suggestions', 'For better view please use logo size 300 ✕ 150'),
(655, 'admin', 'Upload hero image', 'upload-hero-image', 'Upload hero image'),
(656, 'admin', 'Set 0 to disable the trial option', 'set-0-to-disable-the-trial-option', 'Set 0 to disable the trial option'),
(657, 'admin', 'Preferences', 'prefrences', 'Preferences'),
(659, 'admin', 'SMS Verification', 'sms-verification', 'SMS Verification'),
(660, 'admin', 'Enable to allow sms verification for registered users.', 'sms-title1', 'Enable to allow sms verification for registered users.'),
(661, 'admin', 'Note: If you want to enable sms verification please make sure you have disabled the email verification', 'sms-title2', 'Note: If you want to enable sms verification please make sure you have disabled the email verification'),
(663, 'admin', 'Enable to show company list in frontend', 'company-list-title', 'Enable to show company list in frontend'),
(664, 'admin', 'Features Intro', 'features-intro', 'Features Intro'),
(665, 'admin', 'Enable to show home page feature intro section in frontend', 'features-intro-title', 'Enable to show home page feature intro section in frontend'),
(671, 'admin', 'Account SID', 'account-sid', 'Account SID'),
(672, 'admin', 'Auth Token', 'auth-token', 'Auth Token'),
(673, 'admin', 'Sender Number (Twillo)', 'sender-number-tw', 'Sender Number (Twillo)'),
(674, 'admin', 'Tax Name', 'tax-name', 'Tax Name'),
(675, 'admin', 'Tax Amount', 'tax-amount', 'Tax Amount'),
(676, 'admin', 'Gmail Smtp', 'gmail-smtp', 'Gmail Smtp'),
(677, 'admin', 'Two factor authentication off ', 'two-factor-authentication-off', 'Two factor authentication off '),
(678, 'admin', 'Captcha Site key', 'captcha-site-key', 'Captcha Site key'),
(679, 'admin', 'Captcha Secret key', 'captcha-secret-key', 'Captcha Secret key'),
(680, 'admin', 'Payment', 'payment', 'Payment'),
(681, 'admin', 'This currency will used to receive your subscription payments', 'currency-title', 'This currency will used to receive your subscription payments'),
(682, 'admin', 'Setup Your Paypal Account to Accept Payments', 'paypal-title', 'Setup Your Paypal Account to Accept Payments'),
(683, 'admin', 'Setup Your Stripe Account to Accept Payments', 'stripe-title', 'Setup Your Stripe Account to Accept Payments'),
(684, 'admin', 'Offline Payment', 'offline-payment', 'Offline Payment'),
(685, 'admin', 'Setup Your Bank Info to receive offline payment directly to your bank account', 'offline-payment-title', 'Setup Your Bank Info to receive offline payment directly to your bank account'),
(686, 'admin', 'Offline Payment Instructions', 'offline-payment-instructions', 'Offline Payment Instructions'),
(687, 'admin', 'Your customer will see this instruction before submit payment', 'offline-payment-suggestions', 'Your customer will see this instruction before submit payment'),
(688, 'admin', 'License Info', 'license-info', 'License Info'),
(689, 'admin', 'License Type', 'license-type', 'License Type'),
(690, 'admin', 'Regular', 'regular', 'Regular'),
(691, 'admin', 'Extended', 'extended', 'Extended'),
(692, 'admin', 'If you want to upgrade your license from regular to extended please send email to us', 'license-upgrade-info', 'If you want to upgrade your license from regular to extended please send email to us'),
(693, 'admin', 'Click to Send Mail', 'click-to-send-mail', 'Click to Send Mail'),
(694, 'admin', 'Disable', 'disable', 'Disable'),
(695, 'admin', 'Approve Payment', 'approve-payment', 'Approve Payment'),
(696, 'admin', 'View Proof', 'view-proof', 'View Proof'),
(697, 'admin', 'View Invoice', 'view-invoice', 'View Invoice'),
(699, 'admin', 'Posts', 'posts', 'Posts'),
(700, 'admin', 'Filters', 'filters', 'Filters'),
(701, 'admin', 'Informations', 'informations', 'Informations'),
(702, 'admin', 'Booking Page', 'booking-page', 'Booking Page'),
(703, 'admin', 'Expire', 'expire', 'Expire'),
(704, 'admin', 'Record Payment', 'record-payment', 'Record Payment'),
(705, 'admin', 'All', 'all', 'All'),
(706, 'admin', 'Image', 'image', 'Image'),
(707, 'admin', 'Example', 'example', 'Example'),
(709, 'user', 'Time & Date', 'time-date', 'Time & Date'),
(710, 'user', 'Created', 'created', 'Created'),
(715, 'user', 'Yes Continue', 'yes-continue', 'Yes Continue'),
(716, 'user', 'Embedded Code', 'embedded-code', 'Embedded Code'),
(717, 'user', 'QR Code', 'qr-code', 'QR Code'),
(719, 'user', 'Banner image', 'banner-image', 'Banner image'),
(720, 'user', 'Company  Title', 'company-title', 'Company  Title'),
(721, 'user', 'Date Format', 'date-format', 'Date Format'),
(722, 'user', 'Enable Gallery', 'enable-gallery', 'Enable Gallery'),
(723, 'user', 'Enable to show gallery option in booking page', 'enable-gallery-title', 'Enable to show gallery option in booking page'),
(724, 'user', 'Sunday', 'sunday', 'Sunday'),
(725, 'user', 'Monday', 'monday', 'Monday'),
(726, 'user', 'Tuesday', 'tuesday', 'Tuesday'),
(727, 'user', 'Wednesday', 'wednesday', 'Wednesday'),
(728, 'user', 'Thursday', 'thursday', 'Thursday'),
(729, 'user', 'Friday', 'friday', 'Friday'),
(730, 'user', 'Satarday', 'satarday', 'Saturday'),
(731, 'user', 'Add new time', 'add-new-time', 'Add new time'),
(732, 'user', 'Enable Booking Confirmation SMS', 'enable-booking-confirmation-sms', 'Enable Booking Confirmation SMS'),
(733, 'user', 'Enable to send booking notification message to your customers, after make a appointment', 'enable-booking-con-title', 'Enable to send booking notification message to your customers, after make a appointment'),
(734, 'user', 'Enable Booking Reminder Alert', 'enable-booking-reminder-alert', 'Enable Booking Reminder Alert'),
(735, 'user', 'Enable to send booking reminder alert to your users before booking expire', 'enable-booking-alert-title', 'Enable to send booking reminder alert to your users before booking expire'),
(736, 'user', 'Paypal', 'paypal', 'Paypal'),
(737, 'user', 'Stripe', 'stripe', 'Stripe'),
(738, 'user', 'Sandbox', 'sandbox', 'Sandbox'),
(739, 'user', 'Live', 'live', 'Live'),
(740, 'user', 'Your payment has been completed Successfully', 'payment-completed-successfully', 'Your payment has been completed Successfully'),
(741, 'user', 'Your payment has been failed', 'your-payment-has-been-failed', 'Your payment has been failed'),
(742, 'user', 'Copy below code to show your booking page in another site', 'embed-code-copy', ' Copy below code and add to your website'),
(743, 'user', 'Share your business page using QR Code', 'share-qr-code', 'Share your business page using QR Code'),
(744, 'user', 'Preview', 'preview', 'Preview'),
(745, 'user', 'Download', 'download', 'Download'),
(746, 'user', 'New Appointment', 'new-appointment', 'New Appointment'),
(747, 'user', 'Notify Customers', 'notify-customers', 'Notify Customers'),
(748, 'user', 'Booking date', 'booking-date', 'Booking date'),
(749, 'user', 'Today', 'today', 'Today'),
(750, 'user', 'Tomorrow', 'tomorrow', 'Tomorrow'),
(751, 'user', 'Next 7 days', 'next-7-days', 'Next 7 days'),
(752, 'user', 'Next 15 days', 'next-15-days', 'Next 15 days'),
(753, 'user', 'Date & Time', 'date-time', 'Date & Time'),
(754, 'user', 'Reset', 'reset', 'Reset'),
(755, 'user', 'Enter email for username', 'enter-email-for-username', 'Enter email for username'),
(756, 'user', 'Set or reset password', 'set-or-reset-password', 'Set or reset password'),
(757, 'user', 'Create New Category', 'create-new-category', 'Create New Category'),
(765, 'user', 'Minutes', 'minutes', 'Minutes'),
(766, 'user', 'Allow Zoom Meeting', 'allow-zoom-meeting', 'Allow Zoom Meeting'),
(767, 'user', 'Zoom Invitation Link', 'zoom-invitation-link', 'Zoom Invitation Link'),
(768, 'user', 'Not found', 'not-found', 'Not found'),
(770, 'user', 'New Coupon', 'new-coupon', 'New Coupon'),
(771, 'user', 'Coupons', 'coupons', 'Coupons'),
(772, 'user', 'Limit', 'limit', 'Limit'),
(773, 'user', 'Once per customer', 'once-per-customer', 'Once per customer'),
(774, 'user', 'Code', 'code', 'Code'),
(775, 'user', 'Yes', 'yes', 'Yes'),
(776, 'user', 'No', 'no', 'No'),
(777, 'user', 'Start Date', 'start-date', 'Start Date'),
(778, 'user', 'End Date', 'end-date', 'End Date'),
(780, 'user', 'Galleries', 'galleries', 'Galleries'),
(781, 'user', 'Upload Payment Proof', 'upload-payment-proof', 'Upload Payment Proof'),
(782, 'user', 'Please select a valid date', 'select-a-valid-date', 'Please select a valid date'),
(783, 'user', 'Downloaded Successfully', 'downloaded-successfully', 'Downloaded Successfully'),
(787, 'front', 'Canceled Successfully', 'canceled-successfully', 'Canceled Successfully'),
(789, 'admin', 'Translate Language', 'translate-language', 'Translate Language'),
(790, 'user', 'Calendars', 'calendars', 'Calendars'),
(791, 'user', 'Item', 'item', 'Item'),
(792, 'user', 'Total', 'total', 'Total'),
(793, 'user', 'Sub Total', 'sub-total', 'Sub Total'),
(794, 'user', 'Order No', 'order-no', 'Order No'),
(795, 'user', 'Invoice', 'invoice', 'Invoice'),
(796, 'user', 'Time Format', 'time-format', 'Time Format'),
(797, 'user', 'Hours', 'hours', 'Hours'),
(798, 'user', 'Time interval', 'time-interval', 'Time interval'),
(800, 'user', 'Actions are disabled for demo purposes', 'action-off', 'Actions are disabled for demo purposes'),
(803, 'admin', 'You have reached the maximum limit! Please upgrade your plan.', 'reached-maximum-limit', 'You have reached the maximum limit! Please upgrade your plan'),
(804, 'user', 'Enable Category', 'enable-category', 'Enable Category'),
(805, 'user', 'Disable Category', 'disable-category', 'Disable Category'),
(806, 'user', 'Location', 'location', 'Location'),
(807, 'user', 'Locations', 'locations', 'Locations'),
(808, 'user', 'Sub location', 'sub-location', 'Sub location'),
(809, 'user', 'Sub locations', 'sub-locations', 'Sub locations'),
(810, 'user', 'Currency location', 'currency-location', 'Currency location'),
(811, 'user', 'Number format', 'number-format', 'Number format'),
(812, 'user', 'Currency Position', 'currency-position', 'Currency Position'),
(813, 'user', 'Paid', 'paid', 'Paid'),
(814, 'user', 'Minute', 'minute', 'Minute'),
(815, 'user', 'Hour', 'hour', 'Hour'),
(816, 'user', 'Send SMS Reminder', 'send-sms-reminder', 'Send SMS Reminder'),
(817, 'user', 'Review', 'review', 'Review'),
(818, 'user', 'Reviews', 'reviews', 'Reviews'),
(820, 'user', 'Average Rating', 'average-rating', 'Average Rating'),
(821, 'user', 'Ratings Summary', 'ratings-summary', 'Ratings Summary'),
(822, 'user', 'Ratings', 'ratings', 'Ratings'),
(824, 'user', 'Enable Ratings', 'enable-ratings', 'Enable Ratings'),
(825, 'user', 'Enable to publicly visible service ratings, Until complete 3 ratings it will be hidden', 'enable-ratings-title', 'Enable to publicly visible service ratings, Until complete 3 ratings it will be hidden'),
(826, 'user', 'Learn more', 'learn-more', 'Learn more'),
(827, 'user', 'Write your review', 'write-review', 'Write your review'),
(828, 'user', 'January', 'january', 'January'),
(829, 'user', 'February', 'february', 'February'),
(830, 'user', 'March', 'march', 'March'),
(831, 'user', 'April', 'april', 'April'),
(832, 'user', 'May', 'may', 'May'),
(833, 'user', 'June', 'june', 'June'),
(834, 'user', 'July', 'july', 'July'),
(835, 'user', 'August', 'august', 'August'),
(836, 'user', 'September', 'september', 'September'),
(837, 'user', 'October', 'october', 'October'),
(838, 'user', 'November', 'november', 'November'),
(839, 'user', 'December', 'december', 'December'),
(840, 'user', 'Su', 'su', 'Su'),
(841, 'user', 'Mo', 'mo', 'Mo'),
(842, 'user', 'Tu', 'tu', 'Tu'),
(843, 'user', 'We', 'we', 'We'),
(844, 'user', 'Th', 'th', 'Th'),
(845, 'user', 'Fr', 'fr', 'Fr'),
(846, 'user', 'Sa', 'sa', 'Sa'),
(847, 'user', 'Days', 'days', 'Days'),
(848, 'user', 'Day', 'day', 'Day'),
(849, 'user', 'Kay Id', 'kay-id', 'Key Id'),
(850, 'user', 'Key Secret', 'key-secret', 'Key Secret'),
(851, 'user', 'Setup your Razorpay account to accept payments', 'razorpay-title', 'Setup your Razorpay account to accept payments'),
(852, 'user', 'Razorpay ', 'razorpay', 'Razorpay '),
(853, 'user', 'Opening Hour', 'opening-hour', 'Opening Hour'),
(854, 'user', 'End Hour', 'end-hour', 'End Hour'),
(855, 'user', 'Branches', 'branches', 'Branches'),
(856, 'user', 'Enable Locations', 'enable-locations', 'Enable Locations'),
(857, 'user', 'Disable Locations', 'disable-locations', 'Disable Locations'),
(858, 'user', 'Enable to allow locations in booking page', 'enable-location-title', 'Enable to allow locations in booking page'),
(859, 'user', 'Disable to hide locations in booking page', 'disable-location-title', 'Disable to hide locations in booking page'),
(860, 'user', 'Any special notes?', 'any-special-notes', 'Any special notes?'),
(861, 'user', 'Write your notes here', 'write-your-notes-here', 'Write your notes here'),
(862, 'user', 'Enable Frontend', 'enable-frontend', 'Enable Frontend'),
(863, 'user', 'Enable to show frontend site', 'enable-to-show-frontend-site', 'Enable to show frontend site'),
(864, 'user', 'View Details', 'view-details', 'View Details'),
(868, 'user', 'Add Breaks', 'add-breaks', 'Add Breaks'),
(869, 'user', 'This phone number will used for as username', 'phone-as-username', 'This phone number will used for as username'),
(870, 'user', 'Search', 'search', 'Search'),
(871, 'user', 'Search Value', 'search-value', 'Search Value'),
(872, 'user', ' Twillo SMS Settings', 'twillo-sms-settings', ' Twillo SMS Settings'),
(873, 'user', 'Cancel', 'cancel', 'Cancel'),
(874, 'user', 'Phone already exist', 'phone-exist', 'Phone already exist'),
(875, 'user', 'Persons', 'persons', 'Persons'),
(876, 'user', 'Bringing anyone with you?', 'bringing-anyone-with-you', 'Bringing anyone with you?'),
(877, 'user', 'Additional Persons:', 'additional-persons', 'Additional Persons:'),
(878, 'user', 'General Settings', 'general-settings', 'General Settings'),
(879, 'user', 'Enable Group Booking', 'enable-group-booking', 'Enable Group Booking'),
(881, 'user', 'Maximum allowed additional persons', 'max-allowed-persons', 'Maximum allowed additional persons'),
(882, 'user', 'Group Booking', 'group-booking', 'Group Booking'),
(883, 'user', 'Payments', 'payments', 'Payments'),
(884, 'user', 'just now', 'just-now', 'just now'),
(885, 'user', 'one minute ago', 'one-minute-ago', 'one minute ago'),
(886, 'user', 'minutes ago', 'minutes-ago', 'minutes ago'),
(887, 'user', 'an hour ago', 'an-hour-ago', 'an hour ago'),
(888, 'user', 'hours ago', 'hours-ago', 'hours ago'),
(889, 'user', 'yesterday', 'yesterday', 'yesterday'),
(890, 'user', 'days ago', 'days-ago', 'days ago'),
(891, 'user', 'weeks ago', 'weeks-ago', 'weeks ago'),
(892, 'user', 'a month ago', 'a-month-ago', 'a month ago'),
(893, 'user', 'months ago', 'months-ago', 'months ago'),
(894, 'user', 'one year ago', 'one-year-ago', 'one year ago'),
(895, 'user', 'years ago', 'years-ago', 'years ago'),
(896, 'user', 'Jan', 'jan', 'Jan'),
(897, 'user', 'Feb', 'feb', 'Feb'),
(898, 'user', 'Mar', 'mar', 'Mar'),
(899, 'user', 'Apr', 'apr', 'Apr'),
(900, 'user', 'Jun', 'jun', 'Jun');
INSERT INTO `lang_values` (`id`, `type`, `label`, `keyword`, `english`) VALUES
(901, 'user', 'Jul', 'jul', 'Jul'),
(902, 'user', 'Aug', 'aug', 'Aug'),
(903, 'user', 'Sep', 'sep', 'Sep'),
(904, 'user', 'Oct', 'oct', 'Oct'),
(905, 'user', 'Nov', 'nov', 'Nov'),
(906, 'user', 'Dec', 'dec', 'Dec'),
(907, 'user', 'Facebook', 'facebook', 'Facebook'),
(908, 'user', 'Twitter', 'twitter', 'Twitter'),
(909, 'user', 'Instagram', 'instagram', 'Instagram'),
(910, 'user', 'WhatsApp', 'whatsapp', 'WhatsApp'),
(911, 'user', 'LinkedIn', 'linkedin', 'LinkedIn'),
(912, 'user', 'Google Analytics', 'google-analytics', 'Google Analytics'),
(913, 'user', 'reCaptcha', 'recaptcha', 'reCaptcha'),
(914, 'user', 'Total Persons', 'total-persons', 'Total Persons'),
(915, 'user', 'Apply service duration to generate booking time slots', 'generate-booking-time-slots', 'Apply service duration to generate booking time slots'),
(916, 'user', 'Apply fixed duration to generate booking time slots', 'fixed-booking-time-slots', 'Apply fixed duration to generate booking time slots'),
(917, 'user', 'Enable Online Payments', 'enable-online-payment', 'Enable Online Payments'),
(918, 'user', 'Enable to active only payment methods to receive booking payments', 'enable-online-title', 'Enable to active online payment methods to receive booking payments'),
(919, 'user', 'Enable offline payment', 'enable-offline-payment', 'Enable offline payment'),
(920, 'user', 'Enable to active onsite payment option', 'enable-offline-title', 'Enable to active onsite payment option'),
(921, 'admin', 'Not Assigned', 'not-assigned', 'Not Assigned'),
(922, 'admin', 'Notes', 'notes', 'Notes'),
(923, 'admin', 'Appearance', 'appearance', 'Appearance'),
(924, 'admin', 'Frontend Color', 'frontend-color', 'Frontend Color'),
(925, 'admin', 'Set layout', 'set-layout', 'Set layout'),
(926, 'admin', 'Light', 'light', 'Light'),
(927, 'admin', 'Dark', 'dark', 'Dark'),
(928, 'admin', 'Used', 'used', 'Used'),
(929, 'admin', 'Select payment method', 'select-payment-method', 'Select payment method'),
(930, 'admin', 'Designation', 'designation', 'Designation'),
(931, 'admin', 'Feedback', 'feedback', 'Feedback'),
(932, 'admin', 'Testimonials', 'testimonials', 'Testimonials'),
(933, 'admin', 'What our customer says about ', 'testimonial-title', 'What our customer says about '),
(934, 'admin', 'SEO Settings', 'seo-settings', 'SEO Settings'),
(935, 'admin', 'Reports', 'reports', 'Reports'),
(936, 'admin', 'Most booked customers', 'most-booked-customers', 'Most booked customers'),
(937, 'admin', 'Most serviced staffs', 'most-serviced-staffs', 'Most serviced staffs'),
(938, 'admin', 'Most booked services', 'most-booked-services', 'Most booked services'),
(939, 'admin', 'Time Zone', 'time-zone', 'Time Zone'),
(940, 'admin', 'Enable to allow locations in booking page and your staffs will be available by locations', 'location-title-2', 'Enable to allow locations in booking page and your staffs will be available by location'),
(941, 'admin', 'left', 'left', 'left'),
(942, 'admin', 'Calendar Settings', 'calendar-settings', 'Calendar Settings'),
(943, 'admin', 'Client Id', 'client-id', 'Client Id'),
(944, 'admin', 'Client Secret', 'client-secret', 'Client Secret'),
(945, 'admin', 'Google Calendar', 'google-calendar', 'Google Calendar'),
(946, 'admin', 'Authorized redirect URIs', 'authorized-redirect-uris', 'Authorized redirect URIs'),
(947, 'admin', 'Google Callback URL', 'google-callback-url', 'Google Callback URL'),
(948, 'admin', 'Google Calendar Sync', 'google-calendar-sync', 'Google Calendar Sync'),
(949, 'admin', 'Sync Google Calendar', 'sync-google-calendar', 'Sync Google Calendar'),
(950, 'admin', 'Google Calendar Integration Doc', 'google-calendar-integration', 'Google Calendar Integration Doc'),
(955, 'admin', 'For better view use', 'for-better-view-use', 'For better view use'),
(956, 'admin', 'Header Script Codes', 'header-script-codes', 'Header Script Codes'),
(957, 'admin', 'Paste google analytics, Facebook Pixel or any other script codes here', 'header-script-codes-title', 'Paste google analytics, Facebook Pixel or any other script codes here'),
(958, 'admin', 'Welcome to', 'welcome-to', 'Welcome to'),
(959, 'admin', 'Your verification code is', 'your-verification-code-is', 'Your verification code is'),
(961, 'admin', 'booking is confirmed at', 'booking-is-confirmed-at', 'booking is confirmed at'),
(964, 'admin', 'Please login to your account for more details', 'login-more-details', 'Please login to your account for more details'),
(965, 'user', 'Redeem Coupon', 'redeem-coupon', 'Redeem Coupon'),
(966, 'user', 'Apply your coupon code here', 'apply-your-coupon-code-here', 'Apply your coupon code here'),
(967, 'user', 'Coupons limit', 'coupons-limit', 'Coupons limit'),
(968, 'user', 'How many random codes you want to generate?', 'coupons-limit-title', 'How many random codes you want to generate?'),
(969, 'user', 'Coupon code length', 'coupon-code-length', 'Coupon code length'),
(970, 'user', 'Random code will be generated based on your given length', 'coupon-code-length-title', 'Random code will be generated based on your given length'),
(971, 'user', 'Characters', 'characters', 'Characters'),
(972, 'user', 'How many days will be active this coupon', 'how-many-days-will-be-active-this-coupon', 'How many days will be active this coupon'),
(973, 'user', 'Discount must be between 1% - 99%', 'discount-must-be-between', 'Discount must be between 1% - 99%'),
(974, 'user', 'Export as CSV', 'export-as-csv', 'Export as CSV'),
(975, 'user', 'Codes', 'codes', 'Codes'),
(976, 'user', 'See all codes', 'see-all-codes', 'See all codes'),
(977, 'user', 'Your name string contains illegal characters.', 'illegal-characters-title', 'Your name string contains illegal characters.'),
(978, 'user', 'Please Complete these steps', 'please-complete-these-steps', 'Please Complete these steps'),
(979, 'user', 'Set Business Hours', 'set-business-hours', 'Set Business Hours'),
(983, 'user', 'Enter phone number with dial code', 'enter-phone-number-with-dial-code', 'Enter phone number with dial code'),
(984, 'user', 'Cities', 'cities', 'Cities'),
(985, 'user', 'Location is required', 'location-required', 'Location is required'),
(986, 'admin', 'Paystack', 'paystack', 'Paystack'),
(987, 'admin', 'Setup Your Paystack Account to Accept Payments', 'paystack-title', 'Setup Your Paystack Account to Accept Payments'),
(988, 'user', 'Recently booked an appointment at', 'recently-booked-an-appointment', 'Recently booked an appointment at'),
(989, 'user', 'New appointment is booked', 'new-appointment-is-booked', 'Booked new appointment'),
(990, 'user', 'Quantity', 'quantity', 'Quantity'),
(991, 'user', 'Coupon code already applied', 'coupon-code-already-applied', 'Coupon code already applied'),
(992, 'user', 'Have any coupon code?', 'have-any-coupon-code', 'Have any coupon code?'),
(993, 'user', 'Enable to active coupon code feature', 'enable-coupon-title', 'Enable to active coupon code feature'),
(994, 'user', 'Allow Google Meet', 'allow-google-meet', 'Allow Google Meet'),
(995, 'user', 'Google meet link', 'google-meet-link', 'Google meet invitation link'),
(996, 'user', 'Google Meet', 'google-meet', 'Google Meet'),
(997, 'user', 'Virtual Meeting', 'virtual-meeting', 'Virtual Meeting'),
(998, 'user', 'Zoom', 'zoom', 'Zoom'),
(999, 'user', 'Enable Coupon from', 'enable-coupon-from', 'Enable Coupon from'),
(1000, 'user', 'Holidays', 'holidays', 'Holidays'),
(1001, 'user', 'Interval Settings', 'interval-settings', 'Interval Settings'),
(1003, 'user', 'Send reminder email', 'send-reminder-email', 'Send reminder email'),
(1004, 'user', 'Same day', 'same-day', 'Same day'),
(1005, 'user', 'Before', 'before', 'Before'),
(1006, 'user', 'Login', 'login', 'Login'),
(1007, 'user', 'Trial', 'trial', 'Trial'),
(1008, 'user', 'Plan Coupons', 'plan-coupons', 'Plan Coupons'),
(1009, 'user', 'System Settings', 'system-settings', 'System Settings'),
(1010, 'user', 'Guest Booking', 'guest-booking', 'Guest Booking'),
(1011, 'user', 'Enable Guest Booking', 'enable-guest-booking', 'Enable Guest Booking'),
(1012, 'user', 'Enable to allow guest booking', 'enable-guest-booking-title', 'Enable to allow guest booking'),
(1013, 'user', 'Wallet Settings', 'wallet-settings', 'Wallet Settings'),
(1014, 'user', 'Commission Rate', 'commission-rate', 'Commission Rate'),
(1015, 'user', 'Minimum Payout Amount', 'minimum-payout-amount', 'Minimum Payout Amount'),
(1016, 'user', 'Enable Payouts', 'enable-payouts', 'Enable Payouts'),
(1017, 'user', 'Enable to active payouts module and receive users appointment payment to admin account.', 'enable-payout-title', 'Enable to active payouts module and receive users appointment payment to admin account.'),
(1018, 'user', 'Payouts', 'payouts', 'Payouts'),
(1019, 'user', 'Setup Payout Accounts', 'setup-payout-accounts', 'Setup Payout Accounts'),
(1020, 'user', 'Set Payout Account', 'set-payout-account', 'Set Payout Account'),
(1021, 'user', 'Full Name', 'full-name', 'Full Name'),
(1022, 'user', 'IBAN', 'iban', 'IBAN'),
(1023, 'user', 'Bank Name', 'bank-name', 'Bank Name'),
(1024, 'user', 'International Bank Account Number(IBAN) ', 'iban-number', 'International Bank Account Number(IBAN) '),
(1025, 'user', 'State', 'state', 'State'),
(1026, 'user', 'City', 'city', 'City'),
(1027, 'user', 'Postcode', 'post-code', 'Postcode'),
(1028, 'user', 'Bank Account Holder\'s Name', 'bank-account-holders-name', 'Bank Account Holder\'s Name'),
(1029, 'user', 'Bank Branch Country', 'bank-branch-country', 'Bank Branch Country'),
(1030, 'user', 'Bank Branch City', 'bank-branch-city', 'Bank Branch City'),
(1031, 'user', 'Bank Account Number', 'bank-account-number', 'Bank Account Number'),
(1032, 'user', 'Swift Code', 'swift-code', 'Swift Code'),
(1033, 'user', 'Swift', 'swift', 'Swift'),
(1034, 'user', 'Invalid withdrawal amount!', 'invalid-withdrawal-amount', 'Invalid withdrawal amount!'),
(1035, 'user', 'Payout request sent successfully !', 'payout-request-sent-successfully', 'Payout request sent successfully !'),
(1036, 'user', 'Minimum Payout Amounts', 'minimum-payout-amounts', 'Minimum Payout Amounts'),
(1037, 'user', 'Empty Paypal email', 'empty-paypal-email', 'Empty Paypal email'),
(1038, 'user', 'Empty IBAN info', 'empty-iban-info', 'Empty IBAN info'),
(1039, 'user', 'Empty Swift info', 'empty-swift-info', 'Empty Swift info'),
(1040, 'user', 'Transaction ID', 'transaction-id', 'Transaction ID'),
(1041, 'user', 'Withdrawal Method', 'withdrawal-method', 'Withdrawal Method'),
(1042, 'user', 'Amount', 'amount', 'Amount'),
(1043, 'user', 'Send Payout Request', 'send-payout-request', 'Send Payout Request'),
(1044, 'user', 'Total Earnings', 'total-earnings', 'Total Earnings'),
(1045, 'user', 'Total Withdraw', 'total-withdraw', 'Total Withdraw'),
(1046, 'user', 'Balance', 'balance', 'Balance'),
(1047, 'user', 'after commission of', 'after-commission-of', 'after commission of'),
(1048, 'user', 'Payout Settings', 'payout-settings', 'Payout Settings'),
(1049, 'user', 'Payout Requests', 'payout-requests', 'Payout Requests'),
(1050, 'user', 'Payout Completed', 'payout-completed', 'Payout Completed'),
(1051, 'user', 'Request Sent', 'request-sent', 'Request Sent'),
(1052, 'user', 'Enable / Disable Payout Methods', 'enabledisable-payout-methods', 'Enable / Disable Payout Methods'),
(1053, 'user', 'must be between 1% - 99%', 'must-be-between-1-99', 'must be between 1% - 99%'),
(1054, 'user', 'Payout History', 'payout-history', 'Payout History'),
(1055, 'user', 'Payout Method', 'payout-method', 'Payout Method'),
(1056, 'user', 'Add Payout', 'add-payout', 'Add Payout'),
(1057, 'user', 'Wallet', 'wallet', 'Wallet'),
(1058, 'user', 'User Dashboard', 'user-dashboard', 'User Dashboard'),
(1059, 'user', 'has been', 'has-been', 'has been'),
(1062, 'user', 'Dear', 'dear', 'Dear'),
(1063, 'user', 'thank you for your booking at our', 'thank-you-for-your-booking-at-our', 'thank you for your booking at our'),
(1064, 'user', 'at', 'at', 'at'),
(1065, 'user', 'is', 'is', 'is'),
(1066, 'user', 'Confirmed', 'confirmed', 'Confirmed'),
(1068, 'user', 'Your feedback', 'your-feedback', 'Your feedback'),
(1069, 'user', 'Your account has been created successfully, now you can login to your account using below access', 'new-user-account-login', 'Your account has been created successfully, now you can login to your account using below access'),
(1070, 'admin', 'Site Animation', 'site-animation', 'Site Animation'),
(1071, 'user', 'Enable to activate website animation', 'site-animation-title', 'Enable to activate website animation'),
(1072, 'user', 'Enable', 'enable', 'Enable'),
(1073, 'admin', 'Amount Withdraw', 'amount-withdraw', 'Amount Withdraw'),
(1074, 'admin', 'Flutterwave', 'flutterwave', 'Flutterwave'),
(1075, 'admin', 'Copy', 'copy', 'Copy'),
(1076, 'admin', 'Copied', 'copied', 'Copied'),
(1078, 'company', 'View', 'view', 'View'),
(1079, 'company', 'Icon', 'icon', 'Icon'),
(1080, 'company', 'Number', 'number', 'Number'),
(1081, 'company', 'Text', 'text', 'Text'),
(1090, 'company', 'Meta_tags', 'meta_tags', 'Meta tags'),
(1091, 'company', 'Meta_desc', 'meta_desc', 'Meta description'),
(1101, 'company', 'Link', 'link', 'Link'),
(1102, 'company', 'Subcategory', 'subcategory', 'Subcategory'),
(1103, 'company', 'Short_desc', 'short_desc', 'Short description'),
(1107, 'company', 'Total views', 'total-views', 'Total views'),
(1108, 'company', 'Created_at', 'created_at', 'Created at'),
(1117, 'company', 'Download successfully', 'download-successfully', 'Download successfully'),
(1118, 'company', 'Subcategories', 'subcategories', 'Subcategories'),
(1141, 'admin', 'Orders', 'orders', 'Orders'),
(1143, 'company', 'Created at', 'created-at', 'Created at'),
(1144, 'company', 'Gateway', 'gateway', 'Gateway'),
(1145, 'admin', 'Confirm', 'confirm', 'Confirm'),
(1153, 'admin', 'Total orders', 'total-orders', 'Total orders'),
(1156, 'admin', 'Last order', 'last-order', 'Last order'),
(1159, 'admin', 'Confirm date', 'confirm-date', 'Confirm date'),
(1164, 'admin', 'Enable to show portfolio page on front end', 'enable-to-show-portfolio-page-on-front-end', 'Enable to show portfolio page on front end'),
(1165, 'admin', 'Enable to show product page on front end', 'enable-to-show-product-page-on-front-end', 'Enable to show product page on front end'),
(1166, 'admin', 'Enable products', 'enable-products', 'Enable products'),
(1168, 'admin', 'Enable to show teams page on front end', 'enable-to-show-teams-page-on-front-end', 'Enable to show teams page on front end'),
(1169, 'admin', 'Enable Counter', 'enable-counter', 'Enable Counter'),
(1170, 'admin', 'Enable to show counters page on front end', 'enable-to-show-counters-page-on-front-end', 'Enable to show counters page on front end'),
(1192, 'company', 'Accepted', 'accepted', 'Accepted'),
(1193, 'company', 'Reject', 'reject', 'Reject'),
(1194, 'company', 'Activate Successfully', 'activate-successfully', 'Activate Successfully'),
(1195, 'company', 'Update status successfully', 'update-status-successfully', 'Update status successfully'),
(1196, 'admin', 'Booking_Status', 'booking_status', 'Booking Status'),
(1197, 'company', 'Payment Status', 'payment_status', 'Payment Status'),
(1204, 'admin', 'Work Flow', 'work-flow', 'Work Flow'),
(1206, 'company', 'Applied successfully', 'applied-successfully', 'Applied successfully'),
(1207, 'company', 'Sent message successfully', 'sent-message-successfully', 'Sent message successfully'),
(1213, 'admin', 'Career', 'career', 'Career'),
(1214, 'admin', 'Portfolios', 'portfolios', 'Portfolios'),
(1216, 'admin', 'Testimoinals', 'testimoinals', 'Testimoinals'),
(1217, 'admin', 'Team', 'team', 'Team'),
(1218, 'admin', 'Quotes', 'quotes', 'Quotes'),
(1225, 'company', 'Pagination limit', 'pagination-limit', 'Pagination limit'),
(1226, 'company', 'Site color', 'site-color', 'Site color'),
(1228, 'company', 'Youtube', 'youtube', 'Youtube'),
(1237, 'user', 'Check Demo', 'check-demo', 'Check Demo'),
(1238, 'user', '404 Not Found', '404-not-found', '404 Not Found'),
(1239, 'user', 'No credit card required.', 'no-credit-card-required', 'No credit card required.'),
(1240, 'user', 'Start free trial. ', 'start-free-trial', 'Start free trial. '),
(1241, 'user', 'Start free trial. * No credit card required.', 'start-free-trial.-no-credit-card-required', 'Start free trial. * No credit card required.'),
(1242, 'user', '30+ Templates', '30-templates', '30+ Templates'),
(1243, 'user', 'Using pre made templates create engaging, well-researched content at scale.', 'using-pre-made-templates-create-engaging', 'Using pre made templates create engaging, well-researched content at scale.'),
(1244, 'user', 'Testimonials', 'testimonia', 'Testimonials'),
(1246, 'user', '1234', '1234', '1234'),
(1247, 'user', 'Frequently Asked Questions', 'frequently-asked', 'Frequently Asked Questions'),
(1248, 'user', 'Sign Up ', 'sign-up', 'Sign Up '),
(1249, 'user', 'Create an account', 'create-an-account', 'Create an account'),
(1250, 'user', 'Don\'t received any code? ', 'dont-received-any-code', 'Don\'t received any code? '),
(1251, 'user', 'Verify Account', 'verify-acco', 'Verify Account'),
(1252, 'user', 'Verify Account', 'verify', 'Verify Account'),
(1253, 'user', 'We have sent a link to your registered email address, please click this link to verify your account', 'we-have-sent-a-link-to-your-registered-email-address', 'We have sent a link to your registered email address, please click this link to verify your account'),
(1254, 'user', 'Email verification failed!', 'email-failed', 'Email verification failed!'),
(1255, 'user', 'Back Home', 'back-home', 'Back Home'),
(1256, 'user', ' RTL (Right to Left)', 'rtl-right-to-left', ' RTL (Right to Left)'),
(1257, 'user', 'Language Values', 'language-values', 'Language Values'),
(1258, 'user', 'Payment Invoice', 'payment-invoice', 'Payment Invoice'),
(1284, 'user', ' Saved', 'saved', ' Saved'),
(1292, 'user', 'Table', 'table', 'Table'),
(1293, 'user', 'Redemable', 'redemable', 'Redemable'),
(1294, 'user', 'How many time the code can be used', 'how-many-time-the-code-can-be-used', 'How many time the code can be used'),
(1295, 'user', 'CVC', 'cvc', 'CVC'),
(1298, 'user', ' All Templates', 'all-templates', ' All Templates'),
(1299, 'user', 'App Info', 'app-info', 'App Info'),
(1300, 'user', ' Script Version', 'script-version', ' Script Version'),
(1301, 'user', ' Documentation', 'documentation', ' Documentation'),
(1302, 'user', 'Support', 'support', 'Support'),
(1303, 'user', 'codericks.envato@gmail.com', 'codericks.envatogmail.com', 'codericks.envato@gmail.com'),
(1304, 'user', 'Please mention purchase code with your support mail.', 'please-mention-purchase-code-with-your-support-mail', 'Please mention purchase code with your support mail.'),
(1305, 'user', ' Payment gateways are only available with Extended License', 'payment-gateways-are-only-available-with-extended-license', ' Payment gateways are only available with Extended License'),
(1306, 'user', ' OpenAi API', 'openai-api', ' OpenAi API'),
(1307, 'user', ' Twilio ', 'twilio', ' Twilio '),
(1308, 'user', 'reCaptcha V2', 'recaptcha-v2', 'reCaptcha V2'),
(1309, 'user', 'Admin Leftsidebar Style', 'admin-leftsidebar-style', 'Admin Leftsidebar Style'),
(1312, 'user', 'API info is restricted on demo mode', 'api-info-is-restricted-on-demo-mode', 'API info is restricted on demo mode'),
(1313, 'user', 'Gmail Host:&nbsp;&nbsp;smtp.gmail.com', 'gmail-hostsmtp.gmail.com', 'Gmail Host:&nbsp;&nbsp;smtp.gmail.com'),
(1314, 'user', 'Gmail Host:', 'gmail-host', 'Gmail Host:'),
(1315, 'user', 'smtp.gmail.com', 'smtp.gmail.com', 'smtp.gmail.com'),
(1316, 'user', 'Gmail Port:', 'gmail-port', 'Gmail Port:'),
(1317, 'user', 'SMTP', 'smtp', 'SMTP'),
(1318, 'user', 'Codeigniter Mail', 'codeigniter-mail', 'Codeigniter Mail'),
(1323, 'user', 'Affiliate', 'affiliate', 'Affiliate'),
(1324, 'user', 'Referral Settings', 'referral-settings', 'Referral Settings'),
(1325, 'user', 'Payout Request', 'payout-request', 'Payout Request'),
(1326, 'user', 'All Images', 'all-images', 'All Images'),
(1327, 'user', 'Referrals', 'referrals', 'Referrals'),
(1328, 'user', 'Withdrawal Amount', 'withdrawal-amount', 'Withdrawal Amount'),
(1329, 'user', 'Enable Referral', 'enable-referral', 'Enable Referral'),
(1330, 'user', 'Referral Policy', 'referral-policy', 'Referral Policy'),
(1331, 'user', 'Choose Referral policy', 'choose-referral-policy', 'Choose Referral policy'),
(1332, 'user', 'Commission only on first purchase', 'commission-only-on-first-purchase', 'Commission only on first purchase'),
(1333, 'user', 'Commission on every purchase', 'commission-on-every-purchase', 'Commission on every purchase'),
(1334, 'user', 'Commision Rate', 'commision-rate', 'Commision Rate'),
(1335, 'user', 'Minimum Payout', 'minimum-payout', 'Minimum Payout'),
(1336, 'user', 'Payment Method', 'payment-method', 'Payment Method'),
(1337, 'user', 'Refferal Guidelines', 'refferal-guidelines', 'Refferal Guidelines'),
(1338, 'user', 'Total Referrals', 'total-referrals', 'Total Referrals'),
(1339, 'user', 'My Referral URL', 'my-referral-url', 'My Referral URL'),
(1340, 'user', 'First Successful Payment by Referred Person', 'first-successful-payment-by-referred-person', 'First Successful Payment by Referred Person'),
(1341, 'user', 'Every Successful Payment by Referred Person', 'every-successful-payment-by-referred-person', 'Every Successful Payment by Referred Person'),
(1342, 'user', 'Referral guidelines', 'referral-guidelines', 'Referral guidelines'),
(1343, 'user', 'How It works', 'how-it-works', 'How It works'),
(1344, 'user', 'Send Invitation', 'send-invitation', 'Send Invitation'),
(1345, 'user', 'Send your referral link to your friends and tell them how cool is Davinci!', 'send-your-referral-link-to-your-friends-and-tell-them-how-cool-is-davinci', 'Send your referral link to your friends and tell them how cool is Davinci!'),
(1346, 'user', 'Registration', 'registration', 'Registration'),
(1347, 'user', 'Let them register using your referral link.', 'let-them-register-using-your-referral-link', 'Let them register using your referral link.'),
(1348, 'user', 'Get Commissions', 'get-commissions', 'Get Commissions'),
(1349, 'user', 'Earn commission for their first session booking payments!', 'earn-commission-for-their-first-subscription-plan-payments', 'Earn commission for their first session booking payments!'),
(1350, 'user', 'Invite  your Friends to send a email', 'invite-your-friends-to-send-a-email', 'Invite  your Friends to send a email'),
(1351, 'user', 'Insert your friends email and send invitation to join Xxxx', 'insert-your-friends-email-and-send-invitation-to-join-xxxx', 'Insert your friends email and send invitation to join Xxxx'),
(1352, 'user', 'SEND', 'send', 'SEND'),
(1353, 'user', 'Share the referral link', 'share-the-referral-link', 'Share the referral link'),
(1354, 'user', 'You can also share your referral link by copying and sending it or sharing it on your social media profiles.', 'you-can-also-share-your-referral-link-by-copying-and-sending-it-or-sharing-it-on-your-social-media-profiles', 'You can also share your referral link by copying and sending it or sharing it on your social media profiles.'),
(1355, 'user', 'Referrar Id', 'referrar-id', 'Referrar Id'),
(1356, 'user', 'Order Id', 'order-id', 'Order Id'),
(1357, 'user', 'Commision', 'commision', 'Commision'),
(1358, 'user', 'Commision Amount', 'commision-amount', 'Commision Amount'),
(1359, 'user', 'Select your payment method', 'select-your-payment-method', 'Select your payment method'),
(1360, 'user', 'Bank', 'bank', 'Bank'),
(1361, 'user', 'Method Details', 'method-details', 'Method Details'),
(1362, 'admin', 'Skill', 'skill', 'Skill'),
(1363, 'user', 'Profile Settings', 'profile-settings', 'Profile Settings'),
(1364, 'user', 'Mentorship Profile', 'mentorship-profile', 'Mentorship Profile'),
(1365, 'user', 'Profile Photo', 'profile-photo', 'Profile Photo'),
(1366, 'user', 'Upload profile photo', 'upload-profile-photo', 'Upload profile photo'),
(1367, 'user', 'Cover photo', 'cover-photo', 'Cover photo'),
(1368, 'user', 'Gender', 'gender', 'Gender'),
(1369, 'user', 'Select your skill', 'select-your-skill', 'Select your skill'),
(1370, 'user', 'Level Of Experience', 'level-of-experience', 'Level Of Experience'),
(1371, 'user', 'Select your experience level', 'select-your-experience-level', 'Select your experience level'),
(1372, 'user', 'Experience', 'experience', 'Experience'),
(1373, 'user', 'Linkedin Profile', 'linkedin-profile', 'Linkedin Profile'),
(1374, 'user', 'Portfolio / Website', 'portfolio-website', 'Portfolio / Website'),
(1375, 'admin', 'Skills', 'skills', 'Skills'),
(1376, 'user', 'Schedule', 'schedule', 'Schedule'),
(1377, 'user', 'Set interval', 'set-interval', 'Set interval'),
(1378, 'user', 'To', 'to', 'To'),
(1379, 'user', 'Present', 'present', 'Present'),
(1380, 'user', 'Contribution', 'contribution', 'Contribution'),
(1381, 'user', 'Institute', 'institute', 'Institute'),
(1382, 'user', 'Degree', 'degree', 'Degree'),
(1383, 'user', 'Institution', 'institution', 'Institution'),
(1384, 'user', 'Sessions', 'sessions', 'Sessions'),
(1385, 'user', 'Session Name', 'session-name', 'Session Name'),
(1386, 'user', 'Session Type', 'session-type', 'Session Type'),
(1387, 'user', 'One of Session', 'one-of-session', 'One of Session'),
(1388, 'user', 'Recurring Sessions', 'recurring-sessions', 'Recurring Sessions'),
(1389, 'user', 'Number of sessions', 'number-of-sessions', 'Number of sessions'),
(1390, 'user', 'Repeat In', 'repeat-in', 'Repeat In'),
(1391, 'user', 'Repeats weekly', 'repeats-weekly', 'Repeats weekly'),
(1392, 'user', 'Repeats monthly', 'repeats-monthly', 'Repeats monthly'),
(1393, 'user', 'Session Topic', 'session-topic', 'Session Topic'),
(1394, 'user', 'Allow mentee to chose topic', 'allow-mentee-to-chose-topic', 'Allow mentee to chose topic'),
(1395, 'user', 'Show session on your public profile', 'show-session-on-your-public-profile', 'Show session on your public profile'),
(1396, 'user', 'All members who visit your profile will be able to see this session.', 'allow-session-text', 'All members who visit your profile will be able to see this session.'),
(1397, 'user', 'Visibility', 'visibility', 'Visibility'),
(1398, 'user', 'Not available', 'not-available', 'Not available'),
(1399, 'user', 'Recurring', 'recurring', 'Recurring'),
(1400, 'user', 'Session repeats weekly', 'session-repeats-weekly', 'Session repeats weekly'),
(1401, 'user', 'Session repeats monthly', 'session-repeats-monthly', 'Session repeats monthly'),
(1402, 'user', 'One time session', 'one-time-session', 'One time session'),
(1403, 'user', 'Public', 'public', ' Public'),
(1404, 'user', 'Private', 'private', ' Private'),
(1439, 'user', 'after commission of', 'after-commission-of', 'after commission of'),
(1440, 'user', 'Payout Settings', 'payout-settings', 'Payout Settings'),
(1441, 'user', 'Payout Requests', 'payout-requests', 'Payout Requests'),
(1442, 'user', 'Payout Completed', 'payout-completed', 'Payout Completed'),
(1443, 'user', 'Request Sent', 'request-sent', 'Request Sent'),
(1444, 'user', 'Enable / Disable Payout Methods', 'enabledisable-payout-methods', 'Enable / Disable Payout Methods'),
(1445, 'user', 'must be between 1% - 99%', 'must-be-between-1-99', 'must be between 1% - 99%'),
(1446, 'user', 'Payout History', 'payout-history', 'Payout History'),
(1447, 'user', 'Payout Method', 'payout-method', 'Payout Method'),
(1448, 'user', 'Add Payout', 'add-payout', 'Add Payout'),
(1449, 'user', 'Wallet', 'wallet', 'Wallet'),
(1450, 'user', 'User Dashboard', 'user-dashboard', 'User Dashboard'),
(1451, 'user', 'has been', 'has-been', 'has been'),
(1452, 'user', 'Number Of Slot', 'number-of-slot', 'Number Of Slot'),
(1453, 'user', 'Slot For', 'slot-for', 'Slot For'),
(1454, 'user', 'Flag', 'flag', 'Flag'),
(1455, 'user', 'Tim Zone', 'tim-zone', 'Tim Zone'),
(1456, 'user', 'Country Code', 'country-code', 'Country Code'),
(1457, 'user', 'Dial Code', 'dial-code', 'Dial Code'),
(1458, 'admin', 'Currency Name', 'currency-name', 'Currency Name'),
(1459, 'user', 'Currency Symbol', 'currency-symbol', 'Currency Symbol'),
(1460, 'admin', 'Currency Code', 'currency-code', 'Currency Code'),
(1461, 'user', 'Start new message', 'start-new-message', 'Start new message'),
(1462, 'user', 'Messages sent after connecting with a mentee/mentor will appear here.', 'messages-sent-after-connecting', 'Messages sent after connecting with a mentee/mentor will appear here.'),
(1463, 'user', ' NO contact found', 'no-contact-found', ' NO contact found'),
(1464, 'user', 'Mentee Profile', 'mentee-profile', 'Mentee Profile'),
(1465, 'user', 'Mentee', 'mentee', 'Mentee'),
(1466, 'user', 'Booking', 'booking', 'Booking'),
(1467, 'user', 'Coupon', 'coupon', 'Coupon'),
(1468, 'user', 'Work review', 'work-review', 'Work review'),
(1469, 'user', 'Reserve up to 30 minutes for your session.', 'reserve-up-to-30-minutes-for-your-session', 'Reserve up to 30 minutes for your session.'),
(1470, 'user', 'Instant Schedule', 'instant-schedule', 'Instant Schedule'),
(1471, 'user', 'You\'ll be able pick a time that suits you right after booking.', 'youll-be-able-pick-a-time-that-suits-you-right-after-booking', 'You\'ll be able pick a time that suits you right after booking.'),
(1472, 'user', 'Scheduling conflict? No-show? Cancel sessions & get your money back instantly. Review', 'scheduling-conflict-no-show-cancel-sessions-get-your-money-back-instantly.-review', 'Scheduling conflict? No-show? Cancel sessions & get your money back instantly. Review'),
(1473, 'user', 'our cancellation policy', 'our-cancellation-policy', 'our cancellation policy'),
(1474, 'user', 'Session Details', 'session-details', 'Session Details'),
(1475, 'user', 'Create new account', 'create-new-account', 'Create new account'),
(1476, 'user', 'User Name', 'user-name', 'User Name'),
(1477, 'user', 'Session', 'session', 'Session'),
(1478, 'user', 'Overview', 'overview', 'Overview'),
(1479, 'user', 'Background', 'background', 'Background'),
(1480, 'user', 'Community statistics', 'community-statistics', 'Community statistics'),
(1481, 'user', 'Complecated Sessions', 'complecated-sessions', 'Complecated Sessions'),
(1482, 'user', 'Total mentoring time', 'total-mentoring-time', 'Total mentoring time'),
(1483, 'user', 'Average Attendence', 'average-attendence', 'Average Attendence'),
(1484, 'user', 'Book Session', 'book-session', 'Book Session'),
(1485, 'user', 'Booking Id', 'booking-id', 'Booking Id'),
(1486, 'user', 'Booking Status', 'booking-status', 'Booking Status'),
(1487, 'user', 'Mentee Info', 'mentee-info', 'Mentee Info'),
(1488, 'user', 'Mentor', 'mentor', 'Mentor'),
(1489, 'user', 'New Discount', 'new-discount', 'New Discount'),
(1490, 'user', 'Once Per Mentee', 'once-per-mentee', 'Once Per Mentee'),
(1491, 'user', 'Unpaid', 'unpaid', 'Unpaid'),
(1492, 'user', 'Coupon Apllied', 'coupon-apllied', 'Coupon Apllied'),
(1493, 'user', 'Bookings', 'bookings', 'Bookings'),
(1494, 'user', 'Booking Time', 'booking-time', 'Booking Time'),
(1495, 'user', 'Session Booking', 'session-booking', 'Session Booking'),
(1496, 'user', 'Sync Google Calednder', 'sync-google-calednder', 'Sync Google Calednder'),
(1497, 'user', 'select category', 'select-category', 'select category'),
(1498, 'user', 'Mentorship', 'mentorship', 'Mentorship'),
(1499, 'user', 'Select your experience', 'select-your-experience', 'Select your experience'),
(1500, 'user', 'When you are available', 'when-available', 'When you are available'),
(1501, 'user', 'Define your availability for this session. You will receive bookings in your local timezone', 'define-your-availability', 'Define your availability for this session. You will receive bookings in your local timezone'),
(1502, 'user', 'Defult Hours', 'defult-hours', 'Defult Hours'),
(1503, 'user', 'Set Custom Hours', 'set-custom-hours', 'Set Custom Hours'),
(1504, 'user', 'Used Coupon', 'used-coupon', 'Used Coupon'),
(1505, 'admin', 'Zoom Account Id', 'zoom-account-id', 'Zoom Account Id'),
(1506, 'admin', 'Zoom Client Id', 'zoom-client-id', 'Zoom Client Id'),
(1507, 'admin', 'Zoom Client Secret', 'zoom-client-secret', 'Zoom Client Secret'),
(1508, 'admin', 'Zoom API', 'zoom-api', 'Zoom API'),
(1509, 'admin', 'Zoom integration doc', 'zoom-integration-doc', 'Zoom integration doc'),
(1510, 'admin', 'Create Zoom app', 'create-zoom-app', 'Create Zoom app'),
(1511, 'admin', 'Check API Connection', 'check-api-connection', 'Check API Connection'),
(1512, 'user', 'Send notify mail to user for joining meeting', 'send-notify-mail-to-user-for-joining-meeting', 'Send notify mail to user for joining meeting'),
(1513, 'user', 'Start meeting', 'start-meeting', 'Start meeting'),
(1514, 'user', 'Cancel meeting', 'cancel-meeting', 'Cancel meeting'),
(1515, 'user', 'Create Meeting', 'create-meeting', 'Create Meeting'),
(1516, 'user', 'Join meeting', 'join-meeting', 'Join meeting'),
(1517, 'user', 'Host meeting', 'host-meeting', 'Host meeting'),
(1518, 'user', 'Meeting Password', 'meeting-password', 'Meeting Password'),
(1519, 'user', 'Online Meeting', 'online-meeting', 'Online Meeting'),
(1520, 'user', 'Default virtual meeting option', 'default-virtual-meeting-option', 'Default virtual meeting option'),
(1521, 'user', 'Google meet invitation url', 'google-meet-invitation-url', 'Google meet invitation url'),
(1522, 'user', 'Not started yet', 'not-started-yet', 'Not started yet'),
(1523, 'user', 'Respond In', 'respond-in', 'Respond In'),
(1524, 'user', 'Latest Bookings', 'latest-bookings', 'Latest Bookings'),
(1525, 'user', 'Select session', 'select-session', 'Select session'),
(1526, 'user', 'Select mentee', 'select-mentee', 'Select mentee'),
(1527, 'user', 'Select status', 'select-status', 'Select status'),
(1528, 'user', 'Select mentor', 'select-mentor', 'Select mentor'),
(1529, 'user', 'See All', 'see-all', 'See All'),
(1530, 'user', 'Upcoming Bookings', 'upcoming-bookings', 'Upcoming Bookings'),
(1531, 'user', 'Mentors', 'mentors', 'Mentors'),
(1532, 'user', 'Completed Sessions', 'completed-sessions', 'Completed Sessions'),
(1533, 'user', 'Years', 'years', 'Years'),
(1534, 'user', 'Attendence', 'attendence', 'Attendence'),
(1535, 'user', 'Report', 'report', 'Report'),
(1536, 'user', 'Total sessions', 'total-sessions', 'Total sessions'),
(1537, 'user', 'Mentorship level', 'mentorship-level', 'Mentorship level'),
(1538, 'user', 'Mentor Sessions', 'mentor-sessions', 'Mentor Sessions'),
(1539, 'user', 'Total Booking', 'total-booking', 'Total Booking'),
(1540, 'user', 'Countries', 'countries', 'Countries'),
(1541, 'user', 'Earning Info', 'earning-info', 'Earning Info'),
(1542, 'user', 'Total Mentoring', 'total-mentoring', 'Total Mentoring'),
(1543, 'user', 'Recurring Info', 'recurring-info', 'Recurring Info'),
(1544, 'user', 'Upcoming', 'upcoming', 'Upcoming'),
(1545, 'user', 'Payment Receipt', 'payment-receipt', 'Payment Receipt'),
(1546, 'user', 'Most booked sessions', 'most-booked-sessions', 'Most booked sessions'),
(1547, 'user', 'Most booked Mentee', 'most-booked-mentee', 'Most booked Mentee'),
(1548, 'user', 'Most booked Country', 'most-booked-country', 'Most booked Country'),
(1549, 'admin', 'Access Token', 'access-token', 'Access Token'),
(1550, 'user', 'Fluent In', 'fluent-in', 'Fluent In'),
(1551, 'user', 'Favourite', 'favourite', 'Favourite'),
(1552, 'user', 'Favourite Mentor', 'favourite-mentor', 'Favourite Mentors'),
(1553, 'user', 'Favourite Mentees', 'favourite-mentee', 'Favourite Mentees'),
(1554, 'user', 'About us', 'about-us', 'about us'),
(1555, 'user', 'Happy Clients', 'happy-clients', 'Happy Clients'),
(1556, 'user', 'Brands', 'brands', 'Brands'),
(1557, 'user', 'Fonts', 'fonts', 'Fonts'),
(1558, 'user', 'Brand', 'brand', 'Brand'),
(1559, 'user', 'Logo', 'logo', 'Logo'),
(1560, 'user', 'Font Name', 'font-name', 'Font Name'),
(1561, 'user', 'Google Fonts', 'google-fonts', 'Google Fonts'),
(1562, 'user', 'Custom Font', 'custom-font', 'Custom Font'),
(1563, 'user', 'Manage Fonts', 'manage-fonts', 'Manage Fonts'),
(1564, 'user', 'Message to', 'message-to', 'Message to'),
(1565, 'user', 'Send Message', 'send-message', 'Send Message'),
(1566, 'user', 'Select your ountry', 'select-your-ountry', 'Select your ountry'),
(1567, 'user', 'Select your time zone', 'select-your-time-zone', 'Select your time zone'),
(1568, 'user', 'Ultramsg API', 'ultramsg-api', 'Ultramsg API'),
(1569, 'user', 'Instance Id', 'instance-id', 'Instance Id'),
(1570, 'user', 'Token', 'token', 'Token'),
(1571, 'user', 'Enable Booking Confirmation Ultra message', 'enable-ultra-message', 'Enable Booking Confirmation Ultra message'),
(1572, 'user', 'Enable to send booking ultra message to your customers, after make a appointment.', 'enable-ultra-message-tiitle', 'Enable to send booking ultra message to your customers, after make a appointment.'),
(1573, 'user', 'Enable Booking Confirmation SMS', 'enable-booking-sms', 'Enable Booking Confirmation SMS'),
(1574, 'user', 'Enable to send booking notification message to your customers, after make a appointment.', 'enable-to-send-booking-notification-message-to-your-customers-after-make-a-appointment', 'Enable to send booking notification message to your customers, after make a appointment.'),
(1575, 'user', 'Whatsapp Settings', 'whatsapp-settings', 'Whatsapp Settings'),
(1576, 'user', 'consultation', 'consultation', 'consultation'),
(1577, 'user', 'This sessions repeats', 'this-sessions-repeats', 'This sessions repeats'),
(1578, 'user', 'This session have total', 'this-session-have-total', 'This session have total'),
(1579, 'user', 'Availability', 'availability', 'Availability'),
(1580, 'user', 'Go to Checkout', 'go-to-checkout', 'Go to Checkout'),
(1581, 'user', 'Your next chapter, made possible by mentoring', 'your-next-chapter-made-possible-by-mentoring', 'Your next chapter, made possible by mentoring'),
(1582, 'user', 'Build confidence as a leader, grow your network, and define your legacy.', 'build-confidence-as-a-leader-grow-your-network-and-define-your-legacy', 'Build confidence as a leader, grow your network, and define your legacy.'),
(1583, 'user', 'Aplication Title Mentor', 'aplication-title-mentor', 'Application Title Mentor'),
(1584, 'user', 'Became a Member', 'became-a-member', 'Became a Member'),
(1585, 'user', 'Learn and grow across expertise for free', 'learn-and-grow-across-expertise-for-free', 'Learn and grow across expertise for free'),
(1586, 'user', 'Find mentors from product fields across the globe', 'home-category-title', 'Find mentors from product fields across the globe'),
(1587, 'user', 'Our Teams', 'our-teams', 'Our Teams'),
(1588, 'user', 'Discover the world\'s top mentors', 'discover-the-worlds-top-mentors', 'Discover the world\'s top mentors'),
(1589, 'user', 'Usually responds in', 'usually-responds-in', 'Usually responds in'),
(1590, 'user', 'Contries', 'contries', 'Contries'),
(1591, 'user', 'Upgrade Plan', 'upgrade-plan', 'Upgrade Plan'),
(1592, 'user', 'You have reached the maximum limit', 'you-have-reached-the-maximum-limit', 'You have reached the maximum limit'),
(1593, 'user', 'Please upgrade your plan', 'please-upgrade-your-plan', 'Please upgrade your plan'),
(1594, 'user', 'Paypal Email', 'paypal-email', 'Paypal Email'),
(1595, 'user', 'Notifications', 'notifications', 'Notifications'),
(1596, 'user', 'Next Session', 'next-session', 'Next Session'),
(1597, 'user', 'Recurring session complete', 'recurring-session-complete', 'Recurring session complete'),
(1598, 'user', 'Complete Payment', 'complete-payment', 'Complete Payment'),
(1599, 'user', 'Total Minutes', 'total-minutes', 'Total Minutes'),
(1600, 'user', 'Mentees', 'mentees', 'Mentees'),
(1601, 'user', 'Disabled Days', 'disabled-days', 'Disabled Days'),
(1602, 'user', 'Pending Sessions', 'pending-sessions', 'Pending Sessions'),
(1603, 'user', 'Repeated in', 'repeated-in', 'Repeated in'),
(1604, 'user', 'Total session', 'total-session', 'Total session'),
(1605, 'user', 'Recurring Count', 'recurring-count', 'Recurring Count'),
(1606, 'user', 'Coupon applied', 'coupon-applied', 'Coupon applied'),
(1607, 'user', 'Daily', 'daily', 'Daily'),
(1608, 'user', 'weekly', 'weekly', 'weekly'),
(1609, 'user', 'Number of session', 'number-of-session', 'Number of session'),
(1610, 'user', 'Select your gender', 'select-your-gender', 'Select your gender'),
(1611, 'admin', 'Password', 'password', 'Password'),
(1612, 'admin', 'Zoom API', 'zoom-api', 'Zoom API'),
(1613, 'admin', 'Swift', 'swift', 'Swift'),
(1614, 'admin', 'IBAN', 'iban', 'IBAN'),
(1615, 'admin', 'Paypal', 'paypal', 'Paypal'),
(1616, 'admin', 'Eg: University of Dalas', 'eg-university-of-dalas', 'Eg: University of Dalas'),
(1617, 'admin', 'Eg: Bachelors in Architect', 'eg-bachelors-in-rchitect', 'Eg: Bachelors in Architect'),
(1618, 'admin', 'Minimum', 'minimum', 'Minimum'),
(1619, 'admin', 'Search contacts', 'search-contacts', 'Search contacts'),
(1620, 'admin', 'Latest Mentors', 'latest-mentors', 'Latest Mentors'),
(1621, 'user', 'Book Session Time For', 'book-session-time-for', 'Book Session Time For'),
(1622, 'front', 'Build confidence as a leader, grow your network, and define your legacy.', 'build-confidence-as-a-leader', 'Build confidence as a leader, grow your network, and define your legacy.'),
(1623, 'admin', 'Default', 'default', 'Default'),
(1624, 'user', 'Company', 'company', 'Company'),
(1625, 'user', 'Your time', 'your-time', 'Your time'),
(1626, 'user', 'Mentor Auto Approve', 'mentor_auto_approve', 'Mentor Auto Approve'),
(1627, 'user', 'Mentor Auto Approve', 'mentor-auto-approve', 'Mentor Auto Approve'),
(1628, 'user', 'Enable to allow mentor auto approve for new registered mentors.', 'enable-to-allow-mentor-auto-approve-for-new-registered-mentors', 'Enable to allow mentor auto approve for new registered mentors.'),
(1629, 'admin', 'Approve', 'approve', 'Approve'),
(1630, 'admin', 'Your account is under review, once your account is approved you will be notified.', 'not-approve-warning-msg', 'Your account is under review, once your account is approved you will be notified.'),
(1631, 'user', 'Appointment confirmation of', 'appointment-confirmation', 'Appointment confirmation of'),
(1632, 'user', 'Hello', 'hello', 'Hello'),
(1633, 'user', ' We have reset your password', 'we-reset-pass', ' We have reset your password'),
(1634, 'user', 'please use this code to login your account', 'code-to-login-your-account', 'please use this code to login your account'),
(1635, 'user', 'We are thrilled to inform you that your account with', 'we-are-thrilled-to-inform-you-that-your-account-with', 'We are thrilled to inform you that your account with'),
(1636, 'user', 'has been successfully approved! Welcome to our community!', 'successfully-approved-our-community', 'has been successfully approved! Welcome to our community!'),
(1637, 'user', 'Best regards,', 'best-regards', 'Best regards,'),
(1638, 'user', 'Rate this Session', 'rate-this-session', 'Rate this Session'),
(1639, 'user', 'Sub Category', 'sub-category', 'Sub Category'),
(1640, 'user', 'Sub Categories', 'sub-categories', 'Sub Categories'),
(1641, 'admin', 'Must be between 1% - 5%', 'must-be-between-1-5', 'Must be between 1% - 5%'),
(1642, 'user', 'Intro Video', 'intro-video', 'Intro Video'),
(1643, 'user', 'Intro Video Url', 'intro-video-url', 'Intro Video Url ( embedded )'),
(1644, 'user', 'Facebook Profile', 'facebook-profile', 'Facebook Profile'),
(1645, 'user', 'Instagram Profile', 'instagram-profile', 'Instagram Profile'),
(1646, 'user', '  X profile', 'x-profile', '  X ( twitter ) profile'),
(1647, 'user', 'Kyc', 'kyc', 'KYC'),
(1648, 'user', 'Document', 'document', 'Document'),
(1649, 'user', 'Document Number', 'document-number', 'Document Number'),
(1650, 'user', 'Document Photo', 'document-photo', 'Document Photo'),
(1651, 'user', 'Reject Reason', 'reject-reason', 'Reject Reason'),
(1652, 'user', 'Calendar', 'calendar', 'Calendar'),
(1653, 'user', 'National Id', 'national-id', 'National Id'),
(1654, 'user', 'Passport', 'passport', 'Passport'),
(1655, 'user', 'Driving License', 'driving-license', 'Driving License'),
(1656, 'user', 'KYC Verification', 'kyc-verification', ' KYC Verification'),
(1657, 'user', 'Requires a valid government issue ID (National ID, Passport, Drivers license)', 'kyc-document-requirments', 'Requires a valid government issue ID (National ID, Passport, Drivers license)'),
(1658, 'user', 'Upload a proof of your Identity', 'upload-a-proof-of-your-identity', 'Upload a proof of your Identity'),
(1659, 'user', 'Issuing Country/Region ', 'issuing-countryregion', 'Issuing Country/Region '),
(1660, 'user', 'Document Type ', 'document-type', 'Document Type '),
(1661, 'user', 'Front Side of Your', 'front-side-of-your', 'Front Side of Your'),
(1662, 'user', 'Back Side of Your', 'back-side-of-your', 'Back Side of Your'),
(1663, 'user', 'Passport Photo', 'passport-photo', 'Passport Photo'),
(1664, 'user', 'Selfiee with', 'selfiee-with', 'Selfiee with'),
(1665, 'user', 'Make sure your document and face in this same frame.', 'make-sure-your-document-and-face-in-this-same-frame', 'Make sure your document and face in this same frame.'),
(1666, 'user', 'File accept: JPEG/JPG/PNG (Max size: 5mb)', 'file-accept-type', 'File accept: JPEG/JPG/PNG (Max size: 5mb)'),
(1667, 'user', 'Face must be clear visible.', 'face-must-be-clear-visible', 'Face must be clear visible.'),
(1668, 'user', 'Document should be good condition & valid period.', 'document-should-be-good-condition-valid-period', 'Document should be good condition & valid period.'),
(1669, 'user', 'Personal Information', 'personal-information', 'Personal Information'),
(1670, 'user', 'First Name', 'first-name', 'First Name'),
(1671, 'user', '(As on Document)', 'as-on-document', '(As on Document)'),
(1672, 'user', 'Last Name', 'last-name', 'Last Name'),
(1673, 'user', 'Date of Birth', 'date-of-birth', 'Date of Birth'),
(1674, 'user', 'By clicking Confirm, you acknowledge and grant consent for us to securely store and process your information.', 'acknowledge-checkbox-title', 'By clicking Confirm, you acknowledge and grant consent for us to securely store and process your information.'),
(1675, 'user', 'Back to Previous', 'back-to-previous', 'Back to Previous'),
(1676, 'user', 'Front Side Of', 'front-side-of', 'Front Side Of'),
(1677, 'user', 'Back Side Of', 'back-side-of', 'Back Side Of'),
(1678, 'user', 'Image of', 'image-of', 'Image of'),
(1679, 'user', 'Slot for group booking', 'slot-for-group-booking', 'Allow number of persons per slot'),
(1680, 'user', 'Group Booking Info', 'group-booking-info', 'Group Booking Info'),
(1681, 'user', 'Your KYC information is currently being reviewed. Please wait for further updates.', 'kyc-pending-status', 'Your KYC information is currently being reviewed. Please wait for further updates.'),
(1682, 'user', 'Congratulations! Your KYC information has been successfully verified and approved. You can now proceed with your intended actions or services.', 'kyc-approve-status', 'Your KYC information has been successfully verified and approved. You can now proceed with your intended actions or services.'),
(1683, 'user', 'We regret to inform you that your KYC information has been rejected. Please review the provided guidelines and resubmit your information accordingly.', 'kyc-reject-status', 'We regret to inform you that your KYC information has been rejected. Please review the provided guidelines and resubmit your information accordingly.'),
(1684, 'user', 'KYC Reject Reason', 'kyc-reject-reason-title', 'KYC Reject Reason'),
(1685, 'user', 'Resubmitted at', 'resubmitted-at', 'Resubmitted at'),
(1686, 'user', 'Enable to allow your Mentors to verify KYC documents', 'enable-kyc-title', 'Enable to allow your Mentors to verify KYC documents'),
(1687, 'user', 'We kindly remind you that it is mandatory to submit your KYC (Know Your Customer) documents for account verification. Failure to submit these documents may result in restricted access to your account or services.', 'kyc-verify-alert-user', 'We kindly remind you that it is mandatory to submit your KYC (Know Your Customer) documents for account verification. Failure to submit these documents may result in restricted access to your account or services.'),
(1688, 'admin', 'Custom CSS', 'custom-css', 'Custom CSS'),
(1689, 'admin', 'Add your own css code here', 'add-your-own-css-code-here', 'Add your own css code here'),
(1690, 'front', 'Learn that new skill, launch that project, land your dream career.', 'learn-that-new-skill-launch-that-project', 'Learn that new skill, launch that project, land your dream career.'),
(1691, 'front', 'Browse Mentors by Categories', 'browse-mentors-by-categories', 'Browse Mentors by Categories'),
(1692, 'admin', 'PWA Settings', 'pwa-settings', 'PWA Settings'),
(1693, 'admin', 'Enable PWA (Progressive Web Apps)', 'enable-pwa', 'Enable PWA (Progressive Web Apps)'),
(1694, 'admin', 'Enable to allow your users to install PWA on their phone', 'pwa-enable-title', 'Enable to allow your users to install PWA on their phone'),
(1695, 'admin', 'You have reached the limit of free sessions! To continue, please add a price for your session.', 'free-session-limit', 'You have reached the limit of free sessions! To continue, please add a price for your session.'),
(1696, 'admin', 'Set price 0 for free session', 'set-price-0-for-free-session', 'Set price 0 for free session'),
(1697, 'user', 'How many individuals will be permitted for booking per time slot', 'individuals-per-slot-booking', 'How many individuals will be permitted for booking per time slot'),
(1698, 'user', 'Individual Booking', 'individual-booking', 'Individual Booking'),
(1699, 'user', 'Social Login', 'social-login', 'Social Login'),
(1700, 'user', 'Redirect Url', 'redirect-url', 'Redirect Url'),
(1701, 'user', 'Google Login', 'google-login', 'Google Login'),
(1702, 'user', 'Integration Docs', 'integration-docs', 'Integration Docs'),
(1703, 'user', 'Continue with Google', 'continue-with-google', 'Continue with Google'),
(1704, 'user', 'When should we meet?', 'when-should-we-meet', 'When should we meet?'),
(1705, 'user', 'You are already signed in. Please confirm your booking to proceed.', 'already-signed-in-msg', 'You are already signed in. Please confirm your booking to proceed.'),
(1706, 'user', 'API Usage', 'api-usage', 'API Usage'),
(1707, 'user', 'Utilize the Admin Zoom API to manage all users zoom meetings.', 'utilize-the-admin-zoom-api', 'Utilize the Admin Zoom API to manage all users zoom meetings.'),
(1708, 'user', 'Allow users to manage their Zoom meetings via their individual zoom API', 'allow-users-to-manage-their-zoom-api', 'Allow users to manage their Zoom meetings via their individual zoom API'),
(1709, 'user', 'Site Color Mode', 'site-color-mode', 'Site Color Mode'),
(1710, 'user', 'Saturday', 'saturday', 'Saturday');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `mgs_from` int(11) DEFAULT NULL,
  `mgs_to` int(11) DEFAULT NULL,
  `message` text,
  `mgs_time` datetime NOT NULL,
  `mgs_seen` tinyint(1) DEFAULT '0',
  `mgs_seen_time` varchar(255) DEFAULT NULL,
  `ongoing_id` int(11) DEFAULT NULL,
  `reply_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `noti_type` int(11) DEFAULT NULL,
  `noti_time` datetime NOT NULL,
  `seen` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `monthly_price` decimal(10,2) DEFAULT NULL,
  `lifetime_price` decimal(10,2) NOT NULL DEFAULT '1000.00',
  `bill_type` varchar(255) DEFAULT NULL,
  `is_special` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `package`
--

INSERT INTO `package` (`id`, `name`, `slug`, `price`, `monthly_price`, `lifetime_price`, `bill_type`, `is_special`, `status`) VALUES
(1, 'Free', 'basic', '0.00', '0.00', '0.00', 'year', 0, 1),
(2, 'Standard', 'standared', '2.00', '1.00', '3.00', 'year', 1, 1),
(3, 'Premium', 'premium', '5.00', '4.00', '65000.00', 'year', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `lang_id` varchar(155) NOT NULL DEFAULT '1',
  `business_id` varchar(255) DEFAULT NULL,
  `type` varchar(155) DEFAULT 'admin',
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `details` longtext,
  `status` int(11) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `lang_id`, `business_id`, `type`, `title`, `slug`, `details`, `status`, `created_at`) VALUES
(1, '1', '0', 'admin', 'Terms and Condition', 'terms-and-condition', '<h5>Welcome to Mentorship Community!</h5>\n\n<p>These terms and conditions outline the rules and regulations for the use of Mentorship\'s Website.</p>\n\n<p>By accessing this website we assume you accept these terms and conditions. Do not continue to use Mentorship Community if you do not agree to take all of the terms and conditions stated on this page. The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: \"Client\", \"You\" and \"Your\" refers to you, the person log on this website and compliant to the Company’s terms and conditions. \"The Company\", \"Ourselves\", \"We\", \"Our\" and \"Us\", refers to our Company. \"Party\", \"Parties\", or \"Us\", refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client’s needs in respect of provision of the Company’s stated services, in accordance with and subject to, prevailing law of Netherlands. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.</p>\n\n<h5>Cookies</h5>\n\n<p>We employ the use of cookies. By accessing Mentorship Community, you agreed to use cookies in agreement with the Mentorship\'s Privacy Policy.</p>\n\n<p>Most interactive websites use cookies to let us retrieve the user’s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies.</p>\n\n<h5>License</h5>\n\n<p>Unless otherwise stated, Mentorship and/or its licensors own the intellectual property rights for all material on Mentorship Community. All intellectual property rights are reserved. You may access this from Mentorship Community for your own personal use subjected to restrictions set in these terms and conditions.</p>\n\n<p>You must not:</p>\n\n<p>Republish material from Mentorship Community</p>\n\n<p>Sell, rent or sub-license material from Mentorship Community</p>\n\n<p>Reproduce, duplicate or copy material from Mentorship Community</p>\n\n<p>Redistribute content from Mentorship Community</p>\n\n<p>This Agreement shall begin on the date hereof.</p>\n\n<p>Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. Mentorship does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of Mentorship,its agents and/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, Mentorship shall not be liable for the Comments or for any liability, damages or expenses caused and/or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.</p>\n\n<p>Mentorship reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.</p>\n\n<p>You warrant and represent that:</p>\n\n<p>You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;</p>\n\n<p>The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;</p>\n\n<p>The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy.</p>\n\n<p>The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.</p>\n\n<p>You hereby grant Mentorship a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.</p>\n\n<p>Hyperlinking to our Content</p>\n\n<p>The following organizations may link to our Website without prior written approval:</p>\n\n<p>Government agencies;</p>\n\n<p>Search engines;</p>\n\n<p>News organizations;</p>\n\n<p>Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and</p>\n\n<p>System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.</p>\n\n<p>These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and/or services; and (c) fits within the context of the linking party’s site.</p>\n\n<p>We may consider and approve other link requests from the following types of organizations:</p>\n\n<p>commonly-known consumer and/or business information sources;</p>\n\n<p>dot.com community sites;</p>\n\n<p>associations or other groups representing charities;</p>\n\n<p>online directory distributors;</p>\n\n<p>internet portals;</p>\n\n<p>accounting, law and consulting firms; and</p>\n\n<p>educational institutions and trade associations.</p>\n\n<p>We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of Mentorship; and (d) the link is in the context of general resource information.</p>\n\n<p>These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party’s site.</p>\n\n<p>If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to Mentorship. Please include your name, your organization name, contact information as well as the URL of your site, a list of any URLs from which you intend to link to our Website, and a list of the URLs on our site to which you would like to link. Wait 2-3 weeks for a response.</p>\n\n<p>Approved organizations may hyperlink to our Website as follows:</p>\n\n<p>By use of our corporate name; or</p>\n\n<p>By use of the uniform resource locator being linked to; or</p>\n\n<p>By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party’s site.</p>\n\n<p>No use of Mentorship\'s logo or other artwork will be allowed for linking absent a trademark license agreement.</p>\n\n<p>iFrames</p>\n\n<p>Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.</p>\n\n<p>Content Liability</p>\n\n<p>We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.</p>\n\n<p>Your Privacy</p>\n\n<p>Please read our Privacy Policy.</p>\n\n<p>Reservation of Rights</p>\n\n<p>We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it’s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.</p>\n\n<p>Removal of links from our website</p>\n\n<p>If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.</p>\n\n<p>We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.</p>\n\n<h5>Disclaimer</h5>\n\n<p>To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:</p>\n\n<p>limit or exclude our or your liability for death or personal injury;</p>\n\n<p>limit or exclude our or your liability for fraud or fraudulent misrepresentation;</p>\n\n<p>limit any of our or your liabilities in any way that is not permitted under applicable law; or</p>\n\n<p>exclude any of our or your liabilities that may not be excluded under applicable law.</p>\n\n<p>The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.</p>\n\n<p>As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.</p>', 1, '2024-02-25 06:10:45'),
(2, '1', '0', 'admin', 'Privacy Policy', 'privacy-policy', '<p>Privacy Policy for Mentorship Company</p>\n\n<p>Effective Date: 22 Feb 2024</p>\n\n<p>Mentorship Company (\"we\" or \"us\") is committed to protecting the privacy of our users. This Privacy Policy outlines how we collect, use, disclose, and safeguard your personal information when you use our services or website.</p>\n\n<h5>1. Information We Collect:</h5>\n\n<p>1.1. Personal Information: - When you create an account or use our services, we may collect personal information such as your name, email address, and payment information. - We may also collect demographic information, such as your age, gender, and occupation, to better tailor our services to your needs.</p>\n\n<p>1.2. Usage Information: - We collect information about how you interact with our website and services, including your browsing activity, session duration, and IP address.</p>\n\n<p>1.3. Communications: - When you contact us or communicate with other users through our platform, we may collect and store the content of your communications.</p>\n\n<h5>2. Use of Information:</h5>\n\n<p>2.1. We use the information we collect to provide and improve our services, personalize your experience, and communicate with you about our products and promotions.</p>\n\n<p>2.2. We may use your information to respond to your inquiries, troubleshoot technical issues, and enforce our Terms and Conditions.</p>\n\n<p>2.3. We may aggregate and anonymize user data for analytical purposes, such as monitoring usage trends and improving our services.</p>\n\n<h5>3. Disclosure of Information:</h5>\n\n<p>3.1. We may share your personal information with third-party service providers who assist us in operating our website, processing payments, and delivering our services.</p>\n\n<p>3.2. We may disclose your information in response to legal requests, court orders, or government regulations, or to protect our rights, property, or safety, or the rights, property, or safety of others.</p>\n\n<p>3.3. In the event of a merger, acquisition, or sale of all or a portion of our assets, your information may be transferred as part of the transaction. We will notify you via email and/or a prominent notice on our website of any change in ownership or use of your personal information.</p>\n\n<h5>4. Data Security:</h5>\n\n<p>4.1. We employ reasonable security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</p>\n\n<p>4.2. Despite our efforts to safeguard your information, no method of transmission over the Internet or electronic storage is completely secure. Therefore, we cannot guarantee absolute security.</p>\n\n<h5>5. Your Choices:</h5>\n\n<p>5.1. You may update or correct your account information at any time by logging into your account settings.</p>\n\n<p>5.2. You may opt-out of receiving promotional emails from us by following the instructions provided in the email or by contacting us directly.</p>\n\n<h5>6. Children\'s Privacy:</h5>\n\n<p>6.1. Our services are not intended for children under the age of 18. We do not knowingly collect personal information from children under the age of 18. If you are a parent or guardian and believe that your child has provided us with personal information, please contact us immediately.</p>\n\n<p>7. Changes to this Privacy Policy:</p>\n\n<p>7.1. We reserve the right to update or modify this Privacy Policy at any time. Any changes will be effective immediately upon posting the revised Privacy Policy on our website.</p>\n\n<h5>8. Contact Us:</h5>\n\n<p>If you have any questions or concerns about this Privacy Policy, please contact us at [Insert Contact Information].</p>\n\n<p>By using Mentorship Company\'s services, you consent to the collection and use of your information as outlined in this Privacy Policy.</p>', 1, '2024-02-25 06:11:04');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `puid` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` varchar(255) DEFAULT NULL,
  `billing_type` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` date NOT NULL,
  `expire_on` date DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `proof` text,
  `tax` varchar(255) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment_user`
--

CREATE TABLE `payment_user` (
  `id` int(11) NOT NULL,
  `puid` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT '0.00',
  `commission_amount` decimal(10,2) DEFAULT '0.00',
  `commission_rate` int(11) DEFAULT '0',
  `status` varchar(255) NOT NULL,
  `created_at` date NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `proof` varchar(255) DEFAULT NULL,
  `type` varchar(155) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payouts`
--

CREATE TABLE `payouts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payout_method` varchar(255) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `message` text,
  `currency` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `plan_coupons`
--

CREATE TABLE `plan_coupons` (
  `id` int(11) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `user_id` varchar(155) DEFAULT '0',
  `plan` varchar(255) NOT NULL,
  `plan_type` varchar(255) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `days` varchar(155) DEFAULT NULL,
  `discount` int(11) NOT NULL,
  `discount_type` varchar(155) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `quantity` int(11) DEFAULT '0',
  `used` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `apply_date` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `plan_coupons_apply`
--

CREATE TABLE `plan_coupons_apply` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_services`
--

CREATE TABLE `product_services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `details` text,
  `image` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `orders` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_services`
--

INSERT INTO `product_services` (`id`, `name`, `details`, `image`, `thumb`, `orders`) VALUES
(1, 'Profile Creation', 'Users can create detailed profiles highlighting their professional background, skills, and areas of expertise. This information helps facilitate better mentor-mentee matches.', 'uploads/medium/9cb498c3ee1cbe6d121da550526038cc_medium-128x128.png', 'uploads/thumbnail/9cb498c3ee1cbe6d121da550526038cc_thumb-128x128.png', 1),
(2, 'Search and Filter', 'Users can search for mentors based on specific criteria such as industry, location, language, and availability. Advanced filters help narrow down search results to find the most suitable mentors.', 'uploads/medium/ad96cc5e643588f3874fd170a4a46f4d_medium-128x128.png', 'uploads/thumbnail/ad96cc5e643588f3874fd170a4a46f4d_thumb-128x128.png', 2),
(3, 'Messaging System', 'This platform provides a built-in messaging system that allows mentees to communicate with potential mentors before initiating a mentoring relationship. This feature enables mentees to ask questions, discuss goals, and gauge compatibility with mentors.', 'uploads/medium/f8980bf97933f114e19a6c5825149fdd_medium-128x128.png', 'uploads/thumbnail/f8980bf97933f114e19a6c5825149fdd_thumb-128x128.png', 3),
(4, 'Video Sessions', 'The platform offers video conferencing capabilities, allowing mentees and mentors to schedule and conduct mentoring sessions remotely. This feature provides flexibility and convenience for users regardless of their location.', 'uploads/medium/1d1a04205a4886041066c0f47bc42890_medium-128x128.png', 'uploads/thumbnail/1d1a04205a4886041066c0f47bc42890_thumb-128x128.png', 4),
(5, 'Feedback and Ratings', 'After each mentoring session, both mentees and mentors can provide feedback and ratings based on their experience. This feature helps maintain the quality of mentoring relationships and allows users to continuously improve.', 'uploads/medium/84c975c7bf4ebeafc99539b4671fd8d0_medium-128x128.png', 'uploads/thumbnail/84c975c7bf4ebeafc99539b4671fd8d0_thumb-128x128.png', 5),
(6, 'Payment Integration', 'Supports various payment gateways for secure online payments, allowing users to pay for bookings seamlessly.', 'uploads/medium/ebbf5049e785fe3036ff1fd0e921103d_medium-128x128.png', 'uploads/thumbnail/ebbf5049e785fe3036ff1fd0e921103d_thumb-128x128.png', 6),
(7, 'Reports', 'Generates detailed reports, helping your businesses gain insights into most booked sessions, mentees, countries & profits. These insights aid in informed decision-making.', 'uploads/medium/5c2142f70fad6e21ba8da47e334287af_medium-128x128.png', 'uploads/thumbnail/5c2142f70fad6e21ba8da47e334287af_thumb-128x128.png', 7),
(8, 'Privacy and Security', 'We’re committed to keeping your data secure. We uses end-to-end encryption to protect your sensitive financial information. Nobody gets a hand on your important data, not even us.', 'uploads/medium/1b4fba937afddbacef8e81e3c94fc196_medium-128x128.png', 'uploads/thumbnail/1b4fba937afddbacef8e81e3c94fc196_thumb-128x128.png', 8);

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` int(11) NOT NULL,
  `referrar_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `order_id` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `commision` varchar(255) NOT NULL,
  `commision_amount` varchar(255) NOT NULL,
  `created_at` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `referral_payouts`
--

CREATE TABLE `referral_payouts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payout_method` varchar(255) NOT NULL,
  `method_details` varchar(255) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `referral_settings`
--

CREATE TABLE `referral_settings` (
  `id` int(11) NOT NULL,
  `is_enable` varchar(255) NOT NULL,
  `referral_policy` text NOT NULL,
  `commision_rate` varchar(255) NOT NULL,
  `minimum_payout` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) NOT NULL,
  `referral_guideline` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `referral_settings`
--

INSERT INTO `referral_settings` (`id`, `is_enable`, `referral_policy`, `commision_rate`, `minimum_payout`, `payment_method`, `referral_guideline`) VALUES
(1, '1', '1', '60', '50', '1. Paypal 2. Bank Deposit yrty', '<p><span xss=removed>1.fdhgfdhjfghfghfgh12333ytryrtyhgfhfghfghfghfghfghfghfghfghfghf</span></p>\r\n\r\n<p><span xss=removed>2. fdhgfdhjfghfghfgh12333ytryrty gfhgfhgf fghgfhgn gfhgfhgf</span></p>\r\n\r\n<p><span xss=removed>3. fdhgfdhjfghfghfgh12333ytryrtygfhgfh gfhgfh gffhfgh fghgfh</span></p>\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `mentee_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `feedback` varchar(255) NOT NULL,
  `rating` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `lang_id` varchar(155) DEFAULT NULL,
  `business_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `details` longtext NOT NULL,
  `icon` varchar(155) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `enable_booking` varchar(155) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration_type` varchar(155) DEFAULT NULL,
  `duration` varchar(155) DEFAULT NULL,
  `content_type` varchar(155) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `duration` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `total_slot` varchar(255) DEFAULT NULL,
  `slot_for` varchar(255) DEFAULT NULL,
  `session_number` varchar(255) DEFAULT NULL,
  `session_repeat` varchar(255) DEFAULT NULL,
  `skill_id` varchar(255) DEFAULT NULL,
  `allow_session` varchar(255) DEFAULT NULL,
  `is_public` varchar(255) DEFAULT NULL,
  `is_default` varchar(255) DEFAULT NULL,
  `enable_group_booking` int(11) DEFAULT '0',
  `group_booking_slot` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `intro_video` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_booking`
--

CREATE TABLE `session_booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mentee_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `booking_number` varchar(255) NOT NULL,
  `price` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `time` varchar(255) DEFAULT NULL,
  `note` text,
  `is_group` varchar(55) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `host_url` varchar(255) DEFAULT NULL,
  `join_url` varchar(255) DEFAULT NULL,
  `zoom_password` varchar(155) DEFAULT NULL,
  `is_start` varchar(55) NOT NULL DEFAULT '0',
  `type` varchar(155) NOT NULL DEFAULT 'online',
  `pay_info` int(11) DEFAULT NULL,
  `payment_status` int(11) DEFAULT NULL,
  `sync_calendar` varchar(255) DEFAULT NULL,
  `sync_calendar_user` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `confirm_date` varchar(155) DEFAULT NULL,
  `is_recurring` int(11) NOT NULL DEFAULT '0',
  `recurring_count` int(11) NOT NULL DEFAULT '0',
  `next_recur_date` varchar(155) DEFAULT NULL,
  `is_completed` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `site_title` varchar(255) DEFAULT NULL,
  `site_title_mentor` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `hero_img` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` text,
  `footer_about` text,
  `admin_email` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `pagination_limit` int(11) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `google_analytics` longtext,
  `custom_css` longtext,
  `site_color` varchar(255) DEFAULT NULL,
  `site_font` varchar(255) DEFAULT NULL,
  `layout` int(11) DEFAULT NULL,
  `front_layout` int(11) DEFAULT '1',
  `site_mode` varchar(155) DEFAULT 'light',
  `about_info` mediumtext,
  `ind_code` varchar(255) DEFAULT NULL,
  `purchase_code` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `pwa_logo` varchar(155) DEFAULT NULL,
  `enable_pwa` int(11) DEFAULT '0',
  `enable_captcha` int(11) NOT NULL DEFAULT '0',
  `enable_workflow` int(11) NOT NULL DEFAULT '1',
  `enable_feature` int(11) NOT NULL,
  `enable_users` int(11) NOT NULL DEFAULT '1',
  `enable_blog` int(11) NOT NULL,
  `enable_faq` int(11) NOT NULL,
  `enable_animation` int(11) DEFAULT '1',
  `enable_registration` int(11) NOT NULL,
  `enable_payment` int(11) NOT NULL,
  `enable_paypal` int(11) NOT NULL DEFAULT '0',
  `enable_email_verify` int(11) NOT NULL,
  `enable_mentor_auto_approve` int(11) DEFAULT NULL,
  `check_email_verify_user` varchar(155) DEFAULT '0',
  `enable_multilingual` int(11) DEFAULT '0',
  `enable_frontend` varchar(155) DEFAULT '1',
  `enable_lifetime` varchar(155) DEFAULT '0',
  `enable_coupon` int(11) DEFAULT '0',
  `enable_kyc` varchar(5) DEFAULT '0',
  `captcha_type` int(11) DEFAULT NULL,
  `captcha_site_key` varchar(255) DEFAULT NULL,
  `captcha_secret_key` varchar(255) DEFAULT NULL,
  `paypal_email` varchar(255) DEFAULT NULL,
  `paypal_payment` int(11) DEFAULT '0',
  `stripe_payment` int(11) DEFAULT '0',
  `publish_key` varchar(255) DEFAULT NULL,
  `secret_key` varchar(255) DEFAULT NULL,
  `paystack_payment` varchar(155) DEFAULT '0',
  `paystack_secret_key` varchar(255) DEFAULT NULL,
  `paystack_public_key` varchar(255) DEFAULT NULL,
  `razorpay_payment` varchar(155) DEFAULT '0',
  `razorpay_key_id` varchar(255) DEFAULT NULL,
  `razorpay_key_secret` varchar(255) DEFAULT NULL,
  `flutterwave_payment` int(11) DEFAULT '0',
  `flutterwave_public_key` varchar(255) DEFAULT NULL,
  `flutterwave_secret_key` varchar(255) DEFAULT NULL,
  `mercado_payment` int(11) DEFAULT '0',
  `mercado_api_key` varchar(255) DEFAULT NULL,
  `mercado_token` varchar(255) DEFAULT NULL,
  `mercado_currency` varchar(155) DEFAULT NULL,
  `paypal_mode` varchar(255) DEFAULT 'sandbox',
  `openai_key` varchar(255) DEFAULT NULL,
  `openai_model` varchar(255) DEFAULT NULL,
  `twillo_account_sid` varchar(255) DEFAULT NULL,
  `twillo_auth_token` varchar(255) DEFAULT NULL,
  `twillo_number` varchar(255) DEFAULT NULL,
  `enable_sms` int(11) NOT NULL,
  `enable_whatsapp_msg` int(11) DEFAULT '0',
  `ultramsg_instance_id` varchar(255) DEFAULT NULL,
  `ultramsg_token` varchar(255) DEFAULT NULL,
  `enable_wallet` varchar(155) DEFAULT '0',
  `min_payout_amount` varchar(155) DEFAULT '0',
  `commission_rate` varchar(155) DEFAULT '0',
  `paypal_payout` varchar(155) DEFAULT '1',
  `iban_payout` varchar(155) DEFAULT '1',
  `swift_payout` varchar(155) DEFAULT '1',
  `google_client_id` text,
  `google_client_secret` varchar(255) DEFAULT NULL,
  `enable_offline_payment` varchar(255) DEFAULT '0',
  `offline_details` text,
  `zoom_api_user` int(11) DEFAULT '1',
  `zoom_account_id` varchar(155) DEFAULT NULL,
  `zoom_client_id` varchar(155) DEFAULT NULL,
  `zoom_client_secret` varchar(255) DEFAULT NULL,
  `mail_protocol` varchar(255) DEFAULT NULL,
  `mail_title` varchar(255) DEFAULT NULL,
  `mail_host` varchar(255) DEFAULT NULL,
  `mail_port` varchar(255) DEFAULT NULL,
  `mail_encryption` varchar(255) DEFAULT 'ssl',
  `mail_username` varchar(255) DEFAULT NULL,
  `mail_password` varchar(255) DEFAULT NULL,
  `is_smtp` int(11) DEFAULT '1',
  `chart_style` varchar(255) NOT NULL DEFAULT 'style1',
  `num_format` varchar(155) DEFAULT '0',
  `curr_locate` varchar(155) DEFAULT '0',
  `country` int(11) NOT NULL DEFAULT '178',
  `site_info` int(11) DEFAULT NULL,
  `lang` int(11) NOT NULL DEFAULT '1',
  `trial_days` varchar(155) DEFAULT '0',
  `reminder_days` varchar(255) DEFAULT '0',
  `tax_name` varchar(255) DEFAULT NULL,
  `tax_value` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'live',
  `time_zone` int(11) DEFAULT '1',
  `booking_date_type` varchar(255) DEFAULT 'slot',
  `version` varchar(255) NOT NULL DEFAULT 'v1.1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `site_title`, `site_title_mentor`, `favicon`, `logo`, `hero_img`, `keywords`, `description`, `footer_about`, `admin_email`, `mobile`, `copyright`, `pagination_limit`, `facebook`, `instagram`, `twitter`, `linkedin`, `google_analytics`, `custom_css`, `site_color`, `site_font`, `layout`, `front_layout`, `site_mode`, `about_info`, `ind_code`, `purchase_code`, `link`, `pwa_logo`, `enable_pwa`, `enable_captcha`, `enable_workflow`, `enable_feature`, `enable_users`, `enable_blog`, `enable_faq`, `enable_animation`, `enable_registration`, `enable_payment`, `enable_paypal`, `enable_email_verify`, `enable_mentor_auto_approve`, `check_email_verify_user`, `enable_multilingual`, `enable_frontend`, `enable_lifetime`, `enable_coupon`, `enable_kyc`, `captcha_type`, `captcha_site_key`, `captcha_secret_key`, `paypal_email`, `paypal_payment`, `stripe_payment`, `publish_key`, `secret_key`, `paystack_payment`, `paystack_secret_key`, `paystack_public_key`, `razorpay_payment`, `razorpay_key_id`, `razorpay_key_secret`, `flutterwave_payment`, `flutterwave_public_key`, `flutterwave_secret_key`, `mercado_payment`, `mercado_api_key`, `mercado_token`, `mercado_currency`, `paypal_mode`, `openai_key`, `openai_model`, `twillo_account_sid`, `twillo_auth_token`, `twillo_number`, `enable_sms`, `enable_whatsapp_msg`, `ultramsg_instance_id`, `ultramsg_token`, `enable_wallet`, `min_payout_amount`, `commission_rate`, `paypal_payout`, `iban_payout`, `swift_payout`, `google_client_id`, `google_client_secret`, `enable_offline_payment`, `offline_details`, `zoom_api_user`, `zoom_account_id`, `zoom_client_id`, `zoom_client_secret`, `mail_protocol`, `mail_title`, `mail_host`, `mail_port`, `mail_encryption`, `mail_username`, `mail_password`, `is_smtp`, `chart_style`, `num_format`, `curr_locate`, `country`, `site_info`, `lang`, `trial_days`, `reminder_days`, `tax_name`, `tax_value`, `type`, `time_zone`, `booking_date_type`, `version`) VALUES
(1, 'Mentorship', 'Learn and grow with help from world-class mentors', 'Teach and grow with help to a learner for world wide', 'uploads/thumbnail/e0b6950209cb69ef1bde56f35e7cb165_thumb-200x200.png', 'uploads/medium/2b4c216d3c1cc54a61ad6d5b51fbfeda_medium-424x94.png', 'uploads/medium/75a5db7b0f82f7f60e1e2c3bbebb2827_medium-999x451.png', 'saas,appointment,booking,services', 'Build an epic career with expert mentors from all over the word, let\'s start today.', 'Empowering your journey, one mentorship at a time. Unlock your potential with our SaaS mentoring system, designed to guide, inspire, and elevate your success.', 'admin@mail.com', '', '© 2024 All rights reserved.', 0, 'facebook.com', 'Instagram.com', 'Twitter.com', 'linkedin', '', '\"\"', '11C287', '0', 0, 1, 'light', 'SW52YWxpZCBMaWNlbnNlIEtleQ==', '', '', 'aHR0cHM6Ly9jZG5qcy5jbG91ZGZsYXJlLmNvbS9hamF4L2xpYnMv', NULL, 1, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 0, 0, '0', 0, '1', '1', 0, '0', NULL, '6LePpnMbAAAAAF9M3gPZehvReqSKSDzNjwKR2rMgg', '6LePpnMbAAAAAJv2VCnkvvF5LTSBWj9BQypWUwRty', 'paypal@gmail.com', 1, 1, 'pk_test_dmiL3pzy2WRveqVtkSvnAiyEk00TbcdiWmW', 'sk_test_zdHkX8tpxqezUjxwKKrOENoKH00i4EnkxdN', '1', 'sk_test_86229dfa714a8f3aaa612c15b3c7b539fae9c8d65', 'pk_test_23fbt09cba0018fd461e2d12836f2d184212ad9fc', '1', 'rzp_test_vCYySgSNIZmBQoI', 'P0vGtGSuMA0XvlRR96kOH52o5', 1, 'FLWPdBK_TEST-d5d8a175501a8767e1e50855856cf976-X', 'FLWSECK_TEST-c0d83f013abb8dabf2cdea267be53732e-X', 1, 'TEST-efe94707-b66f-404d-d897f-81b5b389b7f6', 'TEST-604d5379489899165-060513-4ed6c822a5daa5a2dde60fdf91f7f477-222526481', 'ARS', 'sandbox', '', '', 'ACe18ba19ed1df9246688a74d6bab688ae', 'Authe18ba19ed1df9246688a74d6bab688ae', '34534', 1, 1, 'instance59937', 'oi3c5tfl0w2ef7z2', '1', '10', '20', '1', '1', '1', '926711531504-lhamb9e7bbpfptvb5t88d8jghqalhg00.apps.googleusercontent.com', 'GkLThGkP55gKbcJwS7agg2aC', '1', '<p xss=removed>Enter your bank info to receive offline payments</p>', 1, '2ApRZ48qRySE95zgmQvkdg', 'i6RRvulPSmWPUURfaa14WA', 'L7JajyRQ1aje8O2O5tyOI884gATdJ6xP', 'smtp', 'Test mail', 'smtp.gmail.com', '465', 'ssl', 'codericksmail@gmail.com', 'ZXpmenB1cGVicHNjYm1taw==', 1, '', '2', '1', 178, 1, 1, '14', '0', '', '', 'live', 1, 'slot', '1.3');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `skill` varchar(500) NOT NULL,
  `category_id` varchar(255) NOT NULL,
  `details` text,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `skill`, `category_id`, `details`, `status`) VALUES
(1, 'Front-end', '1', '', '1'),
(2, 'Back-end', '1', '', '1'),
(3, 'Full stack', '1', '', '1'),
(4, 'Data engineering', '1', '', '1'),
(5, 'UX Engineering', '1', '', '1'),
(6, 'AI/ML Engineering', '1', '', '1'),
(7, 'iOS Engineering', '1', '', '1'),
(8, 'Android Engineering', '1', '', '1'),
(9, 'Development Operations', '1', '', '1'),
(10, 'QA Engineer', '1', '', '1'),
(11, 'Architectural Engineering', '1', '', '1'),
(12, 'Security Engineering', '1', '', '1'),
(13, 'Site Reliability', '1', '', '1'),
(14, 'Branding', '2', '', '1'),
(15, 'Digital Marketing', '2', '', '1'),
(16, 'Content Marketing', '2', '', '1'),
(17, 'Event Marketing', '2', '', '1'),
(18, 'Guerilla Marketing', '2', '', '1'),
(19, 'Growth Hacking', '2', '', '1'),
(20, 'Sales', '2', '', '1'),
(21, 'Business Development', '2', '', '1'),
(22, 'Offline Marketing', '2', '', '1'),
(23, 'Direct Marketing', '2', '', '1'),
(24, 'Account-based Marketing (ABM)', '2', '', '1'),
(25, 'Customer Success Management', '2', '', '1'),
(26, 'Community Management', '2', '', '1'),
(27, 'Product Marketing', '2', '', '1'),
(28, 'Content Creation', '2', '', '1'),
(29, 'Customer Experience (CX)', '3', '', '1'),
(30, 'Generalist Product Management', '3', '', '1'),
(31, 'Technical Product Management', '3', '', '1'),
(32, 'Growth Product Management', '3', '', '1'),
(33, 'Data Product Management', '3', '', '1'),
(34, 'Platform Product Management', '3', '', '1'),
(35, 'Group Product Management', '3', '', '1'),
(36, 'Program Management', '3', '', '1'),
(37, 'Project Management', '3', '', '1'),
(38, 'Graphic Design', '4', '', '1'),
(39, 'UX Design', '4', '', '1'),
(40, 'UI/ Visual Design', '4', '', '1'),
(41, 'Industrial Design', '4', '', '1'),
(42, 'Motion Design', '4', '', '1'),
(43, 'Game Design', '4', '', '1'),
(44, 'Branding and Identity Design', '4', '', '1'),
(45, 'Multimedia Design', '4', '', '1'),
(46, 'XR Design', '4', '', '1'),
(47, '3D Design', '4', '', '1'),
(48, 'Design Operations', '4', '', '1'),
(49, 'Service Design', '4', '', '1'),
(50, 'Content Design', '4', '', '1'),
(51, 'Product Design', '4', '', '1'),
(52, 'Interaction Design', '4', '', '1'),
(53, 'Growth Design', '4', '', '1'),
(54, 'Hardware Design', '4', '', '1'),
(55, 'Data Engineering', '5', '', '1'),
(56, 'Data Analysis', '5', '', '1'),
(57, 'Data Scientist', '5', '', '1'),
(58, 'Creative writing', '6', '', '1'),
(59, 'Technical writing', '6', '', '1'),
(60, 'Scriptwriting', '6', '', '1'),
(61, 'Content Strategy', '6', '', '1'),
(62, 'Copywriting', '6', '', '1'),
(63, 'Social media writing', '6', '', '1'),
(64, 'UX writing', '6', '', '1');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `key` varchar(150) DEFAULT NULL,
  `value` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`) VALUES
(1, 'google_client_id', '559968879930-t6nbt41noogdbf59a4t1mhp1aq0c8p4e.apps.googleusercontent.com'),
(2, 'google_secret_key', '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'),
(3, 'google_redirect', 'http://localhost:8888/mentor/login'),
(4, 'firebase_keypair', 'BAiWdJui0vBIybQCem-5z8i_Iy0t2zl324NMmdGJ0SL0tYPOi1jE09N6nfeV39wj6p_wI29jjDYuDdPov63wvGQ'),
(5, 'firebase_apiKey', 'AIzaSyCrTVhHHyLS7qqunsO-NbUhewMQp6akp-o'),
(6, 'firebase_authDomain', 'aoxio-pus-notification.firebaseapp.com'),
(7, 'firebase_projectId', 'aoxio-pus-notification'),
(8, 'firebase_storageBucket', 'aoxio-pus-notification.appspot.com'),
(9, 'firebase_messagingSenderId', '26280305547'),
(10, 'firebase_appId', '1:26280305547:web:6f93e48c0da11435d7458e'),
(11, 'firebase_server_key', 'AAAABh5to4s:APA91bHd3azwrWtOI3Yo1V6J4dJUyq_mxNkRH6IhFMjz4GQeOYLuPrEe5Ne0CZWoJEVlGtQYYRQ2SF2TT9mhIHp3sUSt481yhuDBlZlVrRZc0znaYM6hOWSA6C0h_Yj2Uc9EHXVDNN20'),
(12, 'facebook_app_id', '1059094885171130'),
(13, 'facebook_app_secret', '_53d21df8623ba691cd700f1ef34df9d0'),
(14, 'facebook_graph_version', 'v3.2'),
(15, 'enable_facebook', '0'),
(16, 'enable_google', '0');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `tag_slug` varchar(255) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `lang_id` varchar(155) NOT NULL DEFAULT '1',
  `business_id` varchar(255) DEFAULT NULL,
  `type` varchar(155) NOT NULL DEFAULT 'admin',
  `name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `feedback` text,
  `image` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `time_zone`
--

CREATE TABLE `time_zone` (
  `id` int(11) NOT NULL,
  `name` varchar(35) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `time_zone`
--

INSERT INTO `time_zone` (`id`, `name`) VALUES
(1, 'Europe/Andorra'),
(2, 'Asia/Dubai'),
(3, 'Asia/Kabul'),
(4, 'America/Antigua'),
(5, 'America/Anguilla'),
(6, 'Europe/Tirane'),
(7, 'Asia/Yerevan'),
(8, 'Africa/Luanda'),
(9, 'Antarctica/McMurdo'),
(10, 'Antarctica/Casey'),
(11, 'Antarctica/Davis'),
(12, 'Antarctica/DumontDUrville'),
(13, 'Antarctica/Mawson'),
(14, 'Antarctica/Palmer'),
(15, 'Antarctica/Rothera'),
(16, 'Antarctica/Syowa'),
(17, 'Antarctica/Troll'),
(18, 'Antarctica/Vostok'),
(19, 'America/Argentina/Buenos_Aires'),
(20, 'America/Argentina/Cordoba'),
(21, 'America/Argentina/Salta'),
(22, 'America/Argentina/Jujuy'),
(23, 'America/Argentina/Tucuman'),
(24, 'America/Argentina/Catamarca'),
(25, 'America/Argentina/La_Rioja'),
(26, 'America/Argentina/San_Juan'),
(27, 'America/Argentina/Mendoza'),
(28, 'America/Argentina/San_Luis'),
(29, 'America/Argentina/Rio_Gallegos'),
(30, 'America/Argentina/Ushuaia'),
(31, 'Pacific/Pago_Pago'),
(32, 'Europe/Vienna'),
(33, 'Australia/Lord_Howe'),
(34, 'Antarctica/Macquarie'),
(35, 'Australia/Hobart'),
(36, 'Australia/Currie'),
(37, 'Australia/Melbourne'),
(38, 'Australia/Sydney'),
(39, 'Australia/Broken_Hill'),
(40, 'Australia/Brisbane'),
(41, 'Australia/Lindeman'),
(42, 'Australia/Adelaide'),
(43, 'Australia/Darwin'),
(44, 'Australia/Perth'),
(45, 'Australia/Eucla'),
(46, 'America/Aruba'),
(47, 'Europe/Mariehamn'),
(48, 'Asia/Baku'),
(49, 'Europe/Sarajevo'),
(50, 'America/Barbados'),
(51, 'Asia/Dhaka'),
(52, 'Europe/Brussels'),
(53, 'Africa/Ouagadougou'),
(54, 'Europe/Sofia'),
(55, 'Asia/Bahrain'),
(56, 'Africa/Bujumbura'),
(57, 'Africa/Porto-Novo'),
(58, 'America/St_Barthelemy'),
(59, 'Atlantic/Bermuda'),
(60, 'Asia/Brunei'),
(61, 'America/La_Paz'),
(62, 'America/Kralendijk'),
(63, 'America/Noronha'),
(64, 'America/Belem'),
(65, 'America/Fortaleza'),
(66, 'America/Recife'),
(67, 'America/Araguaina'),
(68, 'America/Maceio'),
(69, 'America/Bahia'),
(70, 'America/Sao_Paulo'),
(71, 'America/Campo_Grande'),
(72, 'America/Cuiaba'),
(73, 'America/Santarem'),
(74, 'America/Porto_Velho'),
(75, 'America/Boa_Vista'),
(76, 'America/Manaus'),
(77, 'America/Eirunepe'),
(78, 'America/Rio_Branco'),
(79, 'America/Nassau'),
(80, 'Asia/Thimphu'),
(81, 'Africa/Gaborone'),
(82, 'Europe/Minsk'),
(83, 'America/Belize'),
(84, 'America/St_Johns'),
(85, 'America/Halifax'),
(86, 'America/Glace_Bay'),
(87, 'America/Moncton'),
(88, 'America/Goose_Bay'),
(89, 'America/Blanc-Sablon'),
(90, 'America/Toronto'),
(91, 'America/Nipigon'),
(92, 'America/Thunder_Bay'),
(93, 'America/Iqaluit'),
(94, 'America/Pangnirtung'),
(95, 'America/Atikokan'),
(96, 'America/Winnipeg'),
(97, 'America/Rainy_River'),
(98, 'America/Resolute'),
(99, 'America/Rankin_Inlet'),
(100, 'America/Regina'),
(101, 'America/Swift_Current'),
(102, 'America/Edmonton'),
(103, 'America/Cambridge_Bay'),
(104, 'America/Yellowknife'),
(105, 'America/Inuvik'),
(106, 'America/Creston'),
(107, 'America/Dawson_Creek'),
(108, 'America/Fort_Nelson'),
(109, 'America/Vancouver'),
(110, 'America/Whitehorse'),
(111, 'America/Dawson'),
(112, 'Indian/Cocos'),
(113, 'Africa/Kinshasa'),
(114, 'Africa/Lubumbashi'),
(115, 'Africa/Bangui'),
(116, 'Africa/Brazzaville'),
(117, 'Europe/Zurich'),
(118, 'Africa/Abidjan'),
(119, 'Pacific/Rarotonga'),
(120, 'America/Santiago'),
(121, 'America/Punta_Arenas'),
(122, 'Pacific/Easter'),
(123, 'Africa/Douala'),
(124, 'Asia/Shanghai'),
(125, 'Asia/Urumqi'),
(126, 'America/Bogota'),
(127, 'America/Costa_Rica'),
(128, 'America/Havana'),
(129, 'Atlantic/Cape_Verde'),
(130, 'America/Curacao'),
(131, 'Indian/Christmas'),
(132, 'Asia/Nicosia'),
(133, 'Asia/Famagusta'),
(134, 'Europe/Prague'),
(135, 'Europe/Berlin'),
(136, 'Europe/Busingen'),
(137, 'Africa/Djibouti'),
(138, 'Europe/Copenhagen'),
(139, 'America/Dominica'),
(140, 'America/Santo_Domingo'),
(141, 'Africa/Algiers'),
(142, 'America/Guayaquil'),
(143, 'Pacific/Galapagos'),
(144, 'Europe/Tallinn'),
(145, 'Africa/Cairo'),
(146, 'Africa/El_Aaiun'),
(147, 'Africa/Asmara'),
(148, 'Europe/Madrid'),
(149, 'Africa/Ceuta'),
(150, 'Atlantic/Canary'),
(151, 'Africa/Addis_Ababa'),
(152, 'Europe/Helsinki'),
(153, 'Pacific/Fiji'),
(154, 'Atlantic/Stanley'),
(155, 'Pacific/Chuuk'),
(156, 'Pacific/Pohnpei'),
(157, 'Pacific/Kosrae'),
(158, 'Atlantic/Faroe'),
(159, 'Europe/Paris'),
(160, 'Africa/Libreville'),
(161, 'Europe/London'),
(162, 'America/Grenada'),
(163, 'Asia/Tbilisi'),
(164, 'America/Cayenne'),
(165, 'Europe/Guernsey'),
(166, 'Africa/Accra'),
(167, 'Europe/Gibraltar'),
(168, 'America/Nuuk'),
(169, 'America/Danmarkshavn'),
(170, 'America/Scoresbysund'),
(171, 'America/Thule'),
(172, 'Africa/Banjul'),
(173, 'Africa/Conakry'),
(174, 'America/Guadeloupe'),
(175, 'Africa/Malabo'),
(176, 'Europe/Athens'),
(177, 'Atlantic/South_Georgia'),
(178, 'America/Guatemala'),
(179, 'Pacific/Guam'),
(180, 'Africa/Bissau'),
(181, 'America/Guyana'),
(182, 'Asia/Hong_Kong'),
(183, 'America/Tegucigalpa'),
(184, 'Europe/Zagreb'),
(185, 'America/Port-au-Prince'),
(186, 'Europe/Budapest'),
(187, 'Asia/Jakarta'),
(188, 'Asia/Pontianak'),
(189, 'Asia/Makassar'),
(190, 'Asia/Jayapura'),
(191, 'Europe/Dublin'),
(192, 'Asia/Jerusalem'),
(193, 'Europe/Isle_of_Man'),
(194, 'Asia/Kolkata'),
(195, 'Indian/Chagos'),
(196, 'Asia/Baghdad'),
(197, 'Asia/Tehran'),
(198, 'Atlantic/Reykjavik'),
(199, 'Europe/Rome'),
(200, 'Europe/Jersey'),
(201, 'America/Jamaica'),
(202, 'Asia/Amman'),
(203, 'Asia/Tokyo'),
(204, 'Africa/Nairobi'),
(205, 'Asia/Bishkek'),
(206, 'Asia/Phnom_Penh'),
(207, 'Pacific/Tarawa'),
(208, 'Pacific/Enderbury'),
(209, 'Pacific/Kiritimati'),
(210, 'Indian/Comoro'),
(211, 'America/St_Kitts'),
(212, 'Asia/Pyongyang'),
(213, 'Asia/Seoul'),
(214, 'Asia/Kuwait'),
(215, 'America/Cayman'),
(216, 'Asia/Almaty'),
(217, 'Asia/Qyzylorda'),
(218, 'Asia/Qostanay'),
(219, 'Asia/Aqtobe'),
(220, 'Asia/Aqtau'),
(221, 'Asia/Atyrau'),
(222, 'Asia/Oral'),
(223, 'Asia/Vientiane'),
(224, 'Asia/Beirut'),
(225, 'America/St_Lucia'),
(226, 'Europe/Vaduz'),
(227, 'Asia/Colombo'),
(228, 'Africa/Monrovia'),
(229, 'Africa/Maseru'),
(230, 'Europe/Vilnius'),
(231, 'Europe/Luxembourg'),
(232, 'Europe/Riga'),
(233, 'Africa/Tripoli'),
(234, 'Africa/Casablanca'),
(235, 'Europe/Monaco'),
(236, 'Europe/Chisinau'),
(237, 'Europe/Podgorica'),
(238, 'America/Marigot'),
(239, 'Indian/Antananarivo'),
(240, 'Pacific/Majuro'),
(241, 'Pacific/Kwajalein'),
(242, 'Europe/Skopje'),
(243, 'Africa/Bamako'),
(244, 'Asia/Yangon'),
(245, 'Asia/Ulaanbaatar'),
(246, 'Asia/Hovd'),
(247, 'Asia/Choibalsan'),
(248, 'Asia/Macau'),
(249, 'Pacific/Saipan'),
(250, 'America/Martinique'),
(251, 'Africa/Nouakchott'),
(252, 'America/Montserrat'),
(253, 'Europe/Malta'),
(254, 'Indian/Mauritius'),
(255, 'Indian/Maldives'),
(256, 'Africa/Blantyre'),
(257, 'America/Mexico_City'),
(258, 'America/Cancun'),
(259, 'America/Merida'),
(260, 'America/Monterrey'),
(261, 'America/Matamoros'),
(262, 'America/Mazatlan'),
(263, 'America/Chihuahua'),
(264, 'America/Ojinaga'),
(265, 'America/Hermosillo'),
(266, 'America/Tijuana'),
(267, 'America/Bahia_Banderas'),
(268, 'Asia/Kuala_Lumpur'),
(269, 'Asia/Kuching'),
(270, 'Africa/Maputo'),
(271, 'Africa/Windhoek'),
(272, 'Pacific/Noumea'),
(273, 'Africa/Niamey'),
(274, 'Pacific/Norfolk'),
(275, 'Africa/Lagos'),
(276, 'America/Managua'),
(277, 'Europe/Amsterdam'),
(278, 'Europe/Oslo'),
(279, 'Asia/Kathmandu'),
(280, 'Pacific/Nauru'),
(281, 'Pacific/Niue'),
(282, 'Pacific/Auckland'),
(283, 'Pacific/Chatham'),
(284, 'Asia/Muscat'),
(285, 'America/Panama'),
(286, 'America/Lima'),
(287, 'Pacific/Tahiti'),
(288, 'Pacific/Marquesas'),
(289, 'Pacific/Gambier'),
(290, 'Pacific/Port_Moresby'),
(291, 'Pacific/Bougainville'),
(292, 'Asia/Manila'),
(293, 'Asia/Karachi'),
(294, 'Europe/Warsaw'),
(295, 'America/Miquelon'),
(296, 'Pacific/Pitcairn'),
(297, 'America/Puerto_Rico'),
(298, 'Asia/Gaza'),
(299, 'Asia/Hebron'),
(300, 'Europe/Lisbon'),
(301, 'Atlantic/Madeira'),
(302, 'Atlantic/Azores'),
(303, 'Pacific/Palau'),
(304, 'America/Asuncion'),
(305, 'Asia/Qatar'),
(306, 'Indian/Reunion'),
(307, 'Europe/Bucharest'),
(308, 'Europe/Belgrade'),
(309, 'Europe/Kaliningrad'),
(310, 'Europe/Moscow'),
(311, 'Europe/Simferopol'),
(312, 'Europe/Kirov'),
(313, 'Europe/Astrakhan'),
(314, 'Europe/Volgograd'),
(315, 'Europe/Saratov'),
(316, 'Europe/Ulyanovsk'),
(317, 'Europe/Samara'),
(318, 'Asia/Yekaterinburg'),
(319, 'Asia/Omsk'),
(320, 'Asia/Novosibirsk'),
(321, 'Asia/Barnaul'),
(322, 'Asia/Tomsk'),
(323, 'Asia/Novokuznetsk'),
(324, 'Asia/Krasnoyarsk'),
(325, 'Asia/Irkutsk'),
(326, 'Asia/Chita'),
(327, 'Asia/Yakutsk'),
(328, 'Asia/Khandyga'),
(329, 'Asia/Vladivostok'),
(330, 'Asia/Ust-Nera'),
(331, 'Asia/Magadan'),
(332, 'Asia/Sakhalin'),
(333, 'Asia/Srednekolymsk'),
(334, 'Asia/Kamchatka'),
(335, 'Asia/Anadyr'),
(336, 'Africa/Kigali'),
(337, 'Asia/Riyadh'),
(338, 'Pacific/Guadalcanal'),
(339, 'Indian/Mahe'),
(340, 'Africa/Khartoum'),
(341, 'Europe/Stockholm'),
(342, 'Asia/Singapore'),
(343, 'Atlantic/St_Helena'),
(344, 'Europe/Ljubljana'),
(345, 'Arctic/Longyearbyen'),
(346, 'Europe/Bratislava'),
(347, 'Africa/Freetown'),
(348, 'Europe/San_Marino'),
(349, 'Africa/Dakar'),
(350, 'Africa/Mogadishu'),
(351, 'America/Paramaribo'),
(352, 'Africa/Juba'),
(353, 'Africa/Sao_Tome'),
(354, 'America/El_Salvador'),
(355, 'America/Lower_Princes'),
(356, 'Asia/Damascus'),
(357, 'Africa/Mbabane'),
(358, 'America/Grand_Turk'),
(359, 'Africa/Ndjamena'),
(360, 'Indian/Kerguelen'),
(361, 'Africa/Lome'),
(362, 'Asia/Bangkok'),
(363, 'Asia/Dushanbe'),
(364, 'Pacific/Fakaofo'),
(365, 'Asia/Dili'),
(366, 'Asia/Ashgabat'),
(367, 'Africa/Tunis'),
(368, 'Pacific/Tongatapu'),
(369, 'Europe/Istanbul'),
(370, 'America/Port_of_Spain'),
(371, 'Pacific/Funafuti'),
(372, 'Asia/Taipei'),
(373, 'Africa/Dar_es_Salaam'),
(374, 'Europe/Kiev'),
(375, 'Europe/Uzhgorod'),
(376, 'Europe/Zaporozhye'),
(377, 'Africa/Kampala'),
(378, 'Pacific/Midway'),
(379, 'Pacific/Wake'),
(380, 'America/New_York'),
(381, 'America/Detroit'),
(382, 'America/Kentucky/Louisville'),
(383, 'America/Kentucky/Monticello'),
(384, 'America/Indiana/Indianapolis'),
(385, 'America/Indiana/Vincennes'),
(386, 'America/Indiana/Winamac'),
(387, 'America/Indiana/Marengo'),
(388, 'America/Indiana/Petersburg'),
(389, 'America/Indiana/Vevay'),
(390, 'America/Chicago'),
(391, 'America/Indiana/Tell_City'),
(392, 'America/Indiana/Knox'),
(393, 'America/Menominee'),
(394, 'America/North_Dakota/Center'),
(395, 'America/North_Dakota/New_Salem'),
(396, 'America/North_Dakota/Beulah'),
(397, 'America/Denver'),
(398, 'America/Boise'),
(399, 'America/Phoenix'),
(400, 'America/Los_Angeles'),
(401, 'America/Anchorage'),
(402, 'America/Juneau'),
(403, 'America/Sitka'),
(404, 'America/Metlakatla'),
(405, 'America/Yakutat'),
(406, 'America/Nome'),
(407, 'America/Adak'),
(408, 'Pacific/Honolulu'),
(409, 'America/Montevideo'),
(410, 'Asia/Samarkand'),
(411, 'Asia/Tashkent'),
(412, 'Europe/Vatican'),
(413, 'America/St_Vincent'),
(414, 'America/Caracas'),
(415, 'America/Tortola'),
(416, 'America/St_Thomas'),
(417, 'Asia/Ho_Chi_Minh'),
(418, 'Pacific/Efate'),
(419, 'Pacific/Wallis'),
(420, 'Pacific/Apia'),
(421, 'Asia/Aden'),
(422, 'Indian/Mayotte'),
(423, 'Africa/Johannesburg'),
(424, 'Africa/Lusaka'),
(425, 'Africa/Harare');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `auth_type` varchar(20) DEFAULT NULL,
  `google_auth_id` varchar(40) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `balance` bigint(20) DEFAULT '0',
  `total_sales` bigint(20) DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(100) DEFAULT 'user',
  `referral_id` varchar(255) DEFAULT NULL,
  `referral_earn` varchar(255) DEFAULT '0',
  `account_type` varchar(255) DEFAULT NULL,
  `user_type` varchar(100) DEFAULT 'registered',
  `trial_expire` date DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text,
  `email_verified` int(11) DEFAULT '0',
  `is_active` int(11) DEFAULT '0',
  `last_active` varchar(255) DEFAULT NULL,
  `last_logout` varchar(255) DEFAULT NULL,
  `total_attendence` int(11) DEFAULT '0',
  `attendence_date` varchar(255) DEFAULT NULL,
  `respond_in` varchar(255) DEFAULT NULL,
  `respond_time` varchar(255) DEFAULT NULL,
  `verify_code` varchar(255) DEFAULT NULL,
  `phone_verified` varchar(255) DEFAULT '0',
  `kyc_verified` varchar(5) DEFAULT '0',
  `sms_count` varchar(255) DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `country` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT 'USD',
  `about_me` varchar(5000) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` text,
  `gender` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `skill` varchar(500) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL,
  `experience_year` varchar(255) DEFAULT NULL,
  `meet_type` varchar(155) DEFAULT 'zoom',
  `gmeet_url` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `linkedin_profile` varchar(255) DEFAULT NULL,
  `facebook_profile` varchar(255) DEFAULT NULL,
  `instagram_profile` varchar(255) DEFAULT NULL,
  `x_profile` varchar(255) DEFAULT NULL,
  `portfolio` varchar(255) DEFAULT NULL,
  `intro_video` varchar(255) DEFAULT NULL,
  `time_zone` varchar(255) DEFAULT NULL,
  `google_analytics` text,
  `enable_appointment` int(11) DEFAULT '1',
  `enable_rating` int(11) DEFAULT '1',
  `enable_sms_notify` varchar(255) DEFAULT '0',
  `enable_sms_alert` varchar(255) DEFAULT '0',
  `check_email_verify_user` varchar(255) DEFAULT NULL,
  `intervals` varchar(255) DEFAULT NULL,
  `holidays` longtext,
  `zoom_account_id` varchar(255) DEFAULT NULL,
  `zoom_client_id` varchar(255) DEFAULT NULL,
  `zoom_client_secret` varchar(255) DEFAULT NULL,
  `date_format` varchar(255) DEFAULT 'd M Y',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_payout_accounts`
--

CREATE TABLE `users_payout_accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payout_paypal_email` varchar(255) DEFAULT NULL,
  `payout_bank_info` mediumtext,
  `iban_full_name` varchar(255) DEFAULT NULL,
  `iban_country_id` varchar(20) DEFAULT NULL,
  `iban_bank_name` varchar(255) DEFAULT NULL,
  `iban_number` varchar(500) DEFAULT NULL,
  `swift_full_name` varchar(255) DEFAULT NULL,
  `swift_address` varchar(500) DEFAULT NULL,
  `swift_state` varchar(255) DEFAULT NULL,
  `swift_city` varchar(255) DEFAULT NULL,
  `swift_postcode` varchar(100) DEFAULT NULL,
  `swift_country_id` varchar(20) DEFAULT NULL,
  `swift_bank_account_holder_name` varchar(255) DEFAULT NULL,
  `swift_iban` varchar(255) DEFAULT NULL,
  `swift_code` varchar(255) DEFAULT NULL,
  `swift_bank_name` varchar(255) DEFAULT NULL,
  `swift_bank_branch_city` varchar(255) DEFAULT NULL,
  `swift_bank_branch_country_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_skill`
--

CREATE TABLE `users_skill` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `workflows`
--

CREATE TABLE `workflows` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text,
  `image` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `workflows`
--

INSERT INTO `workflows` (`id`, `title`, `details`, `image`, `thumb`, `status`) VALUES
(1, 'Create Account', 'It’s very easy to open an account and start your mentorship journey.', 'uploads/medium/f1b40ba9c6882a0f35817d9e3f63ba86_medium-128x128.png', 'uploads/thumbnail/f1b40ba9c6882a0f35817d9e3f63ba86_thumb-128x128.png', 1),
(2, 'Complete your profile', 'Complete your profile with all the info to get attention of mentees', 'uploads/medium/9f3b8b17bde1a340c61df95bdb1046bc_medium-128x128.png', 'uploads/thumbnail/9f3b8b17bde1a340c61df95bdb1046bc_thumb-128x128.png', 1),
(3, 'Hire Mentors', 'Explore our growing catalogue of experienced mentors until you find the perfect fit.', 'uploads/medium/0df5b22a3b5e36e40e03f74455af822e_medium-128x128.png', 'uploads/thumbnail/0df5b22a3b5e36e40e03f74455af822e_thumb-128x128.png', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assaign_days`
--
ALTER TABLE `assaign_days`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assign_time`
--
ALTER TABLE `assign_time`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_category`
--
ALTER TABLE `blog_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `counters`
--
ALTER TABLE `counters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupon_apply`
--
ALTER TABLE `coupon_apply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dialing_codes`
--
ALTER TABLE `dialing_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `educations`
--
ALTER TABLE `educations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `experiences`
--
ALTER TABLE `experiences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favourite`
--
ALTER TABLE `favourite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feature_assaign`
--
ALTER TABLE `feature_assaign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fonts`
--
ALTER TABLE `fonts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lang_values`
--
ALTER TABLE `lang_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_user`
--
ALTER TABLE `payment_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payouts`
--
ALTER TABLE `payouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plan_coupons`
--
ALTER TABLE `plan_coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plan_coupons_apply`
--
ALTER TABLE `plan_coupons_apply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_services`
--
ALTER TABLE `product_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_payouts`
--
ALTER TABLE `referral_payouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_settings`
--
ALTER TABLE `referral_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session_booking`
--
ALTER TABLE `session_booking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_zone`
--
ALTER TABLE `time_zone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_zone_name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_payout_accounts`
--
ALTER TABLE `users_payout_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `users_skill`
--
ALTER TABLE `users_skill`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workflows`
--
ALTER TABLE `workflows`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assaign_days`
--
ALTER TABLE `assaign_days`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assign_time`
--
ALTER TABLE `assign_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_category`
--
ALTER TABLE `blog_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business`
--
ALTER TABLE `business`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `counters`
--
ALTER TABLE `counters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_apply`
--
ALTER TABLE `coupon_apply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dialing_codes`
--
ALTER TABLE `dialing_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT for table `educations`
--
ALTER TABLE `educations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `favourite`
--
ALTER TABLE `favourite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feature_assaign`
--
ALTER TABLE `feature_assaign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fonts`
--
ALTER TABLE `fonts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lang_values`
--
ALTER TABLE `lang_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1711;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_user`
--
ALTER TABLE `payment_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payouts`
--
ALTER TABLE `payouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan_coupons`
--
ALTER TABLE `plan_coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan_coupons_apply`
--
ALTER TABLE `plan_coupons_apply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_services`
--
ALTER TABLE `product_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_payouts`
--
ALTER TABLE `referral_payouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_settings`
--
ALTER TABLE `referral_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_booking`
--
ALTER TABLE `session_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_zone`
--
ALTER TABLE `time_zone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=426;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_payout_accounts`
--
ALTER TABLE `users_payout_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_skill`
--
ALTER TABLE `users_skill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workflows`
--
ALTER TABLE `workflows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
