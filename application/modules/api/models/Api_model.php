<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model {

    public function get_monthly_data($id_siswa) {
        $this->db->select('m.nama, t.desk');
        $this->db->from('t_nilai_monthly t');
        $this->db->join('m_monthly m', 't.id_ekstra = m.id');
        $this->db->where('t.id_siswa', $id_siswa);
        $this->db->where('t.desk !=', '-');  // Exclude empty strings
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
}
?>
