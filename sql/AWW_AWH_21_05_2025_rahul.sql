ALTER TABLE `tbl_viewed`
	ADD COLUMN `district_lgd_code` INT NULL AFTER `fk_role_name`,
	ADD COLUMN `project_code` INT NULL AFTER `district_lgd_code`,
	ADD INDEX `district_lgd_code` (`district_lgd_code`),
	ADD INDEX `project_code` (`project_code`);
