ALTER TABLE `report_distribution_bookings`
    ADD COLUMN `report_collection_method` ENUM('ONLINE', 'ONSITE') NOT NULL DEFAULT 'ONSITE' AFTER `booking_type`;