/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : icehr

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2019-07-09 12:59:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `featured` tinyint(1) DEFAULT NULL,
  `status` enum('publish','draft') COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `hit` int(10) DEFAULT '0',
  `created_by` int(10) DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  `page_layout` enum('mission','organizational_structure','about_organization','activity','training_project_news','news') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `video_type` enum('youtube_embed','mp4') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_path` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of article
-- ----------------------------

-- ----------------------------
-- Table structure for banners
-- ----------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `use_content_params` text COLLATE utf8mb4_unicode_ci,
  `use_content` enum('news','not_use','singlepage','franchise','promotion') COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('publish','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `target` tinyint(1) DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of banners
-- ----------------------------

-- ----------------------------
-- Table structure for course
-- ----------------------------
DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_master_id` int(11) NOT NULL,
  `batch_number` int(11) NOT NULL,
  `details` text NOT NULL,
  `application_fee` int(11) NOT NULL,
  `place` varchar(200) NOT NULL,
  `begin_date` date NOT NULL,
  `end_date` date NOT NULL,
  `responsible_user_id` int(11) NOT NULL,
  `status` enum('normal','canceled','deleted','') NOT NULL DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of course
-- ----------------------------
INSERT INTO `course` VALUES ('1', '1', '2', '', '7200', 'โรงแรมนารายณ์ ถนนสีลม เขตบางรัก กรุงเทพมหานคร', '2019-06-19', '2019-06-20', '0', 'normal', '2019-05-14 16:21:45');
INSERT INTO `course` VALUES ('2', '2', '1', '', '4600', 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-04-29', '2019-05-01', '0', 'normal', '2019-05-14 14:46:33');
INSERT INTO `course` VALUES ('3', '3', '1', '', '4600', 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-06-27', '2019-06-28', '0', 'normal', '2019-05-14 14:46:37');
INSERT INTO `course` VALUES ('4', '1', '1', '<p>ddd</p>\n', '2900', 'มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์ เขตพระนคร กรุงเทพมหานคร', '2019-05-08', '2019-05-09', '1', 'normal', '2019-05-15 17:47:13');
INSERT INTO `course` VALUES ('5', '2', '40', '<h1>ทดสอบ</h1>\r\n<p>\r\nThis is my <strong>textarea</strong> to be replaced with CKEditor.\r\n</>\r\n                                ', '4000', 'Ko Kreate Space', '2019-05-01', '2019-05-03', '1', 'normal', '2019-05-15 14:11:44');
INSERT INTO `course` VALUES ('6', '1', '111', '111', '111', '111', '2019-06-01', '2019-07-01', '2', 'normal', '2019-05-15 16:41:09');
INSERT INTO `course` VALUES ('7', '2', '555', '<p>444</p>\n', '444', '444', '2019-05-15', '2019-05-16', '1', 'normal', '2019-05-15 17:44:13');
INSERT INTO `course` VALUES ('8', '3', '999', '<h3><strong>หลักการและเหตุผล</strong></h3>\n\n<p>ระบบเรตติ้ง หรือ Rating System for Audit เป็นเครื่องมือที่ สำคัญอย่างหนึ่งของงานการตรวจสอบภายในสมัยใหม่ เพราะสามารถ สะท้อนระดับความรุนแรงของผลที่พบจากการตรวจสอบได้อย่างชัดเจน มีเกณฑ์เงื่อนไขที่ชัดเจน ซึ่งจะช่วยให้เกิดประสิทธิภาพของงานตรวจสอบ ทั้งในมุมมอง ของผู้ตรวจสอบที่มีประสบการณ์ทักษะที่แตกต่างกันและทั้งหน่วยรับ ตรวจในการรับรู้ล่วงหน้าว่าจะปรับปรุงพัฒนาผลดำเนินงานของตนอย่างไรให้ระดับ Rating ดีขึ้นกว่าการตรวจสอบครั้งก่อนหน้า</p>\n\n<h3><strong>วัตถุประสงค์</strong></h3>\n\n<ol>\n	<li>เพื่อให้ผู้เข้าอบรมเข้าใจมาตรฐานการพัฒนา Audit Risk-Based Rating System เพื่อนำไปใช้ในการพัฒนา ใช้งานตรวจสอบภายใน</li>\n	<li>เพื่อให้ผู้เข้าอบรมได้ทดสอบกรอกข้อมูลความเห็นตาม checklist เพื่อนำไปใช้เป็นต้นแบบการพัฒนา Audit Risk-Based Rating System ในอนาคต</li>\n</ol>\n', '99999', 'Nine nine nine', '2019-06-09', '2019-06-11', '1', 'normal', '2019-05-15 17:02:06');
INSERT INTO `course` VALUES ('9', '1', '5678', '<ul>\n	<li>1234</li>\n	<li>5678</li>\n	<li>0000</li>\n</ul>\n', '12345678', '12345678', '2019-05-05', '2019-05-08', '2', 'normal', '2019-05-15 17:45:56');
INSERT INTO `course` VALUES ('10', '9', '2', '<p>จัดทำโครงการ เขียนภาษาอังกฤษ<a href=\"https://ichef.bbci.co.uk/news/976/cpsprodpb/27C9/production/_103158101_tha.jpg\" target=\"_self\"><img alt=\"\" src=\"https://ichef.bbci.co.uk/news/976/cpsprodpb/27C9/production/_103158101_tha.jpg\" style=\"height:620px; width:976px\" /></a></p>\n', '500', 'ธรรมศาสตร์ ท่าพระจันทร์', '2019-05-22', '2019-05-30', '5', 'normal', '2019-05-16 10:37:09');

-- ----------------------------
-- Table structure for course_master
-- ----------------------------
DROP TABLE IF EXISTS `course_master`;
CREATE TABLE `course_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) CHARACTER SET tis620 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of course_master
-- ----------------------------
INSERT INTO `course_master` VALUES ('1', 'กลยุทธ์การเป็นวิทยากรมืออาชีพ (TRAIN THE TRAINER)', '2019-05-13 17:44:21');
INSERT INTO `course_master` VALUES ('2', 'การเขียนอีเมลภาษาอังกฤษสู่ความสำเร็จยุค THAILAND 4.0', '2019-05-14 12:29:06');
INSERT INTO `course_master` VALUES ('3', 'การจัดทำแผนการพัฒนารายบุคคลเพื่อเพิ่มประสิทธิภาพในการปฏิบัติงาน', '2019-05-14 14:45:04');
INSERT INTO `course_master` VALUES ('9', 'การเขียนภาษาอังกฤษ Thailand 2(Test)', '2019-05-16 10:34:51');

-- ----------------------------
-- Table structure for course_registration
-- ----------------------------
DROP TABLE IF EXISTS `course_registration`;
CREATE TABLE `course_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of course_registration
-- ----------------------------
INSERT INTO `course_registration` VALUES ('6', '2019-0006', '22', '1', 'นาง', 'มานี', 'มีนา', '40', 'พนักงานบริษัทเอกชน', '3bugs', '1', '123', 'manee@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', 'approve_waiting', '2019-05-13 15:57:54');
INSERT INTO `course_registration` VALUES ('7', '2019-0007', '22', '1', 'นาง', 'มานี', 'มีนา', '40', 'พนักงานบริษัทเอกชน', '3bugs', '1', '123', 'manee@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', 'completed', '2019-05-13 15:57:54');
INSERT INTO `course_registration` VALUES ('8', '2019-0008', '22', '1', 'นาง', 'มานี', 'มีนา', '40', 'พนักงานบริษัทเอกชน', '3bugs', '1', '123', 'manee@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', 'canceled', '2019-05-13 16:27:30');
INSERT INTO `course_registration` VALUES ('9', '2019-0009', '0', '1', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'completed', '2019-05-14 12:38:09');
INSERT INTO `course_registration` VALUES ('10', '2019-0010', '0', '10', 'นางสาว', 'เอมี่', 'รีไวล์', '33', 'ผู้กำกับ', 'ตจว', '1', '0988899997', 'Ami@hotmail.com', '44ซ7ถนนพหลโยทิน', 'จตุจักร', 'จตุจักร', 'กรุงเทพ', '10900', '023345555', '123456789', 'approve_waiting', '2019-05-16 11:13:51');
INSERT INTO `course_registration` VALUES ('11', '2019-0011', '22', '7', 'นาย', 'aaa', 'aaa', '111', 'aaa', 'aaa', '1', '111', 'aaa@aaa.com', '111', 'aaa', 'aaa', 'aaa', '111', '111', '111', 'started', '2019-05-16 13:49:03');
INSERT INTO `course_registration` VALUES ('12', '2019-0012', '0', '10', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'completed', '2019-05-16 16:32:54');
INSERT INTO `course_registration` VALUES ('13', '2019-0013', '0', '10', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'started', '2019-05-16 16:33:20');
INSERT INTO `course_registration` VALUES ('14', '2019-0014', '0', '10', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'approve_waiting', '2019-05-16 16:37:28');
INSERT INTO `course_registration` VALUES ('15', '2019-0015', '0', '10', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'approve_waiting', '2019-05-16 16:50:14');
INSERT INTO `course_registration` VALUES ('16', '2019-0016', '99', '6', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '45', 'System Analyst', '2fellows', '3', '0850581776', 'promlert@gmail.com', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '0123456789', '9876543210', 'started', '2019-05-16 16:55:23');
INSERT INTO `course_registration` VALUES ('17', '2019-0017', '101', '10', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', 'started', '2019-05-16 16:58:38');
INSERT INTO `course_registration` VALUES ('18', '2019-0018', '102', '2', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '44', 'IT', '2fellows', '3', '012345678', 'promlert123@gmail.com', '123/456', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '012345689', '123', 'started', '2019-05-17 10:17:23');

-- ----------------------------
-- Table structure for course_trainee
-- ----------------------------
DROP TABLE IF EXISTS `course_trainee`;
CREATE TABLE `course_trainee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of course_trainee
-- ----------------------------
INSERT INTO `course_trainee` VALUES ('1', '1', '1', 'พร้อมเลิศ', 'หล่อวิจิตร', '44', 'Programmer', '2fellows', '3', '0850581776', 'promlert@gmail.com', '2019-05-13 15:58:27');
INSERT INTO `course_trainee` VALUES ('2', '2', '1', 'a', 'a', '1', 'a', 'a', '1', '1', 'a@a.com', '2019-05-13 15:58:27');
INSERT INTO `course_trainee` VALUES ('3', '2', '2', 'b', 'b', '2', 'b', 'b', '2', '2', 'b@b.com', '2019-05-13 15:58:27');
INSERT INTO `course_trainee` VALUES ('4', '3', '3', 'x', 'x', '55', 'x', 'x', '3', '55', 'x@x.com', '2019-05-13 15:58:27');
INSERT INTO `course_trainee` VALUES ('5', '4', '1', 'a', 'a', '1', 'a', 'a', '1', '1', 'a@a.com', '2019-05-13 15:58:27');
INSERT INTO `course_trainee` VALUES ('6', '6', 'นาง', 'มานี', 'มีนา', '40', 'พนักงานบริษัทเอกชน', '3bugs', '1', '123', 'manee@gmail.com', '2019-05-13 15:58:27');
INSERT INTO `course_trainee` VALUES ('7', '7', 'นาง', 'มานี', 'มีนา', '40', 'พนักงานบริษัทเอกชน', '3bugs', '1', '123', 'manee@gmail.com', '2019-05-13 15:58:27');
INSERT INTO `course_trainee` VALUES ('8', '8', 'นาง', 'มานี', 'มีนา', '40', 'พนักงานบริษัทเอกชน', '3bugs', '1', '123', 'manee@gmail.com', '2019-05-13 16:27:30');
INSERT INTO `course_trainee` VALUES ('9', '9', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '2019-05-14 12:38:09');
INSERT INTO `course_trainee` VALUES ('10', '10', 'นางสาว', 'เอมี่', 'รีไวล์', '33', 'ผู้กำกับ', 'ตจว', '1', '0988899997', 'Ami@hotmail.com', '2019-05-16 11:13:51');
INSERT INTO `course_trainee` VALUES ('11', '10', 'นาย', 'Revi', 'Kracerment', '32', 'หัวหน้าหน่วย', 'ตชด', '3', '023345545', 'Revi@gmail.com', '2019-05-16 11:13:51');
INSERT INTO `course_trainee` VALUES ('12', '11', 'นาย', 'aaa', 'aaa', '111', 'aaa', 'aaa', '1', '111', 'aaa@aaa.com', '2019-05-16 13:49:03');
INSERT INTO `course_trainee` VALUES ('13', '12', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '2019-05-16 16:32:54');
INSERT INTO `course_trainee` VALUES ('14', '12', 'นางสาว', 'kk', 'kk', '33', 'dd', 'dd', '2', '0988899988', 'KK@gmail.com', '2019-05-16 16:32:54');
INSERT INTO `course_trainee` VALUES ('15', '13', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '2019-05-16 16:33:20');
INSERT INTO `course_trainee` VALUES ('16', '13', 'นางสาว', 'kk', 'kk', '33', 'dd', 'dd', '2', '0988899988', 'KK@gmail.com', '2019-05-16 16:33:20');
INSERT INTO `course_trainee` VALUES ('17', '14', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '2019-05-16 16:37:28');
INSERT INTO `course_trainee` VALUES ('18', '14', 'นาย', 'Livi', 'Mac', '45', 'หัวหน้า', 'CoCompany', '3', '0876676666', 'Co@gmail.com', '2019-05-16 16:37:28');
INSERT INTO `course_trainee` VALUES ('19', '15', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '2019-05-16 16:50:14');
INSERT INTO `course_trainee` VALUES ('20', '15', 'นาย', 'INY', 'NYGEL', '45', 'SA', 'SAcompany', '3', '0988876677', 'SA@gmail.com', '2019-05-16 16:50:14');
INSERT INTO `course_trainee` VALUES ('21', '16', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '45', 'System Analyst', '2fellows', '3', '0850581776', 'promlert@gmail.com', '2019-05-16 16:55:23');
INSERT INTO `course_trainee` VALUES ('22', '16', 'นาง', 'จิตริณีย์', 'หล่อวิจิตร', '47', 'Auditor', 'KU', '5', '0869991827', 'chirinee_l@gmail.com', '2019-05-16 16:55:23');
INSERT INTO `course_trainee` VALUES ('23', '17', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '0978878889', 'Co@hotmail.com', '2019-05-16 16:58:38');
INSERT INTO `course_trainee` VALUES ('24', '17', 'นาง', 'Eden', 'Wongwai', '33', 'SA', 'SA', '3', '0987787765', 'SA@gmail.com', '2019-05-16 16:58:38');
INSERT INTO `course_trainee` VALUES ('25', '18', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '44', 'IT', '2fellows', '3', '012345678', 'promlert123@gmail.com', '2019-05-17 10:17:23');

-- ----------------------------
-- Table structure for documents_download
-- ----------------------------
DROP TABLE IF EXISTS `documents_download`;
CREATE TABLE `documents_download` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) DEFAULT NULL,
  `status` enum('draft','publish') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(10) DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  `document_type` enum('qa_quality_assurance','pamphlet/infographic','km','performance_report','research_and_academic','training_documents') DEFAULT NULL,
  `file_name` varchar(150) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `hit` int(11) DEFAULT '0',
  `download` int(11) DEFAULT '0',
  `short_description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of documents_download
-- ----------------------------

-- ----------------------------
-- Table structure for faq
-- ----------------------------
DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('publish','draft') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(10) DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of faq
-- ----------------------------

-- ----------------------------
-- Table structure for media
-- ----------------------------
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` int(10) unsigned NOT NULL,
  `order_column` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of media
-- ----------------------------

-- ----------------------------
-- Table structure for member
-- ----------------------------
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of member
-- ----------------------------
INSERT INTO `member` VALUES ('22', 'นาง', 'มานี', 'มีนา', '40', 'พนักงานบริษัทเอกชน', '3bugs', '1', '11/13', 'ลาดยาว', 'จตุจักร', 'กรุงเทพมหานคร', '10900', '123', '123', '123', 'manee@gmail.com', 'abc123', null, '2019-05-08 16:46:14');
INSERT INTO `member` VALUES ('99', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '45', 'System Analyst', '2fellows', '3', null, null, null, null, null, null, null, '0850581776', 'promlert@gmail.com', 'abc123', null, '2019-05-02 17:10:21');
INSERT INTO `member` VALUES ('100', 'นางสาว', 'Lily', 'A', '22', 'sa', 'sa', '2', null, null, null, null, null, null, null, '0988888888', 'sudaporn.hen@gmail.com', '123456', null, '2019-05-14 12:02:24');
INSERT INTO `member` VALUES ('101', 'นางสาว', 'เอมอร', 'สมรศรี', '44', 'SA', 'Co coperation', '3', '14 ซอย 2 ถนน กาญจนวานิช', 'จอมพล', 'จตุจัก', 'กรุงเทพมหานคร', '10400', '023345334', '118893245333', '0978878889', 'Co@hotmail.com', '123456', null, '2019-05-14 12:22:03');
INSERT INTO `member` VALUES ('102', 'นาย', 'พร้อมเลิศ', 'หล่อวิจิตร', '44', 'IT', '2fellows', '3', null, null, null, null, null, null, null, '012345678', 'promlert123@gmail.com', '123456', null, '2019-05-17 10:14:30');

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_type` enum('internal','external') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_external` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `target` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `layout` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` enum('header','footer-left','footer-center','footer-right') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('publish','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `layout_params` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(10) DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menus
-- ----------------------------

-- ----------------------------
-- Table structure for name_title
-- ----------------------------
DROP TABLE IF EXISTS `name_title`;
CREATE TABLE `name_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET tis620 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of name_title
-- ----------------------------
INSERT INTO `name_title` VALUES ('1', 'นาย', '2019-04-24 17:30:26');
INSERT INTO `name_title` VALUES ('2', 'นาง', '2019-04-24 17:30:26');
INSERT INTO `name_title` VALUES ('3', 'นางสาว', '2019-04-24 17:30:31');

-- ----------------------------
-- Table structure for organization
-- ----------------------------
DROP TABLE IF EXISTS `organization`;
CREATE TABLE `organization` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `description` text,
  `order` int(10) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_title` varchar(50) DEFAULT NULL,
  `meta_description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(10) DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  `category_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of organization
-- ----------------------------

-- ----------------------------
-- Table structure for organization_categorys
-- ----------------------------
DROP TABLE IF EXISTS `organization_categorys`;
CREATE TABLE `organization_categorys` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `description` text,
  `order` int(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(10) DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  `type` enum('institute_committee','executives_and_personnel') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of organization_categorys
-- ----------------------------

-- ----------------------------
-- Table structure for organization_type
-- ----------------------------
DROP TABLE IF EXISTS `organization_type`;
CREATE TABLE `organization_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET tis620 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of organization_type
-- ----------------------------
INSERT INTO `organization_type` VALUES ('1', 'ราชการ', '2019-05-13 14:36:10');
INSERT INTO `organization_type` VALUES ('2', 'รัฐวิสาหกิจ', '2019-05-13 14:36:10');
INSERT INTO `organization_type` VALUES ('3', 'เอกชน', '2019-05-13 14:36:24');
INSERT INTO `organization_type` VALUES ('4', 'สถาบันการเงิน', '2019-05-13 14:36:24');
INSERT INTO `organization_type` VALUES ('5', 'สถาบันการศึกษา', '2019-05-13 14:38:17');
INSERT INTO `organization_type` VALUES ('6', 'โรงพยาบาล', '2019-05-13 14:38:17');

-- ----------------------------
-- Table structure for research_and_academic
-- ----------------------------
DROP TABLE IF EXISTS `research_and_academic`;
CREATE TABLE `research_and_academic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `featured` tinyint(1) DEFAULT NULL,
  `status` enum('publish','draft') COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `hit` int(10) DEFAULT '0',
  `created_by` int(10) DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  `published_year` int(4) DEFAULT NULL,
  `researcher_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `research_funding_source` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of research_and_academic
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_office` varchar(100) NOT NULL,
  `role` enum('user','admin','super_admin','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive','deleted','') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'promlert', 'e99a18c428cb38d5f260853678922e03', 'พร้อมเลิศ', 'หล่อวิจิตร', 'promlert@gmail.com', '02-555-5555-99 ต่อ 1234', 'super_admin', '2018-11-24 00:13:45', 'active');
INSERT INTO `user` VALUES ('2', 'test', '098f6bcd4621d373cade4e832627b4f6', 'test_first_name', 'test_last_name', null, '', 'admin', '2018-11-24 00:17:09', 'active');
INSERT INTO `user` VALUES ('3', 'user', 'ee11cbb19052e40b07aac0ca060c23ee', 'Normal', 'User', null, '', 'user', '2018-11-28 15:20:36', 'active');
INSERT INTO `user` VALUES ('4', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Normal', 'Admin', null, '', 'admin', '2018-11-28 15:20:36', 'active');
INSERT INTO `user` VALUES ('5', 'superadmin', '17c4520f6cfd1ab53d8745e84681eb49', 'Super', 'Admin', null, '', 'super_admin', '2018-11-28 15:21:08', 'active');
INSERT INTO `user` VALUES ('6', 'super', '1b3231655cebb7a1f783eddf27d254ca', 'Super', 'Admin', null, '', 'super_admin', '2018-11-28 15:22:35', 'active');
