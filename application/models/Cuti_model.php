<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cuti_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function simpan_cuti($data) {
        return $this->db->insert('cuti', $data);
    }

    public function get_cuti_by_user($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('tanggal_pengajuan', 'DESC');
        return $this->db->get('cuti')->result();
    }

    public function hitung_hari_cuti($awal, $akhir) {
        $start = new DateTime($awal);
        $end = new DateTime($akhir);
        $end->modify('+1 day');
        $interval = $start->diff($end);
        return $interval->days;
    }

    public function cek_overlap($user_id, $awal_cuti, $akhir_cuti)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where("('$awal_cuti' <= akhir_cuti AND '$akhir_cuti' >= awal_cuti)", NULL, FALSE);
        $query = $this->db->get('cuti');

        return $query->num_rows() > 0;
    }
}