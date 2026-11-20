ALTER TABLE stakeholder_survey_questions
  MODIFY COLUMN question_type ENUM('RATING','RADIO','CHECKBOX','ESSAY') NOT NULL;
