# ************************************************************
# Sequel Ace SQL dump
# Version 20095
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: 127.0.0.1 (MySQL 12.0.2-MariaDB)
# Database: recruitment_aww_awh
# Generation Time: 2025-10-17 05:44:05 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table master_post_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `master_post_config`;

CREATE TABLE `master_post_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_name` varchar(255) DEFAULT NULL COMMENT 'Post Name / Designation',
  `title` varchar(200) DEFAULT NULL COMMENT 'Title or Headline of the Post',
  `cat_id` int(11) DEFAULT NULL COMMENT 'Category ID (Post Category)',
  `min_age` int(11) DEFAULT NULL,
  `max_age` int(11) DEFAULT NULL,
  `max_age_relax` int(11) DEFAULT NULL,
  `quali_id` int(11) DEFAULT NULL COMMENT 'Minimum Qualification ID',
  `guidelines` text DEFAULT NULL,
  `file_name` text DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `is_weightage` enum('0','1') DEFAULT '0' COMMENT 'Is weightage applicable',
  `fk_district_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Stores all mapped district IDs' CHECK (json_valid(`fk_district_id`)),
  `fk_organization_type_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Stores mapped organization types' CHECK (json_valid(`fk_organization_type_id`)),
  `minimum_experiance_year` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Minimum experience per organization type' CHECK (json_valid(`minimum_experiance_year`)),
  `fk_ques_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Stores linked question IDs' CHECK (json_valid(`fk_ques_id`)),
  `fk_skill_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Stores linked skill IDs' CHECK (json_valid(`fk_skill_id`)),
  `fk_qualification_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Stores linked qualification IDs for subjects' CHECK (json_valid(`fk_qualification_id`)),
  `fk_subject_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Stores linked subject IDs' CHECK (json_valid(`fk_subject_id`)),
  `is_active` enum('0','1') DEFAULT '1',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_post` (`post_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Static post';

LOCK TABLES `master_post_config` WRITE;
/*!40000 ALTER TABLE `master_post_config` DISABLE KEYS */;

INSERT INTO `master_post_config` (`id`, `post_name`, `title`, `cat_id`, `min_age`, `max_age`, `max_age_relax`, `quali_id`, `guidelines`, `file_name`, `file_path`, `is_weightage`, `fk_district_id`, `fk_organization_type_id`, `minimum_experiance_year`, `fk_ques_id`, `fk_skill_id`, `fk_qualification_id`, `fk_subject_id`, `is_active`, `ip_address`, `created_by`, `updated_by`, `created_at`, `updated_at`)
VALUES
	(1,'महिला अधिकारी','महिला अधिकारी',NULL,19,22,25,4,'<p>na</p>','sample.pdf','file/sample_1760524313.pdf','0',NULL,X'5B2232225D',X'5B2234225D',X'5B2231222C2233222C2235222C2236222C2239222C223130222C223131222C223132222C223133225D',X'5B2231225D',X'5B2234225D',X'5B2237225D','1','127.0.0.1',NULL,NULL,'2025-10-15 16:01:53','2025-10-15 16:01:53');

/*!40000 ALTER TABLE `master_post_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table master_weightage_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `master_weightage_config`;

CREATE TABLE `master_weightage_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_post_id` bigint(20) unsigned NOT NULL COMMENT 'Related Post ID',
  `fk_post_config_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Linked static post configuration ID',
  `question_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Linked Question ID if applicable (JSON)' CHECK (json_valid(`question_id`)),
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Option value if applicable (JSON)' CHECK (json_valid(`option_value`)),
  `question_marks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Marks / weightage value (JSON)' CHECK (json_valid(`question_marks`)),
  `qualification_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Linked Qualification ID if applicable (JSON)' CHECK (json_valid(`qualification_id`)),
  `qualification_marks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Marks / weightage value (JSON)' CHECK (json_valid(`qualification_marks`)),
  `caste_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Linked Caste ID if applicable (JSON)' CHECK (json_valid(`caste_id`)),
  `caste_marks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Marks / weightage value (JSON)' CHECK (json_valid(`caste_marks`)),
  `multiplyer_qualification_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Linked Qualification ID if applicable (JSON)' CHECK (json_valid(`multiplyer_qualification_id`)),
  `multiplier_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Multiplier value (for qualification etc.) (JSON)' CHECK (json_valid(`multiplier_value`)),
  `minimum_experience_years` decimal(5,2) DEFAULT NULL COMMENT 'Minimum years of experience applicable',
  `increment_value_per_year` decimal(5,2) DEFAULT NULL COMMENT 'Increment marks per experience year',
  `maximum_experience_marks` decimal(5,2) DEFAULT NULL COMMENT 'Maximum marks for experience',
  `is_active` enum('0','1') NOT NULL DEFAULT '1',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `master_weightage_config` WRITE;
/*!40000 ALTER TABLE `master_weightage_config` DISABLE KEYS */;

INSERT INTO `master_weightage_config` (`id`, `fk_post_id`, `fk_post_config_id`, `question_id`, `option_value`, `question_marks`, `qualification_id`, `qualification_marks`, `caste_id`, `caste_marks`, `multiplyer_qualification_id`, `multiplier_value`, `minimum_experience_years`, `increment_value_per_year`, `maximum_experience_marks`, `is_active`, `ip_address`, `created_by`, `updated_by`, `created_at`, `updated_at`)
VALUES
	(1,1,1,X'5B2233222C2239222C2235222C223130222C223130222C223130222C223130222C223132222C223133222C2231222C2231222C2231222C2231225D',X'5B22E0A4B9E0A4BEE0A481222C6E756C6C2C22E0A4B9E0A4BEE0A481222C22E0A488E0A4B8E0A580E0A4B8E0A580E0A488222C22E0A4A8E0A58DE0A4AFE0A582E0A49FE0A58DE0A4B0E0A4BFE0A4B6E0A4A8222C22E0A49AE0A4BEE0A487E0A4B2E0A58DE0A4A120E0A4B8E0A4BEE0A487E0A495E0A58BE0A4B2E0A589E0A49CE0A580222C22E0A495E0A482E0A4AAE0A58DE0A4AFE0A582E0A49FE0A4B0222C22E0A4B9E0A4BEE0A481222C6E756C6C2C22E0A4B5E0A4BFE0A4B5E0A4BEE0A4B9E0A4BFE0A4A4222C22E0A485E0A4B5E0A4BFE0A4B5E0A4BEE0A4B9E0A4BFE0A4A4222C22E0A4A4E0A4B2E0A4BEE0A495E0A4B6E0A581E0A4A6E0A4BE222C22E0A4B5E0A4BFE0A4A7E0A4B5E0A4BE225D',X'5B312C322C332C342C352C362C372C382C392C312C322C332C345D',X'5B345D',X'5B355D',X'5B312C322C332C342C355D',X'5B312C322C332C342C355D',X'5B345D',X'5B395D',6.00,7.00,8.00,'1','127.0.0.1',0,0,'2025-10-15 16:02:49','2025-10-15 16:02:49');

/*!40000 ALTER TABLE `master_weightage_config` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
