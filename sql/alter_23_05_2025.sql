
DROP TABLE `master_post_questions`;


CREATE TABLE IF NOT EXISTS `master_post_questions` (
  `ques_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ques_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `answer_options` json NOT NULL,
  `parent_id` int DEFAULT NULL,
  `parent_ans` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ans_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`ques_ID`),
  KEY `parent_id` (`parent_id`),
  KEY `parent_ans` (`parent_ans`),
  KEY `ans_type` (`ans_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table recruitment_aww_awh.master_post_questions: ~8 rows (approximately)
INSERT INTO `master_post_questions` (`ques_ID`, `ques_name`, `answer_options`, `parent_id`, `parent_ans`, `ans_type`, `created_at`, `ip_address`) VALUES
	(1, 'विवाह की स्थिति ', '["विवाहित", "अविवाहित", "तलाकशुदा", "विधवा"]', NULL, NULL, 'O', '2025-05-23 10:44:21', '127.0.0.1'),
	(2, 'क्या आप केवल एक या दो बच्चियों की माता हैं एवं आपने नसबंदी करवा लिया है', '["हाँ", "नहीं"]', 1, 'विवाहित', 'O', '2025-05-23 10:44:21', '127.0.0.1'),
	(3, 'क्या आप गरीबी रेखा के अंतर्गत जीवन यापन करते हैं', '["हाँ", "नहीं"]', NULL, NULL, 'O', '2025-05-23 10:44:21', '127.0.0.1'),
	(4, 'क्या आपके पास ईसीसीई/न्यूट्रिशन/मनोविज्ञान में डिग्री/डिप्लोमा है', '["हाँ", "नहीं"]', NULL, NULL, 'F', '2025-05-23 10:44:21', '127.0.0.1'),
	(5, 'क्या आपके पास NCC/NSS/Scout Guide की सर्टिफिकेट है', '["हाँ", "नहीं"]', NULL, NULL, 'F', '2025-05-23 10:44:21', '127.0.0.1'),
	(6, 'क्या आवेदक शासकीय / अर्धशासकीय / गैर शासकीय  संगठन जिसमें सेवारत हैं?', '["हाँ", "नहीं"]', NULL, NULL, 'F', '2025-05-23 10:44:21', '127.0.0.1'),
	(7, 'यदि विवाहित हैं तो विवाह की तिथि ', '["हाँ", "नहीं"]', 1, 'विवाहित', 'D', '2025-05-23 10:44:21', '127.0.0.1'),
	(8, 'जीवित बच्चों की संख्या ', '["हाँ", "नहीं"]', 1, 'विवाहित', 'N', '2025-05-23 10:44:21', '127.0.0.1');


