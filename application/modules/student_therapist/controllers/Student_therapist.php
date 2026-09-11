<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Student_therapist extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_aktif();

        $this->sespre = $this->config->item('session_name_prefix');
        if ($this->session->userdata($this->sespre . 'level') !== 'admin') {
            redirect('unauthorized_access');
        }

        $this->d['admlevel'] = $this->session->userdata($this->sespre . 'level');
        $this->d['url'] = 'student_therapist';
        $this->d['nama_form'] = 'f_student_therapist';
    }

    public function index()
    {
        $this->d['p'] = 'list';
        $this->d['p_students'] = $this->db->select('id, nama, nis, nisn')
            ->where('stat_data', 'A')
            ->order_by('nama', 'ASC')
            ->get('m_siswa')->result_array();
        $this->d['p_therapists'] = $this->db->select('id, name, specialization')
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get('therapists')->result_array();
        $this->d['p_tahun'] = $this->db->order_by('tahun', 'DESC')->get('tahun')->result_array();

        $this->load->view('template_utama', $this->d);
    }

    public function form($id = 0)
    {
        $mapping = array(
            'id' => '',
            'mode' => 'add',
            'student_id' => '',
            'tahun_id' => '',
            'therapist_id' => '',
            'is_active' => 1,
            'notes' => ''
        );

        if ((int) $id > 0) {
            $existing = $this->db->where('id', (int) $id)
                ->get('student_therapists')->row_array();
            if (!empty($existing)) {
                $mapping = array_merge($mapping, $existing, array('mode' => 'edit'));
            }
        }

        $this->d['p'] = 'form';
        $this->d['data'] = $mapping;
        $this->d['p_students'] = $this->db->select('id, nama, nis, nisn')
            ->where('stat_data', 'A')
            ->order_by('nama', 'ASC')
            ->get('m_siswa')->result_array();
        $this->d['p_therapists'] = $this->db->select('id, name, specialization')
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get('therapists')->result_array();
        $this->d['p_tahun'] = $this->db->order_by('tahun', 'DESC')->get('tahun')->result_array();

        $this->load->view('template_utama', $this->d);
    }

    public function datatable()
    {
        $start = (int) $this->input->post('start');
        $length = (int) $this->input->post('length');
        $draw = (int) $this->input->post('draw');
        $search = trim((string) $this->input->post('search')['value']);

        $this->db->from('student_therapists st')
            ->join('m_siswa s', 's.id = st.student_id')
            ->join('tahun y', 'y.id = st.tahun_id')
            ->join('therapists t', 't.id = st.therapist_id')
            ->where('y.aktif', 'Y');
        $total = $this->db->count_all_results();

        $this->db->select('st.id, st.is_active, st.notes, s.nama AS student_name, s.nis, s.nisn,
                y.tahun AS year_label, t.name AS therapist_name, t.specialization')
            ->from('student_therapists st')
            ->join('m_siswa s', 's.id = st.student_id')
            ->join('tahun y', 'y.id = st.tahun_id')
            ->join('therapists t', 't.id = st.therapist_id')
            ->where('y.aktif', 'Y')
            ->where('y.aktif', 'Y');
        if ($search !== '') {
            $this->db->group_start()
                ->like('s.nama', $search)
                ->or_like('s.nis', $search)
                ->or_like('s.nisn', $search)
                ->or_like('t.name', $search)
                ->or_like('y.tahun', $search)
                ->group_end();
        }
        $filtered = $this->db->count_all_results();

        $this->db->select('st.id, st.is_active, st.notes, s.nama AS student_name, s.nis, s.nisn,
                y.tahun AS year_label, t.name AS therapist_name, t.specialization')
            ->from('student_therapists st')
            ->join('m_siswa s', 's.id = st.student_id')
            ->join('tahun y', 'y.id = st.tahun_id')
            ->join('therapists t', 't.id = st.therapist_id')
            ->where('y.aktif', 'Y');
        if ($search !== '') {
            $this->db->group_start()
                ->like('s.nama', $search)
                ->or_like('s.nis', $search)
                ->or_like('s.nisn', $search)
                ->or_like('t.name', $search)
                ->or_like('y.tahun', $search)
                ->group_end();
        }
        $rows = $this->db->order_by('st.id', 'DESC')->limit($length, $start)->get()->result_array();

        $data = array();
        $no = $start + 1;
        foreach ($rows as $row) {
            $identifier = $row['nis'] !== '' ? $row['nis'] : $row['nisn'];
            $data[] = array(
                $no++,
                html_escape($row['student_name']) . '<br><small>' . html_escape($identifier) . '</small>',
                html_escape($row['year_label']),
                html_escape($row['therapist_name']) . '<br><small>' . html_escape($row['specialization']) . '</small>',
                '<span class="label label-' . ((int) $row['is_active'] === 1 ? 'success' : 'default') . '">' .
                    ((int) $row['is_active'] === 1 ? 'Active' : 'Inactive') . '</span>',
                '<button type="button" class="btn btn-xs btn-danger" onclick="return hapus(' . (int) $row['id'] . ');"><i class="fa fa-remove"></i> Hapus</button>'
            );
        }

        j(array(
            'draw' => $draw,
            'iTotalRecords' => $total,
            'iTotalDisplayRecords' => $filtered,
            'data' => $data
        ));
    }

    public function edit($id = 0)
    {
        $mapping = $this->db->where('id', (int) $id)->get('student_therapists')->row_array();
        j(array(
            'status' => 'ok',
            'data' => $mapping ? $mapping : array(
                'id' => '',
                'student_id' => '',
                'tahun_id' => '',
                'therapist_id' => '',
                'is_active' => 1,
                'notes' => ''
            )
        ));
    }

    public function simpan()
    {
        $post = $this->input->post();
        $id = (int) $this->input->post('_id');
        $mode = trim((string) $this->input->post('_mode'));
        $student_id = (int) $this->input->post('student_id');
        $tahun_id = (int) $this->input->post('tahun_id');
        $therapist_id = (int) $this->input->post('therapist_id');
        $mapping = array(
            'student_id' => $student_id,
            'tahun_id' => $tahun_id,
            'therapist_id' => $therapist_id,
            'is_active' => isset($post['is_active']) ? 1 : 0,
            'notes' => trim((string) $this->input->post('notes'))
        );

        if ($student_id <= 0 || $tahun_id <= 0 || $therapist_id <= 0) {
            j(array('status' => 'gagal', 'data' => 'Student, tahun, dan therapist wajib dipilih'));
            return;
        }

        if (!$this->db->where('id', $student_id)->where('stat_data', 'A')->count_all_results('m_siswa') ||
            !$this->db->where('id', $tahun_id)->count_all_results('tahun') ||
            !$this->db->where('id', $therapist_id)->where('is_active', 1)->count_all_results('therapists')) {
            j(array('status' => 'gagal', 'data' => 'Student, tahun, atau therapist tidak valid'));
            return;
        }

        $this->db->where('student_id', $student_id)
            ->where('tahun_id', $tahun_id);
        if ($mode === 'edit' && $id > 0) {
            $this->db->where('id !=', $id);
        }
        if ($this->db->count_all_results('student_therapists')) {
            j(array('status' => 'gagal', 'data' => 'Mapping student untuk tahun tersebut sudah ada'));
            return;
        }

        if ($mode === 'add') {
            $saved = $this->db->insert('student_therapists', $mapping);
            $message = 'Mapping student therapist berhasil disimpan';
        } elseif ($mode === 'edit' && $id > 0) {
            $saved = $this->db->where('id', $id)->update('student_therapists', $mapping);
            $message = 'Mapping student therapist berhasil diubah';
        } else {
            j(array('status' => 'gagal', 'data' => 'Mode penyimpanan tidak valid'));
            return;
        }

        j(array(
            'status' => $saved ? 'ok' : 'gagal',
            'data' => $saved ? $message : 'Mapping student therapist gagal disimpan'
        ));
    }

    public function simpan_massal()
    {
        $post = $this->input->post();
        $mode = trim((string) $this->input->post('_mode'));
        $mapping_id = (int) $this->input->post('_id');
        $student_ids = isset($post['student_ids']) && is_array($post['student_ids'])
            ? array_values(array_unique(array_map('intval', $post['student_ids']))) : array();
        $tahun_id = (int) $this->input->post('tahun_id');
        $therapist_id = (int) $this->input->post('therapist_id');
        $is_active = isset($post['is_active']) ? 1 : 0;
        $notes = trim((string) $this->input->post('notes'));

        if (empty($student_ids) || $tahun_id <= 0 || $therapist_id <= 0) {
            $this->session->set_flashdata('student_therapist_error', 'Pilih tahun, therapist, dan minimal satu student.');
            redirect($this->d['url'] . '/form' . ($mapping_id > 0 ? '/' . $mapping_id : ''));
            return;
        }

        $valid_student_count = $this->db->where_in('id', $student_ids)
            ->where('stat_data', 'A')
            ->count_all_results('m_siswa');

        if ($valid_student_count !== count($student_ids)
            || !$this->db->where('id', $tahun_id)->count_all_results('tahun')
            || !$this->db->where('id', $therapist_id)->where('is_active', 1)
                ->count_all_results('therapists')) {
            $this->session->set_flashdata('student_therapist_error', 'Student, tahun, atau therapist tidak valid.');
            redirect($this->d['url'] . '/form' . ($mapping_id > 0 ? '/' . $mapping_id : ''));
            return;
        }

        $this->db->trans_begin();
        $inserted = 0;
        $skipped = 0;

        if ($mode === 'edit' && $mapping_id > 0) {
            if (count($student_ids) !== 1) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('student_therapist_error', 'Mode edit hanya dapat menyimpan satu student.');
                redirect($this->d['url'] . '/form/' . $mapping_id);
                return;
            }

            $saved = $this->db->where('id', $mapping_id)->update('student_therapists', array(
                'student_id' => $student_ids[0],
                'tahun_id' => $tahun_id,
                'therapist_id' => $therapist_id,
                'is_active' => $is_active,
                'notes' => $notes
            ));
            if (!$saved || !$this->db->trans_status()) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('student_therapist_error', 'Mapping gagal diubah.');
                redirect($this->d['url'] . '/form/' . $mapping_id);
                return;
            }
            $inserted = 1;
        } else {
            foreach ($student_ids as $student_id) {
                $exists = $this->db->where('student_id', $student_id)
                    ->where('tahun_id', $tahun_id)
                    ->count_all_results('student_therapists');
                if ($exists) {
                    $skipped++;
                    continue;
                }

                $this->db->insert('student_therapists', array(
                    'student_id' => $student_id,
                    'tahun_id' => $tahun_id,
                    'therapist_id' => $therapist_id,
                    'is_active' => $is_active,
                    'notes' => $notes
                ));
                $inserted++;
            }

            if (!$this->db->trans_status()) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('student_therapist_error', 'Mapping gagal disimpan.');
                redirect($this->d['url'] . '/form');
                return;
            }
        }

        $this->db->trans_commit();
        $message = $mode === 'edit'
            ? 'Mapping student therapist berhasil diubah.'
            : $inserted . ' mapping berhasil disimpan.' . ($skipped > 0 ? ' ' . $skipped . ' student dilewati karena sudah memiliki mapping pada tahun tersebut.' : '');
        $this->session->set_flashdata('student_therapist_success', $message);
        redirect($this->d['url']);
    }

    public function hapus($id = 0)
    {
        if ((int) $id <= 0) {
            j(array('status' => 'gagal', 'data' => 'ID mapping tidak valid'));
            return;
        }

        $deleted = $this->db->where('id', (int) $id)->delete('student_therapists');
        j(array(
            'status' => $deleted ? 'ok' : 'gagal',
            'data' => $deleted ? 'Mapping berhasil dihapus' : 'Mapping gagal dihapus'
        ));
    }
}