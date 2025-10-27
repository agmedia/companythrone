ALTER TABLE `user_details` ADD `referral_code` VARCHAR(255) NULL AFTER `phone`, ADD `referral_code_used` TINYINT(1) NOT NULL DEFAULT '0' AFTER `referral_code`;
