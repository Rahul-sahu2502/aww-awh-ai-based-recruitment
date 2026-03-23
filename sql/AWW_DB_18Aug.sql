-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               9.2.0 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table recruitment_aww_awh.master_advertisement
DROP TABLE IF EXISTS `master_advertisement`;
CREATE TABLE IF NOT EXISTS `master_advertisement` (
  `Advertisement_ID` int NOT NULL AUTO_INCREMENT,
  `Advertisement_Title` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `district_lgd_code` int NOT NULL,
  `project_code` int NOT NULL,
  `Advertisement_Date` date NOT NULL,
  `Advertisement_Doc_Name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Advertisement_Document` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Date_For_Age` date NOT NULL DEFAULT (0),
  `Advertisement_Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_by` tinyint DEFAULT NULL,
  `Created_On` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Last_Updated_dttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `IP_Address` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Advertisement_ID`),
  KEY `project_code` (`project_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='\r\n';

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_area
DROP TABLE IF EXISTS `master_area`;
CREATE TABLE IF NOT EXISTS `master_area` (
  `area_id` int NOT NULL AUTO_INCREMENT,
  `area_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `area_name_hi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`area_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_awcs
DROP TABLE IF EXISTS `master_awcs`;
CREATE TABLE IF NOT EXISTS `master_awcs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_code` int DEFAULT NULL,
  `project` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sector_code` int DEFAULT NULL,
  `sector` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `awc_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `awc_code` bigint DEFAULT NULL,
  `district_lgd_code` int DEFAULT NULL,
  `district_code` int DEFAULT NULL,
  `area` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gp_nnn_code` bigint DEFAULT NULL,
  `gram_ward_code` bigint DEFAULT NULL,
  `block` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_under_nny` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `is_under_janman` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `awc_belong` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `awc_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `latitude` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `longitude` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `building_ownership` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `building_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `toilet` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `district_lgd_code` (`district_lgd_code`) USING BTREE,
  KEY `awc_code` (`awc_code`) USING BTREE,
  KEY `sector_code` (`sector_code`) USING BTREE,
  KEY `project_code` (`project_code`) USING BTREE,
  KEY `gp_nnn_code` (`gp_nnn_code`) USING BTREE,
  KEY `gram_ward_code` (`gram_ward_code`) USING BTREE,
  KEY `is_under_nny` (`is_under_nny`) USING BTREE,
  KEY `is_under_janman` (`is_under_janman`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=65901 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_blocks
DROP TABLE IF EXISTS `master_blocks`;
CREATE TABLE IF NOT EXISTS `master_blocks` (
  `block_code` mediumint NOT NULL,
  `block_lgd_code` smallint DEFAULT NULL,
  `block_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `block_name_hin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `district_code` smallint DEFAULT NULL,
  `district_lgd_code` smallint DEFAULT NULL,
  PRIMARY KEY (`block_code`) USING BTREE,
  UNIQUE KEY `lgd_block_code` (`block_lgd_code`) USING BTREE,
  KEY `district_code` (`district_code`),
  KEY `district_lgd_code` (`district_lgd_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_district
DROP TABLE IF EXISTS `master_district`;
CREATE TABLE IF NOT EXISTS `master_district` (
  `District_Code_LGD` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `Created_By` varchar(50) NOT NULL,
  `Created_On` varchar(50) NOT NULL,
  `Last_Updated_By` varchar(50) DEFAULT NULL,
  `Last_Updated_On` varchar(50) DEFAULT NULL,
  `IP_Address` varchar(30) NOT NULL,
  PRIMARY KEY (`District_Code_LGD`)
) ENGINE=InnoDB AUTO_INCREMENT=735 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_districts
DROP TABLE IF EXISTS `master_districts`;
CREATE TABLE IF NOT EXISTS `master_districts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `district_lgd_code` smallint NOT NULL,
  `district_code` smallint NOT NULL,
  `district_id` smallint NOT NULL,
  `district_name_eng` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `district_name_hin` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sambhag_id` tinyint NOT NULL DEFAULT (0),
  `sambhag_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `district_lgd_code` (`district_lgd_code`),
  KEY `district_code` (`district_code`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_experience_weightage
DROP TABLE IF EXISTS `master_experience_weightage`;
CREATE TABLE IF NOT EXISTS `master_experience_weightage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `minimum_experience_years` decimal(5,2) NOT NULL DEFAULT '0.00',
  `increment_value_per_year` decimal(5,2) NOT NULL DEFAULT '0.00',
  `maximum_experience_marks` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_grades
DROP TABLE IF EXISTS `master_grades`;
CREATE TABLE IF NOT EXISTS `master_grades` (
  `grade_id` int NOT NULL AUTO_INCREMENT,
  `grade_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `grade_value_from` int NOT NULL,
  `grade_value_to` int NOT NULL,
  `grade_point` decimal(20,6) DEFAULT NULL,
  PRIMARY KEY (`grade_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_nnn
DROP TABLE IF EXISTS `master_nnn`;
CREATE TABLE IF NOT EXISTS `master_nnn` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `district_lgd_code` int DEFAULT NULL,
  `std_nnn_code` int DEFAULT NULL,
  `nnn_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nnn_name_en` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Std_NNN_Code` (`std_nnn_code`) USING BTREE,
  KEY `LGD_CODE` (`district_lgd_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_panchayats
DROP TABLE IF EXISTS `master_panchayats`;
CREATE TABLE IF NOT EXISTS `master_panchayats` (
  `panchayat_code` bigint NOT NULL,
  `panchayat_lgd_code` bigint DEFAULT NULL,
  `panchayat_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `panchayat_name_hin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `block_code` int NOT NULL,
  `block_lgd_code` int NOT NULL,
  `block_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `district_code` smallint NOT NULL,
  `district_lgd_code` smallint NOT NULL,
  `district_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`panchayat_code`) USING BTREE,
  UNIQUE KEY `lgd_panchayat_code` (`panchayat_lgd_code`) USING BTREE,
  KEY `block_lgd_code` (`block_lgd_code`),
  KEY `block_code` (`block_code`),
  KEY `district_code` (`district_code`),
  KEY `district_lgd_code` (`district_lgd_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_pincodes
DROP TABLE IF EXISTS `master_pincodes`;
CREATE TABLE IF NOT EXISTS `master_pincodes` (
  `pin_id` int NOT NULL AUTO_INCREMENT,
  `district_name` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `district_code` smallint DEFAULT NULL,
  `pincode` int DEFAULT NULL,
  PRIMARY KEY (`pin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=277 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_post
DROP TABLE IF EXISTS `master_post`;
CREATE TABLE IF NOT EXISTS `master_post` (
  `post_id` bigint NOT NULL AUTO_INCREMENT,
  `Advertisement_ID` int DEFAULT NULL,
  `post_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fk_area_id` int DEFAULT NULL,
  `project_code` int DEFAULT NULL,
  `std_nnn_code` int DEFAULT NULL,
  `gp_nnn_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ward_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'News Title',
  `cat_id` int DEFAULT NULL COMMENT 'Category ID',
  `min_age` int NOT NULL,
  `max_age` int NOT NULL,
  `max_age_relax` int DEFAULT NULL,
  `Quali_ID` int NOT NULL,
  `guidelines` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `File_Path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_weightage` enum('0','1') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT (now()),
  `updated_at` timestamp NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP,
  `created_date` datetime DEFAULT (curdate()),
  `ip_address` int DEFAULT NULL,
  PRIMARY KEY (`post_id`),
  KEY `cat_id` (`cat_id`),
  KEY `std_nnn_code` (`std_nnn_code`),
  KEY `gp_nnn_code` (`gp_nnn_code`),
  KEY `ward_no` (`ward_no`),
  KEY `project_code` (`project_code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='table for News';

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_post_category
DROP TABLE IF EXISTS `master_post_category`;
CREATE TABLE IF NOT EXISTS `master_post_category` (
  `cat_id` bigint NOT NULL AUTO_INCREMENT COMMENT 'Category ID',
  `cat_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Category Name',
  `status` enum('Active','Block') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active' COMMENT 'Category Status',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table for Category';

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_post_questions
DROP TABLE IF EXISTS `master_post_questions`;
CREATE TABLE IF NOT EXISTS `master_post_questions` (
  `ques_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ques_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `answer_options` json NOT NULL,
  `parent_id` int DEFAULT NULL,
  `parent_ans` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ans_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ques_order_id` tinyint NOT NULL DEFAULT (0),
  `is_active` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1',
  `is_weightage_marks` enum('1','0') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`ques_ID`),
  KEY `parent_id` (`parent_id`),
  KEY `parent_ans` (`parent_ans`),
  KEY `ans_type` (`ans_type`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_projects
DROP TABLE IF EXISTS `master_projects`;
CREATE TABLE IF NOT EXISTS `master_projects` (
  `district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `district_lgd_code` int DEFAULT NULL,
  `project` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_code` int DEFAULT NULL,
  KEY `district_lgd_code` (`district_lgd_code`),
  KEY `project_code` (`project_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_qualification
DROP TABLE IF EXISTS `master_qualification`;
CREATE TABLE IF NOT EXISTS `master_qualification` (
  `Quali_ID` int NOT NULL AUTO_INCREMENT,
  `Quali_Name` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Created_On` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Created_By` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `IP_Address` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Last_Updated_dttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Quali_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_qualification_marks
DROP TABLE IF EXISTS `master_qualification_marks`;
CREATE TABLE IF NOT EXISTS `master_qualification_marks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` bigint NOT NULL,
  `qualification_id` int NOT NULL,
  `marks` int NOT NULL DEFAULT '0',
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_post_qualification` (`post_id`,`qualification_id`),
  KEY `idx_post_id` (`post_id`),
  KEY `idx_qualification_id` (`qualification_id`),
  CONSTRAINT `fk_post_id` FOREIGN KEY (`post_id`) REFERENCES `master_post` (`post_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qualification_id` FOREIGN KEY (`qualification_id`) REFERENCES `master_qualification` (`Quali_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_qualification_multiplier
DROP TABLE IF EXISTS `master_qualification_multiplier`;
CREATE TABLE IF NOT EXISTS `master_qualification_multiplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `qualification_id` int NOT NULL,
  `multiplier_value` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_post_qualification` (`post_id`,`qualification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_sectors
DROP TABLE IF EXISTS `master_sectors`;
CREATE TABLE IF NOT EXISTS `master_sectors` (
  `district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `district_lgd_code` int DEFAULT NULL,
  `project` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_code` int DEFAULT NULL,
  `sector` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sector_code` int DEFAULT NULL,
  KEY `district_lgd_code` (`district_lgd_code`),
  KEY `project_code` (`project_code`),
  KEY `sector_code` (`sector_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_skills
DROP TABLE IF EXISTS `master_skills`;
CREATE TABLE IF NOT EXISTS `master_skills` (
  `skill_id` int NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `skill_options` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT (now()),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`skill_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_subjects
DROP TABLE IF EXISTS `master_subjects`;
CREATE TABLE IF NOT EXISTS `master_subjects` (
  `subject_id` int NOT NULL AUTO_INCREMENT,
  `fk_Quali_ID` int NOT NULL,
  `subject_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `subject_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`subject_id`),
  KEY `fk_Quali_ID` (`fk_Quali_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_tbl_caste
DROP TABLE IF EXISTS `master_tbl_caste`;
CREATE TABLE IF NOT EXISTS `master_tbl_caste` (
  `caste_id` int NOT NULL AUTO_INCREMENT,
  `caste_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`caste_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_tbl_gender
DROP TABLE IF EXISTS `master_tbl_gender`;
CREATE TABLE IF NOT EXISTS `master_tbl_gender` (
  `gender_id` int NOT NULL AUTO_INCREMENT,
  `gender_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gender_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_tbl_organization_type
DROP TABLE IF EXISTS `master_tbl_organization_type`;
CREATE TABLE IF NOT EXISTS `master_tbl_organization_type` (
  `org_id` int NOT NULL AUTO_INCREMENT,
  `org_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_user
DROP TABLE IF EXISTS `master_user`;
CREATE TABLE IF NOT EXISTS `master_user` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Applicant_ID` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Candidate',
  `Designation` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Full_Name` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Mobile_Number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_district_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `sector_id` tinyint DEFAULT NULL,
  `Date_Of_Birth` date DEFAULT NULL,
  `Password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_pic` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Created_By` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Created_On` timestamp NOT NULL DEFAULT (now()),
  `Last_Updated_By` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Last_Updated_On` timestamp NULL DEFAULT NULL,
  `IP_Address` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Last_Updated_dttime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `Full_Name` (`Full_Name`),
  KEY `Mobile_Number` (`Mobile_Number`),
  KEY `admin_district_id` (`admin_district_id`),
  KEY `Password` (`Password`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_villages
DROP TABLE IF EXISTS `master_villages`;
CREATE TABLE IF NOT EXISTS `master_villages` (
  `village_code` bigint NOT NULL,
  `village_lgd_code` bigint DEFAULT NULL,
  `village_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `panchayat_code` bigint NOT NULL,
  `panchayat_lgd_code` bigint DEFAULT NULL,
  `panchayat_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `block_code` int NOT NULL,
  `block_lgd_code` int NOT NULL,
  `block_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `district_code` smallint NOT NULL,
  `district_lgd_code` smallint NOT NULL,
  `district_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  UNIQUE KEY `master_village_mappings_pk` (`village_code`,`panchayat_code`,`block_code`,`district_code`) USING BTREE,
  KEY `block_lgd_code` (`block_lgd_code`),
  KEY `district_lgd_code` (`district_lgd_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_ward
DROP TABLE IF EXISTS `master_ward`;
CREATE TABLE IF NOT EXISTS `master_ward` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `district_lgd_code` int DEFAULT NULL,
  `std_nnn_code` int DEFAULT NULL,
  `nnn_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nnn_name_en` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ward_no` int DEFAULT NULL,
  `ward_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `LGD_CODE` (`district_lgd_code`) USING BTREE,
  KEY `Std_NNN_Code` (`std_nnn_code`) USING BTREE,
  KEY `ward_no` (`ward_no`)
) ENGINE=InnoDB AUTO_INCREMENT=3293 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.master_weightage_marks
DROP TABLE IF EXISTS `master_weightage_marks`;
CREATE TABLE IF NOT EXISTS `master_weightage_marks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL COMMENT 'ID of the post',
  `question_id` bigint unsigned NOT NULL COMMENT 'ID of the question',
  `option_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Option value if applicable',
  `marks` decimal(5,2) NOT NULL COMMENT 'Weightage marks for this question/option',
  `is_active` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_weightage` (`post_id`,`question_id`,`option_value`),
  KEY `post_id` (`post_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.post_map
DROP TABLE IF EXISTS `post_map`;
CREATE TABLE IF NOT EXISTS `post_map` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_post_id` int NOT NULL DEFAULT '0',
  `fk_district_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_post_id` (`fk_post_id`),
  KEY `fk_district_id` (`fk_district_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.post_organization_map
DROP TABLE IF EXISTS `post_organization_map`;
CREATE TABLE IF NOT EXISTS `post_organization_map` (
  `post_organization_id` int NOT NULL AUTO_INCREMENT,
  `fk_post_id` int DEFAULT NULL,
  `fk_organization_type_id` int DEFAULT NULL,
  `minimum_experiance_year` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT (now()),
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`post_organization_id`),
  KEY `fk_post_id` (`fk_post_id`),
  KEY `fk_organization_type_id` (`fk_organization_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.post_question_map
DROP TABLE IF EXISTS `post_question_map`;
CREATE TABLE IF NOT EXISTS `post_question_map` (
  `post_map_id` int NOT NULL AUTO_INCREMENT,
  `fk_post_id` int NOT NULL,
  `fk_ques_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`post_map_id`),
  KEY `fk_post_id` (`fk_post_id`),
  KEY `fk_ques_id` (`fk_ques_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.post_skills_map
DROP TABLE IF EXISTS `post_skills_map`;
CREATE TABLE IF NOT EXISTS `post_skills_map` (
  `post_skill_id` int NOT NULL AUTO_INCREMENT,
  `fk_post_id` int NOT NULL DEFAULT '0',
  `fk_skill_id` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT (now()),
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_date` datetime DEFAULT (curdate()),
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`post_skill_id`),
  KEY `fk_post_id` (`fk_post_id`),
  KEY `fk_skill_id` (`fk_skill_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.post_subject_map
DROP TABLE IF EXISTS `post_subject_map`;
CREATE TABLE IF NOT EXISTS `post_subject_map` (
  `post_subject_id` int NOT NULL AUTO_INCREMENT,
  `fk_post_id` int NOT NULL DEFAULT '0',
  `fk_qualification_id` int NOT NULL DEFAULT '0',
  `fk_subject_id` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT (now()),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_date` datetime DEFAULT (curdate()),
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`post_subject_id`),
  KEY `fk_post_id` (`fk_post_id`),
  KEY `fk_qualification_id` (`fk_qualification_id`),
  KEY `fk_subject_id` (`fk_subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.post_type
DROP TABLE IF EXISTS `post_type`;
CREATE TABLE IF NOT EXISTS `post_type` (
  `post_type_id` int NOT NULL AUTO_INCREMENT,
  `type_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name_en` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name_hi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`post_type_id`) USING BTREE,
  UNIQUE KEY `code` (`type_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.post_vacancy_map
DROP TABLE IF EXISTS `post_vacancy_map`;
CREATE TABLE IF NOT EXISTS `post_vacancy_map` (
  `post_vacancy_map` int NOT NULL AUTO_INCREMENT,
  `fk_post_id` int NOT NULL,
  `fk_caste_id` int DEFAULT '0',
  `no_of_vacancy` tinyint NOT NULL DEFAULT (0),
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT (now()),
  `updated_at` timestamp NOT NULL DEFAULT (now()),
  `created_date` datetime NOT NULL DEFAULT (curdate()),
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`post_vacancy_map`),
  KEY `fk_post_id` (`fk_post_id`),
  KEY `fk_caste_id` (`fk_caste_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.record_applicant_edu_map
DROP TABLE IF EXISTS `record_applicant_edu_map`;
CREATE TABLE IF NOT EXISTS `record_applicant_edu_map` (
  `edu_record_id` int NOT NULL AUTO_INCREMENT,
  `qualification_id` int NOT NULL,
  `fk_apply_id` int NOT NULL,
  `fk_applicant_id` int DEFAULT NULL,
  `fk_Quali_ID` int DEFAULT NULL,
  `fk_subject_id` int DEFAULT NULL,
  `year_passing` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_marks` int DEFAULT NULL,
  `obtained_marks` int DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `fk_grade_id` int DEFAULT NULL,
  `qualification_board` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Created_on` timestamp NULL DEFAULT NULL,
  `Created_at` timestamp NULL DEFAULT NULL,
  `Created_by` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `create_ip` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`edu_record_id`),
  KEY `qualification_id` (`qualification_id`),
  KEY `fk_apply_id` (`fk_apply_id`),
  KEY `fk_applicant_id` (`fk_applicant_id`),
  KEY `fk_Quali_ID` (`fk_Quali_ID`),
  KEY `fk_subject_id` (`fk_subject_id`),
  KEY `fk_grade_id` (`fk_grade_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.record_applicant_experience_map
DROP TABLE IF EXISTS `record_applicant_experience_map`;
CREATE TABLE IF NOT EXISTS `record_applicant_experience_map` (
  `exp_record_id` int NOT NULL AUTO_INCREMENT,
  `ID` int NOT NULL,
  `fk_apply_id` int NOT NULL,
  `Applicant_ID` int NOT NULL,
  `Organization_Name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Organization_Type` tinyint(1) NOT NULL DEFAULT (0),
  `NGO_No` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Designation` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `Date_From` date NOT NULL DEFAULT (0),
  `Date_To` date NOT NULL DEFAULT (0),
  `Total_Experience` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Nature_Of_Work` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `salary` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `org_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `org_contact` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `exp_document` text COLLATE utf8mb4_general_ci,
  `Months` int DEFAULT NULL,
  `Created_By` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Created_On` timestamp NULL DEFAULT NULL,
  `Last_Updated_By` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Last_Updated_On` timestamp NULL DEFAULT NULL,
  `IP_Address` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Last_Updated_dttime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`exp_record_id`),
  KEY `ID` (`ID`),
  KEY `fk_apply_id` (`fk_apply_id`),
  KEY `Applicant_ID` (`Applicant_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.record_user_detail_map
DROP TABLE IF EXISTS `record_user_detail_map`;
CREATE TABLE IF NOT EXISTS `record_user_detail_map` (
  `record_id` int NOT NULL AUTO_INCREMENT,
  `user_details_AI_ID` int NOT NULL,
  `fk_apply_id` int NOT NULL,
  `Applicant_ID` int NOT NULL,
  `Pref_Districts` int DEFAULT '0',
  `First_Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Middle_Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Last_Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `firstName_hindi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `middleName_hindi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lastName_hindi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `FatherName` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `MotherName` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DOB` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Gender` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Contact_Number` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Email` char(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference_no` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `AdharConfirm` tinyint NOT NULL,
  `epicno` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `identity_type` int DEFAULT NULL,
  `Domicile_District_lgd` int DEFAULT '0',
  `Corr_Address` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Corr_District_lgd` int DEFAULT '0',
  `Corr_pincode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Perm_Address` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Perm_District_lgd` int DEFAULT '0',
  `Perm_pincode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sameAddress` int DEFAULT NULL,
  `current_area` int NOT NULL,
  `current_block` int DEFAULT NULL,
  `current_gp` int DEFAULT NULL,
  `current_nagar` int DEFAULT NULL,
  `current_ward` int DEFAULT NULL,
  `Document_Photo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Sign` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Caste` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Nationality` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Marital_Status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `girlchild` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `belowpl` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ncc` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ecci` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Is_Having_Disability` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domicile` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `speciality` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Created_By` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Created_On` datetime DEFAULT NULL,
  `Last_Updated_By` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Last_Updated_On` timestamp NULL DEFAULT NULL,
  `IP_Address` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Aadhar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Caste` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Domicile` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_5th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_8th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_other` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_SSC` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Inter` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_UG` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_PG` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_BPL` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Widow` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Exp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Epic` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`record_id`),
  KEY `user_details_AI_ID` (`user_details_AI_ID`),
  KEY `fk_apply_id` (`fk_apply_id`),
  KEY `current_area` (`current_area`),
  KEY `current_block` (`current_block`),
  KEY `current_gp` (`current_gp`),
  KEY `current_nagar` (`current_nagar`),
  KEY `current_ward` (`current_ward`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.tbl_advertisement_corrigendum_map
DROP TABLE IF EXISTS `tbl_advertisement_corrigendum_map`;
CREATE TABLE IF NOT EXISTS `tbl_advertisement_corrigendum_map` (
  `adv_file_id` int NOT NULL AUTO_INCREMENT,
  `fk_adv_id` int NOT NULL,
  `updated_file_tittle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_file_tittle_path` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `updated_date` date DEFAULT (curdate()),
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`adv_file_id`),
  KEY `fk_adv_id` (`fk_adv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.tbl_applicant_education_qualification
DROP TABLE IF EXISTS `tbl_applicant_education_qualification`;
CREATE TABLE IF NOT EXISTS `tbl_applicant_education_qualification` (
  `qualification_id` int NOT NULL AUTO_INCREMENT,
  `fk_applicant_id` int DEFAULT NULL,
  `fk_Quali_ID` int DEFAULT NULL,
  `fk_subject_id` int DEFAULT NULL,
  `year_passing` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_marks` int DEFAULT NULL,
  `obtained_marks` int DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `fk_grade_id` int DEFAULT NULL,
  `qualification_board` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Created_on` date DEFAULT NULL,
  `Created_at` timestamp NULL DEFAULT NULL,
  `Created_by` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `create_ip` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`qualification_id`),
  KEY `fk_applicant_id` (`fk_applicant_id`),
  KEY `fk_Quali_ID` (`fk_Quali_ID`),
  KEY `fk_subject_id` (`fk_subject_id`),
  KEY `fk_grade_id` (`fk_grade_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.tbl_applicant_experience_details
DROP TABLE IF EXISTS `tbl_applicant_experience_details`;
CREATE TABLE IF NOT EXISTS `tbl_applicant_experience_details` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Applicant_ID` int NOT NULL,
  `Organization_Name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Organization_Type` tinyint(1) NOT NULL DEFAULT (0),
  `NGO_No` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Designation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Date_From` date NOT NULL,
  `Date_To` date NOT NULL DEFAULT (0),
  `Total_Experience` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Nature_Of_Work` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `salary` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `org_address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `org_contact` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `exp_document` text COLLATE utf8mb4_general_ci,
  `Months` int DEFAULT NULL,
  `Created_By` int DEFAULT NULL,
  `Created_On` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Last_Updated_By` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Last_Updated_On` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `IP_Address` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Last_Updated_dttime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `Applicant_ID` (`Applicant_ID`),
  KEY `Organization_Type` (`Organization_Type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.tbl_otp_verification
DROP TABLE IF EXISTS `tbl_otp_verification`;
CREATE TABLE IF NOT EXISTS `tbl_otp_verification` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `identifier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.tbl_post_skill_answer
DROP TABLE IF EXISTS `tbl_post_skill_answer`;
CREATE TABLE IF NOT EXISTS `tbl_post_skill_answer` (
  `skill_ans_id` int NOT NULL AUTO_INCREMENT,
  `fk_apply_id` int NOT NULL,
  `fk_skill_id` int NOT NULL,
  `skill_answers` json NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`skill_ans_id`),
  KEY `fk_apply_id` (`fk_apply_id`),
  KEY `fk_skill_id` (`fk_skill_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.tbl_user_detail
DROP TABLE IF EXISTS `tbl_user_detail`;
CREATE TABLE IF NOT EXISTS `tbl_user_detail` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Applicant_ID` int NOT NULL,
  `Pref_Districts` int DEFAULT '0',
  `First_Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Middle_Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Last_Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `firstName_hindi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `middleName_hindi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lastName_hindi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `FatherName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `MotherName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DOB` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Gender` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Contact_Number` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Email` char(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference_no` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `AdharConfirm` tinyint NOT NULL,
  `epicno` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `identity_type` int DEFAULT NULL,
  `Domicile_District_lgd` int DEFAULT '0',
  `Corr_Address` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Corr_District_lgd` int DEFAULT '0',
  `Corr_pincode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Perm_Address` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Perm_District_lgd` int DEFAULT '0',
  `Perm_pincode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sameAddress` int DEFAULT NULL,
  `current_area` int NOT NULL,
  `current_block` int DEFAULT NULL,
  `current_gp` int DEFAULT NULL,
  `current_nagar` int DEFAULT NULL,
  `current_ward` int DEFAULT NULL,
  `Document_Photo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Sign` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Caste` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Nationality` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Marital_Status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `girlchild` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `belowpl` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ncc` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ecci` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Is_Having_Disability` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domicile` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `speciality` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Created_By` int DEFAULT NULL,
  `Created_On` timestamp NULL DEFAULT NULL,
  `Last_Updated_By` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Last_Updated_On` timestamp NULL DEFAULT NULL,
  `IP_Address` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Aadhar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Caste` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Domicile` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_5th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_8th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_other` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_SSC` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Inter` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_UG` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_PG` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_BPL` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Widow` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Exp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Document_Epic` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  KEY `current_area` (`current_area`),
  KEY `current_block` (`current_block`),
  KEY `current_gp` (`current_gp`),
  KEY `current_nagar` (`current_nagar`),
  KEY `current_ward` (`current_ward`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.tbl_user_post_apply
DROP TABLE IF EXISTS `tbl_user_post_apply`;
CREATE TABLE IF NOT EXISTS `tbl_user_post_apply` (
  `apply_id` int NOT NULL AUTO_INCREMENT,
  `fk_applicant_id` int NOT NULL,
  `fk_post_id` int NOT NULL,
  `fk_district_id` int NOT NULL,
  `application_num` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `post_area` int NOT NULL,
  `post_block` int DEFAULT NULL,
  `post_gp` int DEFAULT NULL,
  `post_nagar` int DEFAULT NULL,
  `post_ward` int DEFAULT NULL,
  `post_projects` int DEFAULT NULL,
  `status` enum('Submitted','Verified','Rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `self_attested_file` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `self_attested_file_upload_date` date DEFAULT NULL,
  `reason_rejection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `domicile_mark` tinyint DEFAULT NULL,
  `ques_mark` tinyint DEFAULT NULL,
  `edu_qualification_mark` tinyint DEFAULT NULL,
  `min_edu_qualification_mark` tinyint DEFAULT NULL,
  `min_experiance_mark` tinyint DEFAULT NULL,
  `total_mark` tinyint DEFAULT NULL,
  `ques_marks_json_with_ques_id` json DEFAULT NULL,
  `is_final_submit` tinyint DEFAULT NULL COMMENT '1=>final submit , 0=>not final submit',
  `apply_date` timestamp NULL DEFAULT (now()),
  `status_date` timestamp NULL DEFAULT (now()),
  `eligiblity_date` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stepCount` tinyint DEFAULT NULL,
  PRIMARY KEY (`apply_id`) USING BTREE,
  UNIQUE KEY `application_num` (`application_num`),
  KEY `fk_applicant_id` (`fk_applicant_id`),
  KEY `fk_post_id` (`fk_post_id`),
  KEY `fk_district_id` (`fk_district_id`),
  KEY `status` (`status`),
  KEY `is_final_submit` (`is_final_submit`),
  KEY `eligiblity_date` (`eligiblity_date`),
  KEY `post_area` (`post_area`),
  KEY `post_block` (`post_block`),
  KEY `post_gp` (`post_gp`),
  KEY `post_nagar` (`post_nagar`),
  KEY `post_ward` (`post_ward`),
  KEY `post_projects` (`post_projects`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.tbl_user_post_question_answer
DROP TABLE IF EXISTS `tbl_user_post_question_answer`;
CREATE TABLE IF NOT EXISTS `tbl_user_post_question_answer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `applicant_id` int NOT NULL,
  `post_id` int NOT NULL,
  `post_map_id` int NOT NULL,
  `fk_question_id` int NOT NULL,
  `answer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `answer_file_upload` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_From` date DEFAULT NULL,
  `date_To` date DEFAULT NULL,
  `total_experience_days` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `applicant_id` (`applicant_id`),
  KEY `post_id` (`post_id`),
  KEY `question_id` (`post_map_id`) USING BTREE,
  KEY `fk_ques_id` (`fk_question_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table recruitment_aww_awh.tbl_viewed
DROP TABLE IF EXISTS `tbl_viewed`;
CREATE TABLE IF NOT EXISTS `tbl_viewed` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_applicant_id` int NOT NULL,
  `document_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `fk_post_id` int NOT NULL,
  `fk_role_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `district_lgd_code` int DEFAULT NULL,
  `project_code` int DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `viewed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_applicant_id` (`fk_applicant_id`),
  KEY `fk_post_id` (`fk_post_id`),
  KEY `fk_role_name` (`fk_role_name`),
  KEY `document_name` (`document_name`),
  KEY `district_lgd_code` (`district_lgd_code`),
  KEY `project_code` (`project_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for trigger recruitment_aww_awh.trg_before_insert_applicant_id
DROP TRIGGER IF EXISTS `trg_before_insert_applicant_id`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_before_insert_applicant_id` BEFORE INSERT ON `master_user` FOR EACH ROW BEGIN
    DECLARE last_number INT DEFAULT 0;
    DECLARE new_applicant_id VARCHAR(30);

    -- Get numeric part from latest Applicant_ID
    SELECT CAST(SUBSTRING(Applicant_ID, 5) AS UNSIGNED)
    INTO last_number
    FROM master_user
    WHERE Applicant_ID LIKE 'REC-%'
    ORDER BY CAST(SUBSTRING(Applicant_ID, 5) AS UNSIGNED) DESC
    LIMIT 1;

    -- Format new Applicant_ID
    SET new_applicant_id = CONCAT('REC-', LPAD(last_number + 1, 6, '0'));

    -- Set it only if Applicant_ID is empty or null
    IF NEW.Applicant_ID IS NULL OR NEW.Applicant_ID = '' THEN
        SET NEW.Applicant_ID = new_applicant_id;
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger recruitment_aww_awh.trg_unique_application_num
DROP TRIGGER IF EXISTS `trg_unique_application_num`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_unique_application_num` BEFORE INSERT ON `tbl_user_post_apply` FOR EACH ROW BEGIN
    SET NEW.application_num = CONCAT(
        LPAD(NEW.fk_district_id, 3, '0'),  -- Pad to 3 digits (optional, for consistency)
        '-',
        LPAD(NEW.fk_applicant_id, 4, '0'), -- Pad to 4 digits
        '-',
        LPAD(NEW.fk_post_id, 2, '0')       -- Pad to 2 digits
    );
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
