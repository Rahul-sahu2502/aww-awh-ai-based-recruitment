ALTER TABLE `record_applicant_experience_map`
	ADD COLUMN `exp_document` TEXT NULL AFTER `org_contact`;
	ALTER TABLE `tbl_applicant_experience_details`
	ADD COLUMN `exp_document` TEXT NULL AFTER `org_contact`;