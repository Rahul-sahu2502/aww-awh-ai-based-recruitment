ALTER TABLE `tbl_user_detail`
	ADD COLUMN `identity_type` INT NULL DEFAULT NULL AFTER `epicno`;
		
	ALTER TABLE `record_user_detail_map`
	ADD COLUMN `identity_type` INT NULL DEFAULT NULL AFTER `epicno`;




    	-- Now Updated new trigger 29-05-2025 (21:15 PM)

DROP TRIGGER trg_tbl_user_detail;


DELIMITER //
CREATE TRIGGER trg_tbl_user_detail
BEFORE UPDATE ON tbl_user_detail
FOR EACH ROW
BEGIN
   
    DECLARE old_values TEXT;
    DECLARE new_values TEXT;

    SET old_values = JSON_OBJECT(
        'ID', OLD.ID,
        'Applicant_ID', OLD.Applicant_ID,
        'Pref_Districts', OLD.Pref_Districts,
        'First_Name', OLD.First_Name,
        'Middle_Name', OLD.Middle_Name,
        'Last_Name', OLD.Last_Name,
        'FatherName', OLD.FatherName,
        'MotherName', OLD.MotherName,
        'DOB', OLD.DOB,
        'Gender', OLD.Gender,
        'Contact_Number', OLD.Contact_Number,
        'Email', OLD.Email,
        'aadharno', OLD.aadharno,
        'AdharConfirm', OLD.AdharConfirm,
        'epicno', OLD.epicno,
        'identity_type', OLD.identity_type,
        'Domicile_District_lgd', OLD.Domicile_District_lgd,
        'Corr_Address', OLD.Corr_Address,
        'Corr_District_lgd', OLD.Corr_District_lgd,
        'Corr_pincode', OLD.Corr_pincode,
        'Perm_Address', OLD.Perm_Address,
        'Perm_District_lgd', OLD.Perm_District_lgd,
        'Perm_pincode', OLD.Perm_pincode,
        'sameAddress', OLD.sameAddress,
        'Document_Photo', OLD.Document_Photo,
        'Document_Sign', OLD.Document_Sign,
        'Caste', OLD.Caste,
        'Nationality', OLD.Nationality,
        'Marital_Status', OLD.Marital_Status,
        'girlchild', OLD.girlchild,
        'belowpl', OLD.belowpl,
        'ncc', OLD.ncc,
        'ecci', OLD.ecci,
        'Is_Having_Disability', OLD.Is_Having_Disability,
        'domicile', OLD.domicile,
        'speciality', OLD.speciality,
        'Created_By', OLD.Created_By,
        'Created_On', OLD.Created_On,
        'Last_Updated_By', OLD.Last_Updated_By,
        'Last_Updated_On', OLD.Last_Updated_On,
        'IP_Address', OLD.IP_Address,
        'Document_Aadhar', OLD.Document_Aadhar,
        'Document_Caste', OLD.Document_Caste,
        'Document_Domicile', OLD.Document_Domicile,
        'Document_SSC', OLD.Document_SSC,
        'Document_Inter', OLD.Document_Inter,
        'Document_UG', OLD.Document_UG,
        'Document_PG', OLD.Document_PG,
        'Document_BPL', OLD.Document_BPL,
        'Document_Widow', OLD.Document_Widow,
        'Document_Exp', OLD.Document_Exp
    );

    -- Construct a JSON object containing the values of the columns from the NEW row.
    SET new_values = JSON_OBJECT(
        'ID', NEW.ID,
        'Applicant_ID', NEW.Applicant_ID,
        'Pref_Districts', NEW.Pref_Districts,
        'First_Name', NEW.First_Name,
        'Middle_Name', NEW.Middle_Name,
        'Last_Name', NEW.Last_Name,
        'FatherName', NEW.FatherName,
        'MotherName', NEW.MotherName,
        'DOB', NEW.DOB,
        'Gender', NEW.Gender,
        'Contact_Number', NEW.Contact_Number,
        'Email', NEW.Email,
        'aadharno', NEW.aadharno,
        'AdharConfirm', NEW.AdharConfirm,
        'epicno', NEW.epicno,
        'identity_type', NEW.identity_type,
        'Domicile_District_lgd', NEW.Domicile_District_lgd,
        'Corr_Address', NEW.Corr_Address,
        'Corr_District_lgd', NEW.Corr_District_lgd,
        'Corr_pincode', NEW.Corr_pincode,
        'Perm_Address', NEW.Perm_Address,
        'Perm_District_lgd', NEW.Perm_District_lgd,
        'Perm_pincode', NEW.Perm_pincode,
        'sameAddress', NEW.sameAddress,
        'Document_Photo', NEW.Document_Photo,
        'Document_Sign', NEW.Document_Sign,
        'Caste', NEW.Caste,
        'Nationality', NEW.Nationality,
        'Marital_Status', NEW.Marital_Status,
        'girlchild', NEW.girlchild,
        'belowpl', NEW.belowpl,
        'ncc', NEW.ncc,
        'ecci', NEW.ecci,
        'Is_Having_Disability', NEW.Is_Having_Disability,
        'domicile', NEW.domicile,
        'speciality', NEW.speciality,
        'Created_By', NEW.Created_By,
        'Created_On', NEW.Created_On,
        'Last_Updated_By', NEW.Last_Updated_By,
        'Last_Updated_On', NEW.Last_Updated_On,
        'IP_Address', NEW.IP_Address,
        'Document_Aadhar', NEW.Document_Aadhar,
        'Document_Caste', NEW.Document_Caste,
        'Document_Domicile', NEW.Document_Domicile,
        'Document_5th', NEW.Document_5th,
        'Document_8th', NEW.Document_8th,
        'Document_other', NEW.Document_other,
        'Document_SSC', NEW.Document_SSC,
        'Document_Inter', NEW.Document_Inter,
        'Document_UG', NEW.Document_UG,
        'Document_PG', NEW.Document_PG,
        'Document_BPL', NEW.Document_BPL,
        'Document_Widow', NEW.Document_Widow,
        'Document_Exp', NEW.Document_Exp
    );

    INSERT INTO tbl_tbl_user_detail_audit (
        tbl_user_detail_id,
        changed_by,
        changed_on,
        action,
        old_data,
        new_data
    )
    VALUES (
        NEW.ID,                 
        NEW.Last_Updated_By,    
        NOW(),                  
        'UPDATE',              
        old_values,            
        new_values             
    );
