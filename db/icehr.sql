-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 21, 2019 at 05:51 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `icehr`
--

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `course_master_id` int(11) NOT NULL,
  `batch_number` int(11) NOT NULL,
  `details` text NOT NULL,
  `application_fee` int(11) NOT NULL,
  `place` varchar(200) NOT NULL,
  `begin_date` date NOT NULL,
  `end_date` date NOT NULL,
  `responsible_user_id` int(11) NOT NULL,
  `status` enum('normal','canceled','deleted','') NOT NULL DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `course_master_id`, `batch_number`, `details`, `application_fee`, `place`, `begin_date`, `end_date`, `responsible_user_id`, `status`, `created_at`) VALUES
(1, 1, 2, '', 7200, 'โรงแรมนารายณ์ ถนนสีลม เขตบางรัก กรุงเทพมหานคร', '2019-06-19', '2019-06-20', 0, 'normal', '2019-05-14 09:21:45'),
(2, 2, 1, '', 4600, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-04-29', '2019-05-01', 0, 'normal', '2019-05-14 07:46:33'),
(3, 3, 1, '', 4600, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-06-27', '2019-06-28', 0, 'normal', '2019-05-14 07:46:37'),
(4, 1, 1, '<p>ddd</p>\n', 2900, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-05-08', '2019-05-09', 1, 'normal', '2019-05-15 10:47:13'),
(5, 2, 40, '<h1>ทดสอบ</h1>\r\n<p>\r\nThis is my <strong>textarea</strong> to be replaced with CKEditor.\r\n</>\r\n                                ', 4000, 'Ko Kreate Space', '2019-05-01', '2019-05-03', 1, 'normal', '2019-05-15 07:11:44'),
(6, 1, 111, '111', 111, '111', '2019-06-01', '2019-07-01', 2, 'normal', '2019-05-15 09:41:09'),
(7, 2, 555, '<p>444</p>\n', 444, '444', '2019-05-15', '2019-05-16', 1, 'normal', '2019-05-15 10:44:13'),
(8, 3, 999, '<h3><strong>หลักการและเหตุผล</strong></h3>\n\n<p>ระบบเรตติ้ง หรือ Rating System for Audit เป็นเครื่องมือที่ สำคัญอย่างหนึ่งของงานการตรวจสอบภายในสมัยใหม่ เพราะสามารถ สะท้อนระดับความรุนแรงของผลที่พบจากการตรวจสอบได้อย่างชัดเจน มีเกณฑ์เงื่อนไขที่ชัดเจน ซึ่งจะช่วยให้เกิดประสิทธิภาพของงานตรวจสอบ ทั้งในมุมมอง ของผู้ตรวจสอบที่มีประสบการณ์ทักษะที่แตกต่างกันและทั้งหน่วยรับ ตรวจในการรับรู้ล่วงหน้าว่าจะปรับปรุงพัฒนาผลดำเนินงานของตนอย่างไรให้ระดับ Rating ดีขึ้นกว่าการตรวจสอบครั้งก่อนหน้า</p>\n\n<h3><strong>วัตถุประสงค์</strong></h3>\n\n<ol>\n	<li>เพื่อให้ผู้เข้าอบรมเข้าใจมาตรฐานการพัฒนา Audit Risk-Based Rating System เพื่อนำไปใช้ในการพัฒนา ใช้งานตรวจสอบภายใน</li>\n	<li>เพื่อให้ผู้เข้าอบรมได้ทดสอบกรอกข้อมูลความเห็นตาม checklist เพื่อนำไปใช้เป็นต้นแบบการพัฒนา Audit Risk-Based Rating System ในอนาคต</li>\n</ol>\n', 99999, 'Nine nine nine', '2019-06-09', '2019-06-11', 1, 'normal', '2019-05-15 10:02:06'),
(9, 1, 5678, '<ul>\n	<li>1234</li>\n	<li>5678</li>\n	<li>0000</li>\n</ul>\n', 12345678, '12345678', '2019-05-05', '2019-05-08', 2, 'normal', '2019-05-15 10:45:56'),
(10, 9, 2, '<p>จัดทำโครงการ เขียนภาษาอังกฤษ<a href=\"https://ichef.bbci.co.uk/news/976/cpsprodpb/27C9/production/_103158101_tha.jpg\" target=\"_self\"><img alt=\"\" src=\"https://ichef.bbci.co.uk/news/976/cpsprodpb/27C9/production/_103158101_tha.jpg\" style=\"height:620px; width:976px\" /></a></p>\n', 500, 'ธรรมศาสตร์ ท่าพระจันทร์', '2019-05-22', '2019-05-30', 5, 'normal', '2019-05-16 03:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `course_master`
--

CREATE TABLE `course_master` (
  `id` int(11) NOT NULL,
  `title` varchar(500) CHARACTER SET tis620 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_master`
--

INSERT INTO `course_master` (`id`, `title`, `created_at`) VALUES
(1, 'กลยุทธ์การเป็นวิทยากรมืออาชีพ (TRAIN THE TRAINER)', '2019-05-13 10:44:21'),
(2, 'การเขียนอีเมลภาษาอังกฤษสู่ความสำเร็จยุค THAILAND 4.0', '2019-05-14 05:29:06'),
(3, 'การจัดทำแผนการพัฒนารายบุคคลเพื่อเพิ่มประสิทธิภาพในการปฏิบัติงาน', '2019-05-14 07:45:04'),
(9, 'การเขียนภาษาอังกฤษ Thailand 2(Test)', '2019-05-16 03:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `course_registration`
--

CREATE TABLE `course_registration` (
  `id` int(11) NOT NULL,
  `form_number` varchar(20) DEFAULT NULL,
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ID ของสมาชิกที่ login เข้าระบบและมากรอกข้อมูลสมัคร (0 = ไม่ได้ login)',
  `course_id` int(11) NOT NULL,
  `coordinator_title` varchar(50) NOT NULL,
  `coordinator_first_name` varchar(200) NOT NULL,
  `coordinator_last_name` varchar(200) NOT NULL,
  `coordinator_age` int(11) NOT NULL,
  `coordinator_job_position` varchar(200) NOT NULL,
  `coordinator_organization_name` varchar(200) NOT NULL,
  `coordinator_organization_type` int(11) NOT NULL,
  `coordinator_phone` varchar(50) NOT NULL,
  `coordinator_email` varchar(200) NOT NULL,
  `receipt_address` varchar(200) NOT NULL,
  `receipt_sub_district` varchar(50) NOT NULL,
  `receipt_district` varchar(50) NOT NULL,
  `receipt_province` varchar(50) NOT NULL,
  `receipt_postal_code` varchar(10) NOT NULL,
  `receipt_organization_phone` varchar(50) NOT NULL,
  `receipt_tax_id` varchar(50) NOT NULL,
  `status` enum('started','approve_waiting','completed','canceled') NOT NULL DEFAULT 'started' COMMENT 'สถานะการลงทะเบียน',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_registration`
--

INSERT INTO `course_registration` (`id`, `form_number`, `member_id`, `course_id`, `coordinator_title`, `coordinator_first_name`, `coordinator_last_name`, `coordinator_age`, `coordinator_job_position`, `coordinator_organization_name`, `coordinator_organization_type`, `coordinator_phone`, `coordinator_email`, `receipt_address`, `receipt_sub_district`, `receipt_district`, `receipt_province`, `receipt_postal_code`, `receipt_organization_phone`, `receipt_tax_id`, `status`, `created_at`) VALUES
(6, '2019-0006', 22, 1, 'นาง', 'มานี', 'มีนา', 40, 'พนักงานบริษัทเอกชน', '3bugs', 1, '123', 'manee@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', 'approve_waiting', '2019-05-13 08:57:54'),
(7, '2019-0007', 22, 1, 'นาง', 'มานี', 'มีนา', 40, 'พนักงานบริษัทเอกชน', '3bugs', 1, '123', 'manee@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', 'completed', '2019-05-13 08:57:54'),
(8, '2019-0008', 22, 1, 'นาง', 'มานี', 'มีนา', 40, 'พนักงานบริษัทเอกชน', '3bugs', 1, '123', 'manee@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', 'canceled', '2019-05-13 09:27:30'),
(9, '2019-0009', 0, 1, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'completed', '2019-05-14 05:38:09'),
(10, '2019-0010', 0, 10, 'นางสาว', 'เอมี่', 'รีไวล์', 33, 'ผู้กำกับ', 'ตจว', 1, '0988899997', 'Ami@hotmail.com', '44ซ7ถนนพหลโยทิน', 'จตุจักร', 'จตุจักร', 'กรุงเทพ', '10900', '023345555', '123456789', 'approve_waiting', '2019-05-16 04:13:51'),
(11, '2019-0011', 22, 7, 'นาย', 'aaa', 'aaa', 111, 'aaa', 'aaa', 1, '111', 'aaa@aaa.com', '111', 'aaa', 'aaa', 'aaa', '111', '111', '111', 'started', '2019-05-16 06:49:03'),
(12, '2019-0012', 0, 10, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'completed', '2019-05-16 09:32:54'),
(13, '2019-0013', 0, 10, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'started', '2019-05-16 09:33:20'),
(14, '2019-0014', 0, 10, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'approve_waiting', '2019-05-16 09:37:28'),
(15, '2019-0015', 0, 10, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'approve_waiting', '2019-05-16 09:50:14'),
(16, '2019-0016', 99, 6, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', 45, 'System Analyst', '2fellows', 3, '0850581776', 'promlert@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '0123456789', '9876543210', 'started', '2019-05-16 09:55:23'),
(17, '2019-0017', 101, 10, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'started', '2019-05-16 09:58:38'),
(18, '2019-0018', 102, 2, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', 44, 'IT', '2fellows', 3, '012345678', 'promlert123@gmail.com', '123/456', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '012345689', '123', 'started', '2019-05-17 03:17:23');

-- --------------------------------------------------------

--
-- Table structure for table `course_trainee`
--

CREATE TABLE `course_trainee` (
  `id` int(11) NOT NULL,
  `course_registration_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `age` int(11) NOT NULL,
  `job_position` varchar(200) NOT NULL,
  `organization_name` varchar(200) NOT NULL,
  `organization_type` int(11) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_trainee`
--

INSERT INTO `course_trainee` (`id`, `course_registration_id`, `title`, `first_name`, `last_name`, `age`, `job_position`, `organization_name`, `organization_type`, `phone`, `email`, `created_at`) VALUES
(1, 1, '1', 'พร้อมเลิศ', 'หล่อวิจิตร', 44, 'Programmer', '2fellows', 3, '0850581776', 'promlert@gmail.com', '2019-05-13 08:58:27'),
(2, 2, '1', 'a', 'a', 1, 'a', 'a', 1, '1', 'a@a.com', '2019-05-13 08:58:27'),
(3, 2, '2', 'b', 'b', 2, 'b', 'b', 2, '2', 'b@b.com', '2019-05-13 08:58:27'),
(4, 3, '3', 'x', 'x', 55, 'x', 'x', 3, '55', 'x@x.com', '2019-05-13 08:58:27'),
(5, 4, '1', 'a', 'a', 1, 'a', 'a', 1, '1', 'a@a.com', '2019-05-13 08:58:27'),
(6, 6, 'นาง', 'มานี', 'มีนา', 40, 'พนักงานบริษัทเอกชน', '3bugs', 1, '123', 'manee@gmail.com', '2019-05-13 08:58:27'),
(7, 7, 'นาง', 'มานี', 'มีนา', 40, 'พนักงานบริษัทเอกชน', '3bugs', 1, '123', 'manee@gmail.com', '2019-05-13 08:58:27'),
(8, 8, 'นาง', 'มานี', 'มีนา', 40, 'พนักงานบริษัทเอกชน', '3bugs', 1, '123', 'manee@gmail.com', '2019-05-13 09:27:30'),
(9, 9, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '2019-05-14 05:38:09'),
(10, 10, 'นางสาว', 'เอมี่', 'รีไวล์', 33, 'ผู้กำกับ', 'ตจว', 1, '0988899997', 'Ami@hotmail.com', '2019-05-16 04:13:51'),
(11, 10, 'นาย', 'Revi', 'Kracerment', 32, 'หัวหน้าหน่วย', 'ตชด', 3, '023345545', 'Revi@gmail.com', '2019-05-16 04:13:51'),
(12, 11, 'นาย', 'aaa', 'aaa', 111, 'aaa', 'aaa', 1, '111', 'aaa@aaa.com', '2019-05-16 06:49:03'),
(13, 12, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '2019-05-16 09:32:54'),
(14, 12, 'นางสาว', 'kk', 'kk', 33, 'dd', 'dd', 2, '0988899988', 'KK@gmail.com', '2019-05-16 09:32:54'),
(15, 13, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '2019-05-16 09:33:20'),
(16, 13, 'นางสาว', 'kk', 'kk', 33, 'dd', 'dd', 2, '0988899988', 'KK@gmail.com', '2019-05-16 09:33:20'),
(17, 14, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '2019-05-16 09:37:28'),
(18, 14, 'นาย', 'Livi', 'Mac', 45, 'หัวหน้า', 'CoCompany', 3, '0876676666', 'Co@gmail.com', '2019-05-16 09:37:28'),
(19, 15, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '2019-05-16 09:50:14'),
(20, 15, 'นาย', 'INY', 'NYGEL', 45, 'SA', 'SAcompany', 3, '0988876677', 'SA@gmail.com', '2019-05-16 09:50:14'),
(21, 16, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', 45, 'System Analyst', '2fellows', 3, '0850581776', 'promlert@gmail.com', '2019-05-16 09:55:23'),
(22, 16, 'นาง', 'จิตริณีย์', 'หล่อวิจิตร', 47, 'Auditor', 'KU', 5, '0869991827', 'chirinee_l@gmail.com', '2019-05-16 09:55:23'),
(23, 17, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '0978878889', 'Co@hotmail.com', '2019-05-16 09:58:38'),
(24, 17, 'นาง', 'Eden', 'Wongwai', 33, 'SA', 'SA', 3, '0987787765', 'SA@gmail.com', '2019-05-16 09:58:38'),
(25, 18, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', 44, 'IT', '2fellows', 3, '012345678', 'promlert123@gmail.com', '2019-05-17 03:17:23');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `age` int(11) NOT NULL,
  `job_position` varchar(200) DEFAULT NULL,
  `organization_name` varchar(200) DEFAULT NULL,
  `organization_type` int(11) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `sub_district` varchar(50) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `organization_phone` varchar(50) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(50) NOT NULL,
  `login_token` varchar(512) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `title`, `first_name`, `last_name`, `age`, `job_position`, `organization_name`, `organization_type`, `address`, `sub_district`, `district`, `province`, `postal_code`, `organization_phone`, `tax_id`, `phone`, `email`, `password`, `login_token`, `created_at`) VALUES
(22, 'นาง', 'มานี', 'มีนา', 40, 'พนักงานบริษัทเอกชน', '3bugs', 1, '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', '123', 'manee@gmail.com', 'abc123', NULL, '2019-05-08 09:46:14'),
(99, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', 45, 'System Analyst', '2fellows', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0850581776', 'promlert@gmail.com', 'abc123', NULL, '2019-05-02 10:10:21'),
(100, 'นางสาว', 'Lily', 'A', 22, 'sa', 'sa', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0988888888', 'sudaporn.hen@gmail.com', '123456', NULL, '2019-05-14 05:02:24'),
(101, 'นางสาว', 'เอมอร', 'สมรศรี', 44, 'SA', 'Co coperation', 3, '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', '0978878889', 'Co@hotmail.com', '123456', NULL, '2019-05-14 05:22:03'),
(102, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', 44, 'IT', '2fellows', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '012345678', 'promlert123@gmail.com', '123456', NULL, '2019-05-17 03:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `name_title`
--

CREATE TABLE `name_title` (
  `id` int(11) NOT NULL,
  `title` varchar(100) CHARACTER SET tis620 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `name_title`
--

INSERT INTO `name_title` (`id`, `title`, `created_at`) VALUES
(1, 'นาย', '2019-04-24 10:30:26'),
(2, 'นาง', '2019-04-24 10:30:26'),
(3, 'นางสาว', '2019-04-24 10:30:31');

-- --------------------------------------------------------

--
-- Table structure for table `organization_type`
--

CREATE TABLE `organization_type` (
  `id` int(11) NOT NULL,
  `name` varchar(200) CHARACTER SET tis620 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `organization_type`
--

INSERT INTO `organization_type` (`id`, `name`, `created_at`) VALUES
(1, 'ราชการ', '2019-05-13 07:36:10'),
(2, 'รัฐวิสาหกิจ', '2019-05-13 07:36:10'),
(3, 'เอกชน', '2019-05-13 07:36:24'),
(4, 'สถาบันการเงิน', '2019-05-13 07:36:24'),
(5, 'สถาบันการศึกษา', '2019-05-13 07:38:17'),
(6, 'โรงพยาบาล', '2019-05-13 07:38:17');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_office` varchar(100) NOT NULL,
  `role` enum('user','admin','super_admin','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive','deleted','') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `phone_office`, `role`, `created_at`, `status`) VALUES
(1, 'promlert', 'e99a18c428cb38d5f260853678922e03', 'พร้อมเลิศ', 'หล่อวิจิตร', 'promlert@gmail.com', '02-555-5555-99 ต่อ 1234', 'super_admin', '2018-11-23 17:13:45', 'active'),
(2, 'test', '098f6bcd4621d373cade4e832627b4f6', 'test_first_name', 'test_last_name', NULL, '', 'admin', '2018-11-23 17:17:09', 'active'),
(3, 'user', 'ee11cbb19052e40b07aac0ca060c23ee', 'Normal', 'User', NULL, '', 'user', '2018-11-28 08:20:36', 'active'),
(4, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Normal', 'Admin', NULL, '', 'admin', '2018-11-28 08:20:36', 'active'),
(5, 'superadmin', '17c4520f6cfd1ab53d8745e84681eb49', 'Super', 'Admin', NULL, '', 'super_admin', '2018-11-28 08:21:08', 'active'),
(6, 'super', '1b3231655cebb7a1f783eddf27d254ca', 'Super', 'Admin', NULL, '', 'super_admin', '2018-11-28 08:22:35', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_master`
--
ALTER TABLE `course_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_registration`
--
ALTER TABLE `course_registration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_trainee`
--
ALTER TABLE `course_trainee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `name_title`
--
ALTER TABLE `name_title`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organization_type`
--
ALTER TABLE `organization_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course_master`
--
ALTER TABLE `course_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `course_registration`
--
ALTER TABLE `course_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `course_trainee`
--
ALTER TABLE `course_trainee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `name_title`
--
ALTER TABLE `name_title`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `organization_type`
--
ALTER TABLE `organization_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
