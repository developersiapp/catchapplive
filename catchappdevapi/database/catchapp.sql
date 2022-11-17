-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 31, 2019 at 11:43 AM
-- Server version: 5.7.27-0ubuntu0.18.04.1
-- PHP Version: 7.2.21-1+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `catchapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `email`, `password`, `profile_image`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@catchapp.com', 'welcome', 'admin-profile-photo-1566540413.jpg', NULL, NULL, '2019-08-23 11:36:53');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `country` int(11) NOT NULL,
  `zip` int(11) NOT NULL,
  `assigned_djs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `email`, `password`, `street_address`, `city`, `state`, `country`, `zip`, `assigned_djs`, `profile_image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Club 1', 'club1@iapptechnologies.com', 'welcome', 'Club 1', 5, 3, 2, 123456, '', 'club-photo-1566542765.jpeg', '2019-08-23 12:16:05', '2019-08-23 12:16:05', NULL),
(2, 'Club 2', 'club2@iapptechnologies.com', 'welcome', 'Club 2', 5, 3, 2, 123456, '', 'club-photo-1566542814.jpg', '2019-08-23 12:16:54', '2019-08-23 12:17:14', NULL),
(3, 'Club 3', 'club3@iapptechnologies.com', 'welcome', 'Club 3', 5, 3, 2, 123456, '5', 'club-photo-1566554999.jpg', '2019-08-23 15:39:59', '2019-08-23 15:39:59', NULL),
(4, 'Club 4', 'club4@iapptechnologies.com', 'welcome', 'Club 4', 5, 3, 2, 123456, '', 'club-photo-1566555753.png', '2019-08-23 15:40:46', '2019-08-23 15:52:33', NULL),
(5, 'Club 5', 'club5@iapptechnologies.com', 'welcome', 'Club 5', 5, 3, 2, 123456, '3,4,5', 'club-photo-1566555219.jpg', '2019-08-23 15:43:39', '2019-08-23 15:43:39', NULL),
(6, 'Club 6', 'club6@iapptechnologies.com', 'welcome', 'Club 6', 5, 3, 2, 123456, '3,4,5', 'club-photo-1566555305.jpg', '2019-08-23 15:45:05', '2019-08-23 15:45:05', NULL),
(7, 'Club 7', 'club7@iapptechnologies.com', 'welcome', 'Club 7', 5, 3, 2, 123456, '3,4,5', 'club-photo-1566555589.jpg', '2019-08-23 15:45:51', '2019-08-23 15:49:49', NULL),
(8, 'Club 8', 'club8@iapptechnologies.com', 'welcome', 'Club 8', 5, 3, 2, 456123, '3,4,5', 'club-photo-1566555402.jpeg', '2019-08-23 15:46:42', '2019-08-23 15:46:42', NULL),
(9, 'Club 9', 'club9@iapptechnologies.com', 'welcome', 'Club 9', 5, 3, 2, 132456, '3,4,5', 'club-photo-1566555480.jpeg', '2019-08-23 15:47:34', '2019-08-23 15:48:00', NULL),
(10, 'Club 10', 'club10@iapptechnologies.com', 'welcome', 'Club 10', 5, 3, 2, 456123, '3,4,5', 'club-photo-1566555517.jpg', '2019-08-23 15:48:37', '2019-08-23 15:48:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `club_stream`
--

CREATE TABLE `club_stream` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `club_id` int(11) NOT NULL,
  `stream_id` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `stream_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection_code` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `female_listeners` int(11) NOT NULL DEFAULT '0',
  `male_listeners` int(11) NOT NULL DEFAULT '0',
  `stream_time` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `club_stream`
--

INSERT INTO `club_stream` (`id`, `club_id`, `stream_id`, `stream_url`, `connection_code`, `female_listeners`, `male_listeners`, `stream_time`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'cwwqwq2f', 'https://wowzaprod210-i.akamaihd.net/hls/live/851734/d2ea76ee/playlist.m3u8', '0WHHLO', 0, 0, '2019-08-23 15:41:09', NULL, '2019-08-23 12:56:39', '2019-08-23 18:53:00'),
(2, 2, 'qrbltyjz', 'https://wowzaprod210-i.akamaihd.net/hls/live/851734/7f0484b7/playlist.m3u8', '0CdFP6', 0, 0, '2019-08-23 17:52:14', NULL, '2019-08-23 15:55:35', '2019-08-23 18:52:49'),
(3, 5, 'gcx2q407', 'https://wowzaprod210-i.akamaihd.net/hls/live/851734/cf90eedc/playlist.m3u8', '0zZIDl', 0, 0, '2019-08-23 17:51:58', NULL, '2019-08-23 17:43:22', '2019-08-26 16:26:18'),
(4, 6, '6jyfgydh', 'https://wowzaprod210-i.akamaihd.net/hls/live/851734/a5902114/playlist.m3u8', '0HEjBt', 0, 0, NULL, NULL, '2019-08-23 17:43:39', '2019-08-26 10:22:45'),
(5, 8, '3mhqw51n', 'https://wowzaprod210-i.akamaihd.net/hls/live/851734/e86cbd34/playlist.m3u8', '004vnd', 0, 0, NULL, NULL, '2019-08-23 17:43:46', '2019-08-23 17:58:28'),
(6, 7, 'p97qbgjl', 'https://wowzaprod210-i.akamaihd.net/hls/live/851734/a4f2d358/playlist.m3u8', '0UBsBw', 0, 0, NULL, NULL, '2019-08-23 17:43:51', '2019-08-26 16:26:11'),
(7, 4, 'cdhhwpmb', 'https://wowzaprod210-i.akamaihd.net/hls/live/851734/0018b990/playlist.m3u8', '0BuNJW', 0, 0, NULL, NULL, '2019-08-23 18:49:01', '2019-08-26 10:22:29'),
(8, 3, 'b3dvl8jz', 'https://wowzaprod210-i.akamaihd.net/hls/live/851734/0e700fc4/playlist.m3u8', '0nbDKG', 0, 0, NULL, NULL, '2019-08-26 10:22:38', '2019-08-26 10:22:38'),
(9, 10, 'v3ybm854', 'https://wowzaprod210-i.akamaihd.net/hls/live/851734/178b6464/playlist.m3u8', '05dZkg', 0, 0, NULL, NULL, '2019-08-26 10:22:51', '2019-08-26 10:23:45');

-- --------------------------------------------------------

--
-- Table structure for table `djs`
--

CREATE TABLE `djs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registeration_type` int(11) NOT NULL DEFAULT '1',
  `client_id` longtext COLLATE utf8mb4_unicode_ci,
  `oauth_key` longtext COLLATE utf8mb4_unicode_ci,
  `assigned_clubs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locatione` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `djs`
--

INSERT INTO `djs` (`id`, `name`, `user_name`, `email`, `password`, `birth_date`, `gender`, `registeration_type`, `client_id`, `oauth_key`, `assigned_clubs`, `profile_image`, `locatione`, `flag`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'DJ 1', 'dj1', 'dj1@iapptechnologies.com', 'welcome', '2019-08-05', 'male', 1, NULL, NULL, '1', 'dj-profile-photo-1566543115.jpg', NULL, 0, '2019-08-23 12:21:55', '2019-08-23 12:21:55', NULL),
(2, 'DJ 2', 'dj2', 'dj2@iapptechnologies.com', 'welcome', '2019-01-01', 'male', 1, NULL, NULL, '2', 'dj-profile-photo-1566543179.jpeg', NULL, 0, '2019-08-23 12:22:36', '2019-08-23 12:22:59', NULL),
(3, 'shakshi', 'shakshi', 'iapptech12@gmail.com', '222222', '2019-08-22', 'female', 3, '105285860800958159749', 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImRmOGQ5ZWU0MDNiY2M3MTg1YWQ1MTA0MTE5NGJkMzQzMzc0MmQ5YWEiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIzODczOTAwNTg0Ni0ydnNpYjlqZ3RhNTF2bDViYzZ2azh2NXY3b2NldTRvay5hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsImF1ZCI6IjM4NzM5MDA1ODQ2LTJ2c2liOWpndGE1MXZsNWJjNnZrOHY1djdvY2V1NG9rLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwic3ViIjoiMTA1Mjg1ODYwODAwOTU4MTU5NzQ5IiwiZW1haWwiOiJpYXBwdGVjaDc2ODBAZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImF0X2hhc2giOiJBTnFtVDg4eGx2NlJObE5LRWpEMUJ3IiwibmFtZSI6ImlhcHAgdGVjaCIsInBpY3R1cmUiOiJodHRwczovL2xoNC5nb29nbGV1c2VyY29udGVudC5jb20vLWRoVzI2YThfUkpFL0FBQUFBQUFBQUFJL0FBQUFBQUFBQUFBL0FDSGkzcmZnOFEwVS1QRzl1aUM5MmFhc2d5MklrZ1Uxb2cvczk2LWMvcGhvdG8uanBnIiwiZ2l2ZW5fbmFtZSI6ImlhcHAiLCJmYW1pbHlfbmFtZSI6InRlY2giLCJsb2NhbGUiOiJlbiIsImlhdCI6MTU2NjU0MzczNCwiZXhwIjoxNTY2NTQ3MzM0fQ.Lwh-Og_ovhVEO_BOy71d6JU-lZk03FjuFpphueHr8bnYrLuUmTxcUNuWbWKA8VzN_vIJf_5GQ0T8P-oEq7qPGJO5JOf41SVjjckqptwAd-jRVUNTcsk7gldHnQGqRFS6Xa6SIJJhTszp7cTtc7y0SJ5dwuUNULU2ySbY7V7zQuWPP_Qx-tsXiikfxVbMIJ1w55an7snyctJM0LghP4jZeLRYnMEPT9dCpYSRLjCKvvlscJtE6rk4GpWlWgomzXy3y-zqDWrj_X87Bl9pi72r0G46EiH-DQvrem7fwRdq5rOiEpVFSy5R8C4MUW4rTr3UcRkcjlIrOJQQgvJLSaw6yQ', '1,2', 'dj-profile-photo-1566545421.png', NULL, 0, '2019-08-23 12:39:28', '2019-08-23 18:44:08', NULL),
(4, 'Shakshi', 'Shakshi12', 'shakshi1@gmail.com', '111111', '1985-07-23', 'male', 4, '1164805955634098176', '1164805955634098176-BUgozYw2lbVSjVAEVaUngGTyRTCNtG', '1', 'dj-profile-photo-1566551240.png', NULL, 0, '2019-08-23 13:25:36', '2019-08-23 15:29:50', NULL),
(5, 'Shakshi iApp', 'shakshiiapp', 'shakshi.g@iapptechnologies.com', 'welcome2iapp', '2019-08-23', 'male', 0, NULL, NULL, '1,2,3,4,5,6,7,8,9,10', 'dj-profile-photo-1566554126.png', NULL, 0, '2019-08-23 15:25:00', '2019-08-23 16:03:52', NULL),
(6, 'Hshshs Dbdj', 'Vdbdhhdhd', 'vsshhdsb@gmail.com', 'vdvdvddvdvdvdvdvdbdfhhddhfhfh', '2019-08-23', 'male', 0, NULL, NULL, NULL, '', NULL, 0, '2019-08-23 17:49:40', '2019-08-23 17:49:40', NULL),
(7, 'Iapp Dev', 'Iapp', 'developer.iapptechnologies@gmail.com', '123456', '2008-09-24', 'male', 2, '458442027663759', '2373697914-9EkFGfRuzwjOVKN116XGsSHaRQULFGauqp1npmf', '1,2,4,5,7', 'dj-profile-photo-1566794672.png', NULL, 1, '2019-08-23 18:14:50', '2019-08-26 16:24:49', NULL),
(8, 'Test Test', 'Test', 'test@yopmail.com', '123456', '2007-08-26', 'male', 0, NULL, NULL, NULL, 'dj-profile-photo-1566803693.png', 'Mohali', 0, '2019-08-26 10:34:02', '2019-08-27 11:35:06', NULL),
(9, 'Test User', 'Testuser', 'testuser@yopmail.com', '123456', '2008-08-26', 'male', 0, NULL, NULL, NULL, '', 'Mohali', 0, '2019-08-26 10:42:50', '2019-08-26 10:42:50', NULL),
(10, 'Test User', 'Testusers', 'test1@yopmail.com', '123456', '2008-08-26', 'male', 0, NULL, NULL, NULL, '', 'Mohali', 0, '2019-08-26 11:14:55', '2019-08-26 11:14:55', NULL),
(11, 'Hdhd Eggs', 'Eggs', 's@sv.con', 'yegshbsndhdxb', '2019-08-27', 'male', 0, NULL, NULL, NULL, '', NULL, 0, '2019-08-27 11:28:44', '2019-08-27 11:28:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_addresses`
--

