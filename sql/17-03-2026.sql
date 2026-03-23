ALTER TABLE `master_user` 
ADD COLUMN `reference_no` VARCHAR(12) NULL DEFAULT NULL AFTER `Password`,
ADD COLUMN `AdharConfirm` TINYINT(1) NOT NULL AFTER `reference_no`;