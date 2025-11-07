ALTER TABLE `referral_links`
    ADD `title` VARCHAR(255) NULL AFTER `label`,
    ADD `phone` VARCHAR(255) NULL AFTER `title`;
ADD COLUMN email varchar(255) AFTER user_id,
  ADD UNIQUE KEY referral_links_user_email_unique (user_id, email);
