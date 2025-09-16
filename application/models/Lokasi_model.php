<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lokasi_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_lokasi()
    {
        $query = $this->db->get('lokasi');
        return $query->result();
    }

    public function get_lokasi_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('lokasi');
        return $query->row();
    }

    public function insert_lokasi($data)
    {
        return $this->db->insert('lokasi', $data);
    }

    public function update_lokasi($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('lokasi', $data);
    }

    public function delete_lokasi($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('lokasi');
    }
}