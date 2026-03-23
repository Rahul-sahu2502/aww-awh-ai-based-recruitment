ALTER TABLE `master_post`
	ADD COLUMN `is_janman_area` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `File_Path`;
ALTER TABLE `log_post_master`
	ADD COLUMN `is_janman_area` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `File_Path`;

ALTER TABLE `master_post`
CHANGE COLUMN `is_janman_area` `is_janman_area` INT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci' AFTER `File_Path`;
ALTER TABLE `master_post`
	CHANGE COLUMN `is_janman_area` `is_janman_area` INT NULL DEFAULT '0' AFTER `File_Path`;


ALTER TABLE `log_post_master`
	CHANGE COLUMN `is_janman_area` `is_janman_area` INT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci' AFTER `File_Path`;
ALTER TABLE `log_post_master`
	CHANGE COLUMN `is_janman_area` `is_janman_area` INT NULL DEFAULT '0' AFTER `File_Path`;
	

ALTER TABLE `tbl_user_detail`
	ADD COLUMN `isJanmanNiwasi` TINYINT NULL AFTER `AdharConfirm`,
	CHANGE COLUMN `isJanmanNiwasi` `isJanmanNiwasi` INT NULL DEFAULT '0' AFTER `AdharConfirm`;

ALTER TABLE `record_user_detail_map`
	ADD COLUMN `isJanmanNiwasi` TINYINT NULL AFTER `AdharConfirm`,
	CHANGE COLUMN `isJanmanNiwasi` `isJanmanNiwasi` INT NULL DEFAULT '0' AFTER `AdharConfirm`;

