CREATE TABLE `tbl_claim_objection` (
    `claim_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `fk_apply_id` BIGINT UNSIGNED NOT NULL,
    `fk_post_id` BIGINT UNSIGNED NOT NULL,
    `fk_applicant_id` BIGINT UNSIGNED NOT NULL,

    `request_type` ENUM('claim','objection') NOT NULL,
    `request_category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,

    `claim_status` ENUM(
        'Submitted',
        'InReview',
        'Resolved',
        'Approved',
        'Rejected'
    ) DEFAULT 'Submitted',

    `admin_remark` TEXT NULL,

    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `updated_by` BIGINT UNSIGNED DEFAULT NULL,

    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`claim_id`),

    INDEX `idx_apply_id` (`fk_apply_id`),
    INDEX `idx_post_id` (`fk_post_id`),
    INDEX `idx_applicant_id` (`fk_applicant_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `tbl_claim_objection`
	CHANGE COLUMN `fk_applicant_id` `fk_applicant_id` BIGINT UNSIGNED NOT NULL AFTER `fk_apply_id`;

ALTER TABLE `tbl_claim_objection`
	ADD COLUMN `create_ip` VARCHAR(50) NULL DEFAULT NULL AFTER `updated_at`;

ALTER TABLE `tbl_claim_objection`
	ADD COLUMN `updated_ip` VARCHAR(50) NULL DEFAULT NULL AFTER `create_ip`;

ALTER TABLE `tbl_claim_objection`
	ADD COLUMN `project_code` INT UNSIGNED NOT NULL DEFAULT (0) AFTER `fk_post_id`;

