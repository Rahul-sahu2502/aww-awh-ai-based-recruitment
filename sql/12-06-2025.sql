
ALTER TABLE `tbl_user_post_apply`
	ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `eligiblity_date`;
