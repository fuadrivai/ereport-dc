<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keterampilan extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['url'] = "keterampilan";
        $this->d['idnya'] = "dataprojek";
        $this->d['nama_form'] = "f_keterampilan";
        
        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['tahun'] = substr( $this->d['tasm'], 0, -1);
        $this->d['semester'] = substr($this->d['tasm'], -1, 1);
    }
    
    public function index() {
    	$this->d['p'] = "list_keterampilan";
        $this->load->view("template_utama", $this->d);
    }
    public function view_set_kelas() {
    	$this->d['p'] = "list_kelas";
        $this->load->view("template_utama", $this->d);
    }
    public function homeroom() {
        $this->d['list_mapelkelas'] = $this->db->query("SELECT t.id id, k.nama mapel,kl.nama kelas FROM `t_guru_keterampilan` t 
            LEFT JOIN m_keterampilan k on k.id = t.id_keterampilan
            LEFT JOIN m_guru g on g.id = t.id_guru
            LEFT JOIN m_kelas kl on kl.id = t.id_kelas
            WHERE tasm = '". $this->d['tasm']."' AND t.id_guru = ". $this->d['admkonid']."") ->result_array();
    	$this->d['p'] = "list_keterampilan_homeroom";
        $this->load->view("template_utama", $this->d);
    }
    
    public function view_keterampilan() {
        $this->d['p'] = "list_keterampilan_homeroom";
        $this->load->view("template_utama", $this->d);
    }
    
    public function kompetensi($id) {
        $this->d['detil_mp'] = $this->db->query("SELECT 
                                        a.*, b.nama nmmapel, c.nama nmkelas, c.tingkat tingkat,g.nama nama_guru
                                        FROM t_guru_keterampilan a
                                        LEFT JOIN m_keterampilan b ON a.id_keterampilan = b.id 
                                        LEFT JOIN m_kelas c ON a.id_kelas = c.id 
                                        LEFT JOIN m_guru g ON g.id = a.id_guru
                                        WHERE a.id  = '$id'")->row_array();
                                        
        $this->d['list_kd'] = $this->db->query("SELECT * FROM t_kompetensi_kd 
                                    WHERE id_guru = '".$this->d['detil_mp']['id_guru']."'
                                    AND id_keterampilan = '".$this->d['detil_mp']['id_keterampilan']."'
                                    AND tingkat = '".$this->d['detil_mp']['tingkat']."'
                                    AND semester = '".$this->d['semester']."'")->result_array();
                                    
        $this->d['list_siswa'] = $this->db->query("SELECT s.id id_siswa,k.id id_kelas, s.nama nama_siswa , k.nama FROM `m_kelas` k
                                LEFT JOIN t_kelas_siswa ks on k.id = ks.id_kelas
                                LEFT JOIN m_siswa s on s.id = ks.id_siswa
                                WHERE ta = {$this->d['tahun']} AND k.id = {$this->d['detil_mp']['id_kelas']} ORDER by s.nama ASC")->result_array();

        $this->d['p'] = "list_kompetensi";
        $this->d['id']=$id;
        $this->load->view("template_utama", $this->d);
    }

    
    public function set_kelas() {
        $q = $this->db->query("SELECT id, nama FROM m_guru ORDER BY nama ASC");
        $r = $this->db->query("SELECT id, nama FROM m_keterampilan ORDER BY nama ASC");
        $s = $this->db->query("SELECT id, nama FROM m_kelas ORDER BY nama ASC");

        $this->d['r_guru'] = $q->result_array();
        $this->d['r_mapel'] = $r->result_array();
        $this->d['r_kelas'] = $s->result_array();
        
    	$this->d['p'] = "form_set_kelas";
        $this->load->view("template_utama", $this->d);
    }
    
     public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT id FROM m_keterampilan")->num_rows();
    
        $q_datanya = $this->db->query("SELECT * FROM `m_keterampilan` WHERE nama LIKE '%".$search['value']."%' OR kode LIKE '%".$search['value']."%' OR deskripsi LIKE '%".$search['value']."%' ORDER BY nama ASC LIMIT ".$start.", ".$length."")->result_array();
        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[] = $no++;
            $data_ok[] = $d['kode'];
            $data_ok[] = $d['nama'];
            $data_ok[] = $d['deskripsi'];
            //$data_ok[3] = ($d['is_sikap'] == "0") ? '<i class="fa fa-minus-circle"></i>' : '<i class="fa fa-check-circle"></i>';
            $data_ok[] = '<a href="#" onclick="return edit(\''.$d['id'].'\');" class="btn btn-xs btn-success edit-skill"><i class="fa fa-edit"></i> Edit</a> 
                <a href="#" onclick="return hapus(\''.$d['id'].'\');" class="btn btn-xs btn-danger"><i class="fa fa-remove"></i> Hapus</a> ';

            $data[] = $data_ok;
        }
        $json_data = array(
                    "draw" => $draw,
                    "iTotalRecords" => $d_total_row,
                    "iTotalDisplayRecords" => $d_total_row,
                    "data" => $data
                );
        j($json_data);
        exit;
    }
     public function serverside() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        
        $query = $this->db->query("SELECT
                                    a.id, b.nama nmguru, c.nama nmkelas, d.nama nmmapel
                                    FROM t_guru_keterampilan a
                                    INNER JOIN m_guru b ON a.id_guru = b.id
                                    INNER JOIN m_kelas c ON a.id_kelas = c.id
                                    INNER JOIN m_keterampilan d ON a.id_keterampilan = d.id
                                    WHERE a.tasm = '".$this->d['tasm']."' AND 
                                    (b.nama LIKE '%".$search['value']."%' 
                                    OR c.nama LIKE '%".$search['value']."%'
                                    OR d.nama LIKE '%".$search['value']."%')
                                    ORDER BY nmguru ASC, nmmapel ASC, nmkelas ASC
                                    LIMIT ".$start.", ".$length."");
        $q_datanya = $query->result_array();
        $d_total_row = $query->num_rows();
        
        $data = array();
        $no = ($start+1);
        
        foreach($q_datanya as $d) {
            $data_ok = array();
            $data_ok[0] = $no++;
            $data_ok[1] = $d['nmguru'];
            $data_ok[2] = $d['nmmapel'];
            $data_ok[3] = $d['nmkelas'];
            $data_ok[4] = '<a href="#" onclick="return hapus(\''.$d['id'].'\');" class="btn btn-xs btn-danger"><i class="fa fa-remove"></i> Hapus</a> ';

            $data[] = $data_ok;
        }

        $json_data = array(
                    "draw" => $draw,
                    "iTotalRecords" => $d_total_row,
                    "iTotalDisplayRecords" => $d_total_row,
                    "data" => $data
                );
        j($json_data);
        exit;
    }
    
    public function simpan()
    {
        $p = $this->input->post();

        $d['status'] = "";
        $d['data'] = "";
        $d['date'] = date("Y-m-d h:i:s");
        
        if($p['id']=="" || $p['id']==null){
            $this->db->query("INSERT INTO m_keterampilan (kode, nama,deskripsi) VALUES ('" . $p['kode'] . "', '" . $p['nama'] . "','" . $p['deskripsi'] . "')");
        }else{
            $this->db->query("UPDATE m_keterampilan SET kode='".$p['kode']."', nama='".$p['nama']."',deskripsi='".$p['deskripsi']."' WHERE id={$p['id']}");
        }

        j($d);
    }
    
    public function edit($id) {
        $q = $this->db->query("SELECT * FROM m_keterampilan WHERE id = '$id'")->row_array();

        $d = array();
        $d['status'] = "ok";
        if (empty($q)) {
            $d['data']['id'] = "";
            $d['data']['mode'] = "add";
            $d['data']['deskripsi'] = "";
            $d['data']['nama'] = "";
        } else {
            $d['data'] = $q;
        }
        j($d);
    }
    
    public function simpan_guru_keterampilan() {
        $p = $this->input->post();

        $jumlah_sudah = 0;

        foreach ($p['data_pilih'] as $s) {
            $cek = $this->db->query("SELECT id FROM t_guru_keterampilan WHERE tasm = '".$this->d['tasm']."' AND id_keterampilan = '".$p['keterampilan']."' AND id_kelas = '$s'")->num_rows();

            if ($cek > 0) {
                $jumlah_sudah ++;
                $this->session->set_flashdata('k', '<div class="alert alert-danger">'.$jumlah_sudah.' mata pelajaran, sudah ada gurunya. Data tidak masuk..</div>');
            } else {
                $this->db->query("INSERT INTO t_guru_keterampilan (tasm, id_guru, id_kelas, id_keterampilan) VALUES ('".$this->d['tasm']."', '".$p['guru']."', '".$s."', '".$p['keterampilan']."')");
            }
        }
        redirect($this->d['url']."/view_set_kelas");
    }
    
    public function simpan_kd() {
        // $p = $this->input->post();
        $json = json_decode(trim(file_get_contents('php://input')), true);
        $d['status'] = "";
        $d['data'] = "";
        try {
            if($json['id_kd']=="" || $json['id_kd']==null){
                $this->db->query("INSERT INTO t_kompetensi_kd (id_guru, id_keterampilan, tingkat, no_kd, nama_kd, semester, mid_final,nama_keterampilan,nama_guru) VALUES ('".$this->d['admkonid']."', {$json['id_keterampilan']}, '".$json['tingkat']."', '".$json['kode']."', '".addslashes($json['nama_kd'])."', '".$json['semester']."', '".$json['semester']."', '".$json['nama_keterampilan']."', '".$json['nama_guru']."')");
            }else{
                $this->db->query("UPDATE t_kompetensi_kd  SET id_guru='".$this->d['admkonid']."',id_keterampilan='".$json['id_keterampilan']."',tingkat='".$json['tingkat']."',no_kd='".$json['kode']."',nama_kd='".addslashes($json['nama_kd'])."',semester='".$json['semester']."',mid_final='".$json['semester']."',nama_keterampilan='".$json['nama_keterampilan']."',nama_guru='".$json['nama_guru']."' WHERE id = {$json['id_kd']}");
            }
            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan";
        } catch (Exception $e) {
             $d['status'] = "gagal";
            $d['data'] = "Kesalahan sistem";
        }
        j($d);
    }
    public function simpan_detail_kd() {
        // $p = $this->input->post();
        $json = json_decode(trim(file_get_contents('php://input')), true);
        $d['status'] = "";
        $d['data'] = "";
        try {
            if($json['id']=="" || $json['id']==null){
                $this->db->query("INSERT INTO t_kompetensi_detail (id_kompetensi,urutan,nama) VALUES ({$json['id_kompetensi']}, '{$json['urutan']}','{$json['nama_detail']}')");
            }else{
                $this->db->query("UPDATE t_kompetensi_detail SET id_kompetensi={$json['id_kompetensi']},urutan='{$json['urutan']}', nama='{$json['nama_detail']}' WHERE id={$json['id']}");
            }
            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan";
        } catch (Exception $e) {
             $d['status'] = "gagal";
            $d['data'] = "Kesalahan sistem";
        }
        
        j($d);
    }
    
    

}