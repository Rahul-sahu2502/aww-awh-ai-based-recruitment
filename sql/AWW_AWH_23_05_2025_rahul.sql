ALTER TABLE `master_advertisement`
	ADD COLUMN `project_code` INT NULL DEFAULT NULL AFTER `district_lgd_code`,
	ADD INDEX `project_code` (`project_code`);
