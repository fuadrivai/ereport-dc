<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stakeholder_survey extends CI_Controller
{
    private $sespre;

    function __construct()
    {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');
        $this->d = array();
        $this->d['admlevel'] = $this->session->userdata($this->sespre . 'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre . 'konid');
        $this->d['url'] = 'stakeholder_survey';
        $this->load->model('stakeholder_survey/Stakeholder_survey_model', 'survey');
    }

    private function require_admin()
    {
        $level = $this->session->userdata($this->sespre . 'level');
        if ($level !== 'admin') {
            redirect('unauthorized_access');
            exit;
        }
    }

    private function require_student()
    {
        $level = $this->session->userdata($this->sespre . 'level');
        $student_id = (int) $this->session->userdata($this->sespre . 'konid');
        if ($level !== 'siswa' || $student_id < 1) {
            redirect('login');
            exit;
        }
        return $student_id;
    }

    public function index()
    {
        $this->require_admin();
        $this->d['p'] = 'admin/index';
        $this->d['url'] = 'stakeholder_survey';
        $this->d['nama_form'] = 'f_stakeholder_survey';
        $this->d['tahun'] = $this->db->order_by('tahun', 'ASC')->get('tahun')->result_array();
        $this->d['classes'] = $this->db->order_by('tingkat', 'ASC')->order_by('nama', 'ASC')->get('m_kelas')->result_array();
        $this->d['surveys'] = $this->db
            ->select('s.*, t.tahun')
            ->from('stakeholder_surveys s')
            ->join('tahun t', 't.id = s.tahun_id')
            ->order_by('t.tahun', 'DESC')
            ->order_by('s.report_type', 'ASC')
            ->get()
            ->result_array();
        $this->load->view('template_utama', $this->d);
    }

    public function datatable()
    {
        $this->require_admin();
        $tahun_id = (int) $this->input->get_post('tahun_id');
        $this->db->select('s.*, t.tahun');
        $this->db->from('stakeholder_surveys s');
        $this->db->join('tahun t', 't.id = s.tahun_id');
        if ($tahun_id > 0) {
            $this->db->where('s.tahun_id', $tahun_id);
        }
        $this->db->order_by('t.tahun', 'DESC')->order_by('s.report_type', 'ASC');
        $rows = $this->db->get()->result_array();

        $data = array();
        foreach ($rows as $row) {
            $data[] = array(
                $row['tahun'] . ' - ' . $row['report_type'],
                htmlspecialchars($row['title']),
                $row['is_mandatory'] === 'Y' ? 'Yes' : 'No',
                $row['is_active'] === 'Y' ? 'Active' : 'Inactive',
                '<a class="btn btn-xs btn-info" href="' . base_url('stakeholder_survey/edit/' . $row['id']) . '">Manage</a> '
                . '<a class="btn btn-xs btn-success" href="' . base_url('stakeholder_survey/results/' . $row['id']) . '">Results</a>'
            );
        }
        j(array('data' => $data));
    }

    public function edit($survey_id = 0)
    {
        $this->require_admin();
        $survey_id = (int) $survey_id;
        $survey = $survey_id > 0
            ? $this->db->where('id', $survey_id)->get('stakeholder_surveys')->row_array()
            : array();

        $this->d['p'] = 'admin/edit';
        $this->d['url'] = 'stakeholder_survey';
        $this->d['survey'] = $survey;
        $this->d['tahun'] = $this->db->order_by('tahun', 'DESC')->get('tahun')->result_array();
        $this->d['classes'] = $this->db->order_by('tingkat', 'ASC')->order_by('nama', 'ASC')->get('m_kelas')->result_array();
        $this->d['questions'] = $survey_id > 0
            ? $this->db->where('survey_id', $survey_id)->order_by('sort_order', 'ASC')->order_by('id', 'ASC')->get('stakeholder_survey_questions')->result_array()
            : array();
        $this->d['options'] = array();
        $this->d['targets'] = array();
        foreach ($this->d['questions'] as &$question) {
            $question_id = (int) $question['id'];
            $this->d['options'][$question_id] = $this->db->where('question_id', $question_id)->order_by('sort_order', 'ASC')->get('stakeholder_survey_question_options')->result_array();
            $target_rows = $this->db->select('class_id')->where('question_id', $question_id)->get('stakeholder_survey_question_classes')->result_array();
            $this->d['targets'][$question_id] = array();
            foreach ($target_rows as $target) {
                $this->d['targets'][$question_id][] = (int) $target['class_id'];
            }
        }
        $this->load->view('template_utama', $this->d);
    }

    public function save_survey()
    {
        $this->require_admin();
        $id = (int) $this->input->post('id');
        $tahun_id = (int) $this->input->post('tahun_id');
        $report_type = strtoupper(trim($this->input->post('report_type')));

        if ($tahun_id < 1 || !in_array($report_type, array('MIDTERM', 'FINAL'), true)) {
            j(array('status' => 'failed', 'data' => 'Invalid survey data.'));
            return;
        }

        $existing = $this->db
            ->where('tahun_id', $tahun_id)
            ->where('report_type', $report_type)
            ->where('id !=', $id)
            ->get('stakeholder_surveys')
            ->row_array();
        if ($existing) {
            j(array('status' => 'failed', 'data' => 'A survey already exists for this academic year and report type.'));
            return;
        }

        $now = date('Y-m-d H:i:s');
        $data = array(
            'tahun_id' => $tahun_id,
            'report_type' => $report_type,
            'title' => trim($this->input->post('title')),
            'description' => trim($this->input->post('description')),
            'is_mandatory' => $this->input->post('is_mandatory') === 'Y' ? 'Y' : 'N',
            'is_active' => $this->input->post('is_active') === 'Y' ? 'Y' : 'N',
            'updated_at' => $now
        );

        if ($id > 0) {
            $this->db->where('id', $id)->update('stakeholder_surveys', $data);
        } else {
            $data['created_by'] = (int) $this->session->userdata($this->sespre . 'id');
            $data['created_at'] = $now;
            $this->db->insert('stakeholder_surveys', $data);
            $id = (int) $this->db->insert_id();
        }

        j(array('status' => 'ok', 'data' => 'Survey saved successfully.', 'id' => $id));
    }

    public function save_question()
    {
        $this->require_admin();
        $survey_id = (int) $this->input->post('survey_id');
        $question_id = (int) $this->input->post('question_id');
        $type = strtoupper(trim($this->input->post('question_type')));
        if (!in_array($type, array('RATING', 'RADIO', 'ESSAY'), true) || $survey_id < 1) {
            j(array('status' => 'failed', 'data' => 'Invalid question data.'));
            return;
        }

        if ($question_id > 0) {
            $question = $this->db
                ->where('id', $question_id)
                ->where('survey_id', $survey_id)
                ->get('stakeholder_survey_questions')
                ->row_array();
            if (!$question) {
                j(array('status' => 'failed', 'data' => 'Question not found.'));
                return;
            }

            $has_answers = $this->db
                ->where('question_id', $question_id)
                ->count_all_results('stakeholder_survey_answers') > 0;
            if ($has_answers) {
                j(array('status' => 'failed', 'data' => 'This question cannot be changed because it already has answers.'));
                return;
            }
        }

        $now = date('Y-m-d H:i:s');
        $data = array(
            'survey_id' => $survey_id,
            'question' => trim($this->input->post('question')),
            'question_type' => $type,
            'is_required' => $this->input->post('is_required') === 'Y' ? 'Y' : 'N',
            'max_length' => $type === 'ESSAY' ? ((int) $this->input->post('max_length') > 0 ? (int) $this->input->post('max_length') : null) : null,
            'sort_order' => (int) $this->input->post('sort_order'),
            'is_active' => $this->input->post('is_active') === 'Y' ? 'Y' : 'N',
            'updated_at' => $now
        );

        if ($question_id > 0) {
            $this->db->where('id', $question_id)->update('stakeholder_survey_questions', $data);
        } else {
            $data['created_at'] = $now;
            $this->db->insert('stakeholder_survey_questions', $data);
            $question_id = (int) $this->db->insert_id();
        }

        $this->db->where('question_id', $question_id)->delete('stakeholder_survey_question_classes');
        $target_all = $this->input->post('target_all');
        if ($target_all !== 'Y') {
            $class_ids = (array) $this->input->post('class_ids');
            foreach ($class_ids as $class_id) {
                $class_id = (int) $class_id;
                if ($class_id > 0) {
                    $this->db->insert('stakeholder_survey_question_classes', array('question_id' => $question_id, 'class_id' => $class_id));
                }
            }
        }

        $this->db->where('question_id', $question_id)->delete('stakeholder_survey_question_options');
        if ($type === 'RADIO') {
            $labels = (array) $this->input->post('option_label');
            foreach ($labels as $index => $label) {
                $label = trim($label);
                if ($label !== '') {
                    $this->db->insert('stakeholder_survey_question_options', array(
                        'question_id' => $question_id,
                        'option_label' => $label,
                        'sort_order' => $index,
                        'created_at' => $now,
                        'updated_at' => $now
                    ));
                }
            }
        }

        j(array('status' => 'ok', 'data' => 'Question saved successfully.'));
    }

    public function delete_question($question_id)
    {
        $this->require_admin();
        $question_id = (int) $question_id;
        $has_answers = $this->db
            ->where('question_id', $question_id)
            ->count_all_results('stakeholder_survey_answers') > 0;
        if ($has_answers) {
            j(array('status' => 'failed', 'data' => 'This question cannot be deleted because it already has answers.'));
            return;
        }
        $this->db->where('id', $question_id)->delete('stakeholder_survey_questions');
        j(array('status' => 'ok', 'data' => 'Question deleted successfully.'));
    }

    public function preview($survey_id)
    {
        $this->require_admin();
        $survey_id = (int) $survey_id;
        $survey = $this->db->where('id', $survey_id)->get('stakeholder_surveys')->row_array();
        if (!$survey) {
            show_404();
            return;
        }
        $questions = $this->db->where('survey_id', $survey_id)->where('is_active', 'Y')->order_by('sort_order', 'ASC')->get('stakeholder_survey_questions')->result_array();
        foreach ($questions as &$question) {
            $question['options'] = $this->db->where('question_id', $question['id'])->order_by('sort_order', 'ASC')->get('stakeholder_survey_question_options')->result_array();
            $targets = $this->db->select('c.nama')->join('m_kelas c', 'c.id = qc.class_id')->where('qc.question_id', $question['id'])->get('stakeholder_survey_question_classes qc')->result_array();
            $question['targets'] = $targets;
        }
        $this->d['p'] = 'admin/preview';
        $this->d['survey'] = $survey;
        $this->d['questions'] = $questions;
        $this->load->view('template_utama', $this->d);
    }

    public function results($survey_id)
    {
        $this->require_admin();
        $survey_id = (int) $survey_id;
        $survey = $this->db->where('id', $survey_id)->get('stakeholder_surveys')->row_array();
        if (!$survey) {
            show_404();
            return;
        }

        $tahun = $this->db->where('id', (int) $survey['tahun_id'])->get('tahun')->row_array();
        $questions = $this->db->where('survey_id', $survey_id)->where('is_active', 'Y')->order_by('sort_order', 'ASC')->get('stakeholder_survey_questions')->result_array();
        $completed = $this->db->where('survey_id', $survey_id)->where('status', 'COMPLETED')->get('stakeholder_survey_responses')->result_array();
        $results = array();

        foreach ($questions as $question) {
            $row = array('question' => $question, 'total' => 0, 'summary' => array());
            $answers = $this->db
                ->join('stakeholder_survey_responses r', 'r.id = a.response_id')
                ->where('a.question_id', (int) $question['id'])
                ->where('r.status', 'COMPLETED')
                ->get('stakeholder_survey_answers a')
                ->result_array();
            $row['total'] = count($answers);

            if ($question['question_type'] === 'RATING') {
                $distribution = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
                $sum = 0;
                foreach ($answers as $answer) {
                    $value = (int) $answer['answer'];
                    if ($value >= 1 && $value <= 5) {
                        $distribution[$value]++;
                        $sum += $value;
                    }
                }
                $row['summary'] = array('distribution' => $distribution, 'average' => $row['total'] > 0 ? round($sum / $row['total'], 2) : 0);
            } elseif ($question['question_type'] === 'RADIO') {
                $options = $this->db->where('question_id', (int) $question['id'])->order_by('sort_order', 'ASC')->get('stakeholder_survey_question_options')->result_array();
                foreach ($options as $option) {
                    $count = 0;
                    foreach ($answers as $answer) {
                        if ((int) $answer['answer'] === (int) $option['id']) {
                            $count++;
                        }
                    }
                    $row['summary'][] = array('label' => $option['option_label'], 'count' => $count);
                }
            } else {
                $row['summary'] = array_column($answers, 'answer');
            }
            $results[] = $row;
        }

        $this->d['p'] = 'admin/results';
        $this->d['survey'] = $survey;
        $this->d['tahun'] = $tahun;
        $this->d['completed'] = $completed;
        $this->d['results'] = $results;
        $this->load->view('template_utama', $this->d);
    }

    public function export($survey_id)
    {
        $this->require_admin();
        $survey_id = (int) $survey_id;
        $this->load->library('Excel');
        $survey = $this->db->where('id', $survey_id)->get('stakeholder_surveys')->row_array();
        $responses = $this->db
            ->select('r.*, s.nama AS student_name')
            ->from('stakeholder_survey_responses r')
            ->join('m_siswa s', 's.id = r.student_id')
            ->where('r.survey_id', $survey_id)
            ->where('r.status', 'COMPLETED')
            ->get()
            ->result_array();
        $questions = $this->db->where('survey_id', $survey_id)->order_by('sort_order', 'ASC')->get('stakeholder_survey_questions')->result_array();

        $this->excel->setActiveSheetIndex(0);
        $sheet = $this->excel->getActiveSheet();
        $headers = array_merge(array('Student', 'Submitted At'), array_column($questions, 'question'));
        foreach ($headers as $column => $header) {
            $sheet->setCellValueByColumnAndRow($column, 1, $header);
        }
        foreach ($responses as $row_index => $response) {
            $sheet->setCellValueByColumnAndRow(0, $row_index + 2, $response['student_name']);
            $sheet->setCellValueByColumnAndRow(1, $row_index + 2, $response['submitted_at']);
            foreach ($questions as $question_index => $question) {
                $answer = $this->db
                    ->where('response_id', (int) $response['id'])
                    ->where('question_id', (int) $question['id'])
                    ->get('stakeholder_survey_answers')
                    ->row_array();
                $sheet->setCellValueByColumnAndRow($question_index + 2, $row_index + 2, $answer ? $answer['answer'] : '');
            }
        }

        $filename = 'survey-' . $survey_id . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $this->excel->getActiveSheet()->setTitle('Survey Results');
        $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    public function form($survey_id, $report_type, $student_id)
    {
        $logged_student_id = $this->require_student();
        $survey_id = (int) $survey_id;
        $report_type = strtoupper($report_type);
        $student_id = (int) $student_id;
        if ($student_id !== $logged_student_id) {
            show_error('You do not have access to this student survey.', 403);
            return;
        }

        $survey = $this->db
            ->where('id', $survey_id)
            ->where('report_type', $report_type)
            ->where('is_active', 'Y')
            ->where('is_mandatory', 'Y')
            ->get('stakeholder_surveys')
            ->row_array();
        if (!$survey) {
            show_404();
            return;
        }
        $tahun = $this->db->where('id', (int) $survey['tahun_id'])->get('tahun')->row_array();
        if (!$tahun) {
            show_404();
            return;
        }
        if ($this->survey->is_completed($survey_id, $student_id)) {
            $redirect_url = $this->session->userdata('stakeholder_survey_redirect');
            if ($redirect_url) {
                redirect($redirect_url);
            } else {
                redirect('lihat_raport');
            }
            return;
        }

        $class_id = $this->survey->get_student_class_id($student_id, $tahun['tahun']);
        $questions = $this->survey->get_applicable_questions($survey['id'], $class_id);

        $this->d['p'] = 'parent/form';
        $this->d['survey'] = $survey;
        $this->d['tahun'] = $tahun;
        $this->d['student_id'] = $student_id;
        $this->d['student'] = $this->db->select('id, nama')->where('id', $student_id)->get('m_siswa')->row_array();
        $this->d['questions'] = $questions;
        $this->d['report_type'] = $report_type;
        $this->d['survey_error'] = $this->session->flashdata('survey_error');
        $this->d['old_answers'] = $this->session->flashdata('survey_answers');
        $this->load->view('parent/form', $this->d);
    }

    public function submit($survey_id)
    {
        $student_id = $this->require_student();
        $parent_id = (int) $this->session->userdata($this->sespre . 'id');
        $result = $this->survey->save_submission((int) $survey_id, $parent_id, $student_id, (array) $this->input->post('answers'));

        if (!$result['ok']) {
            $this->session->set_flashdata('survey_error', $result['error']);
            $this->session->set_flashdata('survey_answers', (array) $this->input->post('answers'));
            redirect('stakeholder_survey/form/' . $survey_id . '/' . $this->input->post('report_type') . '/' . $student_id);
            return;
        }

        $redirect_url = $this->session->userdata('stakeholder_survey_redirect');
        $this->session->unset_userdata('stakeholder_survey_redirect');
        if ($redirect_url) {
            redirect($redirect_url);
            return;
        }
        redirect('lihat_raport');
    }

    public function check($student_id, $tahun_code, $report_type)
    {
        $student_id = $this->require_student();
        $tahun_code = (string) $tahun_code;
        $report_type = strtoupper($report_type);
        $tahun = $this->db->where('tahun', $tahun_code)->get('tahun')->row_array();
        if (!$tahun) {
            return false;
        }
        $survey = $this->survey->get_survey((int) $tahun['id'], $report_type);
        if (!$survey) {
            return true;
        }
        if ($this->survey->is_completed((int) $survey['id'], $student_id)) {
            return true;
        }

        $this->session->set_userdata('stakeholder_survey_redirect', current_url());
        redirect('stakeholder_survey/form/' . $survey['id'] . '/' . $report_type . '/' . $student_id);
        exit;
    }
}
