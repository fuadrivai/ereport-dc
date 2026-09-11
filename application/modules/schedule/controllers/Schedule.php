<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Schedule extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre . 'level');
        $this->d['url'] = "schedule";
        $this->d['idnya'] = "id_schedule";
        $this->d['nama_form'] = "f_schedule";
    }

    public function therapist()
    {
        $data = $this->db->query("SELECT * FROM therapists")->result_array();
        $this->d['p'] = "therapist";
        $this->d['p_therapist'] =  $data;
        $this->load->view("template_utama", $this->d);
    }

    public function simpan_therapist()
    {
        $post = $this->input->post();
        $mode = isset($post['_mode']) ? $post['_mode'] : '';
        $therapist = array(
            'name' => isset($post['name']) ? trim($post['name']) : '',
            'therapist_type' => isset($post['therapist_type']) ? trim($post['therapist_type']) : '',
            'phone' => isset($post['phone']) ? trim($post['phone']) : '',
            'email' => isset($post['email']) ? trim($post['email']) : '',
            'specialization' => isset($post['specialization']) ? trim($post['specialization']) : ''
        );

        if (in_array('', $therapist, true)) {
            j(array('status' => 'gagal', 'data' => 'Semua field therapist wajib diisi'));
            return;
        }

        if (!in_array($therapist['therapist_type'], array('Internal', 'External'), true)) {
            j(array('status' => 'gagal', 'data' => 'Tipe therapist tidak valid'));
            return;
        }

        if (!filter_var($therapist['email'], FILTER_VALIDATE_EMAIL)) {
            j(array('status' => 'gagal', 'data' => 'Format email tidak valid'));
            return;
        }

        if ($mode === 'add') {
            $therapist['is_active'] = 1;
            $saved = $this->db->insert('therapists', $therapist);
            $message = 'Data therapist berhasil disimpan';
        } elseif ($mode === 'edit' && !empty($post['_id'])) {
            $saved = $this->db->where('id', $post['_id'])->update('therapists', $therapist);
            $message = 'Data therapist berhasil diubah';
        } else {
            j(array('status' => 'gagal', 'data' => 'Mode penyimpanan tidak valid'));
            return;
        }

        if (!$saved) {
            j(array('status' => 'gagal', 'data' => 'Data therapist gagal disimpan'));
            return;
        }

        j(array('status' => 'ok', 'data' => $message));
    }

    public function delete_therapist()
    {
        $id = $this->input->post('id');

        if (empty($id)) {
            j(array('status' => 'gagal', 'data' => 'ID therapist tidak valid'));
            return;
        }

        $deleted = $this->db->where('id', $id)->delete('therapists');

        if (!$deleted) {
            j(array('status' => 'gagal', 'data' => 'Data therapist gagal dihapus'));
            return;
        }

        j(array('status' => 'ok', 'data' => 'Data therapist berhasil dihapus'));
    }

    public function report()
    {
        $active_year = $this->db->where('aktif', 'Y')->get('tahun')->row_array();
        $data = array();
        if (!empty($active_year['id'])) {
            $data = $this->db->select('report_distributions.*, tahun.tahun AS tahun_label')
                ->from('report_distributions')
                ->join('tahun', 'tahun.id = report_distributions.tahun_id', 'left')
                ->where('report_distributions.tahun_id', $active_year['id'])
                ->get()->result_array();
        }

        $this->d['p'] = "schedule";
        $this->d['p_report'] =  $data;
        $this->d['p_tahun'] = $this->db->order_by('tahun', 'DESC')->get('tahun')->result_array();
        $this->load->view("template_utama", $this->d);
    }

    public function edit($id = 0)
    {
        $data = array(
            'id' => '',
            'mode' => 'add',
            'tahun_id' => '',
            'semester' => '',
            'report_type' => '',
            'title' => '',
            'description' => '',
            'status' => '',
            'booking_start_at' => '',
            'booking_end_at' => ''
        );

        if (!empty($id)) {
            $schedule = $this->db->where('id', $id)->get('report_distributions')->row_array();
            if (!empty($schedule)) {
                $data = array_merge($data, $schedule, array('mode' => 'edit'));
            }
        }

        $this->d['p'] = "schedule_form";
        $this->d['data'] = $data;
        $this->d['p_tahun'] = $this->db->order_by('tahun', 'DESC')->get('tahun')->result_array();
        $this->d['p_dates'] = !empty($data['id'])
            ? $this->db->where('report_distribution_id', $data['id'])
                ->order_by('distribution_date', 'ASC')->get('report_distribution_dates')->result_array()
            : array();
        $this->d['p_sessions'] = array();
        if (!empty($data['id']) && !empty($this->d['p_dates'])) {
            $date_ids = array_column($this->d['p_dates'], 'id');
            $sessions = $this->db->where_in('report_distribution_date_id', $date_ids)
                ->order_by('session_number', 'ASC')->get('report_distribution_sessions')->result_array();
            foreach ($sessions as $session) {
                $this->d['p_sessions'][$session['report_distribution_date_id']][] = $session;
            }
        }
        $this->load->view("template_utama", $this->d);
    }

    public function simpan_schedule()
    {
        $post = $this->input->post();
        $mode = isset($post['_mode']) ? $post['_mode'] : '';
        $schedule = array(
            'tahun_id' => isset($post['tahun_id']) ? trim($post['tahun_id']) : '',
            'semester' => isset($post['semester']) ? trim($post['semester']) : '',
            'report_type' => isset($post['report_type']) ? trim($post['report_type']) : '',
            'title' => isset($post['title']) ? trim($post['title']) : '',
            'description' => isset($post['description']) ? trim($post['description']) : '',
            'status' => isset($post['status']) ? trim($post['status']) : '',
            'booking_start_at' => isset($post['booking_start_at']) ? trim($post['booking_start_at']) : '',
            'booking_end_at' => isset($post['booking_end_at']) ? trim($post['booking_end_at']) : ''
        );

        if (in_array('', $schedule, true)) {
            j(array('status' => 'gagal', 'data' => 'Semua field schedule wajib diisi'));
            return;
        }

        if (!in_array($schedule['semester'], array('1', '2'), true) ||
            !in_array($schedule['status'], array('DRAFT', 'PUBLISHED', 'CLOSED'), true)) {
            j(array('status' => 'gagal', 'data' => 'Semester atau status tidak valid'));
            return;
        }

        if ($schedule['booking_end_at'] < $schedule['booking_start_at']) {
            j(array('status' => 'gagal', 'data' => 'Waktu selesai harus setelah waktu mulai'));
            return;
        }

        if ($mode === 'add') {
            $code = '';
            for ($attempt = 0; $attempt < 20; $attempt++) {
                $candidate = 'DC-' . str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                if (!$this->db->where('code', $candidate)->count_all_results('report_distributions')) {
                    $code = $candidate;
                    break;
                }
            }

            if ($code === '') {
                j(array('status' => 'gagal', 'data' => 'Kode schedule gagal dibuat'));
                return;
            }

            $schedule['code'] = $code;
            $saved = $this->db->insert('report_distributions', $schedule);
            $message = 'Schedule berhasil disimpan';
        } elseif ($mode === 'edit' && !empty($post['_id'])) {
            $saved = $this->db->where('id', $post['_id'])
                ->update('report_distributions', $schedule);
            $message = 'Schedule berhasil diubah';
        } else {
            j(array('status' => 'gagal', 'data' => 'Mode penyimpanan tidak valid'));
            return;
        }

        j(array(
            'status' => $saved ? 'ok' : 'gagal',
            'data' => $saved ? $message : 'Schedule gagal disimpan'
        ));
    }

    public function simpan_schedule_date()
    {
        $post = $this->input->post();
        $date_id = isset($post['schedule_date_id']) ? trim($post['schedule_date_id']) : '';
        $date = array(
            'report_distribution_id' => isset($post['report_distribution_id']) ? trim($post['report_distribution_id']) : '',
            'distribution_date' => isset($post['distribution_date']) ? trim($post['distribution_date']) : '',
            'label' => isset($post['label']) ? trim($post['label']) : '',
            'is_active' => isset($post['is_active']) ? (int) $post['is_active'] : 0
        );

        if (in_array('', $date, true)) {
            j(array('status' => 'gagal', 'data' => 'Semua field schedule date wajib diisi'));
            return;
        }

        if (!$this->db->where('id', $date['report_distribution_id'])->count_all_results('report_distributions')) {
            j(array('status' => 'gagal', 'data' => 'Report distribution tidak ditemukan'));
            return;
        }

        if (!empty($date_id)) {
            $saved = $this->db->where('id', $date_id)
                ->where('report_distribution_id', $date['report_distribution_id'])
                ->update('report_distribution_dates', $date);
            $message = 'Schedule date berhasil diubah';
        } else {
            $saved = $this->db->insert('report_distribution_dates', $date);
            $message = 'Schedule date berhasil disimpan';
        }

        j(array(
            'status' => $saved ? 'ok' : 'gagal',
            'data' => $saved ? $message : 'Schedule date gagal disimpan'
        ));
    }

    public function delete_schedule_date()
    {
        $id = $this->input->post('id');
        if (empty($id)) {
            j(array('status' => 'gagal', 'data' => 'ID schedule date tidak valid'));
            return;
        }

        $deleted = $this->db->where('id', $id)->delete('report_distribution_dates');
        j(array(
            'status' => $deleted ? 'ok' : 'gagal',
            'data' => $deleted ? 'Schedule date berhasil dihapus' : 'Schedule date gagal dihapus'
        ));
    }

    public function simpan_schedule_session()
    {
        $post = $this->input->post();
        $session_id = isset($post['schedule_session_id']) ? trim($post['schedule_session_id']) : '';
        $session = array(
            'report_distribution_date_id' => isset($post['report_distribution_date_id']) ? trim($post['report_distribution_date_id']) : '',
            'session_number' => isset($post['session_number']) ? trim($post['session_number']) : '',
            'start_time' => isset($post['start_time']) ? trim($post['start_time']) : '',
            'end_time' => isset($post['end_time']) ? trim($post['end_time']) : '',
            'therapy_capacity' => isset($post['therapy_capacity']) ? trim($post['therapy_capacity']) : '',
            'non_therapy_capacity' => isset($post['non_therapy_capacity']) ? trim($post['non_therapy_capacity']) : '',
            'is_active' => isset($post['is_active']) ? (int) $post['is_active'] : 0
        );

        if (in_array('', $session, true)) {
            j(array('status' => 'gagal', 'data' => 'Semua field session wajib diisi'));
            return;
        }

        if (!$this->db->where('id', $session['report_distribution_date_id'])
            ->count_all_results('report_distribution_dates')) {
            j(array('status' => 'gagal', 'data' => 'Report distribution date tidak ditemukan'));
            return;
        }

        if (!ctype_digit((string) $session['session_number']) ||
            !ctype_digit((string) $session['therapy_capacity']) ||
            !ctype_digit((string) $session['non_therapy_capacity'])) {
            j(array('status' => 'gagal', 'data' => 'Nomor session dan kapasitas harus berupa angka'));
            return;
        }

        if ($session['end_time'] <= $session['start_time']) {
            j(array('status' => 'gagal', 'data' => 'Waktu selesai harus setelah waktu mulai'));
            return;
        }

        if (!empty($session_id)) {
            $saved = $this->db->where('id', $session_id)
                ->where('report_distribution_date_id', $session['report_distribution_date_id'])
                ->update('report_distribution_sessions', $session);
            $message = 'Session berhasil diubah';
        } else {
            $saved = $this->db->insert('report_distribution_sessions', $session);
            $message = 'Session berhasil disimpan';
        }

        j(array(
            'status' => $saved ? 'ok' : 'gagal',
            'data' => $saved ? $message : 'Session gagal disimpan'
        ));
    }

    public function delete_schedule_session()
    {
        $id = $this->input->post('id');
        if (empty($id)) {
            j(array('status' => 'gagal', 'data' => 'ID session tidak valid'));
            return;
        }

        $deleted = $this->db->where('id', $id)->delete('report_distribution_sessions');
        j(array(
            'status' => $deleted ? 'ok' : 'gagal',
            'data' => $deleted ? 'Session berhasil dihapus' : 'Session gagal dihapus'
        ));
    }

    public function delete_schedule()
    {
        $id = $this->input->post('id');
        if (empty($id)) {
            j(array('status' => 'gagal', 'data' => 'ID schedule tidak valid'));
            return;
        }

        $deleted = $this->db->where('id', $id)->delete('report_distributions');
        j(array(
            'status' => $deleted ? 'ok' : 'gagal',
            'data' => $deleted ? 'Schedule berhasil dihapus' : 'Schedule gagal dihapus'
        ));
    }
}