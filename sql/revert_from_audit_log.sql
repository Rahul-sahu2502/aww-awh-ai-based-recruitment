-- Revert a single audit_log entry by id
-- Usage: CALL revert_audit_log_entry(12345);

DELIMITER //

DROP PROCEDURE IF EXISTS revert_audit_log_entry //
CREATE PROCEDURE revert_audit_log_entry(IN p_audit_id BIGINT)
BEGIN
  DECLARE v_table VARCHAR(64);
  DECLARE v_action VARCHAR(10);
  DECLARE v_row_pk VARCHAR(255);
  DECLARE v_old JSON;
  DECLARE v_new JSON;

  SELECT table_name, action, row_pk, old_data, new_data
    INTO v_table, v_action, v_row_pk, v_old, v_new
  FROM audit_log
  WHERE id = p_audit_id;

  IF v_table = 'master_panchayats' THEN
    IF v_action = 'INSERT' THEN
      DELETE FROM master_panchayats
      WHERE panchayat_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_new, '$.panchayat_code')), 'null') AS SIGNED);

    ELSEIF v_action = 'UPDATE' THEN
      UPDATE master_panchayats
      SET
        panchayat_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_code')), 'null') AS SIGNED),
        panchayat_lgd_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_lgd_code')), 'null') AS SIGNED),
        panchayat_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_name')),
        panchayat_name_hin = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_name_hin')),
        block_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_code')), 'null') AS SIGNED),
        block_lgd_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_lgd_code')), 'null') AS SIGNED),
        block_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_name')),
        district_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_code')), 'null') AS SIGNED),
        district_lgd_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')), 'null') AS SIGNED),
        district_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_name'))
      WHERE panchayat_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_new, '$.panchayat_code')), 'null') AS SIGNED);

      IF ROW_COUNT() = 0 THEN
        UPDATE master_panchayats
        SET
          panchayat_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_code')), 'null') AS SIGNED),
          panchayat_lgd_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_lgd_code')), 'null') AS SIGNED),
          panchayat_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_name')),
          panchayat_name_hin = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_name_hin')),
          block_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_code')), 'null') AS SIGNED),
          block_lgd_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_lgd_code')), 'null') AS SIGNED),
          block_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_name')),
          district_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_code')), 'null') AS SIGNED),
          district_lgd_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')), 'null') AS SIGNED),
          district_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_name'))
        WHERE panchayat_code = CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_code')), 'null') AS SIGNED);
      END IF;

    ELSEIF v_action = 'DELETE' THEN
      INSERT INTO master_panchayats (
        panchayat_code, panchayat_lgd_code, panchayat_name, panchayat_name_hin,
        block_code, block_lgd_code, block_name,
        district_code, district_lgd_code, district_name
      ) VALUES (
        CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_code')), 'null') AS SIGNED),
        CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_lgd_code')), 'null') AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_name')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_name_hin')),
        CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_code')), 'null') AS SIGNED),
        CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_lgd_code')), 'null') AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_name')),
        CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_code')), 'null') AS SIGNED),
        CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')), 'null') AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_name'))
      );
    END IF;

  ELSEIF v_table = 'master_villages' THEN
    IF v_action = 'INSERT' THEN
      DELETE FROM master_villages
      WHERE village_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_new, '$.village_code')) AS SIGNED)
        AND panchayat_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_new, '$.panchayat_code')) AS SIGNED)
        AND block_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_new, '$.block_code')) AS SIGNED)
        AND district_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_new, '$.district_code')) AS SIGNED);

    ELSEIF v_action = 'UPDATE' THEN
      UPDATE master_villages
      SET
        village_lgd_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.village_lgd_code')) AS SIGNED),
        village_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.village_name')),
        village_name_hin = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.village_name_hin')),
        panchayat_lgd_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_lgd_code')) AS SIGNED),
        panchayat_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_name')),
        block_lgd_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_lgd_code')) AS SIGNED),
        block_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_name')),
        district_lgd_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')) AS SIGNED),
        district_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_name'))
      WHERE village_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.village_code')) AS SIGNED)
        AND panchayat_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_code')) AS SIGNED)
        AND block_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_code')) AS SIGNED)
        AND district_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_code')) AS SIGNED);

    ELSEIF v_action = 'DELETE' THEN
      INSERT INTO master_villages (
        village_code, village_lgd_code, village_name, village_name_hin,
        panchayat_code, panchayat_lgd_code, panchayat_name,
        block_code, block_lgd_code, block_name,
        district_code, district_lgd_code, district_name
      ) VALUES (
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.village_code')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.village_lgd_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.village_name')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.village_name_hin')),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_code')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_lgd_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.panchayat_name')),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_code')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_lgd_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block_name')),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_code')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_name'))
      );
    END IF;

  ELSEIF v_table = 'master_awcs' THEN
    IF v_action = 'INSERT' THEN
      DELETE FROM master_awcs
      WHERE id = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_new, '$.id')) AS SIGNED);

    ELSEIF v_action = 'UPDATE' THEN
      UPDATE master_awcs
      SET
        district = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district')),
        project_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.project_code')) AS SIGNED),
        project = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.project')),
        sector_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.sector_code')) AS SIGNED),
        sector = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.sector')),
        awc_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.awc_name')),
        awc_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.awc_code')) AS SIGNED),
        district_lgd_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')) AS SIGNED),
        district_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_code')) AS SIGNED),
        area = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.area')),
        gp_nnn_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.gp_nnn_code')) AS SIGNED),
        gram_ward_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.gram_ward_code')) AS SIGNED),
        block = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block')),
        is_under_nny = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.is_under_nny')),
        is_under_janman = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.is_under_janman')),
        awc_belong = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.awc_belong')),
        awc_type = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.awc_type')),
        latitude = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.latitude')),
        longitude = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.longitude')),
        building_ownership = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.building_ownership')),
        building_type = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.building_type')),
        toilet = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.toilet'))
      WHERE id = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.id')) AS SIGNED);

    ELSEIF v_action = 'DELETE' THEN
      INSERT INTO master_awcs (
        id, district, project_code, project, sector_code, sector, awc_name, awc_code,
        district_lgd_code, district_code, area, gp_nnn_code, gram_ward_code, block,
        is_under_nny, is_under_janman, awc_belong, awc_type, latitude, longitude,
        building_ownership, building_type, toilet
      ) VALUES (
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.id')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district')),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.project_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.project')),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.sector_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.sector')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.awc_name')),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.awc_code')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.area')),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.gp_nnn_code')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.gram_ward_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.block')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.is_under_nny')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.is_under_janman')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.awc_belong')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.awc_type')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.latitude')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.longitude')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.building_ownership')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.building_type')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.toilet'))
      );
    END IF;

  ELSEIF v_table = 'master_nnn' THEN
    IF v_action = 'INSERT' THEN
      DELETE FROM master_nnn
      WHERE std_nnn_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_new, '$.std_nnn_code')) AS SIGNED);

    ELSEIF v_action = 'UPDATE' THEN
      UPDATE master_nnn
      SET
        district_lgd_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')) AS SIGNED),
        std_nnn_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.std_nnn_code')) AS SIGNED),
        nnn_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.nnn_name')),
        nnn_name_en = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.nnn_name_en'))
      WHERE ID = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.ID')) AS SIGNED);

    ELSEIF v_action = 'DELETE' THEN
      INSERT INTO master_nnn (ID, district_lgd_code, std_nnn_code, nnn_name, nnn_name_en)
      VALUES (
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.ID')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.std_nnn_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.nnn_name')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.nnn_name_en'))
      );
    END IF;

  ELSEIF v_table = 'master_ward' THEN
    IF v_action = 'INSERT' THEN
      DELETE FROM master_ward
      WHERE ID = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_new, '$.ID')) AS SIGNED);

    ELSEIF v_action = 'UPDATE' THEN
      UPDATE master_ward
      SET
        district_lgd_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')) AS SIGNED),
        std_nnn_code = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.std_nnn_code')) AS SIGNED),
        nnn_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.nnn_name')),
        nnn_name_en = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.nnn_name_en')),
        ward_no = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.ward_no')) AS SIGNED),
        ward_name = JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.ward_name'))
      WHERE ID = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.ID')) AS SIGNED);

    ELSEIF v_action = 'DELETE' THEN
      INSERT INTO master_ward (ID, district_lgd_code, std_nnn_code, nnn_name, nnn_name_en, ward_no, ward_name)
      VALUES (
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.ID')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.district_lgd_code')) AS SIGNED),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.std_nnn_code')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.nnn_name')),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.nnn_name_en')),
        CAST(JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.ward_no')) AS SIGNED),
        JSON_UNQUOTE(JSON_EXTRACT(v_old, '$.ward_name'))
      );
    END IF;
  END IF;
END //

DROP PROCEDURE IF EXISTS revert_audit_log_range //
CREATE PROCEDURE revert_audit_log_range(IN p_from BIGINT, IN p_to BIGINT)
BEGIN
  DECLARE v_id BIGINT;
  DECLARE done INT DEFAULT 0;
  DECLARE cur CURSOR FOR
    SELECT id FROM audit_log WHERE id BETWEEN p_from AND p_to ORDER BY id DESC;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO v_id;
    IF done = 1 THEN
      LEAVE read_loop;
    END IF;
    CALL revert_audit_log_entry(v_id);
  END LOOP;
  CLOSE cur;
END //

DELIMITER ;
