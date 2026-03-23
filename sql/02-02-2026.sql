ALTER TABLE `tbl_user_post_question_answer`
	CHANGE COLUMN `answer_file_upload` `answer_file_upload` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `answer`;

DROP TABLE tbl_user_other_documents;
CREATE TABLE `tbl_user_other_documents` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`fk_applicant_id` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_general_ci',
	`other_documents` TEXT NOT NULL COLLATE 'utf8mb4_general_ci',
	`file_name` TEXT NOT NULL COLLATE 'utf8mb4_general_ci',
	`created_at` TIMESTAMP NULL DEFAULT (CURRENT_TIMESTAMP),
	`updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`create_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`update_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `fk_applicant_id` (`fk_applicant_id`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

DROP TABLE tbl_claim_objection;
CREATE TABLE `tbl_claim_objection` (
	`claim_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`fk_apply_id` BIGINT UNSIGNED NOT NULL,
	`fk_applicant_id` BIGINT UNSIGNED NOT NULL,
	`fk_post_id` BIGINT UNSIGNED NOT NULL,
	`project_code` INT UNSIGNED NOT NULL DEFAULT '0',
	`request_type` ENUM('claim','objection') NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`request_category` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`description` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`claim_status` ENUM('Submitted','InReview','Resolved','Approved','Rejected') NULL DEFAULT 'Submitted' COLLATE 'utf8mb4_0900_ai_ci',
	`admin_remark` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`meeting_held` INT NULL DEFAULT NULL,
	`meeting_date` DATE NULL DEFAULT NULL,
	`meeting_file` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`created_by` BIGINT UNSIGNED NULL DEFAULT NULL,
	`updated_by` BIGINT UNSIGNED NULL DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT (CURRENT_TIMESTAMP),
	`updated_at` TIMESTAMP NULL DEFAULT (CURRENT_TIMESTAMP) ON UPDATE CURRENT_TIMESTAMP,
	`create_ip` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`updated_ip` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`claim_id`) USING BTREE,
	INDEX `idx_apply_id` (`fk_apply_id`) USING BTREE,
	INDEX `idx_post_id` (`fk_post_id`) USING BTREE,
	INDEX `idx_applicant_id` (`fk_applicant_id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
;

ALTER TABLE `record_applicant_edu_map`
	CHANGE COLUMN `qualification_board` `qualification_board` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `fk_grade_id`;

ALTER TABLE `tbl_applicant_education_qualification`
	CHANGE COLUMN `qualification_board` `qualification_board` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `fk_grade_id`;

ALTER TABLE `record_user_detail_map`
	CHANGE COLUMN `Document_Epic` `Document_Epic` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Exp`;

ALTER TABLE `tbl_user_detail`
	CHANGE COLUMN `Document_Epic` `Document_Epic` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Exp`;

ALTER TABLE `tbl_user_post_apply`
	CHANGE COLUMN `self_attested_file` `self_attested_file` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci' AFTER `status`;

DROP TABLE master_post_questions;
CREATE TABLE IF NOT EXISTS `master_post_questions` (
  `ques_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ques_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `answer_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `parent_id` int DEFAULT NULL,
  `parent_ans` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ans_type` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ques_order_id` tinyint NOT NULL DEFAULT '0',
  `is_active` enum('1','0') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1',
  `is_weightage_marks` enum('1','0') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`ques_ID`),
  KEY `parent_id` (`parent_id`),
  KEY `parent_ans` (`parent_ans`),
  KEY `ans_type` (`ans_type`),
  CONSTRAINT `master_post_questions_chk_1` CHECK (json_valid(`answer_options`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `master_post_questions` (`ques_ID`, `ques_name`, `answer_options`, `parent_id`, `parent_ans`, `ans_type`, `ques_order_id`, `is_active`, `is_weightage_marks`, `created_at`, `ip_address`) VALUES
	(1, 'विवाह की स्थिति ', '["विवाहित", "अविवाहित", "परित्यक्ता", "तलाकशुदा", "विधवा"]', NULL, NULL, 'O', 1, '1', '1', '2025-05-27 00:28:06', '127.0.0.1'),
	(2, 'क्या आप केवल एक या दो बच्चियों की माता हैं एवं आपने नसबंदी करवा लिया है ?', '["हाँ", "नहीं"]', 1, 'विवाहित', 'O', 4, '0', '0', '2025-05-27 00:28:06', '127.0.0.1'),
	(3, 'क्या आप गरीबी रेखा से नीचे जीवन यापन करने वाले परिवार की महिला हैं (प्रभावशील सूची से) ?', '["हाँ", "नहीं"]', NULL, NULL, 'F', 5, '1', '1', '2025-05-27 00:28:06', '127.0.0.1'),
	(4, 'क्या आपके पास ईसीसीई/न्यूट्रिशन/मनोविज्ञान में डिग्री/डिप्लोमा है ?', '["हाँ", "नहीं"]', NULL, NULL, 'F', 6, '0', '0', '2025-05-27 00:28:06', '127.0.0.1'),
	(5, 'क्या आपके पास NCC/NSS/Scout Guide की सर्टिफिकेट है ?', '["हाँ", "नहीं"]', NULL, NULL, 'F', 7, '0', '1', '2025-05-27 00:28:06', '127.0.0.1'),
	(6, 'क्या आवेदिका शासकीय / अर्धशासकीय / गैर शासकीय  संगठन जिसमें सेवारत हैं ?', '["हाँ", "नहीं"]', NULL, NULL, 'O', 8, '0', '0', '2025-05-27 00:28:06', '127.0.0.1'),
	(7, 'विवाह की तिथि ', '["हाँ", "नहीं"]', 1, 'विवाहित', 'D', 2, '1', '0', '2025-05-27 00:28:06', '127.0.0.1'),
	(8, 'जीवित बच्चों की संख्या ', '["हाँ", "नहीं"]', 1, 'विवाहित', 'N', 3, '0', '0', '2025-05-27 00:28:06', '127.0.0.1'),
	(9, 'पूर्व में आंगनबाड़ी कार्यकर्ता, सहायिका, सह सहायिका, संगठिका के रूप में एक वर्ष से अधिक अनुभव होने की स्थिति में परियोजना अधिकारी द्वारा जारी प्रमाण पत्र। ?\r\n', '["हाँ", "नहीं"]', NULL, NULL, 'OFD', 9, '1', '1', '2025-05-27 00:28:06', '127.0.0.1'),
	(10, 'क्या आपके पास यूजीसी द्वारा मान्यता प्राप्त किसी संस्थान से डिग्री, डिप्लोमा या पीजी डिप्लोमा प्रमाणपत्र है ?', '["ईसीसीई", "न्यूट्रिशन", "चाइल्ड साइकोलॉजी", "कंप्यूटर"]', NULL, NULL, 'M', 11, '0', '1', '2025-05-27 00:28:06', '127.0.0.1'),
	(11, 'क्या आपको पहले अनियमितता के कारण सेवा से अलग किया गया था ?', '["हाँ", "नहीं"]', NULL, NULL, 'O', 12, '1', '0', '2025-05-27 00:28:06', '127.0.0.1'),
	(12, 'क्या आपने किसी भी खेल में राज्य/राष्ट्रीय स्तर पर प्रतिभागिता की है ?', '["हाँ", "नहीं"]', NULL, NULL, 'F', 13, '0', '1', '2025-05-27 00:28:06', '127.0.0.1'),
	(13, 'प्राथमिक/पूर्व माध्यमिक स्कूल में रसोइया या मितानिन के पद पर कार्यरत है ?', '["हाँ", "नहीं"]', NULL, NULL, 'OFD', 10, '0', '1', '2025-05-27 00:28:06', '127.0.0.1'),
	(15, 'क्या आपने अनुसूचित जाति /जनजाति विभाग द्वारा संचालित कन्या आश्रम में आठवीं तक अध्ययन किया है?', '["हाँ", "नहीं"]', NULL, NULL, 'O', 14, '1', '1', '2025-10-29 05:26:42', '127.0.0.1'),
	(16, 'परित्यक्ता की तिथि ', '["हाँ", "नहीं"]', 1, 'परित्यक्ता', 'FD', 2, '1', '0', '2025-05-27 00:28:06', '127.0.0.1'),
	(17, 'तलाक की तिथि ', '["हाँ", "नहीं"]', 1, 'तलाकशुदा', 'FD', 2, '1', '0', '2025-05-27 00:28:06', '127.0.0.1'),
	(18, 'विधवा प्रमाण पत्र अपलोड करें ', '["हाँ"]', 1, 'विधवा', 'F', 2, '1', '0', '2026-02-02 10:29:31', '127.0.0.1');


UPDATE `master_post_config` SET `fk_ques_id`='["1","3","16","17","7","18"]' WHERE  `id`=1;
UPDATE`master_post_config` SET `fk_ques_id`='["1","3","7","18"]' WHERE  `id`=2;