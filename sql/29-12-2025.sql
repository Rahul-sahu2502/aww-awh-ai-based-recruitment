
ALTER TABLE tbl_user_post_apply ADD COLUMN post_village INT NULL DEFAULT NULL AFTER post_gp;
ALTER TABLE record_user_detail_map ADD COLUMN current_village INT NULL DEFAULT NULL AFTER current_gp;
ALTER TABLE tbl_user_detail	ADD COLUMN current_village INT NULL DEFAULT NULL AFTER current_gp;

DROP TRIGGER IF EXISTS `trg_user_detail_after_update`;
DELIMITER //
CREATE TRIGGER `trg_user_detail_after_update` AFTER UPDATE ON `tbl_user_detail` FOR EACH ROW BEGIN
    DECLARE old_values TEXT;
    DECLARE new_values TEXT;

    SET old_values = JSON_OBJECT(
        'ID', OLD.ID, 'Applicant_ID', OLD.Applicant_ID, 'Pref_Districts', OLD.Pref_Districts, 'First_Name', OLD.First_Name,
        'Middle_Name', OLD.Middle_Name, 'Last_Name', OLD.Last_Name, 'firstName_hindi', OLD.firstName_hindi,
        'middleName_hindi', OLD.middleName_hindi, 'lastName_hindi', OLD.lastName_hindi, 'FatherName', OLD.FatherName,
        'MotherName', OLD.MotherName, 'DOB', OLD.DOB, 'Gender', OLD.Gender, 'Contact_Number', OLD.Contact_Number,
        'Email', OLD.Email, 'reference_no', OLD.reference_no, 'AdharConfirm', OLD.AdharConfirm, 'epicno', OLD.epicno,
        'identity_type', OLD.identity_type, 'Domicile_District_lgd', OLD.Domicile_District_lgd, 'Corr_Address', OLD.Corr_Address,
        'Corr_District_lgd', OLD.Corr_District_lgd, 'Corr_pincode', OLD.Corr_pincode, 'Perm_Address', OLD.Perm_Address,
        'Perm_District_lgd', OLD.Perm_District_lgd, 'Perm_pincode', OLD.Perm_pincode, 'sameAddress', OLD.sameAddress,
        'Document_Photo', OLD.Document_Photo, 'Document_Sign', OLD.Document_Sign, 'current_area', OLD.current_area,
        'current_block', OLD.current_block, 'current_gp', OLD.current_gp, 'current_village', OLD.current_village,'current_nagar', OLD.current_nagar,
        'current_ward', OLD.current_ward, 'Caste', OLD.Caste, 'Nationality', OLD.Nationality, 'Marital_Status', OLD.Marital_Status,
        'girlchild', OLD.girlchild, 'belowpl', OLD.belowpl, 'ncc', OLD.ncc, 'ecci', OLD.ecci, 'Is_Having_Disability', OLD.Is_Having_Disability,
        'domicile', OLD.domicile, 'speciality', OLD.speciality, 'Created_By', OLD.Created_By, 'Created_On', OLD.Created_On,
        'Last_Updated_By', OLD.Last_Updated_By, 'Last_Updated_On', OLD.Last_Updated_On, 'IP_Address', OLD.IP_Address,
        'Document_Aadhar', OLD.Document_Aadhar, 'Document_Caste', OLD.Document_Caste, 'Document_Domicile', OLD.Document_Domicile,
        'Document_5th', OLD.Document_5th, 'Document_8th', OLD.Document_8th, 'Document_other', OLD.Document_other,
        'Document_SSC', OLD.Document_SSC, 'Document_Inter', OLD.Document_Inter, 'Document_UG', OLD.Document_UG,
        'Document_PG', OLD.Document_PG, 'Document_BPL', OLD.Document_BPL, 'Document_Widow', OLD.Document_Widow,
        'Document_Exp', OLD.Document_Exp, 'Document_Epic', OLD.Document_Epic
    );

    SET new_values = JSON_OBJECT(
        'ID', NEW.ID, 'Applicant_ID', NEW.Applicant_ID, 'Pref_Districts', NEW.Pref_Districts, 'First_Name', NEW.First_Name,
        'Middle_Name', NEW.Middle_Name, 'Last_Name', NEW.Last_Name, 'firstName_hindi', NEW.firstName_hindi,
        'middleName_hindi', NEW.middleName_hindi, 'lastName_hindi', NEW.lastName_hindi, 'FatherName', NEW.FatherName,
        'MotherName', NEW.MotherName, 'DOB', NEW.DOB, 'Gender', NEW.Gender, 'Contact_Number', NEW.Contact_Number,
        'Email', NEW.Email, 'reference_no', NEW.reference_no, 'AdharConfirm', NEW.AdharConfirm, 'epicno', NEW.epicno,
        'identity_type', NEW.identity_type, 'Domicile_District_lgd', NEW.Domicile_District_lgd, 'Corr_Address', NEW.Corr_Address,
        'Corr_District_lgd', NEW.Corr_District_lgd, 'Corr_pincode', NEW.Corr_pincode, 'Perm_Address', NEW.Perm_Address,
        'Perm_District_lgd', NEW.Perm_District_lgd, 'Perm_pincode', NEW.Perm_pincode, 'sameAddress', NEW.sameAddress,
        'Document_Photo', NEW.Document_Photo, 'Document_Sign', NEW.Document_Sign, 'current_area', NEW.current_area,
        'current_block', NEW.current_block, 'current_gp', NEW.current_gp, 'current_village', NEW.current_village,'current_nagar', NEW.current_nagar,
        'current_ward', NEW.current_ward, 'Caste', NEW.Caste, 'Nationality', NEW.Nationality, 'Marital_Status', NEW.Marital_Status,
        'girlchild', NEW.girlchild, 'belowpl', NEW.belowpl, 'ncc', NEW.ncc, 'ecci', NEW.ecci, 'Is_Having_Disability', NEW.Is_Having_Disability,
        'domicile', NEW.domicile, 'speciality', NEW.speciality, 'Created_By', NEW.Created_By, 'Created_On', NEW.Created_On,
        'Last_Updated_By', NEW.Last_Updated_By, 'Last_Updated_On', NEW.Last_Updated_On, 'IP_Address', NEW.IP_Address,
        'Document_Aadhar', NEW.Document_Aadhar, 'Document_Caste', NEW.Document_Caste, 'Document_Domicile', NEW.Document_Domicile,
        'Document_5th', NEW.Document_5th, 'Document_8th', NEW.Document_8th, 'Document_other', NEW.Document_other,
        'Document_SSC', NEW.Document_SSC, 'Document_Inter', NEW.Document_Inter, 'Document_UG', NEW.Document_UG,
        'Document_PG', NEW.Document_PG, 'Document_BPL', NEW.Document_BPL, 'Document_Widow', NEW.Document_Widow,
        'Document_Exp', NEW.Document_Exp, 'Document_Epic', NEW.Document_Epic
    );

    INSERT INTO tbl_audit_user_detail (
        user_detail_id, changed_by, changed_on, action, old_data, new_data
    )
    VALUES (
        NEW.ID, NEW.Last_Updated_By, NOW(), 'UPDATE', old_values, new_values 
    );
END//
DELIMITER ;