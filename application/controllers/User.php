<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->model('Absensi_model');
        $this->load->model('Cuti_model');
        $this->load->model('Izin_model');
        $this->load->model('User_model');
        $this->load->model('Lokasi_model');
        $autoload['helper'] = array('url');
        $this->load->config('cloudinary');
        $this->load->library('form_validation');
        if (!$this->session->userdata('logged_in')) {
            redirect(base_url());
        }
    }

    public function index()
    {
        $user_id = $this->session->userdata('id');
        $username = $this->session->userdata('username');
        $jabatan = $this->session->userdata('jabatan');

        $bulan = date('m');
        $tahun = date('Y');

        $absensi_masuk = $this->db->where('user_id', $user_id)
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->where('jam_masuk IS NOT NULL')
            ->count_all_results('absensi');

        $cuti = $this->db->where('user_id', $user_id)
            ->where('MONTH(awal_cuti)', $bulan)
            ->where('YEAR(awal_cuti)', $tahun)
            ->count_all_results('cuti');

        $izin = $this->db->where('user_id', $user_id)
            ->where('MONTH(awal_izin)', $bulan)
            ->where('YEAR(awal_izin)', $tahun)
            ->count_all_results('izin');

        $query = $this->db->query("
            SELECT 
                a.tanggal, 
                a.jam_masuk, 
                a.jam_pulang, 
                lm.nama_lokasi AS lokasi_masuk, 
                lp.nama_lokasi AS lokasi_pulang,
                IF(a.jam_masuk IS NOT NULL, 'Masuk', 'Tidak Masuk') as status,
                CONCAT(
                    IFNULL(a.keterangan_pulang, ''), 
                    IF(a.durasi_kerja IS NOT NULL, 
                        CONCAT(' (', 
                            FLOOR(a.durasi_kerja), ' jam ', 
                            LPAD(ROUND((a.durasi_kerja - FLOOR(a.durasi_kerja)) * 100), 2, '0'), 
                            ' menit)'
                        ), 
                        ''
                    )
                ) as keterangan
            FROM absensi a
            LEFT JOIN lokasi lm ON a.id_lokasi_masuk = lm.id
            LEFT JOIN lokasi lp ON a.id_lokasi_pulang = lp.id
            WHERE a.user_id = ? 
            AND MONTH(a.tanggal) = ? 
            AND YEAR(a.tanggal) = ?

            UNION ALL

            SELECT 
                awal_izin as tanggal, 
                NULL as jam_masuk, 
                NULL as jam_pulang, 
                NULL as lokasi_masuk, 
                NULL as lokasi_pulang,
                'Izin' as status, 
                alasan_izin as keterangan
            FROM izin
            WHERE user_id = ? 
            AND MONTH(awal_izin) = ? 
            AND YEAR(awal_izin) = ?

            UNION ALL

            SELECT 
                awal_cuti as tanggal, 
                NULL as jam_masuk, 
                NULL as jam_pulang, 
                NULL as lokasi_masuk, 
                NULL as lokasi_pulang,
                'Cuti' as status, 
                alasan_cuti as keterangan
            FROM cuti
            WHERE user_id = ? 
            AND MONTH(awal_cuti) = ? 
            AND YEAR(awal_cuti) = ?

            ORDER BY tanggal ASC
        ", [$user_id, $bulan, $tahun, $user_id, $bulan, $tahun, $user_id, $bulan, $tahun]);

        $data['riwayat'] = $query->result();
        $data['username'] = $username;
        $data['jabatan'] = $jabatan;
        $data['id'] = $user_id;

        $data['absensi_masuk'] = $absensi_masuk;
        $data['cuti'] = $cuti;
        $data['izin'] = $izin;

        $this->load->view('user/dashboard', $data);
    }

    public function absen(){
        $user_id = $this->session->userdata('id');
        $today = date('Y-m-d');

        $absensi_hari_ini = $this->Absensi_model->get_absensi_today($user_id, $today);
        
        $data['username'] = $this->session->userdata('username');
        $data['jabatan'] = $this->session->userdata('jabatan');
        $data['id'] = $user_id;
        $data['jam_kerja'] = $this->session->userdata('jam_kerja');
        $data['absensi_today'] = $absensi_hari_ini;
        
        $data['riwayat_masuk'] = $this->Absensi_model->get_riwayat_masuk($user_id, $today);
        $data['riwayat_pulang'] = $this->Absensi_model->get_riwayat_pulang($user_id, $today);

        $this->load->view('user/absen', $data);
    }

    public function submit_absensi()
    {
        header('Content-Type: application/json');
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            exit;
        }

        $user_id     = $this->session->userdata('id');
        $jabatan     = $this->session->userdata('jabatan');
        $today       = date('Y-m-d');
        $current_time = date('H:i:s');
        $jam_kerja   = $this->session->userdata('jam_kerja');

        $latitude  = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');

        $id_lokasi = $this->get_nearest_location($latitude, $longitude);

        if ($jabatan === 'KARYAWAN AREA') {
            if (empty($latitude) || empty($longitude)) {
                echo json_encode(['status' => 'error', 'message' => 'Lokasi tidak terkirim']); 
                exit;
            }

            $lok_lat = $this->session->userdata('lokasi_lat');
            $lok_lon = $this->session->userdata('lokasi_lng');

            if (empty($lok_lat) || empty($lok_lon)) {
                echo json_encode(['status' => 'error', 'message' => 'Lokasi user kosong']); 
                exit;
            }

            $distance = $this->calculate_distance($latitude, $longitude, $lok_lat, $lok_lon);
            if ($distance > 1000) {
                echo json_encode(['status' => 'error','message' => 'Anda di luar lokasi absensi. Jarak: '.round($distance).' m']); 
                exit;
            }
        }

        try {
            $absensi_today = $this->Absensi_model->get_absensi_today($user_id, $today);

            $image_data = $_POST['image_data'];
            $image_data = str_replace('data:image/jpeg;base64,', '', $image_data);
            $image_data = str_replace(' ', '+', $image_data);
            $image_binary = base64_decode($image_data);

            $cloudinary_url = $this->upload_to_cloudinary($image_binary);

            if (!$cloudinary_url) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
                return;
            }

            if (!$absensi_today) {
                $data_absensi = [
                    'user_id' => $user_id,
                    'tanggal' => $today,
                    'jam_masuk' => $current_time,
                    'foto_masuk' => $cloudinary_url,
                    'keterangan_masuk' => 'Tepat waktu',
                    'created_at' => date('Y-m-d H:i:s'),
                    'latitude_masuk' => $latitude,
                    'longitude_masuk' => $longitude,
                    'id_lokasi_masuk' => $id_lokasi,
                ];

                $result = $this->Absensi_model->insert_absensi($data_absensi);

                if ($result) {
                    echo json_encode([
                        'status' => 'success', 
                        'message' => 'Absensi masuk berhasil dicatat pada ' . $current_time,
                        'type' => 'masuk'
                    ]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to save attendance']);
                }

            } elseif ($absensi_today->jam_pulang == null) {
                $jam_masuk = strtotime($absensi_today->jam_masuk);
                $jam_pulang = strtotime($current_time);
                $durasi_kerja = ($jam_pulang - $jam_masuk) / 3600;

                $keterangan_pulang = '';
                if ($durasi_kerja >= $jam_kerja) {
                    $keterangan_pulang = 'Tepat waktu';
                } else {
                    $keterangan_pulang = 'Pulang cepat';
                }

                $data_update = [
                    'jam_pulang' => $current_time,
                    'foto_pulang' => $cloudinary_url,
                    'keterangan_pulang' => $keterangan_pulang,
                    'durasi_kerja' => round($durasi_kerja, 2),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'latitude_pulang' => $latitude,
                    'longitude_pulang' => $longitude,
                    'id_lokasi_pulang' => $id_lokasi,
                ];

                $result = $this->Absensi_model->update_absensi($absensi_today->id, $data_update);

                if ($result) {
                    echo json_encode([
                        'status' => 'success', 
                        'message' => 'Absensi pulang berhasil dicatat pada ' . $current_time,
                        'type' => 'pulang',
                        'durasi_kerja' => round($durasi_kerja, 2) . ' jam'
                    ]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update attendance']);
                }

            } else {
                echo json_encode(['status' => 'error', 'message' => 'Anda sudah menyelesaikan absensi hari ini']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    private function get_nearest_location($user_lat, $user_lon)
    {
        if (empty($user_lat) || empty($user_lon)) {
            return 999;
        }
        
        $lokasi_list = $this->Lokasi_model->get_all_lokasi();
        
        if (empty($lokasi_list)) {
            return 999;
        }

        $nearest_distance = PHP_FLOAT_MAX;
        $nearest_id = 999;
        
        foreach ($lokasi_list as $lokasi) {
            $distance = $this->calculate_distance(
                $user_lat, 
                $user_lon, 
                $lokasi->latitude, 
                $lokasi->longitude
            );
            
            if ($distance < $nearest_distance) {
                $nearest_distance = $distance;
                $nearest_id = $lokasi->id;
            }
        }
        
        if ($nearest_distance > 1000) {
            return 999;
        }
        
        return $nearest_id;
    }

    private function calculate_distance($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earth_radius * $c;
    }


    private function upload_to_cloudinary($image_binary)
    {
        $cloudinary_config = $this->config->item('cloudinary');
        $cloud_name = $cloudinary_config['cloud_name'];
        $api_key = $cloudinary_config['api_key'];
        $api_secret = $cloudinary_config['api_secret'];
        $folder = $cloudinary_config['folder'];

        $timestamp = time();
        
        $params_to_sign = [
            'folder' => $folder,
            'timestamp' => $timestamp
        ];
        
        ksort($params_to_sign);
        $string_to_sign = '';
        foreach ($params_to_sign as $key => $value) {
            $string_to_sign .= $key . '=' . $value . '&';
        }
        $string_to_sign = rtrim($string_to_sign, '&') . $api_secret;
        $signature = sha1($string_to_sign);

        $post_data = [
            'file' => 'data:image/jpeg;base64,' . base64_encode($image_binary),
            'api_key' => $api_key,
            'timestamp' => $timestamp,
            'folder' => $folder,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $result = json_decode($response, true);
            return $result['secure_url'] ?? false;
        }

        log_message('error', 'Cloudinary upload failed: ' . $response);
        return false;
    }

    public function cuti() {
        $data['username'] = $this->session->userdata('username');
        $this->load->view('user/cuti', $data);
    }

    public function aksi_cuti() {
        $this->form_validation->set_rules('alasan_cuti', 'Alasan Cuti', 'required|trim');
        $this->form_validation->set_rules('awal_cuti', 'Awal Cuti', 'required');
        $this->form_validation->set_rules('akhir_cuti', 'Akhir Cuti', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(base_url('user/cuti'));
        } else {
            $user_id   = $this->session->userdata('id');
            $awal_cuti = $this->input->post('awal_cuti');
            $akhir_cuti = $this->input->post('akhir_cuti');

            $cek_overlap = $this->Cuti_model->cek_overlap($user_id, $awal_cuti, $akhir_cuti);

            if ($cek_overlap) {
                $this->session->set_flashdata('error', 'Tanggal cuti yang dipilih bertabrakan dengan pengajuan cuti sebelumnya.');
                redirect(base_url('user/cuti'));
            }

            $data = array(
                'user_id' => $user_id,
                'alasan_cuti' => $this->input->post('alasan_cuti'),
                'awal_cuti' => $awal_cuti,
                'akhir_cuti' => $akhir_cuti
            );

            $result = $this->Cuti_model->simpan_cuti($data);

            if ($result) {
                $this->session->set_flashdata('success', 'Pengajuan cuti berhasil dikirim!');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengajukan cuti. Silakan coba lagi.');
            }

            redirect(base_url('user/cuti'));
        }
    }

    public function izin() {
        $data['username'] = $this->session->userdata('username');
        $this->load->view('user/izin', $data);
    }

    public function aksi_izin()
    {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Anda harus login terlebih dahulu');
            redirect('login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->session->set_flashdata('error', 'Metode request tidak valid');
            redirect(base_url('user/izin'));
            return;
        }

        $user_id = $this->session->userdata('id');
        $alasan_izin = $this->input->post('alasan_izin');
        $awal_izin   = $this->input->post('awal_izin');
        $akhir_izin  = $this->input->post('akhir_izin');
        $tanggal_pengajuan = date('Y-m-d H:i:s');

        try {
            $cek_tabrakan = $this->Izin_model->cek_tanggal_tabrakan($user_id, $awal_izin, $akhir_izin);

            if ($cek_tabrakan) {
                $this->session->set_flashdata('error', 'Tanggal izin bertabrakan dengan pengajuan izin/cuti lainnya');
                redirect(base_url('user/izin'));
                return;
            }

            if (empty($_FILES['bukti_izin']['tmp_name'])) {
                $this->session->set_flashdata('error', 'File bukti izin harus diupload');
                redirect(base_url('user/izin'));
                return;
            }

            $image_binary = file_get_contents($_FILES['bukti_izin']['tmp_name']);
            $cloudinary_url = $this->upload_to_cloudinary($image_binary);

            if (!$cloudinary_url) {
                $this->session->set_flashdata('error', 'Gagal mengupload gambar ke server');
                redirect(base_url('user/izin'));
                return;
            }

            $data_izin = [
                'user_id' => $user_id,
                'alasan_izin' => $alasan_izin,
                'awal_izin' => $awal_izin,
                'akhir_izin' => $akhir_izin,
                'tanggal_pengajuan' => $tanggal_pengajuan,
                'foto_izin' => $cloudinary_url,
            ];

            $result = $this->Izin_model->insert_izin($data_izin);

            if ($result) {
                $this->session->set_flashdata('success', 'Pengajuan izin berhasil dikirim');
                redirect(base_url('user/izin'));
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan pengajuan izin');
                redirect(base_url('user/izin'));
            }

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            redirect(base_url('user/izin'));
        }
    }

    public function profil() {
        $data['username'] = $this->session->userdata('username');
        $this->load->view('user/profil', $data);
    }

    public function ubah_username() {
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[3]|max_length[20]|is_unique[user.username]');
        
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            $this->session->set_flashdata('error', $error_message);
            redirect(base_url('user/profil'));
        } else {
            $new_username = $this->input->post('username');
            $user_id = $this->session->userdata('id');
            
            if ($this->User_model->update_username($user_id, $new_username)) {
                $this->session->set_userdata('username', $new_username);
                $this->session->set_flashdata('success', 'Username berhasil diubah');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengubah username');
            }
            
            redirect(base_url('user/profil'));
        }
    }

    public function ubah_password() {
        // Validasi form
        $this->form_validation->set_rules('password_lama', 'Password Lama', 'required');
        $this->form_validation->set_rules('password_baru', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('konfirmasi_password_baru', 'Konfirmasi Password Baru', 'required|matches[password_baru]');
        
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            $this->session->set_flashdata('error', $error_message);
            redirect(base_url('user/profil'));
        } else {
            $password_lama = $this->input->post('password_lama');
            $password_baru = $this->input->post('password_baru');
            $user_id = $this->session->userdata('id');
            
            if ($this->User_model->verify_password($user_id, $password_lama)) {
                if ($this->User_model->update_password($user_id, $password_baru)) {
                    $this->session->set_flashdata('success', 'Password berhasil diubah');
                } else {
                    $this->session->set_flashdata('error', 'Gagal mengubah password');
                }
            } else {
                $this->session->set_flashdata('error', 'Password lama tidak sesuai');
            }
            
            redirect(base_url('user/profil'));
        }
    }

    public function riwayat_absensi() {
        $this->authorize(['admin']);
        $data['username'] = $this->session->userdata('username');
        $this->load->view('user/riwayat_absensi', $data);
    }

    public function get_karyawan()
    {
        header('Content-Type: application/json');
        
        try {
            $search = $this->input->get('search');
            $results = array();

            if (strlen($search) >= 3) {
                $this->db->select('id, nama as text');
                $this->db->from('user');
                $this->db->group_start();
                $this->db->like('nama', $search);
                $this->db->or_like('username', $search);
                $this->db->group_end();
                $this->db->order_by('nama', 'ASC');
                $query = $this->db->get();

                if ($query) {
                    $results = $query->result_array();
                }
            }

            $response = array();
            foreach ($results as $row) {
                $response[] = array(
                    'id' => $row['id'],
                    'text' => $row['text']
                );
            }

            echo json_encode($response);
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_karyawan: ' . $e->getMessage());
            echo json_encode(array());
        }
    }

    public function get_absensi_karyawan() {
        header('Content-Type: application/json');
        
        try {
            $this->authorize(['admin']);
            
            $employee_id = $this->input->post('employee_id');
            $date_range_type = $this->input->post('date_range_type');
            
            if (empty($employee_id)) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'ID karyawan tidak valid'
                ));
                return;
            }
            
            if (empty($date_range_type)) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Jenis filter tanggal tidak valid'
                ));
                return;
            }

            $this->db->where('id', $employee_id);
            $employee = $this->db->get('user')->row();
            
            if (!$employee) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Karyawan tidak ditemukan'
                ));
                return;
            }

            if ($date_range_type === 'daily') {
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                
                if (empty($start_date) || empty($end_date)) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Tanggal mulai dan tanggal akhir harus diisi'
                    ));
                    return;
                }
                
                if (!$this->validateDate($start_date) || !$this->validateDate($end_date)) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Format tanggal tidak valid'
                    ));
                    return;
                }
                
                if (strtotime($start_date) > strtotime($end_date)) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir'
                    ));
                    return;
                }
                
            } elseif ($date_range_type === 'monthly') {
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                
                if (empty($month) || empty($year)) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Bulan dan tahun harus diisi'
                    ));
                    return;
                }
                
                if (!is_numeric($month) || !is_numeric($year) || 
                    $month < 1 || $month > 12 || 
                    $year < 2020 || $year > date('Y')) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Bulan atau tahun tidak valid'
                    ));
                    return;
                }
                
                $start_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
                $end_date = date('Y-m-t', strtotime($start_date)); // Last day of month
                
            } else {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Tipe filter tidak dikenali'
                ));
                return;
            }

            $this->db->select('
                absensi.*,
                user.nama as nama_karyawan,
                DATE_FORMAT(absensi.tanggal, "%d %M %Y") as tanggal_formatted,
                DAYNAME(absensi.tanggal) as hari,
                TIME(absensi.jam_masuk) as jam_masuk_only,
                TIME(absensi.jam_pulang) as jam_pulang_only,
                lokasi_masuk.nama_lokasi as nama_lokasi_masuk,
                lokasi_pulang.nama_lokasi as nama_lokasi_pulang
            ');
            $this->db->from('absensi');
            $this->db->join('user', 'user.id = absensi.user_id', 'left');
            $this->db->join('lokasi as lokasi_masuk', 'lokasi_masuk.id = absensi.id_lokasi_masuk', 'left');
            $this->db->join('lokasi as lokasi_pulang', 'lokasi_pulang.id = absensi.id_lokasi_pulang', 'left');
            $this->db->where('absensi.user_id', $employee_id);
            $this->db->where('absensi.tanggal >=', $start_date);
            $this->db->where('absensi.tanggal <=', $end_date);
            $this->db->order_by('absensi.tanggal', 'DESC');
            
            $query = $this->db->get();
            
            if (!$query) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data dari database'
                ));
                return;
            }
            
            $absensi_data = $query->result();

            $formatted_data = array();
            foreach ($absensi_data as $absensi) {
                $status = 'Tidak Absen';
                if ($absensi->jam_masuk && $absensi->jam_pulang) {
                    $status = 'Masuk';
                } elseif ($absensi->jam_masuk && !$absensi->jam_pulang) {
                    $status = 'Masuk (Belum Pulang)';
                }
                
                $keterangan_parts = array();
                if (!empty($absensi->keterangan_masuk)) {
                    $keterangan_parts[] = 'Masuk: ' . $absensi->keterangan_masuk;
                }
                if (!empty($absensi->keterangan_pulang)) {
                    $keterangan_parts[] = 'Pulang: ' . $absensi->keterangan_pulang;
                }
                if (!empty($absensi->durasi_kerja)) {
                    $keterangan_parts[] = 'Durasi: ' . $absensi->durasi_kerja . ' jam';
                }
                
                $keterangan = !empty($keterangan_parts) ? implode(' | ', $keterangan_parts) : '';

                $lokasi_masuk = '';
                if (!empty($absensi->nama_lokasi_masuk)) {
                    $lokasi_masuk = $absensi->nama_lokasi_masuk;
                    if (!empty($absensi->latitude_masuk) && !empty($absensi->longitude_masuk)) {
                        $lokasi_masuk .= ' (' . $absensi->latitude_masuk . ', ' . $absensi->longitude_masuk . ')';
                    }
                }
                
                $lokasi_pulang = '';
                if (!empty($absensi->nama_lokasi_pulang)) {
                    $lokasi_pulang = $absensi->nama_lokasi_pulang;
                    if (!empty($absensi->latitude_pulang) && !empty($absensi->longitude_pulang)) {
                        $lokasi_pulang .= ' (' . $absensi->latitude_pulang . ', ' . $absensi->longitude_pulang . ')';
                    }
                }

                $formatted_data[] = array(
                    'tanggal' => $absensi->tanggal,
                    'tanggal_formatted' => $absensi->tanggal_formatted,
                    'hari' => $this->getIndonesianDay($absensi->hari),
                    'jam_masuk' => $absensi->jam_masuk_only ? date('H:i', strtotime($absensi->jam_masuk_only)) : null,
                    'jam_pulang' => $absensi->jam_pulang_only ? date('H:i', strtotime($absensi->jam_pulang_only)) : null,
                    'lokasi_masuk' => $lokasi_masuk ?: null,
                    'lokasi_pulang' => $lokasi_pulang ?: null,
                    'status' => $status,
                    'keterangan' => $keterangan,
                    'foto_masuk' => $absensi->foto_masuk ? $absensi->foto_masuk : null,
                    'foto_pulang' => $absensi->foto_pulang ? $absensi->foto_pulang : null,
                    'nama_karyawan' => $absensi->nama_karyawan,
                    // Data tambahan untuk debugging/report
                    'latitude_masuk' => $absensi->latitude_masuk,
                    'longitude_masuk' => $absensi->longitude_masuk,
                    'latitude_pulang' => $absensi->latitude_pulang,
                    'longitude_pulang' => $absensi->longitude_pulang,
                    'durasi_kerja' => $absensi->durasi_kerja
                );
            }
            
            $summary = $this->generateAttendanceSummary($formatted_data);
            
            echo json_encode(array(
                'status' => 'success',
                'data' => $formatted_data,
                'summary' => $summary,
                'message' => 'Data berhasil dimuat',
                'employee_name' => $employee->nama,
                'date_range' => array(
                    'type' => $date_range_type,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'formatted' => $this->formatDateRange($date_range_type, $start_date, $end_date)
                )
            ));
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_absensi_karyawan: ' . $e->getMessage());
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ));
        }
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    private function generateAttendanceSummary($data) {
        $total_days = count($data);
        $present_days = 0;
        $late_days = 0;
        $incomplete_days = 0;
        $absent_days = 0;
        
        $standard_work_time = '08:00';
        
        foreach ($data as $record) {
            if ($record['status'] === 'Masuk') {
                $present_days++;
                
                // Cek keterlambatan
                if ($record['jam_masuk'] && strtotime($record['jam_masuk']) > strtotime($standard_work_time)) {
                    $late_days++;
                }
            } elseif ($record['status'] === 'Masuk (Belum Pulang)') {
                $incomplete_days++;
            } else {
                $absent_days++;
            }
        }
        
        return array(
            'total_days' => $total_days,
            'present_days' => $present_days,
            'late_days' => $late_days,
            'incomplete_days' => $incomplete_days,
            'absent_days' => $absent_days,
            'attendance_rate' => $total_days > 0 ? round(($present_days / $total_days) * 100, 1) : 0
        );
    }

    private function formatDateRange($type, $start_date, $end_date) {
        if ($type === 'daily') {
            return date('d M Y', strtotime($start_date)) . ' - ' . date('d M Y', strtotime($end_date));
        } elseif ($type === 'monthly') {
            return date('F Y', strtotime($start_date));
        }
        return '';
    }

    private function getIndonesianDay($englishDay) {
        $days = array(
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        );
        
        return $days[$englishDay] ?? $englishDay;
    }

    private function authorize($roles) {
        $userRole = $this->session->userdata('role');
        if (!in_array($userRole, $roles)) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses'
                ));
                exit;
            } else {
                show_error('Anda tidak memiliki akses', 403);
            }
        }
    }
}