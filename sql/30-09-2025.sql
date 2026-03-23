
ALTER TABLE `tbl_user_post_apply`
	ADD COLUMN `widow_divorce_document_status` VARCHAR(50) NULL DEFAULT NULL AFTER `stepCount`;

ALTER TABLE `tbl_claim_objection` 
ADD COLUMN `meeting_held` INT NULL DEFAULT NULL AFTER `admin_remark`,
ADD COLUMN `meeting_date` DATE NULL DEFAULT NULL AFTER `meeting_held`,
ADD COLUMN `meeting_file` TEXT NULL DEFAULT NULL AFTER `meeting_date`;


CREATE TABLE `tbl_user_other_documents` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`fk_applicant_id` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_general_ci',
	`other_documents` TEXT NOT NULL COLLATE 'utf8mb4_general_ci',
	`file_name` TEXT NOT NULL COLLATE 'utf8mb4_general_ci',
	`created_at` TIMESTAMP NULL DEFAULT (CURRENT_TIMESTAMP),
	`updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`create_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`update_ip` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `fk_applicant_id` (`fk_applicant_id`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;
