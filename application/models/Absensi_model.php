<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {

    private $table = 'absensi';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_absensi_today($user_id, $date)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('tanggal', $date);
        $query = $this->db->get($this->table);
        
        return $query->row();
    }

    public function insert_absensi($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update_absensi($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function get_riwayat_masuk($user_id, $date)
    {
        $this->db->select('a.tanggal, a.jam_masuk, a.keterangan_masuk, a.foto_masuk, a.latitude_masuk, a.longitude_masuk, a.id_lokasi_masuk, l.nama_lokasi as nama_lokasi_masuk');
        $this->db->from($this->table . ' a');
        $this->db->join('lokasi l', 'a.id_lokasi_masuk = l.id', 'left');
        $this->db->where('a.user_id', $user_id);
        $this->db->where('a.jam_masuk IS NOT NULL');
        $this->db->where('a.tanggal', $date);
        $query = $this->db->get();
        
        return $query->result();
    }

    public function get_riwayat_pulang($user_id, $date)
    {
        $this->db->select('a.tanggal, a.jam_pulang, a.keterangan_pulang, a.foto_pulang, a.durasi_kerja, a.latitude_pulang, a.longitude_pulang, a.id_lokasi_pulang, l.nama_lokasi as nama_lokasi_pulang');
        $this->db->from($this->table . ' a');
        $this->db->join('lokasi l', 'a.id_lokasi_pulang = l.id', 'left');
        $this->db->where('a.user_id', $user_id);
        $this->db->where('a.jam_pulang IS NOT NULL');
        $this->db->where('a.tanggal', $date);
        $query = $this->db->get();
        
        return $query->result();
    }

    public function get_absensi_by_month($user_id, $year, $month)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('YEAR(tanggal)', $year);
        $this->db->where('MONTH(tanggal)', $month);
        $this->db->order_by('tanggal', 'ASC');
        $query = $this->db->get($this->table);
        
        return $query->result();
    }
}