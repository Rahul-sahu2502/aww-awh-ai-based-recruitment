-- ============================================================
-- Fix Foreign Key Constraints for Static Post Weightage System
-- Date: 17 December 2025
-- Purpose: Remove foreign key constraints from weightage tables
--          so they can work with master_post_config.id instead of master_post.post_id
-- ============================================================

USE recruitment_aww_awh;

-- 1. Drop foreign key from master_weightage_marks
ALTER TABLE `master_weightage_marks` 
DROP FOREIGN KEY IF EXISTS `fk_weightage_post`;

-- 2. Drop foreign key from master_qualification_marks
ALTER TABLE `master_qualification_marks` 
DROP FOREIGN KEY IF EXISTS `fk_post_id`;

-- 3. Drop foreign key from master_experience_weightage
ALTER TABLE `master_experience_weightage` 
DROP FOREIGN KEY IF EXISTS `fk_experience_post`;

-- 4. Drop foreign key from master_qualification_multiplier
ALTER TABLE `master_qualification_multiplier` 
DROP FOREIGN KEY IF EXISTS `fk_multiplier_post`;

-- 5. Drop foreign key from master_caste_marks
ALTER TABLE `master_caste_marks` 
DROP FOREIGN KEY IF EXISTS `fk_caste_marks_post`;

-- ============================================================
-- Note: After running this script, weightage tables will accept
-- post_id from both master_post and master_post_config tables
-- ============================================================

-- Verify the changes
SHOW CREATE TABLE master_weightage_marks;
SHOW CREATE TABLE master_qualification_marks;
SHOW CREATE TABLE master_experience_weightage;
SHOW CREATE TABLE master_qualification_multiplier;
SHOW CREATE TABLE master_caste_marks;
