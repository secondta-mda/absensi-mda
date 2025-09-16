<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Izin_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insert_izin($data)
    {
        return $this->db->insert('izin', $data);
    }

    public function cek_tanggal_tabrakan($user_id, $awal, $akhir)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where("(
            (awal_izin <= '$akhir' AND akhir_izin >= '$awal')
        )", null, false); 

        $query = $this->db->get('izin');
        return $query->num_rows() > 0;
    }

}