-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jul 15, 2024 at 03:43 PM
-- Server version: 5.7.39
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mentors_db`
--

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
