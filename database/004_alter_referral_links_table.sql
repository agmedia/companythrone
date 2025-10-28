ALTER TABLE `referral_links`
    ADD `title` VARCHAR(255) NULL AFTER `label`,
    ADD `phone` VARCHAR(255) NULL AFTER `title`;
