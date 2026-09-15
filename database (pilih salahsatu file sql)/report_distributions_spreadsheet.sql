ALTER TABLE `report_distributions`
    ADD COLUMN `sheetId` VARCHAR(1024) NOT NULL AFTER `description`,
    ADD COLUMN `sheetName` VARCHAR(255) NOT NULL AFTER `sheetId`;