CREATE TABLE `email_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email_type` int(11) NOT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_addresses`
--

INSERT INTO `email_addresses` (`id`, `email_type`, `email_address`, `template`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 22, 'newdjemail@gmail.com', '<p><span style=\"font-weight: 700; color: rgb(51, 51, 51); background-color: rgb(231, 214, 222);\"><u style=\"\">Hi {name} , Its an email template to be sent on forgot password.</u></span><br></p>', '2019-06-27 23:27:52', '2019-07-02 05:45:09', NULL),
(2, 27, 'newusermail@gmail.com', '<b><u style=\"background-color: rgb(255, 231, 156);\">Hi name , Its an email template to be sent on new d registration.<br></u></b><br>', '2019-06-27 04:11:26', '2019-06-27 06:39:36', NULL),
(3, 21, 'forgotpasswordemail@gmail.com', '<b>Hi user! Its template for forgot password!</b>', '2019-06-27 04:29:43', '2019-08-23 14:50:56', NULL),
(4, 23, 'newclub@gmail.com', '<p><b style=\"background-color: rgb(247, 247, 247);\">Hello name<font face=\"Source Code Pro\" style=\"\"><span style=\"font-size: 15.0667px;\">&nbsp;, How are you doing?</span></font></b></p>', '2019-06-27 05:13:27', '2019-08-23 14:51:15', NULL),
(5, 28, 'newtesting@gmail.com', 'NEW TEST {name}', '2019-06-28 00:35:36', '2019-06-28 00:35:36', NULL),
(6, 29, 'abc@gmail.com', '<p><b>weddre</b></p>', '2019-06-28 03:15:11', '2019-08-23 14:51:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_configuration`
--