END//

DELIMITER ;



ALTER TABLE `post_organization_map`
	CHANGE COLUMN `ip_address` `ip_address` VARCHAR(45) NULL DEFAULT NULL AFTER `created_date`;


ALTER TABLE `post_skills_map`
	CHANGE COLUMN `ip_address` `ip_address` VARCHAR(45) NULL DEFAULT NULL AFTER `created_date`;

ALTER TABLE `post_subject_map`
	CHANGE COLUMN `ip_address` `ip_address` VARCHAR(45) NULL DEFAULT NULL AFTER `created_date`;


ALTER TABLE `post_vacancy_map`
	CHANGE COLUMN `ip_address` `ip_address` VARCHAR(45) NULL DEFAULT NULL AFTER `created_date`;



    -- ## Run Below After Trigger Create 

ALTER TABLE `tbl_audit_post_map`
	ADD COLUMN `audit_by` INT NULL DEFAULT NULL AFTER `audit_action`;

ALTER TABLE `tbl_audit_post_organization_map`
	ADD COLUMN `audit_by` INT NULL DEFAULT NULL AFTER `audit_action`;

ALTER TABLE `tbl_audit_post_question_map`
	ADD COLUMN `audit_by` INT NULL DEFAULT NULL AFTER `audit_action`;

ALTER TABLE `tbl_audit_post_skills_map`
	ADD COLUMN `audit_by` INT NULL DEFAULT NULL AFTER `audit_action`;

ALTER TABLE `tbl_audit_post_subject_map`
	ADD COLUMN `audit_by` INT NULL DEFAULT NULL AFTER `audit_action`;

ALTER TABLE `tbl_audit_post_vacancy_map`
	ADD COLUMN `audit_by` INT NULL DEFAULT NULL AFTER `audit_action`;



TRUNCATE master_skills;

INSERT INTO `master_skills` (`skill_id`, `skill_name`, `skill_options`, `created_at`, `updated_at`) VALUES
(1, 'ऑफिस प्रबंधन', '["फ़ाइल प्रबंधन", "रिकॉर्ड रखरखाव", "समय प्रबंधन"]', '2025-05-09 08:36:02', NULL),
(2, 'कम्प्यूटर संचालन', '["एमएस वर्ड", "एमएस एक्सेल", "इंटरनेट ब्राउज़िंग"]', '2025-05-09 08:36:02', NULL),
(3, 'डेटा एंट्री', '["तेज़ टाइपिंग", "त्रुटिरहित प्रविष्टि", "फॉर्म भरना"]', '2025-05-09 08:36:02', NULL),
(4, 'डॉक्युमेंटेशन कार्य', '["पत्र लेखन", "रिपोर्ट तैयार करना", "स्कैनिंग और प्रिंटिंग"]', '2025-05-09 08:36:02', NULL),
(5, 'कंप्यूटर का अच्छा ज्ञान', '["पीजीडीसीए", "डीसीए", "कोपा"]', '2025-05-14 05:52:55', '2025-05-14 06:27:52'),
(6, 'अन्य ', '["किसी भी क्षेत्र में"]', '2025-05-14 06:38:21', NULL);

ALTER TABLE `tbl_post_skill_answer`
	ADD COLUMN `skill_answers` JSON NOT NULL AFTER `fk_skill_id`;
