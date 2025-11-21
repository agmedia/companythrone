ALTER TABLE `users`
    ADD COLUMN `last_reminder_sent` TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN `daily_reminder_opt_out` TINYINT(1) NOT NULL DEFAULT 0;
