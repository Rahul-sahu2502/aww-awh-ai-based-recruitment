CREATE TABLE `tbl_anantim_list` (
	`anantim_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`fk_advertiesment_id` INT UNSIGNED NULL DEFAULT NULL,
	`fk_post_id` INT UNSIGNED NULL DEFAULT NULL,
	`claim_start_date` DATE NULL DEFAULT NULL,
	`claim_end_date` DATE NULL DEFAULT NULL,
	`anantim_list_file` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`uploaded_by` INT UNSIGNED NULL DEFAULT NULL,
	`district_lgd_code` INT UNSIGNED NULL DEFAULT NULL,
	`project_code` INT UNSIGNED NULL DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`create_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`update_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	PRIMARY KEY (`anantim_id`) USING BTREE,
	INDEX `idx_advertiesment_id` (`fk_advertiesment_id`) USING BTREE,
	INDEX `idx_post_id` (`fk_post_id`) USING BTREE,
	INDEX `idx_district_lgd_code` (`district_lgd_code`) USING BTREE,
	INDEX `idx_project_code` (`project_code`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB;
