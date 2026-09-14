ALTER TABLE `report_distribution_bookings`
    ADD COLUMN `google_calendar_event_id` VARCHAR(255) NULL AFTER `notes`,
    ADD COLUMN `gmeet_link` VARCHAR(1024) NULL AFTER `google_calendar_event_id`;

ALTER TABLE `report_distribution_bookings`
    ADD KEY `idx_report_distribution_google_calendar_event` (`google_calendar_event_id`);
