-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 10, 2019 at 12:46 PM
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
  `name` varchar(500) CHARACTER SET tis620 NOT NULL,
  `details` text NOT NULL,
  `application_fee` int(11) NOT NULL,
  `place` varchar(200) NOT NULL,
  `begin_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `name`, `details`, `application_fee`, `place`, `begin_date`, `end_date`, `created_at`) VALUES
(1, 'กลยุทธ์การเป็นวิทยากรมืออาชีพ (TRAIN THE TRAINER) รุ่นที่ 25', '', 7200, 'โรงแรมนารายณ์ ถนนสีลม เขตบางรัก กรุงเทพมหานคร', '2019-06-19 00:00:00', '2019-06-20 00:00:00', '2019-05-02 10:47:01'),
(2, 'การเขียนอีเมลภาษาอังกฤษสู่ความสำเร็จยุค THAILAND 4.0 รุ่นที่ 14', '', 4600, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-04-29 00:00:00', '2019-05-01 00:00:00', '2019-05-03 05:47:58'),
(3, 'การจัดทำแผนการพัฒนารายบุคคลเพื่อเพิ่มประสิทธิภาพในการปฏิบัติงาน รุ่นที่ 6', '', 4600, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-06-27 00:00:00', '2019-06-28 00:00:00', '2019-05-02 10:47:01'),
(4, 'การบริหารความสัมพันธ์กับลูกค้าและการสร้างคุณค่าในการบริการ (CRM) รุ่นที่ 35', '', 2900, 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-05-08 00:00:00', '2019-05-08 00:00:00', '2019-05-02 10:47:01');

-- --------------------------------------------------------

--
-- Table structure for table `course_registration`
--

CREATE TABLE `course_registration` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ID ของสมาชิกที่ login เข้าระบบและมากรอกข้อมูลสมัคร (0 = ไม่ได้ login)',
  `course_id` int(11) NOT NULL,
  `coordinator_title` varchar(50) NOT NULL,
  `coordinator_first_name` varchar(200) NOT NULL,
  `coordinator_last_name` varchar(200) NOT NULL,
  `coordinator_age` int(11) NOT NULL,
  `coordinator_job_position` varchar(200) NOT NULL,
  `coordinator_organization_name` varchar(200) NOT NULL,
  `coordinator_organization_type` varchar(50) NOT NULL,
  `coordinator_phone` varchar(50) NOT NULL,
  `coordinator_email` varchar(200) NOT NULL,
  `receipt_address` varchar(200) NOT NULL,
  `receipt_sub_district` varchar(50) NOT NULL,
  `receipt_district` varchar(50) NOT NULL,
  `receipt_province` varchar(50) NOT NULL,
  `receipt_postal_code` varchar(10) NOT NULL,
  `receipt_organization_phone` varchar(50) NOT NULL,
  `receipt_tax_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_registration`
--

INSERT INTO `course_registration` (`id`, `member_id`, `course_id`, `coordinator_title`, `coordinator_first_name`, `coordinator_last_name`, `coordinator_age`, `coordinator_job_position`, `coordinator_organization_name`, `coordinator_organization_type`, `coordinator_phone`, `coordinator_email`, `receipt_address`, `receipt_sub_district`, `receipt_district`, `receipt_province`, `receipt_postal_code`, `receipt_organization_phone`, `receipt_tax_id`) VALUES
(1, 0, 2, '1', 'พร้อมเลิศ', 'หล่อวิจิตร', 44, 'Programmer', '2fellows', '3', '0850581776', 'promlert@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '0123456789', '555'),
(2, 0, 1, '1', 'a', 'a', 1, 'a', 'a', '1', '1', 'a@a.com', '3', 'c', 'c', 'c', '333', '333', '333'),
(3, 0, 1, '3', 'x', 'x', 55, 'x', 'x', '3', '55', 'x@x.com', 'x', 'x', 'x', 'x', '1', 'x', 'x'),
(4, 2, 1, '1', 'a', 'a', 1, 'a', 'a', '1', '1', 'a@a.com', 'a', 'a', 'a', 'a', '1', '1', '1');

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
  `organization_type` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_trainee`
--

