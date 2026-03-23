ALTER TABLE `tbl_user_post_apply`
	ADD COLUMN `stepCount` TINYINT NULL DEFAULT NULL AFTER `updated_at`;