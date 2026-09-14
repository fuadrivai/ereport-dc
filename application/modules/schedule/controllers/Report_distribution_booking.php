<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report_distribution_booking extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index($code = '')
    {
        $report = $this->get_report($code);
        if (empty($report)) {
            $this->render_page('booking/unavailable', array(
                'title' => 'Report Distribution Not Found',
                'message' => 'This report distribution could not be found.'
            ));
            return;
        }

        if ($report['status'] !== 'PUBLISHED') {
            $this->render_page('booking/unavailable', array(
                'title' => 'Booking Unavailable',
                'message' => 'Booking is currently unavailable.'
            ));
            return;
        }

        if (!$this->is_booking_open($report)) {
            $this->render_page('booking/unavailable', array(
                'title' => 'Booking Closed',
                'message' => 'Booking is currently closed.'
            ));
            return;
        }

        $dates = $this->db->where('report_distribution_id', $report['id'])
            ->where('is_active', 1)
            ->order_by('distribution_date', 'ASC')
            ->get('report_distribution_dates')->result_array();

        $date_ids = array_column($dates, 'id');
        $sessions = empty($date_ids) ? array() : $this->db
            ->where_in('report_distribution_date_id', $date_ids)
            ->where('is_active', 1)
            ->order_by('session_number', 'ASC')
            ->get('report_distribution_sessions')->result_array();

        $sessions_by_date = array();
        $booking_counts = array();
        if (!empty($sessions)) {
            $session_ids = array_column($sessions, 'id');
            $count_rows = $this->db->select('session_id, booking_type, COUNT(*) AS total')
                ->where_in('session_id', $session_ids)
                ->where('status', 'BOOKED')
                ->group_by(array('session_id', 'booking_type'))
                ->get('report_distribution_bookings')->result_array();
            foreach ($count_rows as $count_row) {
                $booking_counts[$count_row['session_id']][$count_row['booking_type']] = (int) $count_row['total'];
            }
        }

        foreach ($sessions as $session) {
            $session['therapy_booked'] = isset($booking_counts[$session['id']]['THERAPY'])
                ? $booking_counts[$session['id']]['THERAPY'] : 0;
            $session['non_therapy_booked'] = isset($booking_counts[$session['id']]['NON_THERAPY'])
                ? $booking_counts[$session['id']]['NON_THERAPY'] : 0;
            $sessions_by_date[$session['report_distribution_date_id']][] = $session;
        }

        $this->render_page('booking/index', array(
            'report' => $report,
            'dates' => $dates,
            'sessions_by_date' => $sessions_by_date,
            'student_search_url' => site_url('report-distribution/booking/students'),
            'booking_status_url' => site_url('report-distribution/booking/booking-status'),
            'submit_url' => site_url('report-distribution/booking/submit')
        ));
    }

    public function students()
    {
        $query = trim((string) $this->input->get('q'));
        $booking_type = strtoupper(trim((string) $this->input->get('booking_type')));
        if (strlen($query) < 2 || !in_array($booking_type, array('THERAPY', 'NON_THERAPY'), true)) {
            $this->json(array('results' => array()));
            return;
        }

        $this->db->select("m.id, m.nama, m.nis, m.nisn, k.nama AS student_grade, g.nama AS homeroom_teacher, (SELECT nama_kepsek FROM tahun WHERE aktif = 'Y' LIMIT 1) AS principal_name")
            ->from('m_siswa m')
            ->join('t_kelas_siswa ks', "ks.id_siswa = m.id AND ks.ta = (SELECT CAST(LEFT(tahun, 4) AS UNSIGNED) FROM tahun WHERE aktif = 'Y' LIMIT 1)", 'left')
            ->join('m_kelas k', 'k.id = ks.id_kelas', 'left')
            ->join('t_walikelas wk', "wk.id_kelas = ks.id_kelas AND wk.tasm = (SELECT CAST(LEFT(tahun, 4) AS UNSIGNED) FROM tahun WHERE aktif = 'Y' LIMIT 1)", 'left')
            ->join('m_guru g', 'g.id = wk.id_guru', 'left')
            ->where('m.stat_data', 'A')
            ->group_start()
            ->like('m.nama', $query)
            ->or_like('m.nis', $query)
            ->or_like('m.nisn', $query)
            ->group_end();

        if ($booking_type === 'THERAPY') {
            $this->db->join('student_therapists st', 'st.student_id = m.id', 'inner')
                ->join('tahun y', 'y.id = st.tahun_id', 'inner')
                ->where('st.is_active', 1)
                ->where('y.aktif', 'Y');
        } else {
            $this->db->join('student_therapists st', 'st.student_id = m.id AND st.is_active = 1', 'left')
                ->join('tahun y', "y.id = st.tahun_id AND y.aktif = 'Y'", 'left')
                ->where('st.id IS NULL', null, false);
        }

        $students = $this->db->order_by('m.nama', 'ASC')
            ->limit(20)
            ->get()->result_array();

        $results = array();
        foreach ($students as $student) {
            $identifier = trim((string) $student['nis']);
            if ($identifier === '') {
                $identifier = trim((string) $student['nisn']);
            }
            $results[] = array(
                'id' => $student['id'],
                'text' => $student['nama'] . ($identifier !== '' ? ' (' . $identifier . ')' : ''),
                'name' => $student['nama'],
                'identifier' => $identifier,
                'student_grade' => $student['student_grade'],
                'homeroom_teacher' => $student['homeroom_teacher'],
                'principal_name' => $student['principal_name']
            );
        }

        $this->json(array('results' => $results));
    }

    public function booking_status()
    {
        $code = trim((string) $this->input->get('report_code'));
        $student_id = trim((string) $this->input->get('student_id'));
        $report = $this->get_report($code);

        if (empty($report) || $student_id === '') {
            $this->json(array('status' => 'error', 'message' => 'Unable to check the student booking status.'));
            return;
        }

        $active_booking = $this->get_active_booking($report['id'], $student_id);
        $has_active_booking = !empty($active_booking);
        $schedule = $has_active_booking
            ? date('d M Y', strtotime($active_booking['distribution_date'])) . ', ' .
                date('H:i', strtotime($active_booking['start_time'])) . ' - ' .
                date('H:i', strtotime($active_booking['end_time']))
            : '';

        $this->json(array(
            'status' => 'ok',
            'has_active_booking' => $has_active_booking,
            'active_schedule' => $schedule,
            'message' => $has_active_booking
                ? 'This student already has an active booking for ' . $schedule .
                    '. Please contact the admin division to cancel the existing booking.'
                : ''
        ));
    }

    public function submit()
    {
        $code = trim((string) $this->input->post('report_code'));
        $student_id = trim((string) $this->input->post('student_id'));
        $session_id = trim((string) $this->input->post('session_id'));
        $booking_type = strtoupper(trim((string) $this->input->post('booking_type')));
        $parent_name = trim((string) $this->input->post('parent_name'));
        $parent_email = trim((string) $this->input->post('parent_email'));
        $parent_phone = trim((string) $this->input->post('parent_phone'));
        $notes = trim((string) $this->input->post('notes'));

        if ($code === '' || $student_id === '' || $session_id === '' ||
            $parent_name === '' || $parent_phone === '' ||
            !filter_var($parent_email, FILTER_VALIDATE_EMAIL) ||
            !in_array($booking_type, array('THERAPY', 'NON_THERAPY'), true)) {
            $this->json(array('status' => 'error', 'message' => 'Please complete all required booking fields.'));
            return;
        }

        $report = $this->get_report($code);
        if (empty($report) || $report['status'] !== 'PUBLISHED') {
            $this->json(array('status' => 'error', 'message' => 'Booking is currently unavailable.'));
            return;
        }

        if (!$this->is_booking_open($report)) {
            $this->json(array('status' => 'error', 'message' => 'Booking is currently closed.'));
            return;
        }

        $student = $this->db->where('id', $student_id)
            ->where('stat_data', 'A')
            ->get('m_siswa')->row_array();
        if (empty($student)) {
            $this->json(array('status' => 'error', 'message' => 'The selected student is not valid.'));
            return;
        }

        $student_therapist = $this->db->where('student_therapists.student_id', $student_id)
            ->where('student_therapists.tahun_id', $report['tahun_id'])
            ->where('student_therapists.is_active', 1)
            ->join('therapists', 'therapists.id = student_therapists.therapist_id')
            ->where('therapists.is_active', 1)
            ->get('student_therapists')->row_array();
        if ($booking_type === 'THERAPY' && empty($student_therapist)) {
            $this->json(array('status' => 'error', 'message' => 'The selected student does not have an active therapy assignment.'));
            return;
        }
        if ($booking_type === 'NON_THERAPY' && !empty($student_therapist)) {
            $this->json(array('status' => 'error', 'message' => 'Students with an active therapy assignment must use a therapy booking.'));
            return;
        }

        $this->db->trans_begin();

        $locked_report = $this->db->query(
            'SELECT id, status, booking_start_at, booking_end_at
             FROM report_distributions
             WHERE id = ?
             FOR UPDATE',
            array($report['id'])
        )->row_array();

        if (empty($locked_report) || $locked_report['status'] !== 'PUBLISHED' ||
            !$this->is_booking_open($locked_report)) {
            $this->db->trans_rollback();
            $this->json(array('status' => 'error', 'message' => 'Booking is no longer available.'));
            return;
        }

        $session = $this->db->query(
            'SELECT s.*, d.report_distribution_id, d.distribution_date
             FROM report_distribution_sessions s
             INNER JOIN report_distribution_dates d ON d.id = s.report_distribution_date_id
             WHERE s.id = ? AND s.is_active = 1 AND d.is_active = 1
             FOR UPDATE',
            array($session_id)
        )->row_array();

        if (empty($session) || (string) $session['report_distribution_id'] !== (string) $report['id']) {
            $this->db->trans_rollback();
            $this->json(array('status' => 'error', 'message' => 'The selected session is no longer available.'));
            return;
        }

        $active_booking = $this->get_active_booking($report['id'], $student_id);
        if (!empty($active_booking)) {
            $schedule = date('d M Y', strtotime($active_booking['distribution_date'])) . ', ' .
                date('H:i', strtotime($active_booking['start_time'])) . ' - ' .
                date('H:i', strtotime($active_booking['end_time']));
            $this->db->trans_rollback();
            $this->json(array('status' => 'error', 'message' => 'This student already has an active booking for ' . $schedule . '. Please contact the admin division to cancel the existing booking.'));
            return;
        }

        $booking_count = $this->db->where('session_id', $session_id)
            ->where('booking_type', $booking_type)
            ->where('status', 'BOOKED')
            ->count_all_results('report_distribution_bookings');
        $capacity = $booking_type === 'THERAPY'
            ? $session['therapy_capacity']
            : $session['non_therapy_capacity'];

        if ($capacity !== null && $capacity !== '' && $booking_count >= (int) $capacity) {
            $this->db->trans_rollback();
            $this->json(array(
                'status' => 'error',
                'message' => $booking_type === 'THERAPY'
                    ? 'This therapy session is fully booked.'
                    : 'This session is fully booked.'
            ));
            return;
        }

        $booking_code = $this->generate_booking_code();
        $booking = array(
            'booking_code' => $booking_code,
            'report_distribution_id' => $report['id'],
            'session_id' => $session_id,
            'student_id' => $student_id,
            'parent_name' => $parent_name,
            'parent_email' => $parent_email,
            'parent_phone' => $parent_phone,
            'booking_type' => $booking_type,
            'therapist_id' => $booking_type === 'THERAPY' ? $student_therapist['therapist_id'] : null,
            'status' => 'BOOKED',
            'booked_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        );

        $saved = $this->db->insert('report_distribution_bookings', $booking);
        if (!$saved || !$this->db->trans_status()) {
            $this->db->trans_rollback();
            $this->json(array('status' => 'error', 'message' => 'Booking could not be completed. Please try again.'));
            return;
        }

        $this->db->trans_commit();
        $therapist_name = 'Not assigned';
        if ($booking_type === 'THERAPY' && !empty($student_therapist['therapist_id'])) {
            $therapist = $this->db->select('name')
                ->where('id', $student_therapist['therapist_id'])
                ->get('therapists')->row_array();
            if (!empty($therapist['name'])) {
                $therapist_name = $therapist['name'];
            }
        }
        $calendar_event = $this->sync_google_calendar_event(
            '',
            $booking_code,
            $report['title'],
            $student['nama'],
            $booking_type,
            $parent_email,
            $session['distribution_date'],
            $session['start_time'],
            $session['end_time'],
            $parent_name,
            $therapist_name
        );
        if (!empty($calendar_event['event_id'])) {
            $calendar_saved = $this->db->where('booking_code', $booking_code)->update('report_distribution_bookings', array(
                'google_calendar_event_id' => $calendar_event['event_id'],
                'gmeet_link' => !empty($calendar_event['gmeet_link']) ? $calendar_event['gmeet_link'] : null
            ));
            if (!$calendar_saved) {
                log_message('error', 'Google Calendar event ID could not be saved for booking ' . $booking_code . ': ' .
                    $this->db->error()['message']);
            }
        } else {
            log_message('error', 'Google Calendar did not return an event ID for booking ' . $booking_code . '.');
        }
        $this->send_booking_confirmation_email(
            $parent_email,
            $parent_name,
            $booking_code,
            $student['nama'],
            $booking_type,
            $therapist_name,
            $session['start_time'],
            $session['end_time'],
            $session['distribution_date'],
            $report['title'],
            $report['semester'],
            $report['report_type'],
            !empty($calendar_event['gmeet_link']) ? $calendar_event['gmeet_link'] : ''
        );
        $this->json(array(
            'status' => 'ok',
            'redirect' => site_url('report-distribution/booking/success/' . rawurlencode($booking_code)),
            'data' => array(
                'booking_code' => $booking_code,
                'student_name' => $student['nama'],
                'booking_type' => $booking_type,
                'session' => $session
            )
        ));
    }

    private function sync_google_calendar_event($event_id, $booking_code, $report_title, $student_name,
        $booking_type, $attendee_email, $distribution_date, $start_time, $end_time, $parent_name, $therapist_name)
    {
        if ($event_id !== '') {
            $updated_event = $this->update_google_calendar_event(
                $event_id,
                $booking_code,
                $report_title,
                $student_name,
                $booking_type,
                $attendee_email,
                $distribution_date,
                $start_time,
                $end_time
            );
            if (!empty($updated_event['id'])) {
                return array(
                    'event_id' => $updated_event['id'],
                    'gmeet_link' => !empty($updated_event['hangoutLink']) ? $updated_event['hangoutLink'] : ''
                );
            }
        }

        return $this->create_google_calendar_event(
            $booking_code,
            $report_title,
            $student_name,
            $booking_type,
            $attendee_email,
            $distribution_date,
            $start_time,
            $end_time,
            $parent_name,
            $therapist_name
        );
        }

    private function create_google_calendar_event($booking_code, $report_title, $student_name, $booking_type,
        $attendee_email, $distribution_date, $start_time, $end_time, $parent_name, $therapist_name)
    {
        $calendar = $this->get_google_calendar_service();
        if ($calendar === null) {
            return array();
        }

        $type_label = $booking_type === 'THERAPY' ? 'Therapy' : 'Without Therapy';
        $description = 'MHIS Report Distribution' . "\n\n" .
            'Booking Code: ' . $booking_code . "\n" .
            'Student: ' . $student_name . "\n" .
            'Parent: ' . $parent_name . "\n" .
            'Booking Type: ' . $type_label;
        if ($booking_type === 'THERAPY') {
            $description .= "\nTherapist: " . $therapist_name;
        }
        $description .= "\n\nPlease join Google Meet at the scheduled time.";

        try {
            $timezone = $this->config->item('google_calendar_timezone') ?: 'Asia/Jakarta';
            $timezone_object = new \DateTimeZone($timezone);
            $start_datetime = new \DateTime($distribution_date . ' ' . $start_time, $timezone_object);
            $end_datetime = new \DateTime($distribution_date . ' ' . $end_time, $timezone_object);
            $event = new \Google\Service\Calendar\Event(array(
                'summary' => 'Report Distribution - ' . $student_name,
                'description' => $description,
                'attendees' => array(array('email' => $attendee_email)),
                'start' => array('dateTime' => $start_datetime->format('c'), 'timeZone' => $timezone),
                'end' => array('dateTime' => $end_datetime->format('c'), 'timeZone' => $timezone),
                'conferenceData' => array('createRequest' => array(
                    'requestId' => 'report-distribution-' . $booking_code,
                    'conferenceSolutionKey' => array('type' => 'hangoutsMeet')
                ))
            ));
            $created_event = $calendar->events->insert($this->config->item('google_calendar_id'), $event, array(
                'conferenceDataVersion' => 1,
                'sendUpdates' => 'none'
            ));
            $event_id = $created_event->getId();
            try {
                $gmeet_link = $this->get_google_meet_link($calendar, $event_id, $created_event);
            } catch (\Exception $exception) {
                log_message('error', 'Report Distribution Google Meet Error: ' . $exception->getMessage());
                $gmeet_link = '';
            }
            if ($gmeet_link === '') {
                log_message('error', 'Google Meet link is not ready for booking ' . $booking_code . '.');
            }
            return array('event_id' => $event_id, 'gmeet_link' => $gmeet_link);
        } catch (\Exception $exception) {
            log_message('error', 'Report Distribution Google Calendar Error: ' . $exception->getMessage());
            return array();
        }
    }

    private function update_google_calendar_event($event_id, $booking_code, $report_title, $student_name,
        $booking_type, $attendee_email, $distribution_date, $start_time, $end_time)
    {
        $calendar = $this->get_google_calendar_service();
        if ($calendar === null) {
            return array();
        }

        try {
            $event = new \Google\Service\Calendar\Event(array(
                'summary' => 'Report Distribution - ' . $student_name,
                'start' => array('dateTime' => date('c', strtotime($distribution_date . ' ' . $start_time))),
                'end' => array('dateTime' => date('c', strtotime($distribution_date . ' ' . $end_time)))
            ));
            $updated_event = $calendar->events->patch($this->config->item('google_calendar_id'), $event_id, $event);
            return array('id' => $updated_event->getId());
        } catch (\Exception $exception) {
            log_message('error', 'Report Distribution Google Calendar Error: ' . $exception->getMessage());
            return array();
        }
    }

    private function delete_google_calendar_event($event_id)
    {
        if ($event_id === '') {
            return true;
        }
        $calendar = $this->get_google_calendar_service();
        if ($calendar === null) {
            return false;
        }
        try {
            $calendar->events->delete($this->config->item('google_calendar_id'), $event_id);
            return true;
        } catch (\Exception $exception) {
            log_message('error', 'Report Distribution Google Calendar Error: ' . $exception->getMessage());
            return false;
        }
    }

    private function get_google_calendar_service()
    {
        if (!$this->config->item('google_calendar_enabled')) {
            return null;
        }

        $credentials_path = $this->config->item('google_calendar_credentials');
        $calendar_id = $this->config->item('google_calendar_id');
        if ($credentials_path === '' || $calendar_id === '' || !is_readable($credentials_path)) {
            log_message('error', 'Report Distribution Google Calendar Error: credentials or calendar ID is missing.');
            return null;
        }

        try {
            $client = new \Google\Client();
            $client->setAuthConfig($credentials_path);
            $client->setScopes(array(\Google\Service\Calendar::CALENDAR));
            $subject = trim((string) $this->config->item('google_calendar_impersonate'));
            if ($subject !== '') {
                $client->setSubject($subject);
            }
            return new \Google\Service\Calendar($client);
        } catch (\Exception $exception) {
            log_message('error', 'Report Distribution Google Calendar Error: ' . $exception->getMessage());
            return null;
        }
    }

    private function get_google_meet_link($calendar, $event_id, $created_event = null)
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $event = $attempt === 0 ? $created_event : $calendar->events->get(
                $this->config->item('google_calendar_id'),
                $event_id
            );
            if ($event !== null && $event->getConferenceData() !== null) {
                foreach ($event->getConferenceData()->getEntryPoints() ?: array() as $entry_point) {
                    if ($entry_point->getEntryPointType() === 'video' && $entry_point->getUri() !== null) {
                        return $entry_point->getUri();
                    }
                }
            }
            sleep(1);
        }
        return '';
    }

    private function send_booking_confirmation_email($recipient, $parent_name, $booking_code, $student_name,
        $booking_type, $therapist_name, $start_time, $end_time, $distribution_date, $report_title, $semester,
        $report_type, $gmeet_link)
    {
        $type_label = $booking_type === 'THERAPY' ? 'Therapy' : 'Without Therapy';
        $message = $this->load->view('booking/email_confirmation', array(
            'parent_name' => $parent_name,
            'booking_code' => $booking_code,
            'student_name' => $student_name,
            'type_label' => $type_label,
            'therapist_name' => $therapist_name,
            'distribution_date' => $distribution_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'report_title' => $report_title,
            'semester' => $semester,
            'report_type' => $report_type,
            'gmeet_link' => $gmeet_link
        ), true);

        $this->load->library('email');
        $this->email->initialize(array(
            'protocol' => $this->config->item('MAIL_MAILER'),
            'smtp_host' => $this->config->item('MAIL_HOST'),
            'smtp_port' => $this->config->item('MAIL_PORT'),
            'smtp_user' => $this->config->item('MAIL_USERNAME'),
            'smtp_pass' => $this->config->item('MAIL_PASSWORD'),
            'smtp_crypto' => $this->config->item('MAIL_ENCRYPTION'),
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => true,
            'newline' => "\r\n",
            'crlf' => "\r\n"
        ));
        $this->email->from(
            $this->config->item('MAIL_FROM_ADDRESS'),
            $this->config->item('MAIL_FROM_NAME')
        );
        $this->email->to($recipient);
        $this->email->subject('Report Distribution Booking Confirmation - ' . $booking_code);
        $this->email->message($message);

        if (!$this->email->send()) {
            log_message('error', 'Booking confirmation email failed for ' . $booking_code . ': ' .
                $this->email->print_debugger(array('headers')));
            return false;
        }
        if ($this->db->field_exists('confirmation_email_sent_at', 'report_distribution_bookings')) {
            $this->db->where('booking_code', $booking_code)->update('report_distribution_bookings', array(
                'confirmation_email_sent_at' => date('Y-m-d H:i:s')
            ));
        }
        return true;
    }

    public function success($booking_code = '')
    {
        $booking = $this->db->select('b.*, r.code AS report_code, r.title, r.report_type, r.semester, t.tahun AS tahun_label,
                s.start_time, s.end_time, d.distribution_date, m.nama AS student_name,
                m.nis AS student_nis, th.name AS therapist_name')
            ->from('report_distribution_bookings b')
            ->join('report_distributions r', 'r.id = b.report_distribution_id')
            ->join('tahun t', 't.id = r.tahun_id', 'left')
            ->join('report_distribution_sessions s', 's.id = b.session_id')
            ->join('report_distribution_dates d', 'd.id = s.report_distribution_date_id')
            ->join('m_siswa m', 'm.id = b.student_id')
            ->join('therapists th', 'th.id = b.therapist_id', 'left')
            ->where('b.booking_code', $booking_code)
            ->where('b.status', 'BOOKED')
            ->get()->row_array();

        if (empty($booking)) {
            $this->render_page('booking/unavailable', array(
                'title' => 'Booking Not Found',
                'message' => 'This booking confirmation could not be found.'
            ));
            return;
        }

        $this->render_page('booking/success', array('booking' => $booking));
    }

    private function get_report($code)
    {
        if ($code === '') {
            return array();
        }

        return $this->db->select('r.*, t.tahun AS tahun_label')
            ->from('report_distributions r')
            ->join('tahun t', 't.id = r.tahun_id', 'left')
            ->where('r.code', $code)
            ->get()->row_array();
    }

    private function get_active_booking($report_distribution_id, $student_id)
    {
        return $this->db->select('b.id, d.distribution_date, s.start_time, s.end_time')
            ->from('report_distribution_bookings b')
            ->join('report_distribution_sessions s', 's.id = b.session_id')
            ->join('report_distribution_dates d', 'd.id = s.report_distribution_date_id')
            ->where('b.report_distribution_id', $report_distribution_id)
            ->where('b.student_id', $student_id)
            ->where('b.status', 'BOOKED')
            ->order_by('b.booked_at', 'DESC')
            ->get()->row_array();
    }

    private function is_booking_open($report)
    {
        $now = time();
        return $now >= strtotime($report['booking_start_at']) &&
            $now <= strtotime($report['booking_end_at']);
    }

    private function generate_booking_code()
    {
        do {
            $code = 'RDB-' . date('Ymd') . '-' . str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $exists = $this->db->where('booking_code', $code)
                ->count_all_results('report_distribution_bookings');
        } while ($exists > 0);

        return $code;
    }

    private function render_page($view, $data)
    {
        $this->load->view('booking/layout', array_merge($data, array('content_view' => $view)));
    }

    private function json($data)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}