CREATE TABLE `email_configuration` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mail_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email_type` int(11) DEFAULT NULL,
  `is_sent` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_configuration`
--

INSERT INTO `email_configuration` (`id`, `mail_to`, `mail_from`, `mail_subject`, `mail_content`, `created_at`, `updated_at`, `email_type`, `is_sent`) VALUES
(1, 'shakshi.g@iapptechnologies.com', 'newdjemail@gmail.com', 'Welcome to CatchApp!', 'Hi Shakshi iApp (DJ) ! Welcome to CatchApp.', '2019-08-23 15:25:00', '2019-08-23 15:25:05', 22, 1),
(2, 'club3@iapptechnologies.com', 'newclub@gmail.com', 'Welcome to CatchApp!', 'Hi Club 3(Club) !. Welcome to CatchApp.', '2019-08-23 15:39:59', '2019-08-23 15:40:06', 23, 1),
(3, 'club4@iapptechnologies.com', 'newclub@gmail.com', 'Welcome to CatchApp!', 'Hi Club 4(Club) !. Welcome to CatchApp.', '2019-08-23 15:40:46', '2019-08-23 15:40:51', 23, 1),
(4, 'club5@iapptechnologies.com', 'newclub@gmail.com', 'Welcome to CatchApp!', 'Hi Club 5(Club) !. Welcome to CatchApp.', '2019-08-23 15:43:39', '2019-08-23 15:43:44', 23, 1),
(5, 'club6@iapptechnologies.com', 'newclub@gmail.com', 'Welcome to CatchApp!', 'Hi Club 6(Club) !. Welcome to CatchApp.', '2019-08-23 15:45:05', '2019-08-23 15:45:11', 23, 1),
(6, 'club7@iapptechnologies.com', 'newclub@gmail.com', 'Welcome to CatchApp!', 'Hi Club 7(Club) !. Welcome to CatchApp.', '2019-08-23 15:45:51', '2019-08-23 15:45:55', 23, 1),
(7, 'club8@iapptechnologies.com', 'newclub@gmail.com', 'Welcome to CatchApp!', 'Hi Club 8(Club) !. Welcome to CatchApp.', '2019-08-23 15:46:42', '2019-08-23 15:46:47', 23, 1),
(8, 'club9@iapptechnologies.com', 'newclub@gmail.com', 'Welcome to CatchApp!', 'Hi Club 9(Club) !. Welcome to CatchApp.', '2019-08-23 15:47:34', '2019-08-23 15:47:40', 23, 1),
(9, 'club10@iapptechnologies.com', 'newclub@gmail.com', 'Welcome to CatchApp!', 'Hi Club 10(Club) !. Welcome to CatchApp.', '2019-08-23 15:48:37', '2019-08-23 15:48:42', 23, 1),
(10, 'vishal.p@iapptechnologies.com', 'developer.iapptechnologies@gmail.com', '[CatchApp] Password reset link', 'Hi Vishal Puri! Please visit this link to reset your CatchApp login password. <br><a href=https://catchapp.iapplabz.co.in/user/reset-password/vishal.p@iapptechnologies.com>Click here!</a>', '2019-08-23 16:05:56', '2019-08-23 16:06:01', 0, 1),
(11, 'vsshhdsb@gmail.com', 'newdjemail@gmail.com', 'Welcome to CatchApp!', 'Hi Hshshs Dbdj (DJ) ! Welcome to CatchApp.', '2019-08-23 17:49:40', '2019-08-23 17:49:45', 22, 1),
(12, 'developer.iapptechnologies@gmail.com', 'newdjemail@gmail.com', 'Welcome to CatchApp!', 'Hi Iapp Dev (DJ) ! Welcome to CatchApp.', '2019-08-23 18:14:50', '2019-08-23 18:14:55', 22, 1),
(13, 'test@yopmail.com', 'newdjemail@gmail.com', 'Welcome to CatchApp!', 'Hi Test Test (DJ) ! Welcome to CatchApp.', '2019-08-26 10:34:02', '2019-08-26 10:34:10', 22, 1),
(14, 'testuser@yopmail.com', 'newdjemail@gmail.com', 'Welcome to CatchApp!', 'Hi Test User (DJ) ! Welcome to CatchApp.', '2019-08-26 10:42:50', '2019-08-26 10:42:55', 22, 1),
(15, 'test1@yopmail.com', 'newdjemail@gmail.com', 'Welcome to CatchApp!', 'Hi Test User (DJ) ! Welcome to CatchApp.', '2019-08-26 11:14:55', '2019-08-26 11:15:00', 22, 1),
(16, 's@sv.con', 'newdjemail@gmail.com', 'Welcome to CatchApp!', 'Hi Hdhd Eggs (DJ) ! Welcome to CatchApp.', '2019-08-27 11:28:44', '2019-08-27 11:28:50', 22, 1);

