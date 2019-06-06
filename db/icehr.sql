-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 06, 2019 at 11:52 AM
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
  `trainee_limit` int(11) NOT NULL DEFAULT '0',
  `application_fee` int(11) NOT NULL DEFAULT '0',
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

INSERT INTO `course` (`id`, `course_master_id`, `batch_number`, `details`, `trainee_limit`, `application_fee`, `place`, `begin_date`, `end_date`, `responsible_user_id`, `status`, `created_at`) VALUES
(1, 1, 2, '', 30, 7200, 'โรงแรมนารายณ์ ถนนสีลม เขตบางรัก กรุงเทพมหานคร', '2019-06-19', '2019-06-20', 0, 'normal', '2019-06-06 08:05:10'),
(2, 2, 1, '', 30, 4600, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-04-29', '2019-05-01', 0, 'normal', '2019-06-06 08:05:10'),
(3, 3, 1, '', 30, 4600, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-06-27', '2019-06-28', 0, 'normal', '2019-06-06 08:05:10'),
(4, 1, 1, '<p>ddd</p>\n', 30, 2900, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-05-08', '2019-05-09', 1, 'normal', '2019-06-06 08:05:10'),
(5, 2, 40, '<h1>ทดสอบ</h1>\r\n<p>\r\nThis is my <strong>textarea</strong> to be replaced with CKEditor.\r\n</>\r\n                                ', 30, 4000, 'Ko Kreate Space', '2019-05-01', '2019-05-03', 1, 'normal', '2019-06-06 08:05:10'),
(6, 1, 111, '111', 30, 111, '111', '2019-06-01', '2019-07-01', 2, 'normal', '2019-06-06 08:05:10'),
(7, 2, 555, '<p>444</p>\n', 30, 444, '444', '2019-05-15', '2019-05-16', 1, 'normal', '2019-06-06 08:05:10'),
(8, 3, 999, '<h3><strong>หลักการและเหตุผล</strong></h3>\n\n<p>ระบบเรตติ้ง หรือ Rating System for Audit เป็นเครื่องมือที่ สำคัญอย่างหนึ่งของงานการตรวจสอบภายในสมัยใหม่ เพราะสามารถ สะท้อนระดับความรุนแรงของผลที่พบจากการตรวจสอบได้อย่างชัดเจน มีเกณฑ์เงื่อนไขที่ชัดเจน ซึ่งจะช่วยให้เกิดประสิทธิภาพของงานตรวจสอบ ทั้งในมุมมอง ของผู้ตรวจสอบที่มีประสบการณ์ทักษะที่แตกต่างกันและทั้งหน่วยรับ ตรวจในการรับรู้ล่วงหน้าว่าจะปรับปรุงพัฒนาผลดำเนินงานของตนอย่างไรให้ระดับ Rating ดีขึ้นกว่าการตรวจสอบครั้งก่อนหน้า</p>\n\n<h3><strong>วัตถุประสงค์</strong></h3>\n\n<ol>\n	<li>เพื่อให้ผู้เข้าอบรมเข้าใจมาตรฐานการพัฒนา Audit Risk-Based Rating System เพื่อนำไปใช้ในการพัฒนา ใช้งานตรวจสอบภายใน</li>\n	<li>เพื่อให้ผู้เข้าอบรมได้ทดสอบกรอกข้อมูลความเห็นตาม checklist เพื่อนำไปใช้เป็นต้นแบบการพัฒนา Audit Risk-Based Rating System ในอนาคต</li>\n</ol>\n', 30, 99999, 'Nine nine nine', '2019-06-09', '2019-06-11', 1, 'normal', '2019-06-06 08:05:10'),
(9, 1, 5678, '<ul>\n	<li>1234</li>\n	<li>5678</li>\n	<li>0000</li>\n</ul>\n', 30, 12345678, '12345678', '2019-05-05', '2019-05-08', 2, 'normal', '2019-06-06 08:05:10'),
(10, 9, 2, '<p>จัดทำโครงการ เขียนภาษาอังกฤษ<a href=\"https://ichef.bbci.co.uk/news/976/cpsprodpb/27C9/production/_103158101_tha.jpg\" target=\"_self\"><img alt=\"\" src=\"https://ichef.bbci.co.uk/news/976/cpsprodpb/27C9/production/_103158101_tha.jpg\" style=\"height:620px; width:976px\" /></a></p>\n', 30, 500, 'ธรรมศาสตร์ ท่าพระจันทร์', '2019-05-22', '2019-05-30', 5, 'normal', '2019-06-06 08:05:10'),
(11, 11, 1, '', 30, 0, 'โรงแรมแอมบาสเดอร์', '2019-06-01', '2019-06-01', 1, 'normal', '2019-06-06 08:05:10'),
(12, 12, 1, '<p>ห้องประชุมนิคม จันทรวิทุร ชั้น 7 อาคาอเนกประสงค์ 1 มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์</p>\r\n<p>สอบถามข้อมูลเพิ่มเติมได้ที่</p>\r\n<ol>\r\n<li>082-241-2212 คุณปวีณา (เจ้าหน้าที่ดูแลหลักสูตร)</li>\r\n<li>085-927-0795 คุณวิปัศยา (เจ้าหน้าที่ดูแลหลักสูตร)</li>\r\n</ol>', 30, 500, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์', '2019-05-30', '2019-05-30', 1, 'normal', '2019-06-06 08:05:10'),
(13, 12, 2, '<p>ห้องประชุมนิคม จันทรวิทุร ชั้น 7 อาคาอเนกประสงค์ 1 มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์</p>\r\n<p>สอบถามข้อมูลเพิ่มเติมได้ที่</p>\r\n<ol>\r\n<li>082-241-2212 คุณปวีณา (เจ้าหน้าที่ดูแลหลักสูตร)</li>\r\n<li>085-927-0795 คุณวิปัศยา (เจ้าหน้าที่ดูแลหลักสูตร)</li>\r\n</ol>', 30, 500, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์', '2019-06-04', '2019-06-04', 1, 'normal', '2019-06-06 08:05:10'),
(14, 12, 3, '<p>ห้องประชุมนิคม จันทรวิทุร ชั้น 7 อาคาอเนกประสงค์ 1 มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์</p>\r\n<p>สอบถามข้อมูลเพิ่มเติมได้ที่</p>\r\n<ol>\r\n<li>082-241-2212 คุณปวีณา (เจ้าหน้าที่ดูแลหลักสูตร)</li>\r\n<li>085-927-0795 คุณวิปัศยา (เจ้าหน้าที่ดูแลหลักสูตร)</li>\r\n</ol>', 30, 500, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์', '2019-06-06', '2019-06-06', 1, 'normal', '2019-06-06 08:05:10');

-- --------------------------------------------------------

--
-- Table structure for table `course_master`
--

CREATE TABLE `course_master` (
  `id` int(11) NOT NULL,
  `title` varchar(500) CHARACTER SET tis620 NOT NULL,
  `service_type` enum('training','social','driving-license','') NOT NULL COMMENT 'training = บริการวิชาการ (ฝึกอบรม), social = บริการสังคม, driving_license = บริการอบรมใบขับขี่',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_master`
--

INSERT INTO `course_master` (`id`, `title`, `service_type`, `created_at`) VALUES
(1, 'กลยุทธ์การเป็นวิทยากรมืออาชีพ (TRAIN THE TRAINER)', 'training', '2019-05-13 10:44:21'),
(2, 'การเขียนอีเมลภาษาอังกฤษสู่ความสำเร็จยุค THAILAND 4.0', 'training', '2019-05-14 05:29:06'),
(3, 'การจัดทำแผนการพัฒนารายบุคคลเพื่อเพิ่มประสิทธิภาพในการปฏิบัติงาน', 'training', '2019-05-14 07:45:04'),
(9, 'การเขียนภาษาอังกฤษ Thailand 2(Test)', 'training', '2019-05-16 03:34:51'),
(10, 'การยืดเหยียดเพื่อสุนทรียการเคลื่อนไหว', 'social', '2019-05-23 10:20:35'),
(11, 'โครงการความรู้เบื้องต้นด้านกฎหมายแก่ประชาชน เรื่อง \"กฎหมายเกี่ยวกับการล่วงละเมิดทางเพศ\"', 'social', '2019-05-23 10:20:35'),
(12, 'บริการอบรมภาคทฤษฎีเพื่อขอใบอนุญาตขับขี่', 'driving-license', '2019-05-28 10:19:38');

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
  `coordinator_birth_date` date NOT NULL,
  `coordinator_job_position` varchar(200) NOT NULL,
  `coordinator_organization_name` varchar(200) NOT NULL,
  `coordinator_organization_type` int(11) NOT NULL,
  `coordinator_organization_type_custom` varchar(50) DEFAULT NULL,
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

INSERT INTO `course_registration` (`id`, `form_number`, `member_id`, `course_id`, `coordinator_title`, `coordinator_first_name`, `coordinator_last_name`, `coordinator_birth_date`, `coordinator_job_position`, `coordinator_organization_name`, `coordinator_organization_type`, `coordinator_organization_type_custom`, `coordinator_phone`, `coordinator_email`, `receipt_address`, `receipt_sub_district`, `receipt_district`, `receipt_province`, `receipt_postal_code`, `receipt_organization_phone`, `receipt_tax_id`, `status`, `created_at`) VALUES
(6, '2019-0006', 22, 1, 'นาง', 'มานี', 'มีนา', '0000-00-00', 'พนักงานบริษัทเอกชน', '3bugs', 1, NULL, '123', 'manee@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', 'approve_waiting', '2019-05-13 08:57:54'),
(7, '2019-0007', 22, 1, 'นาง', 'มานี', 'มีนา', '0000-00-00', 'พนักงานบริษัทเอกชน', '3bugs', 1, NULL, '123', 'manee@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', 'completed', '2019-05-13 08:57:54'),
(8, '2019-0008', 22, 1, 'นาง', 'มานี', 'มีนา', '0000-00-00', 'พนักงานบริษัทเอกชน', '3bugs', 1, NULL, '123', 'manee@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', 'canceled', '2019-05-13 09:27:30'),
(9, '2019-0009', 0, 1, 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'completed', '2019-05-14 05:38:09'),
(10, '2019-0010', 0, 10, 'นางสาว', 'เอมี่', 'รีไวล์', '0000-00-00', 'ผู้กำกับ', 'ตจว', 1, NULL, '0988899997', 'Ami@hotmail.com', '44ซ7ถนนพหลโยทิน', 'จตุจักร', 'จตุจักร', 'กรุงเทพ', '10900', '023345555', '123456789', 'approve_waiting', '2019-05-16 04:13:51'),
(11, '2019-0011', 22, 7, 'นาย', 'aaa', 'aaa', '0000-00-00', 'aaa', 'aaa', 1, NULL, '111', 'aaa@aaa.com', '111', 'aaa', 'aaa', 'aaa', '111', '111', '111', 'started', '2019-05-16 06:49:03'),
(12, '2019-0012', 0, 10, 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'completed', '2019-05-16 09:32:54'),
(13, '2019-0013', 0, 10, 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'started', '2019-05-16 09:33:20'),
(14, '2019-0014', 0, 10, 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'approve_waiting', '2019-05-16 09:37:28'),
(15, '2019-0015', 0, 10, 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'approve_waiting', '2019-05-16 09:50:14'),
(16, '2019-0016', 99, 6, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 3, NULL, '0850581776', 'promlert@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '0123456789', '9876543210', 'started', '2019-05-16 09:55:23'),
(17, '2019-0017', 101, 10, 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'started', '2019-05-16 09:58:38'),
(18, '2019-0018', 102, 2, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', '123/456', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '012345689', '123', 'started', '2019-05-17 03:17:23'),
(19, '2019-0019', 102, 2, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'a', 'a', 'a', 'a', '1', '1', '1', 'started', '2019-05-21 09:50:46'),
(20, '2019-0020', 102, 2, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'b', 'b', 'b', 'b', '2', '2', '2', 'started', '2019-05-21 09:52:07'),
(21, '2019-0021', 102, 2, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'b', 'b', 'b', 'b', '2', '2', '2', 'started', '2019-05-21 09:52:43'),
(22, '2019-0022', 102, 2, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'b', 'b', 'b', 'b', '2', '2', '2', 'started', '2019-05-21 09:53:37'),
(23, '2019-0023', 102, 2, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'c', 'c', 'c', 'c', '3', '3', '3', 'started', '2019-05-21 09:57:07'),
(24, '2019-0024', 102, 5, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'a', 'a', 'a', 'a', '1', '1', '1', 'started', '2019-05-23 08:19:44'),
(25, '2019-0025', 99, 5, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 9999, 'ทดสอบ', '0850581776', 'promlert@gmail.com', 'ฟฟฟ', 'ฟฟฟ', 'ฟฟฟ', 'ฟฟฟ', '555', '555', '555', 'started', '2019-05-27 06:30:58'),
(26, '2019-0026', 99, 5, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 9999, 'องค์กรลับ', '0850581776', 'promlert@gmail.com', '1', '1', '1', '1', '1', '1', '1', 'started', '2019-05-27 06:34:44'),
(27, '2019-0027', 99, 9, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 9999, 'hahaha', '0850581776', 'promlert@gmail.com', '5', '5', '5', '5', '5', '5', '5', 'started', '2019-05-27 06:37:06'),
(28, '2019-0028', 99, 9, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 9999, 'อื่นๆๆๆๆๆ', '0850581776', 'promlert@gmail.com', '6', '6', '6', '6', '6', '6', '6', 'started', '2019-05-27 06:38:59'),
(29, '2019-0029', 99, 9, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 9999, 'yyy', '0850581776', 'promlert@gmail.com', '8', '8', '8', '8', '8', '8', '8', 'started', '2019-05-27 06:56:39'),
(30, '2019-0030', 99, 9, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 9999, 'ttt', '0850581776', 'promlert@gmail.com', '9', '9', '9', '9', '9', '9', '9', 'started', '2019-05-27 06:58:48'),
(31, '2019-0031', 99, 9, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 3, NULL, '0850581776', 'promlert@gmail.com', '0', '0', '0', '0', '0', '0', '0', 'started', '2019-05-27 07:03:08'),
(32, '2019-0032', 99, 9, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 3, NULL, '0850581776', 'promlert@gmail.com', '0', '0', '0', '0', '0', '0', '0', 'started', '2019-05-27 07:21:24'),
(33, '2019-0033', 99, 9, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 1, '', '0850581776', 'promlert@gmail.com', '0', '0', '0', '0', '0', '0', '0', 'started', '2019-05-27 07:23:04'),
(34, '2019-0034', 99, 9, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 3, NULL, '0850581776', 'promlert@gmail.com', '3', '3', '3', '3', '3', '3', '3', 'started', '2019-05-27 07:25:43'),
(35, '2019-0035', 0, 9, 'นาย', 'q', 'q', '0000-00-00', 'q', 'q', 1, 'q', 'q', 'q@q.com', '1', '1', '1', '1', '1', '1', '1', 'started', '2019-05-27 09:17:15'),
(36, '2019-0036', 0, 9, 'นาย', '1', '1', '0000-00-00', '1', '1', 9999, '1', '1', 'a@a.com', '1', '1', '1', '1', '1', '1', '1', 'started', '2019-05-27 09:19:19'),
(37, '2019-0037', 107, 9, 'นาย', 'a', 'a', '0000-00-00', 'a', 'a', 9999, 'a', 'a', 'a@a.com', '2', '2', '2', '2', '2', '2', '2', 'started', '2019-05-27 09:20:57'),
(38, '2019-0038', 0, 9, 'นาง', '2', '2', '0000-00-00', '2', '2', 9999, '2', '2', 'b@b.com', '2', '2', '2', '2', '2', '2', '2', 'started', '2019-05-27 09:22:17'),
(39, 'AC-2019-0039', 99, 8, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-22', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', '2', '2', '2', '2', '22222', '2', '2', 'started', '2019-06-04 04:15:33'),
(40, 'AC-2019-0040', 99, 8, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-21', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', '3', '3', '3', '3', '33333', '3', '3', 'started', '2019-06-04 04:28:08'),
(41, 'AC-2019-0041', 0, 8, 'นาง', 'ข', 'ข', '2019-06-02', 'ข', 'ข', 2, NULL, '2', 'b@b.com', 'ค', 'ค', 'ค', 'ค', '33333', 'ค', 'ค', 'started', '2019-06-04 04:52:55'),
(42, 'AC-2019-0042', 0, 8, 'นาย', '1', '1', '2019-01-01', '1', '1', 1, NULL, '1', 'aaa@aaa.com', '3', '3', '3', '3', '33333', '3', '3', 'started', '2019-06-04 04:55:55'),
(43, 'AC-2019-0043', 0, 5, 'นาย', '1', '1', '2019-06-01', '1', '1', 1, NULL, '1', 'a@a.com', '1', '1', '1', '1', '11111', '1', '1', 'started', '2019-06-04 09:53:47'),
(44, 'AC-2019-0044', 99, 5, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-21', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', '5', '5', '5', '5', '55555', '5', '5', 'started', '2019-06-04 10:03:15'),
(45, 'AC-2019-0045', 99, 5, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-21', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', '3', '3', '3', '3', '33333', '3', '3', 'started', '2019-06-05 06:30:43'),
(46, 'AC-2019-0046', 99, 5, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-21', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', 'aaa', 'aaa', 'aaa', 'aaa', '11111', '111', '111', 'started', '2019-06-05 08:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `course_registration_driving_license`
--

CREATE TABLE `course_registration_driving_license` (
  `id` int(11) NOT NULL,
  `form_number` varchar(20) NOT NULL,
  `member_id` int(11) NOT NULL COMMENT 'ID ของสมาชิกที่ login เข้าระบบและมากรอกข้อมูลสมัคร (0 = ไม่ได้ login)',
  `course_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `pid` varchar(20) NOT NULL COMMENT 'เลขประจำตัวประชาชน 13 หลัก',
  `address` varchar(200) NOT NULL,
  `sub_district` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `course_type` enum('5-hours','2-hours','1-hour','') NOT NULL COMMENT 'ประเภทหลักสูตรอบรม 5 ชม., 2 ชม. หรือ 1 ชม.',
  `license_type` int(11) NOT NULL COMMENT 'ประเภทใบอนุญาตขับรถ (bit field: bit 0 = รถ, bit 1 = มอเตอร์ไซค์, bit 2 = สามล้อ)',
  `status` enum('normal','canceled','','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_registration_driving_license`
--

INSERT INTO `course_registration_driving_license` (`id`, `form_number`, `member_id`, `course_id`, `title`, `first_name`, `last_name`, `pid`, `address`, `sub_district`, `district`, `province`, `postal_code`, `phone`, `course_type`, `license_type`, `status`, `created_at`) VALUES
(1, '', 0, 14, 'นางสาว', 'ส', 'ส', '3100600317068', 'ส', 'ส', 'ส', 'ส', '111', '111', '2-hours', 7, 'normal', '2019-05-30 07:42:15'),
(2, '', 0, 14, 'นาย', '1', '1', '3100600317068', '1', '1', '1', '1', '1', '1', '5-hours', 1, 'normal', '2019-05-30 08:58:58'),
(3, '', 0, 14, 'นาง', '2', '2', '3100600317068', '2', '2', '2', '2', '2', '2', '2-hours', 2, 'normal', '2019-05-30 09:03:41'),
(4, '', 0, 14, 'นางสาว', '5', '5', '3100600317068', '5', '5', '5', '5', '5', '5', '1-hour', 7, 'normal', '2019-05-30 09:54:38'),
(5, '', 99, 13, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '3100600317068', '11/13 ซ.วัดเทวสุนทร', 'ลาดยาว', 'จตุจักร', 'กรุงเทพฯ', '10900', '0850581776', '5-hours', 1, 'normal', '2019-05-31 03:09:27');

-- --------------------------------------------------------

--
-- Table structure for table `course_registration_social`
--

CREATE TABLE `course_registration_social` (
  `id` int(11) NOT NULL,
  `form_number` varchar(20) NOT NULL,
  `member_id` int(11) NOT NULL COMMENT 'ID ของสมาชิกที่ login เข้าระบบและมากรอกข้อมูลสมัคร (0 = ไม่ได้ login)',
  `course_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `birth_date` date NOT NULL,
  `occupation` varchar(200) NOT NULL,
  `work_place` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `sub_district` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact_name` varchar(50) NOT NULL,
  `contact_phone` varchar(50) NOT NULL,
  `disease` varchar(50) NOT NULL,
  `news_source` int(11) NOT NULL COMMENT 'ทราบข่าวการอบรมจากไหน (bit field: bit 0 = เว็บ, bit 1 = อีเมล, bit 2 = โบรชัวร์, bit 3 = ออนไลน์, bit 4 = บอกต่อ)',
  `status` enum('normal','canceled','','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_registration_social`
--

INSERT INTO `course_registration_social` (`id`, `form_number`, `member_id`, `course_id`, `title`, `first_name`, `last_name`, `birth_date`, `occupation`, `work_place`, `address`, `sub_district`, `district`, `province`, `postal_code`, `phone`, `email`, `contact_name`, `contact_phone`, `disease`, `news_source`, `status`, `created_at`) VALUES
(1, '2019-0001', 99, 11, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'Programmer', '3bugs', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '0850581776', 'promlert@gmail.com', 'ยุ้ย', '0869991827', '-', 0, 'normal', '2019-05-28 08:10:36'),
(2, '2019-0002', 99, 11, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'a', 'a', 'a', 'a', 'a', 'a', 'a', '0850581776', 'promlert@gmail.com', 'a', 'a', 'a', 10, 'normal', '2019-05-28 08:19:32'),
(3, '2019-0003', 0, 11, 'นาง', 'b', 'b', '0000-00-00', 'b', 'b', 'b', 'b', 'b', 'b', 'b', 'b', 'b@b.com', 'b', 'b', 'b', 17, 'normal', '2019-05-28 08:24:24'),
(4, '2019-0004', 0, 11, 'นาย', 'กกก', 'กกก', '0000-00-00', 'กกก', 'กกก', 'กกก', 'กกก', 'กกก', 'กกก', 'กกก', 'กกก', 'aaa@aaa.com', 'กกก', 'กกก', 'กกก', 10, 'normal', '2019-05-30 04:11:02');

-- --------------------------------------------------------

--
-- Table structure for table `course_trainee`
--

CREATE TABLE `course_trainee` (
  `id` int(11) NOT NULL,
  `course_registration_id` int(11) NOT NULL,
  `form_number` varchar(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `birth_date` date NOT NULL,
  `job_position` varchar(200) NOT NULL,
  `organization_name` varchar(200) NOT NULL,
  `organization_type` int(11) NOT NULL,
  `organization_type_custom` varchar(50) DEFAULT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `register_status` enum('start','wait-approve','complete','cancel') NOT NULL DEFAULT 'start' COMMENT 'สถานะการลงทะเบียน (start = ลงทะเบียน, wait-approve = แจ้งชำระเงินแล้ว รอตรวจสอบ, complete = ลงทะเบียนสมบูรณ์, cancel = ยกเลิกการลงทะเบียน)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_trainee`
--

INSERT INTO `course_trainee` (`id`, `course_registration_id`, `form_number`, `title`, `first_name`, `last_name`, `birth_date`, `job_position`, `organization_name`, `organization_type`, `organization_type_custom`, `phone`, `email`, `register_status`, `created_at`) VALUES
(1, 1, '', '1', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'Programmer', '2fellows', 3, NULL, '0850581776', 'promlert@gmail.com', 'start', '2019-05-13 08:58:27'),
(2, 2, '', '1', 'a', 'a', '0000-00-00', 'a', 'a', 1, NULL, '1', 'a@a.com', 'start', '2019-05-13 08:58:27'),
(3, 2, '', '2', 'b', 'b', '0000-00-00', 'b', 'b', 2, NULL, '2', 'b@b.com', 'start', '2019-05-13 08:58:27'),
(4, 3, '', '3', 'x', 'x', '0000-00-00', 'x', 'x', 3, NULL, '55', 'x@x.com', 'start', '2019-05-13 08:58:27'),
(5, 4, '', '1', 'a', 'a', '0000-00-00', 'a', 'a', 1, NULL, '1', 'a@a.com', 'start', '2019-05-13 08:58:27'),
(6, 6, '', 'นาง', 'มานี', 'มีนา', '0000-00-00', 'พนักงานบริษัทเอกชน', '3bugs', 1, NULL, '123', 'manee@gmail.com', 'start', '2019-05-13 08:58:27'),
(7, 7, '', 'นาง', 'มานี', 'มีนา', '0000-00-00', 'พนักงานบริษัทเอกชน', '3bugs', 1, NULL, '123', 'manee@gmail.com', 'start', '2019-05-13 08:58:27'),
(8, 8, '', 'นาง', 'มานี', 'มีนา', '0000-00-00', 'พนักงานบริษัทเอกชน', '3bugs', 1, NULL, '123', 'manee@gmail.com', 'start', '2019-05-13 09:27:30'),
(9, 9, '', 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', 'start', '2019-05-14 05:38:09'),
(10, 10, '', 'นางสาว', 'เอมี่', 'รีไวล์', '0000-00-00', 'ผู้กำกับ', 'ตจว', 1, NULL, '0988899997', 'Ami@hotmail.com', 'start', '2019-05-16 04:13:51'),
(11, 10, '', 'นาย', 'Revi', 'Kracerment', '0000-00-00', 'หัวหน้าหน่วย', 'ตชด', 3, NULL, '023345545', 'Revi@gmail.com', 'start', '2019-05-16 04:13:51'),
(12, 11, '', 'นาย', 'aaa', 'aaa', '0000-00-00', 'aaa', 'aaa', 1, NULL, '111', 'aaa@aaa.com', 'start', '2019-05-16 06:49:03'),
(13, 12, '', 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', 'start', '2019-05-16 09:32:54'),
(14, 12, '', 'นางสาว', 'kk', 'kk', '0000-00-00', 'dd', 'dd', 2, NULL, '0988899988', 'KK@gmail.com', 'start', '2019-05-16 09:32:54'),
(15, 13, '', 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', 'start', '2019-05-16 09:33:20'),
(16, 13, '', 'นางสาว', 'kk', 'kk', '0000-00-00', 'dd', 'dd', 2, NULL, '0988899988', 'KK@gmail.com', 'start', '2019-05-16 09:33:20'),
(17, 14, '', 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', 'start', '2019-05-16 09:37:28'),
(18, 14, '', 'นาย', 'Livi', 'Mac', '0000-00-00', 'หัวหน้า', 'CoCompany', 3, NULL, '0876676666', 'Co@gmail.com', 'start', '2019-05-16 09:37:28'),
(19, 15, '', 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', 'start', '2019-05-16 09:50:14'),
(20, 15, '', 'นาย', 'INY', 'NYGEL', '0000-00-00', 'SA', 'SAcompany', 3, NULL, '0988876677', 'SA@gmail.com', 'start', '2019-05-16 09:50:14'),
(21, 16, '', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 3, NULL, '0850581776', 'promlert@gmail.com', 'start', '2019-05-16 09:55:23'),
(22, 16, '', 'นาง', 'จิตริณีย์', 'หล่อวิจิตร', '0000-00-00', 'Auditor', 'KU', 5, NULL, '0869991827', 'chirinee_l@gmail.com', 'start', '2019-05-16 09:55:23'),
(23, 17, '', 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, NULL, '0978878889', 'Co@hotmail.com', 'start', '2019-05-16 09:58:38'),
(24, 17, '', 'นาง', 'Eden', 'Wongwai', '0000-00-00', 'SA', 'SA', 3, NULL, '0987787765', 'SA@gmail.com', 'start', '2019-05-16 09:58:38'),
(25, 18, '', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'start', '2019-05-17 03:17:23'),
(26, 19, '', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'start', '2019-05-21 09:50:46'),
(27, 22, '2019-0022-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'start', '2019-05-21 09:53:37'),
(28, 23, '2019-0023-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'start', '2019-05-21 09:57:07'),
(29, 23, '2019-0023-0002', 'นาย', 'บัลลพ', 'หล่อวิจิตร', '0000-00-00', 'ทดสอบ', '3bugs', 3, NULL, '555', 'tonyod53@gmail.com', 'start', '2019-05-21 09:57:07'),
(30, 24, '2019-0024-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, NULL, '012345678', 'promlert123@gmail.com', 'start', '2019-05-23 08:19:44'),
(31, 25, '2019-0025-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 9999, NULL, '0850581776', 'promlert@gmail.com', 'start', '2019-05-27 06:30:58'),
(32, 26, '2019-0026-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 9999, NULL, 'องค์กรลับ', '0850581776', 'start', '2019-05-27 06:34:44'),
(33, 28, '2019-0028-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 9999, 'อื่นๆๆๆๆๆ', '0850581776', 'promlert@gmail.com', 'start', '2019-05-27 06:38:59'),
(34, 31, '2019-0031-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 3, NULL, '0850581776', 'promlert@gmail.com', 'start', '2019-05-27 07:03:08'),
(35, 32, '2019-0032-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 3, NULL, '0850581776', 'promlert@gmail.com', 'start', '2019-05-27 07:21:24'),
(36, 33, '2019-0033-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 1, '', '0850581776', 'promlert@gmail.com', 'start', '2019-05-27 07:23:04'),
(37, 34, '2019-0034-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'System Analyst', '2fellows', 3, NULL, '0850581776', 'promlert@gmail.com', 'start', '2019-05-27 07:25:43'),
(38, 35, '2019-0035-0001', 'นาย', 'q', 'q', '0000-00-00', 'q', 'q', 1, 'q', 'q', 'q@q.com', 'start', '2019-05-27 09:17:15'),
(39, 36, '2019-0036-0001', 'นาย', '1', '1', '0000-00-00', '1', '1', 9999, '1', '1', 'a@a.com', 'start', '2019-05-27 09:19:19'),
(40, 37, '2019-0037-0001', 'นาย', 'a', 'a', '0000-00-00', 'a', 'a', 9999, 'a', 'a', 'a@a.com', 'start', '2019-05-27 09:20:57'),
(41, 38, '2019-0038-0001', 'นาง', '2', '2', '0000-00-00', '2', '2', 9999, '2', '2', 'b@b.com', 'start', '2019-05-27 09:22:17'),
(42, 39, 'AC-2019-0039-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-22', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', 'start', '2019-06-04 04:15:33'),
(43, 40, 'AC-2019-0040-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-21', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', 'start', '2019-06-04 04:28:08'),
(44, 41, 'AC-2019-0041-0001', 'นาย', 'ก', 'ก', '2019-06-01', 'ก', 'ก', 1, NULL, '1', 'a@a.com', 'start', '2019-06-04 04:52:55'),
(45, 41, 'AC-2019-0041-0002', 'นาง', 'ข', 'ข', '2019-06-02', 'ข', 'ข', 2, NULL, '2', 'b@b.com', 'start', '2019-06-04 04:52:55'),
(46, 42, 'AC-2019-0042-0001', 'นาย', '1', '1', '2019-01-01', '1', '1', 1, NULL, '1', 'aaa@aaa.com', 'start', '2019-06-04 04:55:55'),
(47, 42, 'AC-2019-0042-0002', 'นาง', '2', '2', '2019-01-02', '2', '2', 2, NULL, '2', 'bbb@bbb.com', 'start', '2019-06-04 04:55:55'),
(48, 43, 'AC-2019-0043-0001', 'นาย', '1', '1', '2019-06-01', '1', '1', 1, NULL, '1', 'a@a.com', 'start', '2019-06-04 09:53:47'),
(49, 44, 'AC-2019-0044-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-21', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', 'start', '2019-06-04 10:03:15'),
(50, 45, 'AC-2019-0045-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-21', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', 'start', '2019-06-05 06:30:43'),
(51, 45, 'AC-2019-0045-0002', 'นาง', 'b', 'b', '2019-06-02', 'b', 'b', 2, NULL, 'b', 'b@b.com', 'start', '2019-06-05 06:30:43'),
(52, 46, 'AC-2019-0046-0001', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-21', 'System Analyst', '2fellows', 3, '', '0850581776', 'promlert@gmail.com', 'start', '2019-06-05 08:42:00'),
(53, 46, 'AC-2019-0046-0002', 'นาง', 'จิตริณีย์', 'หล่อวิจิตร', '1972-01-12', 'นักตรวจสอบบัญชี', 'มหาวิทยาลัยเกษตรศาสตร์', 1, NULL, '0869991827', 'chitrinee_l@gmail.com', 'start', '2019-06-05 08:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `birth_date` date NOT NULL,
  `job_position` varchar(200) DEFAULT NULL,
  `organization_name` varchar(200) DEFAULT NULL,
  `organization_type` int(11) DEFAULT NULL,
  `organization_type_custom` varchar(50) DEFAULT NULL,
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

INSERT INTO `member` (`id`, `title`, `first_name`, `last_name`, `birth_date`, `job_position`, `organization_name`, `organization_type`, `organization_type_custom`, `address`, `sub_district`, `district`, `province`, `postal_code`, `organization_phone`, `tax_id`, `phone`, `email`, `password`, `login_token`, `created_at`) VALUES
(22, 'นาง', 'มานี', 'มีนา', '0000-00-00', 'พนักงานบริษัทเอกชน', '3bugs', 1, '', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', '123', 'manee@gmail.com', 'abc123', NULL, '2019-05-08 09:46:14'),
(99, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '1974-11-21', 'System Analyst', '2fellows', 3, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0850581776', 'promlert@gmail.com', 'abc123', NULL, '2019-05-02 10:10:21'),
(100, 'นางสาว', 'Lily', 'A', '0000-00-00', 'sa', 'sa', 2, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0988888888', 'sudaporn.hen@gmail.com', '123456', NULL, '2019-05-14 05:02:24'),
(101, 'นางสาว', 'เอมอร', 'สมรศรี', '0000-00-00', 'SA', 'Co coperation', 3, '', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', '0978878889', 'Co@hotmail.com', '123456', NULL, '2019-05-14 05:22:03'),
(102, 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '0000-00-00', 'IT', '2fellows', 3, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '012345678', 'promlert123@gmail.com', '123456', NULL, '2019-05-17 03:14:30'),
(103, 'นาย', 'a', 'b', '0000-00-00', 'c', 'd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', 'd@e.com', 'aaaaaa', NULL, '2019-05-27 05:46:03'),
(105, 'นาย', 'aaa', 'bbb', '0000-00-00', 'ccc', 'ddd', 9999, 'eee', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '222', 'fff@ggg.com', 'hhhhhh', NULL, '2019-05-27 05:58:12'),
(106, 'นาง', 'กกก', 'ขขข', '0000-00-00', 'คคค', 'งงง', 9999, 'จจจ', '1', '1', '1', '1', '1', '1', '1', '1', 'mail@mail.com', '111111', NULL, '2019-05-27 06:01:54'),
(107, 'นาย', 'a', 'a', '2019-07-02', 'a', 'a', 9999, 'a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a', 'a@a.com', 'aaaaaa', NULL, '2019-05-27 08:57:45'),
(108, 'นาย', 'Promlert', 'Lovichit', '1974-11-21', 'System Analyst', '3bugs', 9999, 'Enterprise Company', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0850581776', 'promlert@3bugs.com', 'abc123', NULL, '2019-05-31 09:41:28'),
(109, 'นาย', 'Banlop', 'Lovichit', '2010-01-07', 'Student', 'KUS', 5, NULL, '11/13 Varawan Park Condominium', 'Ladyao', 'Jatujak', 'Bangkok', '10900', '0812345678', '999', '0898765432', 'tonyod@gmail.com', '123456', NULL, '2019-05-31 09:54:31');

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
(6, 'โรงพยาบาล', '2019-05-13 07:38:17'),
(7, 'มูลนิธิ / สมาคม', '2019-05-24 09:29:30');

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
-- Indexes for table `course_registration_driving_license`
--
ALTER TABLE `course_registration_driving_license`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_registration_social`
--
ALTER TABLE `course_registration_social`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `course_master`
--
ALTER TABLE `course_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `course_registration`
--
ALTER TABLE `course_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `course_registration_driving_license`
--
ALTER TABLE `course_registration_driving_license`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_registration_social`
--
ALTER TABLE `course_registration_social`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_trainee`
--
ALTER TABLE `course_trainee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `name_title`
--
ALTER TABLE `name_title`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `organization_type`
--
ALTER TABLE `organization_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
