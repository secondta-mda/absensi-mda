<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('encryption');
    }

    public function update_username($user_id, $new_username) {
        $data = array(
            'username' => $new_username
        );
        
        $this->db->where('id', $user_id);
        return $this->db->update('user', $data);
    }

    public function verify_password($user_id, $password) {
        $this->db->select('password');
        $this->db->where('id', $user_id);
        $query = $this->db->get('user');
        
        if ($query->num_rows() > 0) {
            $hashed_password = $query->row()->password;
            return md5($password) === $hashed_password;
        }
        
        return false;
    }

    public function update_password($user_id, $new_password) {
        $data = array(
            'password' => md5($new_password)
        );
        
        $this->db->where('id', $user_id);
        return $this->db->update('user', $data);
    }
}