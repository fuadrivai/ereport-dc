<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anecdotal extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
	}

	public function index()
	{
		$this->load->view('anecdotal/index');
	}

	public function search_student()
	{
		$term = $this->input->get('term');
		
		$this->db->select('id, nama as text');
		$this->db->from('m_siswa');
		if ($term) {
			$this->db->like('nama', $term);
		}
		$this->db->where('stat_data', 'A'); // Only active students
		$this->db->limit(10);
		$query = $this->db->get();
		
		$results = $query->result_array();
		echo json_encode($results);
	}

	public function get_history($id_siswa)
	{
		$this->db->select('*');
		$this->db->from('t_anecdotal_record');
		$this->db->where('id_siswa', $id_siswa);
		$this->db->order_by('tanggal', 'DESC');
		$this->db->order_by('waktu', 'DESC');
		$query = $this->db->get();
		
		$data['records'] = $query->result_array();
		$this->load->view('anecdotal/history_partial', $data);
	}

	public function save()
	{
		$id_siswa = $this->input->post('id_siswa');
		$tanggal = $this->input->post('tanggal');
		$reporter_name = $this->input->post('reporter_name');
		$catatan = $this->input->post('catatan');

		// Get active academic year
		$get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $ta = substr($get_tasm['tahun'], 0, 4);

		if ($id_siswa && $tanggal && $reporter_name && $catatan) {
			$data = array(
				'id_siswa' => $id_siswa,
				'tanggal' => $tanggal,
				'waktu' => date('H:i:s'),
				'reporter_name' => $reporter_name,
				'catatan' => $catatan,
				'ta' => $ta
			);
			$this->db->insert('t_anecdotal_record', $data);
			
			echo json_encode(array('status' => 'success'));
		} else {
			echo json_encode(array('status' => 'error', 'message' => 'All fields are required.'));
		}
	}

	public function records_by_nis($nis = null)
	{
		if (!$nis) {
			$nis = $this->input->get('nis');
		}

		if (!$nis) {
			header('Content-Type: application/json');
			echo json_encode(array('status' => 'error', 'message' => 'NIS parameter is required'));
			return;
		}

		// Find student by NIS
		$this->db->select('id, nama, nis');
		$this->db->from('m_siswa');
		$this->db->where('nis', $nis);
		$siswa = $this->db->get()->row_array();

		if (!$siswa) {
			header('Content-Type: application/json');
			echo json_encode(array('status' => 'error', 'message' => 'Student not found'));
			return;
		}

		// Fetch anecdotal records
		$this->db->select('id, tanggal, waktu, reporter_name, catatan');
		$this->db->from('t_anecdotal_record');
		$this->db->where('id_siswa', $siswa['id']);
		$this->db->order_by('tanggal', 'DESC');
		$this->db->order_by('waktu', 'DESC');
		$records = $this->db->get()->result_array();

		$response = array(
			'status' => 'success',
			'student' => $siswa,
			'records' => $records
		);

		header('Content-Type: application/json');
		echo json_encode($response);
	}
}
