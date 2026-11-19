<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stakeholder_survey_model extends CI_Model
{
    public function get_survey($tahun_id, $report_type)
    {
        return $this->db
            ->where('tahun_id', (int) $tahun_id)
            ->where('report_type', $report_type)
            ->where('is_active', 'Y')
            ->where('is_mandatory', 'Y')
            ->get('stakeholder_surveys')
            ->row_array();
    }

    public function get_student_class_id($student_id, $tahun_code)
    {
        $year = substr((string) $tahun_code, 0, 4);
        $row = $this->db
            ->select('id_kelas')
            ->where('id_siswa', (int) $student_id)
            ->where('ta', (int) $year)
            ->get('t_kelas_siswa')
            ->row_array();

        if (!$row) {
            $row = $this->db
                ->select('id_kelas')
                ->where('id_siswa', (int) $student_id)
                ->where('ta', (int) $year)
                ->get('t_kelas_siswa_reguler')
                ->row_array();
        }

        return $row ? (int) $row['id_kelas'] : null;
    }

    public function get_applicable_questions($survey_id, $class_id)
    {
        $this->db
            ->select('q.*')
            ->from('stakeholder_survey_questions q')
            ->where('q.survey_id', (int) $survey_id)
            ->where('q.is_active', 'Y')
            ->group_start()
                ->where('NOT EXISTS (SELECT 1 FROM stakeholder_survey_question_classes qc WHERE qc.question_id = q.id)', null, false)
                ->or_group_start()
                    ->where('EXISTS (SELECT 1 FROM stakeholder_survey_question_classes qc WHERE qc.question_id = q.id AND qc.class_id = ' . (int) $class_id . ')', null, false)
                ->group_end()
            ->group_end()
            ->order_by('q.sort_order', 'ASC')
            ->order_by('q.id', 'ASC');

        $questions = $this->db->get()->result_array();

        foreach ($questions as &$question) {
            if ($question['question_type'] === 'RADIO') {
                $question['options'] = $this->db
                    ->where('question_id', (int) $question['id'])
                    ->order_by('sort_order', 'ASC')
                    ->order_by('id', 'ASC')
                    ->get('stakeholder_survey_question_options')
                    ->result_array();
            } else {
                $question['options'] = array();
            }
        }

        return $questions;
    }

    public function is_completed($survey_id, $student_id)
    {
        return $this->db
            ->where('survey_id', (int) $survey_id)
            ->where('student_id', (int) $student_id)
            ->where('status', 'COMPLETED')
            ->count_all_results('stakeholder_survey_responses') > 0;
    }

    public function save_submission($survey_id, $parent_id, $student_id, $answers)
    {
        $survey = $this->db
            ->where('id', (int) $survey_id)
            ->where('is_active', 'Y')
            ->get('stakeholder_surveys')
            ->row_array();

        if (!$survey) {
            return array('ok' => false, 'error' => 'Survey tidak tersedia.');
        }

        $tahun = $this->db
            ->where('id', (int) $survey['tahun_id'])
            ->get('tahun')
            ->row_array();

        if (!$tahun) {
            return array('ok' => false, 'error' => 'Tahun ajaran tidak ditemukan.');
        }

        $class_id = $this->get_student_class_id($student_id, $tahun['tahun']);
        $questions = $this->get_applicable_questions($survey_id, $class_id);
        $errors = array();
        $clean_answers = array();

        foreach ($questions as $question) {
            $question_id = (int) $question['id'];
            $value = isset($answers[$question_id]) ? trim($answers[$question_id]) : '';

            if ($question['is_required'] === 'Y' && $value === '') {
                $errors[] = 'Pertanyaan wajib belum diisi.';
                continue;
            }

            if ($value === '') {
                continue;
            }

            if ($question['question_type'] === 'RATING') {
                $rating = (int) $value;
                if ($rating < 1 || $rating > 5) {
                    $errors[] = 'Rating harus bernilai 1 sampai 5.';
                    continue;
                }
                $value = (string) $rating;
            } elseif ($question['question_type'] === 'RADIO') {
                $option = $this->db
                    ->where('id', (int) $value)
                    ->where('question_id', $question_id)
                    ->get('stakeholder_survey_question_options')
                    ->row_array();
                if (!$option) {
                    $errors[] = 'Pilihan radio tidak valid.';
                    continue;
                }
            } elseif ($question['question_type'] === 'ESSAY') {
                if (!empty($question['max_length']) && strlen($value) > (int) $question['max_length']) {
                    $errors[] = 'Jawaban essay melebihi batas karakter.';
                    continue;
                }
            }

            $clean_answers[$question_id] = $value;
        }

        if (!empty($errors)) {
            return array('ok' => false, 'error' => implode(' ', array_unique($errors)));
        }

        $this->db->trans_start();

        $existing = $this->db
            ->where('survey_id', (int) $survey_id)
            ->where('student_id', (int) $student_id)
            ->get('stakeholder_survey_responses')
            ->row_array();

        if ($existing) {
            if ($existing['status'] === 'COMPLETED') {
                $this->db->trans_rollback();
                return array('ok' => false, 'error' => 'Survei ini sudah pernah dikirim.');
            }
            $response_id = (int) $existing['id'];
            $this->db
                ->where('id', $response_id)
                ->update('stakeholder_survey_responses', array(
                    'status' => 'COMPLETED',
                    'submitted_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ));
            $this->db->where('response_id', $response_id)->delete('stakeholder_survey_answers');
        } else {
            $now = date('Y-m-d H:i:s');
            $this->db->insert('stakeholder_survey_responses', array(
                'survey_id' => (int) $survey_id,
                'parent_id' => (int) $parent_id,
                'student_id' => (int) $student_id,
                'status' => 'COMPLETED',
                'submitted_at' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ));
            $response_id = (int) $this->db->insert_id();
        }

        foreach ($clean_answers as $question_id => $answer) {
            $this->db->insert('stakeholder_survey_answers', array(
                'response_id' => $response_id,
                'question_id' => (int) $question_id,
                'answer' => $answer,
                'created_at' => date('Y-m-d H:i:s')
            ));
        }

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            return array('ok' => false, 'error' => 'Survey gagal disimpan.');
        }

        return array('ok' => true);
    }
}
