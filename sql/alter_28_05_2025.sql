
-- Check if it avaialble or not in tables
ALTER TABLE `master_advertisement`
	ADD COLUMN `district_lgd_code` INT NOT NULL AFTER `Advertisement_Title`,
	ADD COLUMN `project_code` INT NOT NULL AFTER `district_lgd_code`;


    ALTER TABLE `post_question_map`
	ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;
