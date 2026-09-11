CREATE TABLE IF NOT EXISTS `student_therapists` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `tahun_id` INT NOT NULL,
    `therapist_id` INT NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_student_therapists_student_year` (`student_id`, `tahun_id`),
    KEY `idx_student_therapists_year` (`tahun_id`),
    KEY `idx_student_therapists_therapist` (`therapist_id`),
    KEY `idx_student_therapists_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
