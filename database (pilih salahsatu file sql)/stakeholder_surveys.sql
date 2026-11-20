CREATE TABLE IF NOT EXISTS stakeholder_surveys (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tahun_id INT NOT NULL,
  report_type ENUM('MIDTERM','FINAL') NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT NULL,
  is_mandatory ENUM('Y','N') NOT NULL DEFAULT 'Y',
  is_active ENUM('Y','N') NOT NULL DEFAULT 'N',
  created_by INT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_survey_period_type (tahun_id, report_type),
  KEY idx_survey_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS stakeholder_survey_questions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  survey_id INT UNSIGNED NOT NULL,
  question TEXT NOT NULL,
  question_type ENUM('RATING','RADIO','CHECKBOX','ESSAY') NOT NULL,
  is_required ENUM('Y','N') NOT NULL DEFAULT 'Y',
  max_length INT UNSIGNED NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_active ENUM('Y','N') NOT NULL DEFAULT 'Y',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_question_survey_order (survey_id, sort_order),
  CONSTRAINT fk_survey_question FOREIGN KEY (survey_id) REFERENCES stakeholder_surveys(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS stakeholder_survey_question_options (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  question_id INT UNSIGNED NOT NULL,
  option_label VARCHAR(255) NOT NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_option_question_order (question_id, sort_order),
  CONSTRAINT fk_survey_question_option FOREIGN KEY (question_id) REFERENCES stakeholder_survey_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS stakeholder_survey_question_classes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  question_id INT UNSIGNED NOT NULL,
  class_id INT NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_question_class (question_id, class_id),
  CONSTRAINT fk_survey_question_class FOREIGN KEY (question_id) REFERENCES stakeholder_survey_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS stakeholder_survey_responses (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  survey_id INT UNSIGNED NOT NULL,
  parent_id INT UNSIGNED NOT NULL,
  student_id INT UNSIGNED NOT NULL,
  status ENUM('DRAFT','COMPLETED') NOT NULL DEFAULT 'DRAFT',
  submitted_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_completed_response (survey_id, student_id),
  KEY idx_response_status (survey_id, status),
  CONSTRAINT fk_survey_response FOREIGN KEY (survey_id) REFERENCES stakeholder_surveys(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS stakeholder_survey_answers (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  response_id INT UNSIGNED NOT NULL,
  question_id INT UNSIGNED NOT NULL,
  answer TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_response_question (response_id, question_id),
  KEY idx_answer_question (question_id),
  CONSTRAINT fk_survey_response_answer FOREIGN KEY (response_id) REFERENCES stakeholder_survey_responses(id) ON DELETE CASCADE,
  CONSTRAINT fk_survey_answer_question FOREIGN KEY (question_id) REFERENCES stakeholder_survey_questions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

