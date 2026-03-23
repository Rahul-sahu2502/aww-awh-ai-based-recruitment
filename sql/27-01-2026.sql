INSERT INTO `master_post_questions` (`ques_name`, `answer_options`, `parent_id`, `parent_ans`, `ans_type`, `ques_order_id`, `is_active`, `is_weightage_marks`, `created_at`, `ip_address`) 
VALUES ('परित्यक्ता की तिथि ', '["हाँ", "नहीं"]', 1, 'परित्यक्ता', 'D', 2, '1', '0', '2025-05-27 05:58:06', '127.0.0.1'),
('तलाक की तिथि ', '["हाँ", "नहीं"]', 1, 'तलाकशुदा', 'D', 2, '1', '0', '2025-05-27 05:58:06', '127.0.0.1');

ALTER TABLE `tbl_anantim_list`
	ADD COLUMN `std_nnn_code` INT UNSIGNED NULL DEFAULT NULL AFTER `project_code`,
	ADD COLUMN `gp_nnn_code` INT UNSIGNED NULL DEFAULT NULL AFTER `std_nnn_code`,
	ADD COLUMN `village_code` INT UNSIGNED NULL DEFAULT NULL AFTER `gp_nnn_code`,
	ADD COLUMN `ward_no` INT UNSIGNED NULL DEFAULT NULL AFTER `village_code`,
    ADD INDEX `idx_std_nnn_code` (`std_nnn_code`),
    ADD INDEX `idx_gp_nnn_code` (`gp_nnn_code`),
    ADD INDEX `idx_village_code` (`village_code`),
    ADD INDEX `idx_ward_no` (`ward_no`);

    ALTER TABLE `tbl_anantim_list`
	CHANGE COLUMN `village_code` `village_code` VARCHAR(255) NULL DEFAULT NULL AFTER `gp_nnn_code`,
	CHANGE COLUMN `ward_no` `ward_no` VARCHAR(50) NULL DEFAULT NULL AFTER `village_code`;

	UPDATE `recruitment_aww_awh`.`master_post_questions` SET `ques_name`='क्या आप गरीबी रेखा के अंतर्गत जीवन यापन करते है (प्रभावशाली सूची से ) ?' WHERE  `ques_ID`=3;
