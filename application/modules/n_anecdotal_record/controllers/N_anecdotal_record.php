<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class N_anecdotal_record extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['url'] = "n_anecdotal_record";

        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['ta'] = substr($this->d['tasm'], 0, 4);

        $wali = $this->session->userdata($this->sespre."walikelas");

        $this->d['id_kelas'] = isset($wali['id_walikelas']) ? $wali['id_walikelas'] : 0;
        $this->d['nama_kelas'] = isset($wali['nama_walikelas']) ? $wali['nama_walikelas'] : '';
    }

    public function index() {
        // List students in the homeroom teacher's class
        $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    b.id as id_siswa, b.nama, b.nis
                                                    FROM t_kelas_siswa a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_kelas = '".$this->d['id_kelas']."' AND a.ta = '".$this->d['ta']."'
                                                    ORDER BY b.nama ASC")->result_array();

    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

    public function detail($id_siswa) {
        $siswa = $this->db->query("SELECT id, nama FROM m_siswa WHERE id = '$id_siswa'")->row_array();
        $this->d['siswa'] = $siswa;

        $this->d['records'] = $this->db->query("SELECT * FROM t_anecdotal_record WHERE id_siswa = '$id_siswa' ORDER BY tanggal DESC, waktu DESC")->result_array();

        $this->d['p'] = "detail";
        $this->load->view("template_utama", $this->d);
    }

    public function save() {
        $p = $this->input->post();
        
        $id_siswa = $p['id_siswa'];
        $tanggal = $p['tanggal'];
        $catatan = $p['catatan'];
        
        // Use logged in user name as reporter
        $reporter_name = $this->session->userdata($this->sespre."user") . " (Homeroom Teacher)";

        if ($id_siswa && $tanggal && $catatan) {
            $data = array(
				'id_siswa' => $id_siswa,
				'tanggal' => $tanggal,
				'waktu' => date('H:i:s'),
				'reporter_name' => $reporter_name,
				'catatan' => $catatan,
				'ta' => $this->d['ta']
			);
			$this->db->insert('t_anecdotal_record', $data);
            
            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan..";
        } else {
            $d['status'] = "error";
            $d['data'] = "Harap isi semua kolom";
        }
        
        // Because my_helper defines function j($data) which echoes json_encode
        j($d);
    }
}