INSERT INTO `course_trainee` (`id`, `course_registration_id`, `title`, `first_name`, `last_name`, `age`, `job_position`, `organization_name`, `organization_type`, `phone`, `email`) VALUES
(1, 1, '1', 'พร้อมเลิศ', 'หล่อวิจิตร', 44, 'Programmer', '2fellows', '3', '0850581776', 'promlert@gmail.com'),
(2, 2, '1', 'a', 'a', 1, 'a', 'a', '1', '1', 'a@a.com'),
(3, 2, '2', 'b', 'b', 2, 'b', 'b', '2', '2', 'b@b.com'),
(4, 3, '3', 'x', 'x', 55, 'x', 'x', '3', '55', 'x@x.com'),
(5, 4, '1', 'a', 'a', 1, 'a', 'a', '1', '1', 'a@a.com');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `title` int(11) NOT NULL,
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
(14, 1, 'a', 'a', 1, 'a', 'a', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 'a@a.com', 'a', NULL, '2019-05-02 10:10:21'),
(16, 3, 'b', 'b', 2, 'b', 'b', 2, 'b', 'b', 'b', 'b', '2', '2', '2', '2', 'b@b.com', 'b', NULL, '2019-05-02 10:10:21'),
(17, 2, 'จิตริณีย์', 'หล่อวิจิตร', 47, 'เจ้าหน้าที่ตรวจสอบบัญชี', 'ม.เกษตรศาสตร์', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0123456789', 'chitrinee@gmail.com', '123456', NULL, '2019-05-02 10:10:21'),
(18, 3, 'x', 'x', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'x', 'x@x.com', 'xxxxxx', NULL, '2019-05-02 10:10:21'),
(19, 1, 'y', 'y', 2, 'y', 'y', 3, 'y', 'y', 'y', 'y', '22222', 'y', 'y', 'y', 'y@y.com', 'yyyyyy', NULL, '2019-05-02 10:10:21'),
(20, 1, 'z', 'z', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'z', 'z@z.com', 'zzzzzz', NULL, '2019-05-02 10:10:21'),
(21, 1, 'a', 'a', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a', 'a2@a.com', 'aaaaaa', NULL, '2019-05-02 10:10:21'),
(22, 1, 'บัลลพ', 'หล่อวิจิตร', 9, 'นักเรียน', '3bugs', 3, '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', '123', 'tonyod@gmail.com', '123456', NULL, '2019-05-08 09:46:14'),
(99, 1, 'พร้อมเลิศ', 'หล่อวิจิตร', 45, 'System Analyst', '2fellows', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0850581776', 'promlert@gmail.com', 'abc123', NULL, '2019-05-02 10:10:21');

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
(3, 'นางสาว', '2019-04-24 10:30:31'),
(4, 'พลเอก', '2019-05-10 07:35:13');

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
  `role` enum('user','admin','super_admin','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive','deleted','') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `role`, `created_at`, `status`) VALUES
(1, 'promlert', 'e99a18c428cb38d5f260853678922e03', 'พร้อมเลิศ', 'หล่อวิจิตร', NULL, 'super_admin', '2018-11-23 17:13:45', 'active'),
(2, 'test', '098f6bcd4621d373cade4e832627b4f6', 'test_first_name', 'test_last_name', NULL, 'admin', '2018-11-23 17:17:09', 'active'),
(3, 'user', 'ee11cbb19052e40b07aac0ca060c23ee', 'Normal', 'User', NULL, 'user', '2018-11-28 08:20:36', 'active'),
(4, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Normal', 'Admin', NULL, 'admin', '2018-11-28 08:20:36', 'active'),
(5, 'superadmin', '17c4520f6cfd1ab53d8745e84681eb49', 'Super', 'Admin', NULL, 'super_admin', '2018-11-28 08:21:08', 'active'),
(6, 'super', '1b3231655cebb7a1f783eddf27d254ca', 'Super', 'Admin', NULL, 'super_admin', '2018-11-28 08:22:35', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_registration`
--
ALTER TABLE `course_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_trainee`
--
ALTER TABLE `course_trainee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `name_title`
--
ALTER TABLE `name_title`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
