
Drop TABLE IF EXISTS `tbl_anantim_list`;
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
	`std_nnn_code` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`gp_nnn_code` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`village_code` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`ward_no` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`create_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`update_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	PRIMARY KEY (`anantim_id`) USING BTREE,
	INDEX `idx_advertiesment_id` (`fk_advertiesment_id`) USING BTREE,
	INDEX `idx_post_id` (`fk_post_id`) USING BTREE,
	INDEX `idx_district_lgd_code` (`district_lgd_code`) USING BTREE,
	INDEX `idx_project_code` (`project_code`) USING BTREE,
	INDEX `idx_std_nnn_code` (`std_nnn_code`) USING BTREE,
	INDEX `idx_gp_nnn_code` (`gp_nnn_code`) USING BTREE,
	INDEX `idx_village_code` (`village_code`) USING BTREE,
	INDEX `idx_ward_no` (`ward_no`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
;

Drop TABLE IF EXISTS `tbl_antim_list`;
CREATE TABLE `tbl_antim_list` (
	`antim_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`fk_advertiesment_id` INT UNSIGNED NULL DEFAULT NULL,
	`fk_post_id` INT UNSIGNED NULL DEFAULT NULL,
	`antim_list_file` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`uploaded_by` INT UNSIGNED NULL DEFAULT NULL,
	`district_lgd_code` INT UNSIGNED NULL DEFAULT NULL,
	`project_code` INT UNSIGNED NULL DEFAULT NULL,
	`std_nnn_code` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`gp_nnn_code` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`village_code` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`ward_no` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`create_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`update_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	PRIMARY KEY (`antim_id`) USING BTREE,
	INDEX `idx_advertiesment_id` (`fk_advertiesment_id`) USING BTREE,
	INDEX `idx_post_id` (`fk_post_id`) USING BTREE,
	INDEX `idx_district_lgd_code` (`district_lgd_code`) USING BTREE,
	INDEX `idx_project_code` (`project_code`) USING BTREE,
	INDEX `idx_std_nnn_code` (`std_nnn_code`) USING BTREE,
	INDEX `idx_gp_nnn_code` (`gp_nnn_code`) USING BTREE,
	INDEX `idx_village_code` (`village_code`) USING BTREE,
	INDEX `idx_ward_no` (`ward_no`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
;
