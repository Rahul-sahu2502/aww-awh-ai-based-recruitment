ALTER TABLE tbl_user_post_apply
	ADD COLUMN is_marks_confirmed ENUM('1','0') NULL DEFAULT '0' AFTER total_mark;


-- Dumping structure for table recruitment_aww_awh.master_caste_marks
DROP TABLE IF EXISTS `master_caste_marks`;
CREATE TABLE IF NOT EXISTS `master_caste_marks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `caste_id` int NOT NULL,
  `marks` int NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_post_caste` (`post_id`,`caste_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