-- --------------------------------------------------------

--
-- Table structure for table `email_types`
--

CREATE TABLE `email_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_types`
--

INSERT INTO `email_types` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'test', '2019-06-26 23:13:56', '2019-06-27 01:51:43', '2019-06-27 01:51:43'),
(2, 'test again', '2019-06-26 23:14:09', '2019-06-27 01:46:56', '2019-06-27 01:46:56'),
(3, 'bg', '2019-06-27 00:18:38', '2019-06-27 01:51:34', '2019-06-27 01:51:34'),
(4, 'bg', '2019-06-27 00:18:38', '2019-06-27 01:49:02', '2019-06-27 01:49:02'),
(5, 'bg', '2019-06-27 00:18:38', '2019-06-27 01:45:21', '2019-06-27 01:45:21'),
(6, 'bg', '2019-06-27 00:18:46', '2019-06-27 01:49:52', '2019-06-27 01:49:52'),
(7, 'sds', '2019-06-27 00:25:04', '2019-06-27 01:46:20', '2019-06-27 01:46:20'),
(8, 'Test', '2019-06-27 00:29:10', '2019-06-27 01:46:12', '2019-06-27 01:46:12'),
(9, 'frg', '2019-06-27 00:29:32', '2019-06-27 01:46:17', '2019-06-27 01:46:17'),
(10, 'frg', '2019-06-27 00:29:33', '2019-06-27 01:45:31', '2019-06-27 01:45:31'),
(11, 'frg', '2019-06-27 00:29:33', '2019-06-27 01:45:27', '2019-06-27 01:45:27'),
(12, 'frg', '2019-06-27 00:29:33', '2019-06-27 01:46:09', '2019-06-27 01:46:09'),
(13, 'frg', '2019-06-27 00:29:33', '2019-06-27 01:45:37', '2019-06-27 01:45:37'),
(14, 'frg', '2019-06-27 00:29:33', '2019-06-27 01:45:42', '2019-06-27 01:45:42'),
(15, 'frg', '2019-06-27 00:29:33', '2019-06-27 01:45:46', '2019-06-27 01:45:46'),
(16, 'frg', '2019-06-27 00:29:33', '2019-06-27 01:45:51', '2019-06-27 01:45:51'),
(17, 'frg', '2019-06-27 00:29:34', '2019-06-27 01:45:55', '2019-06-27 01:45:55'),
(18, 'Super Admin', '2019-06-27 02:07:11', '2019-06-27 02:12:09', '2019-06-27 02:12:09'),
(19, 'Super Admin', '2019-06-27 02:09:49', '2019-06-27 02:12:05', '2019-06-27 02:12:05'),
(20, 'New User Registeration', '2019-06-27 02:11:25', '2019-06-27 02:16:27', '2019-06-27 02:16:27'),
(21, 'Forgot Password', '2019-06-27 02:12:47', '2019-06-28 00:34:06', NULL),
(22, 'New DJ Registeration', '2019-06-27 02:14:40', '2019-06-27 02:14:40', NULL),
(23, 'New Club Registeration', '2019-06-27 02:15:35', '2019-06-27 02:15:35', NULL),
(24, 'Test', '2019-06-27 02:15:52', '2019-06-27 02:16:22', '2019-06-27 02:16:22'),
(25, 'Testing Purpose', '2019-06-27 02:16:01', '2019-06-27 02:16:18', '2019-06-27 02:16:18'),
(26, 'Test', '2019-06-27 02:16:11', '2019-06-27 02:16:15', '2019-06-27 02:16:15'),
(27, 'New User Registeration', '2019-06-27 02:16:35', '2019-06-27 02:16:35', NULL),
(28, 'New Testing', '2019-06-28 00:35:36', '2019-08-23 14:51:23', '2019-08-23 14:51:23'),
(29, 'Super Admin', '2019-06-28 03:15:11', '2019-06-28 03:15:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `name`, `email`, `message`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Vishal', 'vishal.p@iapptechnologies.com', 'Test name', '2019-08-23 13:09:04', '2019-08-23 13:09:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `insights`
--

CREATE TABLE `insights` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hype_count` int(11) NOT NULL DEFAULT '0',
  `normal_count` int(11) NOT NULL DEFAULT '0',
  `slow_count` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `insights`
--

INSERT INTO `insights` (`id`, `hype_count`, `normal_count`, `slow_count`, `created_at`, `updated_at`) VALUES
(1, 10, 7, 5, '2019-06-07 07:27:30', '2019-06-26 05:06:50');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_06_04_123139_create_users_table', 2),
(4, '2019_06_04_123339_create_users_table', 3),
(5, '2019_06_05_075740_create_admin_users_table', 4),
(8, '2019_06_06_052356_create_clubs_table', 5),
(12, '2019_06_06_054458_create_djs_table', 6),
(13, '2019_06_06_100400_update_clubs_table', 6),
(14, '2019_06_07_051908_update_users_table', 7),
(15, '2019_06_07_064934_update_admin_users_table', 8),
(18, '2019_06_07_104941_create_insights_table', 9),
(19, '2019_06_10_062358_create_email_configuration_table', 10),
(20, '2019_06_10_120703_update_email_configuration_table', 11),
(21, '2019_06_11_113728_update_email_configuration_table', 12),
(22, '2019_06_12_052327_create_user_stories_table', 13),
(23, '2016_06_01_000001_create_oauth_auth_codes_table', 14),
(24, '2016_06_01_000002_create_oauth_access_tokens_table', 14),
(25, '2016_06_01_000003_create_oauth_refresh_tokens_table', 14),
(26, '2016_06_01_000004_create_oauth_clients_table', 14),
(27, '2016_06_01_000005_create_oauth_personal_access_clients_table', 14),
(28, '2019_06_17_051352_update_user_stories_table', 15),
(29, '2019_06_20_122128_update_djs_table', 16),
(30, '2019_06_26_091834_create_email_addresses_table', 17),
(31, '2019_06_26_092941_create_email_types_table', 17),
(32, '2019_06_26_102000_create_test_table', 17),
(33, '2019_06_27_112444_update_email_configuration_table', 18),
(34, '2019_07_01_065747_create_static_pages_table', 19),
(35, '2019_07_01_102759_update_static_pages_table', 20),
(36, '2019_07_01_103435_update_static_pages_table', 21),
(37, '2019_07_01_104644_update_static_pages_table', 22),
(38, '2019_07_02_120854_update_clubs_table', 23),
(39, '2019_07_02_121323_update_clubs_table', 24),
(40, '2019_07_05_052849_update_clubs_table', 25),
(41, '2019_07_08_093323_update_users_table', 26),
(42, '2019_07_11_072206_create_seen_stories_table', 27),
(43, '2019_07_12_044010_update_user_stories_table', 28);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pivot_club_dj`
--

CREATE TABLE `pivot_club_dj` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `club_id` int(11) NOT NULL,
  `dj_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pivot_club_dj`
--

INSERT INTO `pivot_club_dj` (`id`, `club_id`, `dj_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2019-08-23 12:21:55', '2019-08-23 12:21:55'),
(3, 2, 2, '2019-08-23 12:22:59', '2019-08-23 12:22:59'),
(4, 1, 3, '2019-08-23 12:41:47', '2019-08-23 12:41:47'),
(5, 2, 3, '2019-08-23 12:41:47', '2019-08-23 12:41:47'),
(7, 1, 4, '2019-08-23 15:29:50', '2019-08-23 15:29:50'),
(12, 5, 3, '2019-08-23 15:43:39', '2019-08-23 15:43:39'),
(13, 5, 4, '2019-08-23 15:43:39', '2019-08-23 15:43:39'),
(15, 6, 3, '2019-08-23 15:45:05', '2019-08-23 15:45:05'),
(16, 6, 4, '2019-08-23 15:45:05', '2019-08-23 15:45:05'),
(18, 7, 3, '2019-08-23 15:45:51', '2019-08-23 15:45:51'),
(19, 7, 4, '2019-08-23 15:45:51', '2019-08-23 15:45:51'),
(21, 8, 3, '2019-08-23 15:46:42', '2019-08-23 15:46:42'),
(22, 8, 4, '2019-08-23 15:46:42', '2019-08-23 15:46:42'),
(24, 9, 3, '2019-08-23 15:47:34', '2019-08-23 15:47:34'),
(25, 9, 4, '2019-08-23 15:47:34', '2019-08-23 15:47:34'),
(27, 10, 3, '2019-08-23 15:48:37', '2019-08-23 15:48:37'),
(28, 10, 4, '2019-08-23 15:48:37', '2019-08-23 15:48:37'),
(30, 1, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(31, 2, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(32, 3, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(33, 4, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(34, 5, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(35, 6, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(36, 7, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(37, 8, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(38, 9, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(39, 10, 5, '2019-08-23 15:54:27', '2019-08-23 15:54:27'),
(40, 1, 7, '2019-08-23 18:48:40', '2019-08-23 18:48:40'),
(41, 2, 7, '2019-08-23 18:48:40', '2019-08-23 18:48:40'),
(42, 4, 7, '2019-08-23 18:48:40', '2019-08-23 18:48:40'),
(43, 5, 7, '2019-08-23 18:48:40', '2019-08-23 18:48:40'),
(44, 7, 7, '2019-08-23 18:48:40', '2019-08-23 18:48:40');

-- --------------------------------------------------------

--
-- Table structure for table `seen_stories`
--

CREATE TABLE `seen_stories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `static_pages`
--

CREATE TABLE `static_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `static_pages`
--

INSERT INTO `static_pages` (`id`, `type`, `title`, `page_content`, `created_at`, `updated_at`) VALUES
(1, 1, 'Privacy Policy', '<blockquote><b>Hi, This is catchApp\'s Privacy policies page. </b></blockquote>', '2019-07-01 02:03:22', '2019-08-23 14:53:17'),
(2, 2, 'Terms And Conditions', '<blockquote><b>Hi, This is terms &amp; Conditions page! </b></blockquote>', '2019-07-01 02:10:28', '2019-08-23 14:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` char(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registeration_type` int(11) DEFAULT '1',
  `client_id` longtext COLLATE utf8mb4_unicode_ci,
  `oauth_key` longtext COLLATE utf8mb4_unicode_ci,
  `device_token` longtext COLLATE utf8mb4_unicode_ci,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `get_notification` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `is_active`, `first_name`, `last_name`, `email`, `user_name`, `password`, `birth_date`, `gender`, `registeration_type`, `client_id`, `oauth_key`, `device_token`, `profile_image`, `location`, `email_verified_at`, `remember_token`, `get_notification`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 0, 'Vishal', 'Puri', 'vishal.p@iapptechnologies.com', 'vishaliapp1', 'eyJpdiI6IjNFcTh3Nkl6ejNuQVVmQ1J5SERoSnc9PSIsInZhbHVlIjoiZXhoUlwvVk5iRmVOcmFFU2QxWUdGN2Y5bDNMMDAwNmoyRUlPVmhUbXBxaTg9IiwibWFjIjoiZDQ1YzdkMGQ3NWU1ZTQ0NGFhMzRlZDZjNmM4OGQ0MzRkNDJhNzgxNjU4ZDM3NzU1NmVlYTc2OTk2NjhhNDMyYyJ9', '1990-04-02', 'male', 1, NULL, NULL, '1203d5c9c9c0593856b9093cbf0032566b8425eb06a381dd0e48d3a29686bfd1', 'user-profile-photo-1566545817.png', 'Ambala', NULL, NULL, 0, '2019-08-23 12:48:49', '2019-08-23 16:06:47', NULL),
(2, 0, 'Tester', 'IApp', 'testing.iapp001@gmail.com', 'testingiapp', 'eyJpdiI6InBldzlZXC9UZHQ2TjczcitzUnRacHVnPT0iLCJ2YWx1ZSI6IlhUTGVyZklPcVwvOUdwRUtMK05lSFFiWUt2RlhJTm5EdUxKZWZxUUJCdElvPSIsIm1hYyI6ImNmMDAyNDYxODM1YmMzOWVhNjdlN2E0ZjQ3MTI0NmI1OTc3ZTY2Y2Q0NjkyZDFiYjBhZGJmM2FhMWQzNzkwMTYifQ==', '2000-08-23', 'male', 2, '882553762132232', 'EAAMXz617jHoBAOld6EaqO0Lk7tvan3UXSYM47dG81Q5G6IbBLFUu1btrldPj5STIoFiZAFMA3IobhzXA7RsZAFR2jvchMwLcNCErgvhvN75SZA49si44GGqNqZCWVR5ZCmkKZCH2ol9CN7E3ETrJLEZAjYQq04xqXq8BP5zaR5VcoWHfifa9UE8CdZCvcMdANoawZCfr2LT69Ocs6ZCJEYewRsZAvLoZBBhXDZCGIhGdZARZC8SVgZDZD', 'b1faab82d435ab8e6784c380422412b4c0abb35b4343b91cd7c9c02ecd210961', 'user-profile-photo-1566546768.png', 'Mohali', NULL, NULL, 1, '2019-08-23 13:22:48', '2019-08-23 13:23:54', NULL),
(3, 0, 'abc', 'abc', 'abc@abc.com', 'abcd', 'eyJpdiI6ImN0T1JlQkQ1YzNpaEVxUWRYa2ZDOHc9PSIsInZhbHVlIjoiRGZFUmx0ZlwvK201ZWgzMFZTRXZNajUyRHlYXC9jUzZWMnBldEJaSmtyTTBvPSIsIm1hYyI6Ijc0MmRmM2M0N2IyYWFhNjI3ZjE1NDk3YmQ0ZGQzYWQyYjZjOTYzNTZhOTllNWE2M2YyNDVmNGJiNDhmYmVjZDEifQ==', '2006-08-23', 'male', 1, NULL, NULL, 'b1faab82d435ab8e6784c380422412b4c0abb35b4343b91cd7c9c02ecd210961', '', 'Mohali', NULL, NULL, 1, '2019-08-23 13:27:43', '2019-08-23 13:27:43', NULL),
(4, 1, 'Test', 'Test', 'developer.iapptechnologies@gmail.com', 'Test', 'eyJpdiI6InBZVXUydDJXemcrcDVTK1ZIZUNacmc9PSIsInZhbHVlIjoid1BtRTBZdG0rQytEN1VPNWpKODJsdz09IiwibWFjIjoiYTcwOTYyMTdmMTFlNzdjODVhMWY4YmI1NTRiMTYwZThlNzFiNDEwODk0NTQwMzAzMWZmYjFmZTAyMDU3YmU2MCJ9', '1998-08-26', 'male', 3, '123456', 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImRmOGQ5ZWU0MDNiY2M3MTg1YWQ1MTA0MTE5NGJkMzQzMzc0MmQ5YWEiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIzODczOTAwNTg0Ni0ydnNpYjlqZ3RhNTF2bDViYzZ2azh2NXY3b2NldTRvay5hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsImF1ZCI6IjM4NzM5MDA1ODQ2LTJ2c2liOWpndGE1MXZsNWJjNnZrOHY1djdvY2V1NG9rLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwic3ViIjoiMTEyODI3ODAwMTUwNzM4ODUyOTY5IiwiZW1haWwiOiJkZXZlbG9wZXIuaWFwcHRlY2hub2xvZ2llc0BnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwiYXRfaGFzaCI6InQyMFNaRTdTYUlIcUtmSVpJVXU0OXciLCJuYW1lIjoiSGFyc2hkZWVwIFNpbmdoIiwicGljdHVyZSI6Imh0dHBzOi8vbGgzLmdvb2dsZXVzZXJjb250ZW50LmNvbS9hLS9BQXVFN21DNjRKdjlxNTk0VHV5OXB2bDFhb29xTG9QYnZGVllPUl9IV2hPYmtRPXM5Ni1jIiwiZ2l2ZW5fbmFtZSI6IkhhcnNoZGVlcCIsImZhbWlseV9uYW1lIjoiU2luZ2giLCJsb2NhbGUiOiJlbiIsImlhdCI6MTU2Njc5Mzg3MSwiZXhwIjoxNTY2Nzk3NDcxfQ.lEndLUmfmhbwMtwd01l0xyJMHMBcNiiYPBAfA23kPA_wJnkdCMuUwW3sxkfd8BAEF-qBiPDRNQ1rMRzTNscCkzuzPQx97YXg3brqCmLQZkdNGz-VZGlkgoBRKeatg9N_ySjUYUjsc-n5LHrZrkcLnnB-6O-61Ss-1cXC2gL_Prgxl4RNCYadUqXZ_6uLS32e84TydMWUDVoevLQLc3Gqaf_liMpK3njDsqHGYBoY1tSAX-eTki5FQ3JGwauQL4-xBXxFQJkbObOpoZx57KT5d16JC1m_vZX4BLHAVzAv9M4e0jpMXdJEMtB4Ed-xnYpuGUzI0vd2hx97k2NUjX0iFQ', '1203d5c9c9c0593856b9093cbf0032566b8425eb06a381dd0e48d3a29686bfd1', 'user-profile-photo-1566793932.png', 'Ambala', NULL, NULL, 1, '2019-08-26 10:02:12', '2019-08-27 15:05:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_club_logs`
--

CREATE TABLE `user_club_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_club_logs`
--

INSERT INTO `user_club_logs` (`id`, `user_id`, `club_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2019-08-23 15:35:41', '2019-08-23 15:35:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_device_tokens`
--

CREATE TABLE `user_device_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_device_tokens`
--

INSERT INTO `user_device_tokens` (`id`, `user_id`, `device_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'b1faab82d435ab8e6784c380422412b4c0abb35b4343b91cd7c9c02ecd210961', '2019-08-23 12:48:49', '2019-08-23 12:48:49'),
(2, 2, 'b1faab82d435ab8e6784c380422412b4c0abb35b4343b91cd7c9c02ecd210961', '2019-08-23 13:22:48', '2019-08-23 13:22:48'),
(3, 3, 'b1faab82d435ab8e6784c380422412b4c0abb35b4343b91cd7c9c02ecd210961', '2019-08-23 13:27:43', '2019-08-23 13:27:43'),
(4, 1, '1203d5c9c9c0593856b9093cbf0032566b8425eb06a381dd0e48d3a29686bfd1', '2019-08-23 16:06:38', '2019-08-23 16:06:38'),
(5, 4, '91a91d3bd85c598da2fda6596edb0351c9d79ac7e992548c7764b9e87455c5b4', '2019-08-26 10:02:12', '2019-08-26 10:02:12'),
(6, 4, '7ddb10c0ee3d9643caed0ee091bcca089e9e0d89b1ca46e604ffda48f6a697da', '2019-08-26 15:56:35', '2019-08-26 15:56:35'),
(7, 4, '1203d5c9c9c0593856b9093cbf0032566b8425eb06a381dd0e48d3a29686bfd1', '2019-08-27 15:05:32', '2019-08-27 15:05:32');

-- --------------------------------------------------------

--
-- Table structure for table `user_stories`
--

CREATE TABLE `user_stories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `story_type` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `story` longtext COLLATE utf8mb4_unicode_ci,
  `text_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `font` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_stories`
--

INSERT INTO `user_stories` (`id`, `user_id`, `story_type`, `status`, `is_active`, `story`, `text_color`, `font`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 1, 1, 'user-photo-story-1566543446.jpg', NULL, NULL, '2019-08-23 12:27:26', '2019-08-23 14:50:28', NULL),
(2, 1, 3, 1, 1, 'user-video-story-1566546637.mov', NULL, NULL, '2019-08-23 13:20:41', '2019-08-23 14:50:28', NULL),
(3, 1, 1, 1, 1, 'Hello Team', '#FFFFFF', 'CircularStd-Book:35.0', '2019-08-23 13:21:21', '2019-08-23 14:50:28', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_email_unique` (`email`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `club_stream`
--
ALTER TABLE `club_stream`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `djs`
--
ALTER TABLE `djs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `djs_email_unique` (`email`);

--
-- Indexes for table `email_addresses`
--
ALTER TABLE `email_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_configuration`
--
ALTER TABLE `email_configuration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_types`
--
ALTER TABLE `email_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `insights`
--
ALTER TABLE `insights`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `pivot_club_dj`
--
ALTER TABLE `pivot_club_dj`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seen_stories`
--
ALTER TABLE `seen_stories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `static_pages`
--
ALTER TABLE `static_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- Indexes for table `user_club_logs`
--
ALTER TABLE `user_club_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_device_tokens`
--
ALTER TABLE `user_device_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_stories`
--
ALTER TABLE `user_stories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `club_stream`
--
ALTER TABLE `club_stream`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `djs`
--
ALTER TABLE `djs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `email_addresses`
--
ALTER TABLE `email_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `email_configuration`
--
ALTER TABLE `email_configuration`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `email_types`
--
ALTER TABLE `email_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `insights`
--
ALTER TABLE `insights`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `pivot_club_dj`
--
ALTER TABLE `pivot_club_dj`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `seen_stories`
--
ALTER TABLE `seen_stories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `static_pages`
--
ALTER TABLE `static_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `user_club_logs`
--
ALTER TABLE `user_club_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_device_tokens`
--
ALTER TABLE `user_device_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `user_stories`
--
ALTER TABLE `user_stories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
