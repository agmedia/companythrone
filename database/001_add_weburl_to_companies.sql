ALTER TABLE `companies` ADD `weburl` VARCHAR(255) NULL AFTER `email`;

ALTER TABLE `companies`

    ADD UNIQUE KEY `companies_weburl_unique` (`weburl`);
