CREATE TABLE IF NOT EXISTS audit_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  table_name VARCHAR(64) NOT NULL,
  action ENUM('INSERT','UPDATE','DELETE') NOT NULL,
  row_pk VARCHAR(255) NULL,
  old_data JSON NULL,
  new_data JSON NULL,
  uid VARCHAR(64) NULL,
  app_route VARCHAR(255) NULL,
  app_action VARCHAR(255) NULL,
  changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_table_name (table_name),
  KEY idx_changed_at (changed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER //

DROP TRIGGER IF EXISTS trg_audit_master_panchayats_insert //
DROP TRIGGER IF EXISTS trg_audit_master_panchayats_update //
DROP TRIGGER IF EXISTS trg_audit_master_panchayats_delete //

CREATE TRIGGER trg_audit_master_panchayats_insert AFTER INSERT ON master_panchayats
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_panchayats',
    'INSERT',
    NEW.panchayat_code,
    NULL,
    JSON_OBJECT(
      'panchayat_code', NEW.panchayat_code,
      'panchayat_lgd_code', NEW.panchayat_lgd_code,
      'panchayat_name', NEW.panchayat_name,
      'panchayat_name_hin', NEW.panchayat_name_hin,
      'block_code', NEW.block_code,
      'block_lgd_code', NEW.block_lgd_code,
      'block_name', NEW.block_name,
      'district_code', NEW.district_code,
      'district_lgd_code', NEW.district_lgd_code,
      'district_name', NEW.district_name
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//

CREATE TRIGGER trg_audit_master_panchayats_update AFTER UPDATE ON master_panchayats
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_panchayats',
    'UPDATE',
    NEW.panchayat_code,
    JSON_OBJECT(
      'panchayat_code', OLD.panchayat_code,
      'panchayat_lgd_code', OLD.panchayat_lgd_code,
      'panchayat_name', OLD.panchayat_name,
      'panchayat_name_hin', OLD.panchayat_name_hin,
      'block_code', OLD.block_code,
      'block_lgd_code', OLD.block_lgd_code,
      'block_name', OLD.block_name,
      'district_code', OLD.district_code,
      'district_lgd_code', OLD.district_lgd_code,
      'district_name', OLD.district_name
    ),
    JSON_OBJECT(
      'panchayat_code', NEW.panchayat_code,
      'panchayat_lgd_code', NEW.panchayat_lgd_code,
      'panchayat_name', NEW.panchayat_name,
      'panchayat_name_hin', NEW.panchayat_name_hin,
      'block_code', NEW.block_code,
      'block_lgd_code', NEW.block_lgd_code,
      'block_name', NEW.block_name,
      'district_code', NEW.district_code,
      'district_lgd_code', NEW.district_lgd_code,
      'district_name', NEW.district_name
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//

CREATE TRIGGER trg_audit_master_panchayats_delete AFTER DELETE ON master_panchayats
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_panchayats',
    'DELETE',
    OLD.panchayat_code,
    JSON_OBJECT(
      'panchayat_code', OLD.panchayat_code,
      'panchayat_lgd_code', OLD.panchayat_lgd_code,
      'panchayat_name', OLD.panchayat_name,
      'panchayat_name_hin', OLD.panchayat_name_hin,
      'block_code', OLD.block_code,
      'block_lgd_code', OLD.block_lgd_code,
      'block_name', OLD.block_name,
      'district_code', OLD.district_code,
      'district_lgd_code', OLD.district_lgd_code,
      'district_name', OLD.district_name
    ),
    NULL,
    @app_user_id,
    @app_route,
    @app_action
  );
END//

DROP TRIGGER IF EXISTS trg_audit_master_villages_insert //
DROP TRIGGER IF EXISTS trg_audit_master_villages_update //
DROP TRIGGER IF EXISTS trg_audit_master_villages_delete //

CREATE TRIGGER trg_audit_master_villages_insert AFTER INSERT ON master_villages
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_villages',
    'INSERT',
    CONCAT(NEW.village_code, '|', NEW.panchayat_code, '|', NEW.block_code, '|', NEW.district_code),
    NULL,
    JSON_OBJECT(
      'village_code', NEW.village_code,
      'village_lgd_code', NEW.village_lgd_code,
      'village_name', NEW.village_name,
      'village_name_hin', NEW.village_name_hin,
      'panchayat_code', NEW.panchayat_code,
      'panchayat_lgd_code', NEW.panchayat_lgd_code,
      'panchayat_name', NEW.panchayat_name,
      'block_code', NEW.block_code,
      'block_lgd_code', NEW.block_lgd_code,
      'block_name', NEW.block_name,
      'district_code', NEW.district_code,
      'district_lgd_code', NEW.district_lgd_code,
      'district_name', NEW.district_name
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//

CREATE TRIGGER trg_audit_master_villages_update AFTER UPDATE ON master_villages
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_villages',
    'UPDATE',
    CONCAT(NEW.village_code, '|', NEW.panchayat_code, '|', NEW.block_code, '|', NEW.district_code),
    JSON_OBJECT(
      'village_code', OLD.village_code,
      'village_lgd_code', OLD.village_lgd_code,
      'village_name', OLD.village_name,
      'village_name_hin', OLD.village_name_hin,
      'panchayat_code', OLD.panchayat_code,
      'panchayat_lgd_code', OLD.panchayat_lgd_code,
      'panchayat_name', OLD.panchayat_name,
      'block_code', OLD.block_code,
      'block_lgd_code', OLD.block_lgd_code,
      'block_name', OLD.block_name,
      'district_code', OLD.district_code,
      'district_lgd_code', OLD.district_lgd_code,
      'district_name', OLD.district_name
    ),
    JSON_OBJECT(
      'village_code', NEW.village_code,
      'village_lgd_code', NEW.village_lgd_code,
      'village_name', NEW.village_name,
      'village_name_hin', NEW.village_name_hin,
      'panchayat_code', NEW.panchayat_code,
      'panchayat_lgd_code', NEW.panchayat_lgd_code,
      'panchayat_name', NEW.panchayat_name,
      'block_code', NEW.block_code,
      'block_lgd_code', NEW.block_lgd_code,
      'block_name', NEW.block_name,
      'district_code', NEW.district_code,
      'district_lgd_code', NEW.district_lgd_code,
      'district_name', NEW.district_name
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//

CREATE TRIGGER trg_audit_master_villages_delete AFTER DELETE ON master_villages
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_villages',
    'DELETE',
    CONCAT(OLD.village_code, '|', OLD.panchayat_code, '|', OLD.block_code, '|', OLD.district_code),
    JSON_OBJECT(
      'village_code', OLD.village_code,
      'village_lgd_code', OLD.village_lgd_code,
      'village_name', OLD.village_name,
      'village_name_hin', OLD.village_name_hin,
      'panchayat_code', OLD.panchayat_code,
      'panchayat_lgd_code', OLD.panchayat_lgd_code,
      'panchayat_name', OLD.panchayat_name,
      'block_code', OLD.block_code,
      'block_lgd_code', OLD.block_lgd_code,
      'block_name', OLD.block_name,
      'district_code', OLD.district_code,
      'district_lgd_code', OLD.district_lgd_code,
      'district_name', OLD.district_name
    ),
    NULL,
    @app_user_id,
    @app_route,
    @app_action
  );
END//

DROP TRIGGER IF EXISTS trg_audit_master_awcs_insert //
DROP TRIGGER IF EXISTS trg_audit_master_awcs_update //
DROP TRIGGER IF EXISTS trg_audit_master_awcs_delete //

CREATE TRIGGER trg_audit_master_awcs_insert AFTER INSERT ON master_awcs
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_awcs',
    'INSERT',
    NEW.id,
    NULL,
    JSON_OBJECT(
      'id', NEW.id,
      'district', NEW.district,
      'project_code', NEW.project_code,
      'project', NEW.project,
      'sector_code', NEW.sector_code,
      'sector', NEW.sector,
      'awc_name', NEW.awc_name,
      'awc_code', NEW.awc_code,
      'district_lgd_code', NEW.district_lgd_code,
      'district_code', NEW.district_code,
      'area', NEW.area,
      'gp_nnn_code', NEW.gp_nnn_code,
      'gram_ward_code', NEW.gram_ward_code,
      'block', NEW.block,
      'is_under_nny', NEW.is_under_nny,
      'is_under_janman', NEW.is_under_janman,
      'awc_belong', NEW.awc_belong,
      'awc_type', NEW.awc_type,
      'latitude', NEW.latitude,
      'longitude', NEW.longitude,
      'building_ownership', NEW.building_ownership,
      'building_type', NEW.building_type,
      'toilet', NEW.toilet
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//

CREATE TRIGGER trg_audit_master_awcs_update AFTER UPDATE ON master_awcs
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_awcs',
    'UPDATE',
    NEW.id,
    JSON_OBJECT(
      'id', OLD.id,
      'district', OLD.district,
      'project_code', OLD.project_code,
      'project', OLD.project,
      'sector_code', OLD.sector_code,
      'sector', OLD.sector,
      'awc_name', OLD.awc_name,
      'awc_code', OLD.awc_code,
      'district_lgd_code', OLD.district_lgd_code,
      'district_code', OLD.district_code,
      'area', OLD.area,
      'gp_nnn_code', OLD.gp_nnn_code,
      'gram_ward_code', OLD.gram_ward_code,
      'block', OLD.block,
      'is_under_nny', OLD.is_under_nny,
      'is_under_janman', OLD.is_under_janman,
      'awc_belong', OLD.awc_belong,
      'awc_type', OLD.awc_type,
      'latitude', OLD.latitude,
      'longitude', OLD.longitude,
      'building_ownership', OLD.building_ownership,
      'building_type', OLD.building_type,
      'toilet', OLD.toilet
    ),
    JSON_OBJECT(
      'id', NEW.id,
      'district', NEW.district,
      'project_code', NEW.project_code,
      'project', NEW.project,
      'sector_code', NEW.sector_code,
      'sector', NEW.sector,
      'awc_name', NEW.awc_name,
      'awc_code', NEW.awc_code,
      'district_lgd_code', NEW.district_lgd_code,
      'district_code', NEW.district_code,
      'area', NEW.area,
      'gp_nnn_code', NEW.gp_nnn_code,
      'gram_ward_code', NEW.gram_ward_code,
      'block', NEW.block,
      'is_under_nny', NEW.is_under_nny,
      'is_under_janman', NEW.is_under_janman,
      'awc_belong', NEW.awc_belong,
      'awc_type', NEW.awc_type,
      'latitude', NEW.latitude,
      'longitude', NEW.longitude,
      'building_ownership', NEW.building_ownership,
      'building_type', NEW.building_type,
      'toilet', NEW.toilet
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//

CREATE TRIGGER trg_audit_master_awcs_delete AFTER DELETE ON master_awcs
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_awcs',
    'DELETE',
    OLD.id,
    JSON_OBJECT(
      'id', OLD.id,
      'district', OLD.district,
      'project_code', OLD.project_code,
      'project', OLD.project,
      'sector_code', OLD.sector_code,
      'sector', OLD.sector,
      'awc_name', OLD.awc_name,
      'awc_code', OLD.awc_code,
      'district_lgd_code', OLD.district_lgd_code,
      'district_code', OLD.district_code,
      'area', OLD.area,
      'gp_nnn_code', OLD.gp_nnn_code,
      'gram_ward_code', OLD.gram_ward_code,
      'block', OLD.block,
      'is_under_nny', OLD.is_under_nny,
      'is_under_janman', OLD.is_under_janman,
      'awc_belong', OLD.awc_belong,
      'awc_type', OLD.awc_type,
      'latitude', OLD.latitude,
      'longitude', OLD.longitude,
      'building_ownership', OLD.building_ownership,
      'building_type', OLD.building_type,
      'toilet', OLD.toilet
    ),
    NULL,
    @app_user_id,
    @app_route,
    @app_action
  );
END//

DROP TRIGGER IF EXISTS trg_audit_master_nnn_insert //
DROP TRIGGER IF EXISTS trg_audit_master_nnn_update //
DROP TRIGGER IF EXISTS trg_audit_master_nnn_delete //
DELIMITER //
CREATE TRIGGER trg_audit_master_nnn_insert AFTER INSERT ON master_nnn
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_nnn',
    'INSERT',
    NEW.std_nnn_code,
    NULL,
    JSON_OBJECT(
      'ID', NEW.ID,
      'district_lgd_code', NEW.district_lgd_code,
      'std_nnn_code', NEW.std_nnn_code,
      'nnn_name', NEW.nnn_name,
      'nnn_name_en', NEW.nnn_name_en
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//
DELIMITER ;
DELIMITER //
CREATE TRIGGER trg_audit_master_nnn_update AFTER UPDATE ON master_nnn
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_nnn',
    'UPDATE',
    NEW.std_nnn_code,
    JSON_OBJECT(
      'ID', OLD.ID,
      'district_lgd_code', OLD.district_lgd_code,
      'std_nnn_code', OLD.std_nnn_code,
      'nnn_name', OLD.nnn_name,
      'nnn_name_en', OLD.nnn_name_en
    ),
    JSON_OBJECT(
      'ID', NEW.ID,
      'district_lgd_code', NEW.district_lgd_code,
      'std_nnn_code', NEW.std_nnn_code,
      'nnn_name', NEW.nnn_name,
      'nnn_name_en', NEW.nnn_name_en
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//
DELIMITER ;
DELIMITER //
CREATE TRIGGER trg_audit_master_nnn_delete AFTER DELETE ON master_nnn
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_nnn',
    'DELETE',
    OLD.std_nnn_code,
    JSON_OBJECT(
      'ID', OLD.ID,
      'district_lgd_code', OLD.district_lgd_code,
      'std_nnn_code', OLD.std_nnn_code,
      'nnn_name', OLD.nnn_name,
      'nnn_name_en', OLD.nnn_name_en
    ),
    NULL,
    @app_user_id,
    @app_route,
    @app_action
  );
END//
DELIMITER ;
DROP TRIGGER IF EXISTS trg_audit_master_ward_insert //
DROP TRIGGER IF EXISTS trg_audit_master_ward_update //
DROP TRIGGER IF EXISTS trg_audit_master_ward_delete //
DELIMITER //
CREATE TRIGGER trg_audit_master_ward_insert AFTER INSERT ON master_ward
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_ward',
    'INSERT',
    NEW.ID,
    NULL,
    JSON_OBJECT(
      'ID', NEW.ID,
      'district_lgd_code', NEW.district_lgd_code,
      'std_nnn_code', NEW.std_nnn_code,
      'nnn_name', NEW.nnn_name,
      'nnn_name_en', NEW.nnn_name_en,
      'ward_no', NEW.ward_no,
      'ward_name', NEW.ward_name
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//
DELIMITER ;
DELIMITER //
CREATE TRIGGER trg_audit_master_ward_update AFTER UPDATE ON master_ward
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_ward',
    'UPDATE',
    NEW.ID,
    JSON_OBJECT(
      'ID', OLD.ID,
      'district_lgd_code', OLD.district_lgd_code,
      'std_nnn_code', OLD.std_nnn_code,
      'nnn_name', OLD.nnn_name,
      'nnn_name_en', OLD.nnn_name_en,
      'ward_no', OLD.ward_no,
      'ward_name', OLD.ward_name
    ),
    JSON_OBJECT(
      'ID', NEW.ID,
      'district_lgd_code', NEW.district_lgd_code,
      'std_nnn_code', NEW.std_nnn_code,
      'nnn_name', NEW.nnn_name,
      'nnn_name_en', NEW.nnn_name_en,
      'ward_no', NEW.ward_no,
      'ward_name', NEW.ward_name
    ),
    @app_user_id,
    @app_route,
    @app_action
  );
END//
DELIMITER ;
DELIMITER //
CREATE TRIGGER trg_audit_master_ward_delete AFTER DELETE ON master_ward
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (table_name, action, row_pk, old_data, new_data, uid, app_route, app_action)
  VALUES (
    'master_ward',
    'DELETE',
    OLD.ID,
    JSON_OBJECT(
      'ID', OLD.ID,
      'district_lgd_code', OLD.district_lgd_code,
      'std_nnn_code', OLD.std_nnn_code,
      'nnn_name', OLD.nnn_name,
      'nnn_name_en', OLD.nnn_name_en,
      'ward_no', OLD.ward_no,
      'ward_name', OLD.ward_name
    ),
    NULL,
    @app_user_id,
    @app_route,
    @app_action
  );
END//
DELIMITER ;
