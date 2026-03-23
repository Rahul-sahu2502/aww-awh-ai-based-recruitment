ALTER TABLE `master_post`
	ADD COLUMN `project_code` INT NULL DEFAULT NULL AFTER `fk_area_id`,
	ADD INDEX `project_code` (`project_code`);