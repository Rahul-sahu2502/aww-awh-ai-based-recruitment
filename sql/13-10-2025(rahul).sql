ALTER TABLE `master_user` ADD `is_password_change` ENUM('0','1')  NOT NULL  DEFAULT '0'  AFTER `admin_pic`;
