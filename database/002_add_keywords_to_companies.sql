ALTER TABLE `companies` ADD `keywords` VARCHAR(191) NULL AFTER `phone`;

ALTER TABLE `companies`
    ADD FULLTEXT INDEX `fulltext_keywords` (`keywords`);


