<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';

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
        $this->d['p_slots'] = array();
        if (!empty($data)) {
            $report_ids = array_column($data, 'id');
            $slot_rows = $this->db->select("d.report_distribution_id, d.id AS date_id, d.distribution_date, d.label AS date_label,
                    d.is_active AS date_active, s.id AS session_id, s.session_number, s.start_time, s.end_time,
                    s.therapy_capacity, s.non_therapy_capacity,
                    SUM(CASE WHEN b.booking_type = 'THERAPY' THEN 1 ELSE 0 END) AS therapy_booked,
                    SUM(CASE WHEN b.booking_type = 'NON_THERAPY' THEN 1 ELSE 0 END) AS non_therapy_booked", false)
                ->from('report_distribution_dates d')
                ->join('report_distribution_sessions s', 's.report_distribution_date_id = d.id', 'left')
                ->join('report_distribution_bookings b', "b.session_id = s.id AND b.status = 'BOOKED'", 'left', false)
                ->where_in('d.report_distribution_id', $report_ids)
                ->group_by(array(
                    'd.report_distribution_id', 'd.id', 'd.distribution_date', 'd.label', 'd.is_active',
                    's.id', 's.session_number', 's.start_time', 's.end_time',
                    's.therapy_capacity', 's.non_therapy_capacity'
                ))
                ->order_by('d.distribution_date', 'ASC')
                ->order_by('s.session_number', 'ASC')
                ->get()->result_array();

            foreach ($slot_rows as $slot_row) {
                $this->d['p_slots'][$slot_row['report_distribution_id']][$slot_row['date_id']]['date'] = $slot_row;
                if (!empty($slot_row['session_id'])) {
                    $this->d['p_slots'][$slot_row['report_distribution_id']][$slot_row['date_id']]['sessions'][] = $slot_row;
                }
            }
        }
        $this->d['p_tahun'] = $this->db->order_by('tahun', 'DESC')->get('tahun')->result_array();
        $this->load->view("template_utama", $this->d);
    }

    public function registration_list($report_id = 0)
    {
        $report = $this->db->select('r.*, t.tahun AS tahun_label')
            ->from('report_distributions r')
            ->join('tahun t', 't.id = r.tahun_id', 'left')
            ->where('r.id', $report_id)
            ->get()->row_array();
        if (empty($report)) {
            redirect('schedule/report');
            return;
        }

        $rows = $this->db->select('d.id AS date_id, d.distribution_date, d.label AS date_label,
                s.id AS session_id, s.session_number, s.start_time, s.end_time,
            b.id AS booking_id, b.booking_type, b.report_collection_method, b.gmeet_link, m.nama AS student_name,
                k.nama AS class_name', false)
            ->from('report_distribution_dates d')
            ->join('report_distribution_sessions s', 's.report_distribution_date_id = d.id', 'left')
            ->join('report_distribution_bookings b', "b.session_id = s.id AND b.status = 'BOOKED'", 'left', false)
            ->join('m_siswa m', 'm.id = b.student_id', 'left')
            ->join('t_kelas_siswa ks', 'ks.id_siswa = m.id AND ks.ta = (SELECT CAST(LEFT(tahun, 4) AS UNSIGNED) FROM tahun WHERE id = ' . (int) $report['tahun_id'] . ' LIMIT 1)', 'left', false)
            ->join('m_kelas k', 'k.id = ks.id_kelas', 'left')
            ->where('d.report_distribution_id', $report['id'])
            ->where('d.is_active', 1)
            ->order_by('d.distribution_date', 'ASC')
            ->order_by('s.session_number', 'ASC')
            ->order_by('m.nama', 'ASC')
            ->get()->result_array();

        $dates = array();
        foreach ($rows as $row) {
            if (empty($row['session_id'])) {
                continue;
            }
            $date_id = $row['date_id'];
            $session_id = $row['session_id'];
            if (!isset($dates[$date_id])) {
                $dates[$date_id] = array(
                    'distribution_date' => $row['distribution_date'],
                    'date_label' => $row['date_label'],
                    'sessions' => array()
                );
            }
            if (!isset($dates[$date_id]['sessions'][$session_id])) {
                $dates[$date_id]['sessions'][$session_id] = array(
                    'session_number' => $row['session_number'],
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'THERAPY' => array(),
                    'NON_THERAPY' => array()
                );
            }
            if (!empty($row['booking_id'])) {
                $dates[$date_id]['sessions'][$session_id][$row['booking_type']][] = $row;
            }
        }

        $this->d['p'] = 'schedule_registration_list';
        $this->d['report'] = $report;
        $this->d['registration_dates'] = $dates;
        $this->load->view('template_utama', $this->d);
    }

        public function booking_management($report_id = 0)
        {
                if (!cek_hak_akses($this->d['admlevel'], array('admin'))) {
                    redirect('unauthorized_access');
                    return;
                }

            $report = $report_id > 0 ? $this->db->select('title')->where('id', (int) $report_id)
                ->get('report_distributions')->row_array() : array();

            $query = $this->db->select('b.*, r.title AS report_title, d.distribution_date, d.label AS date_label,
                    s.session_number, s.start_time, s.end_time, m.nama AS student_name, k.nama AS student_grade', false)
                ->from('report_distribution_bookings b')
                ->join('report_distributions r', 'r.id = b.report_distribution_id')
                ->join('report_distribution_sessions s', 's.id = b.session_id')
                ->join('report_distribution_dates d', 'd.id = s.report_distribution_date_id')
                ->join('m_siswa m', 'm.id = b.student_id')
                ->join('t_kelas_siswa ks', 'ks.id_siswa = m.id AND ks.ta = (SELECT CAST(LEFT(tahun, 4) AS UNSIGNED) FROM tahun WHERE id = r.tahun_id LIMIT 1)', 'left', false)
                ->join('m_kelas k', 'k.id = ks.id_kelas', 'left')
                ->order_by('d.distribution_date', 'ASC')
                ->order_by('s.start_time', 'ASC')
                    ->order_by('m.nama', 'ASC');
                if ($report_id > 0) {
                    $query->where('b.report_distribution_id', (int) $report_id);
                }
                $bookings = $query->get()->result_array();

            $slots = $this->get_booking_slots();
            $this->d['p'] = 'booking_management';
            $this->d['bookings'] = $bookings;
            $this->d['slots'] = $slots;
            $this->d['grades'] = $this->db->select('nama')->order_by('nama', 'ASC')->get('m_kelas')->result_array();
            $this->d['dates'] = $report_id > 0 ? $this->db->select('distribution_date, label')
                ->where('report_distribution_id', (int) $report_id)
                ->order_by('distribution_date', 'ASC')->get('report_distribution_dates')->result_array() : array();
            $this->d['report_title'] = !empty($report['title']) ? $report['title'] : 'Manage Report Distribution Bookings';
            $this->load->view('template_utama', $this->d);
        }

        public function update_booking()
        {
                if (!cek_hak_akses($this->d['admlevel'], array('admin'))) {
                    j(array('status' => 'gagal', 'data' => 'Anda tidak memiliki akses untuk mengubah booking'));
                    return;
                }

            $booking_id = (int) $this->input->post('booking_id');
            $action = $this->input->post('action');
            $booking = $this->db->where('id', $booking_id)->where('status', 'BOOKED')
                ->get('report_distribution_bookings')->row_array();
            if (empty($booking)) {
                j(array('status' => 'gagal', 'data' => 'Booking aktif tidak ditemukan'));
                return;
            }

            if ($action === 'cancel') {
                    if (!empty($booking['google_calendar_event_id']) &&
                        !$this->delete_booking_calendar_event($booking['google_calendar_event_id'])) {
                        j(array('status' => 'gagal', 'data' => 'Event Google Calendar gagal dihapus. Booking tidak dibatalkan.'));
                        return;
                    }
                $saved = $this->db->where('id', $booking_id)->where('status', 'BOOKED')->update(
                        'report_distribution_bookings', array(
                            'status' => 'CANCELLED',
                            'cancelled_at' => date('Y-m-d H:i:s'),
                            'google_calendar_event_id' => null,
                            'gmeet_link' => null
                        )
                );
                j(array('status' => $saved ? 'ok' : 'gagal', 'data' => $saved ? 'Booking berhasil dibatalkan' : 'Booking gagal dibatalkan'));
                return;
            }

            if ($action === 'complete') {
                $saved = $this->db->where('id', $booking_id)->where('status', 'BOOKED')->update(
                    'report_distribution_bookings', array('status' => 'COMPLETED', 'completed_at' => date('Y-m-d H:i:s'))
                );
                j(array('status' => $saved ? 'ok' : 'gagal', 'data' => $saved ? 'Kedatangan berhasil dikonfirmasi' : 'Kedatangan gagal dikonfirmasi'));
                return;
            }

            $session_id = (int) $this->input->post('session_id');
            $session = $this->db->select('s.*, d.report_distribution_id, d.is_active')
                ->from('report_distribution_sessions s')
                ->join('report_distribution_dates d', 'd.id = s.report_distribution_date_id')
                ->where('s.id', $session_id)->where('s.is_active', 1)->where('d.is_active', 1)
                ->get()->row_array();
            if ($action !== 'reschedule' || empty($session) || (int) $session['report_distribution_id'] !== (int) $booking['report_distribution_id']) {
                j(array('status' => 'gagal', 'data' => 'Tanggal atau jam tujuan tidak valid'));
                return;
            }

            $capacity_column = $booking['booking_type'] === 'THERAPY' ? 'therapy_capacity' : 'non_therapy_capacity';
            $booked = $this->db->where('session_id', $session_id)->where('booking_type', $booking['booking_type'])
                ->where('status', 'BOOKED')->where('id !=', $booking_id)->count_all_results('report_distribution_bookings');
            if ($booked >= (int) $session[$capacity_column]) {
                j(array('status' => 'gagal', 'data' => 'Kuota sesi tujuan sudah penuh'));
                return;
            }

            $saved = $this->db->where('id', $booking_id)->where('status', 'BOOKED')
                ->update('report_distribution_bookings', array('session_id' => $session_id));
            j(array('status' => $saved ? 'ok' : 'gagal', 'data' => $saved ? 'Jadwal booking berhasil diubah' : 'Jadwal booking gagal diubah'));
        }

        public function delete_booking()
        {
            if (!cek_hak_akses($this->d['admlevel'], array('admin'))) {
                j(array('status' => 'gagal', 'data' => 'Anda tidak memiliki akses untuk menghapus booking'));
                return;
            }

            $booking_id = (int) $this->input->post('booking_id');
            $booking = $this->db->where('id', $booking_id)->get('report_distribution_bookings')->row_array();
            if (empty($booking)) {
                j(array('status' => 'gagal', 'data' => 'Data booking tidak ditemukan'));
                return;
            }

            if ($booking['status'] === 'BOOKED' && !empty($booking['google_calendar_event_id']) &&
                !$this->delete_booking_calendar_event($booking['google_calendar_event_id'])) {
                j(array('status' => 'gagal', 'data' => 'Event Google Calendar gagal dihapus. Data booking tidak dihapus.'));
                return;
            }

            $deleted = $this->db->where('id', $booking_id)->delete('report_distribution_bookings');
            j(array(
                'status' => $deleted ? 'ok' : 'gagal',
                'data' => $deleted ? 'Data booking berhasil dihapus' : 'Data booking gagal dihapus'
            ));
        }

        private function get_booking_slots()
        {
            $rows = $this->db->select('s.id, d.report_distribution_id, d.distribution_date, d.label AS date_label,
                    s.session_number, s.start_time, s.end_time, s.therapy_capacity, s.non_therapy_capacity', false)
                ->from('report_distribution_sessions s')
                ->join('report_distribution_dates d', 'd.id = s.report_distribution_date_id')
                ->where('s.is_active', 1)->where('d.is_active', 1)
                ->order_by('d.distribution_date', 'ASC')->order_by('s.start_time', 'ASC')
                ->get()->result_array();
            $slots = array();
            foreach ($rows as $row) {
                $slots[$row['report_distribution_id']][] = $row;
            }
            return $slots;
        }

        private function delete_booking_calendar_event($event_id)
        {
            if (!$this->config->item('google_calendar_enabled')) {
                log_message('error', 'Booking cancellation Calendar Error: Google Calendar is disabled.');
                return false;
            }

            $credentials_path = $this->config->item('google_calendar_credentials');
            $calendar_id = $this->config->item('google_calendar_id');
            if ($credentials_path === '' || !is_readable($credentials_path) || $calendar_id === '') {
                log_message('error', 'Booking cancellation Calendar Error: Calendar credentials or ID is missing.');
                return false;
            }

            try {
                $client = new \Google\Client();
                $client->setAuthConfig($credentials_path);
                $client->setScopes(array(\Google\Service\Calendar::CALENDAR));
                $subject = trim((string) $this->config->item('google_calendar_impersonate'));
                if ($subject !== '') {
                    $client->setSubject($subject);
                }
                $calendar = new \Google\Service\Calendar($client);
                $calendar->events->delete($calendar_id, $event_id);
                return true;
            } catch (\Exception $exception) {
                if (in_array((int) $exception->getCode(), array(404, 410), true)) {
                    return true;
                }
                log_message('error', 'Booking cancellation Calendar Error: ' . $exception->getMessage());
                return false;
            }
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
            'sheetId' => '',
            'sheetName' => '',
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
            'sheetId' => isset($post['sheetId']) ? trim($post['sheetId']) : '',
            'sheetName' => isset($post['sheetName']) ? trim($post['sheetName']) : '',
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