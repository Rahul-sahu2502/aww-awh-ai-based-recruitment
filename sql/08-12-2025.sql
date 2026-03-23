## AWW-AWH

ALTER TABLE `tbl_advertisement_corrigendum_map`
	CHANGE COLUMN `updated_file_tittle` `updated_file_tittle` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `fk_adv_id`,
	CHANGE COLUMN `updated_file_tittle_path` `updated_file_tittle_path` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `updated_file_tittle`,
	CHANGE COLUMN `file_description` `file_description` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `updated_file_tittle_path`;
ALTER TABLE `master_advertisement`
	CHANGE COLUMN `Advertisement_Doc_Name` `Advertisement_Doc_Name` TEXT NOT NULL COLLATE 'utf8mb4_general_ci' AFTER `Advertisement_Date`,
	CHANGE COLUMN `Advertisement_Document` `Advertisement_Document` TEXT NOT NULL COLLATE 'utf8mb4_general_ci' AFTER `Advertisement_Doc_Name`;
ALTER TABLE `master_advertisement`
	CHANGE COLUMN `Advertisement_Title` `Advertisement_Title` VARCHAR(200) NOT NULL COLLATE 'utf8mb4_general_ci' AFTER `Advertisement_ID`;

ALTER TABLE `tbl_user_detail`
	CHANGE COLUMN `Document_Photo` `Document_Photo` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `sameAddress`,
	CHANGE COLUMN `Document_Sign` `Document_Sign` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Photo`,
	CHANGE COLUMN `Document_Aadhar` `Document_Aadhar` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `IP_Address`,
	CHANGE COLUMN `Document_Caste` `Document_Caste` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Aadhar`,
	CHANGE COLUMN `Document_Domicile` `Document_Domicile` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Caste`,
	CHANGE COLUMN `Document_5th` `Document_5th` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Domicile`,
	CHANGE COLUMN `Document_8th` `Document_8th` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_5th`,
	CHANGE COLUMN `Document_other` `Document_other` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_8th`,
	CHANGE COLUMN `Document_SSC` `Document_SSC` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_other`,
	CHANGE COLUMN `Document_Inter` `Document_Inter` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_SSC`,
	CHANGE COLUMN `Document_UG` `Document_UG` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Inter`,
	CHANGE COLUMN `Document_PG` `Document_PG` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_UG`,
	CHANGE COLUMN `Document_BPL` `Document_BPL` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_PG`,
	CHANGE COLUMN `Document_Widow` `Document_Widow` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_BPL`,
	CHANGE COLUMN `Document_Exp` `Document_Exp` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Widow`;


ALTER TABLE `record_user_detail_map`
	CHANGE COLUMN `Document_Photo` `Document_Photo` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `sameAddress`,
	CHANGE COLUMN `Document_Sign` `Document_Sign` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Photo`,
	CHANGE COLUMN `Document_Aadhar` `Document_Aadhar` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `IP_Address`,
	CHANGE COLUMN `Document_Caste` `Document_Caste` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Aadhar`,
	CHANGE COLUMN `Document_Domicile` `Document_Domicile` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Caste`,
	CHANGE COLUMN `Document_5th` `Document_5th` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Domicile`,
	CHANGE COLUMN `Document_8th` `Document_8th` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_5th`,
	CHANGE COLUMN `Document_other` `Document_other` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_8th`,
	CHANGE COLUMN `Document_SSC` `Document_SSC` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_other`,
	CHANGE COLUMN `Document_Inter` `Document_Inter` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_SSC`,
	CHANGE COLUMN `Document_UG` `Document_UG` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Inter`,
	CHANGE COLUMN `Document_PG` `Document_PG` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_UG`,
	CHANGE COLUMN `Document_BPL` `Document_BPL` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_PG`,
	CHANGE COLUMN `Document_Widow` `Document_Widow` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_BPL`,
	CHANGE COLUMN `Document_Exp` `Document_Exp` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `Document_Widow`;
