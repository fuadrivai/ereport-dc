ALTER TABLE stakeholder_survey_responses
  DROP INDEX uq_completed_response,
  ADD UNIQUE KEY uq_completed_response (survey_id, student_id);
