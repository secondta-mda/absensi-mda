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

        // Enhanced query untuk data riwayat dengan format yang sama seperti di riwayat absensi
        $combined_data = $this->getCombinedDataForDashboard($user_id, $bulan, $tahun);
        $formatted_data = $this->formatCombinedDataForDashboard($combined_data);

        $data['riwayat'] = $formatted_data;
        $data['username'] = $username;
        $data['jabatan'] = $jabatan;
        $data['id'] = $user_id;

        $data['absensi_masuk'] = $absensi_masuk;
        $data['cuti'] = $cuti;
        $data['izin'] = $izin;

        $this->load->view('user/dashboard', $data);
    }

    private function getCombinedDataForDashboard($user_id, $bulan, $tahun)
    {
        $combined_data = array();
        
        // Get absensi data
        $this->db->select('
            "absensi" as type,
            absensi.tanggal,
            user.nama as nama_karyawan,
            DATE_FORMAT(absensi.tanggal, "%d %M %Y") as tanggal_formatted,
            DAYNAME(absensi.tanggal) as hari,
            TIME(absensi.jam_masuk) as jam_masuk_only,
            TIME(absensi.jam_pulang) as jam_pulang_only,
            CASE 
                WHEN absensi.id_lokasi_masuk = 999 THEN CONCAT("Koordinat: ", absensi.latitude_masuk, ", ", absensi.longitude_masuk)
                ELSE lokasi_masuk.nama_lokasi 
            END as nama_lokasi_masuk,
            CASE 
                WHEN absensi.id_lokasi_pulang = 999 THEN CONCAT("Koordinat: ", absensi.latitude_pulang, ", ", absensi.longitude_pulang)
                ELSE lokasi_pulang.nama_lokasi 
            END as nama_lokasi_pulang,
            absensi.foto_masuk,
            absensi.foto_pulang,
            absensi.latitude_masuk,
            absensi.longitude_masuk,
            absensi.latitude_pulang,
            absensi.longitude_pulang,
            absensi.keterangan_masuk,
            absensi.keterangan_pulang,
            absensi.durasi_kerja,
            absensi.id_lokasi_masuk,
            absensi.id_lokasi_pulang,
            "" as alasan,
            "" as awal_periode,
            "" as akhir_periode,
            "" as foto_dokumen
        ', FALSE);
        $this->db->from('absensi');
        $this->db->join('user', 'user.id = absensi.user_id', 'left');
        $this->db->join('lokasi as lokasi_masuk', 'lokasi_masuk.id = absensi.id_lokasi_masuk AND absensi.id_lokasi_masuk != 999', 'left');
        $this->db->join('lokasi as lokasi_pulang', 'lokasi_pulang.id = absensi.id_lokasi_pulang AND absensi.id_lokasi_pulang != 999', 'left');
        $this->db->where('absensi.user_id', $user_id);
        $this->db->where('MONTH(absensi.tanggal)', $bulan);
        $this->db->where('YEAR(absensi.tanggal)', $tahun);
        
        $absensi_query = $this->db->get();
        if ($absensi_query) {
            $combined_data = array_merge($combined_data, $absensi_query->result());
        }
        
        // Get cuti data
        $this->db->select('
            "cuti" as type,
            cuti.awal_cuti as tanggal,
            user.nama as nama_karyawan,
            DATE_FORMAT(cuti.awal_cuti, "%d %M %Y") as tanggal_formatted,
            DAYNAME(cuti.awal_cuti) as hari,
            "" as jam_masuk_only,
            "" as jam_pulang_only,
            "" as nama_lokasi_masuk,
            "" as nama_lokasi_pulang,
            "" as foto_masuk,
            "" as foto_pulang,
            "" as latitude_masuk,
            "" as longitude_masuk,
            "" as latitude_pulang,
            "" as longitude_pulang,
            "" as keterangan_masuk,
            "" as keterangan_pulang,
            "" as durasi_kerja,
            "" as id_lokasi_masuk,
            "" as id_lokasi_pulang,
            cuti.alasan_cuti as alasan,
            cuti.awal_cuti as awal_periode,
            cuti.akhir_cuti as akhir_periode,
            "" as foto_dokumen
        ', FALSE);
        $this->db->from('cuti');
        $this->db->join('user', 'user.id = cuti.user_id', 'left');
        $this->db->where('cuti.user_id', $user_id);
        $this->db->where('MONTH(cuti.awal_cuti)', $bulan);
        $this->db->where('YEAR(cuti.awal_cuti)', $tahun);
        
        $cuti_query = $this->db->get();
        if ($cuti_query) {
            $combined_data = array_merge($combined_data, $cuti_query->result());
        }
        
        // Get izin data
        $this->db->select('
            "izin" as type,
            izin.awal_izin as tanggal,
            user.nama as nama_karyawan,
            DATE_FORMAT(izin.awal_izin, "%d %M %Y") as tanggal_formatted,
            DAYNAME(izin.awal_izin) as hari,
            "" as jam_masuk_only,
            "" as jam_pulang_only,
            "" as nama_lokasi_masuk,
            "" as nama_lokasi_pulang,
            "" as foto_masuk,
            "" as foto_pulang,
            "" as latitude_masuk,
            "" as longitude_masuk,
            "" as latitude_pulang,
            "" as longitude_pulang,
            "" as keterangan_masuk,
            "" as keterangan_pulang,
            "" as durasi_kerja,
            "" as id_lokasi_masuk,
            "" as id_lokasi_pulang,
            izin.alasan_izin as alasan,
            izin.awal_izin as awal_periode,
            izin.akhir_izin as akhir_periode,
            izin.foto_izin as foto_dokumen
        ', FALSE);
        $this->db->from('izin');
        $this->db->join('user', 'user.id = izin.user_id', 'left');
        $this->db->where('izin.user_id', $user_id);
        $this->db->where('MONTH(izin.awal_izin)', $bulan);
        $this->db->where('YEAR(izin.awal_izin)', $tahun);
        
        $izin_query = $this->db->get();
        if ($izin_query) {
            $combined_data = array_merge($combined_data, $izin_query->result());
        }
        
        // Sort by date (descending)
        usort($combined_data, function($a, $b) {
            return strtotime($b->tanggal) - strtotime($a->tanggal);
        });
        
        return $combined_data;
    }

    private function formatCombinedDataForDashboard($combined_data)
    {
        $formatted_data = array();
        
        foreach ($combined_data as $record) {
            $formatted_record = array(
                'type' => $record->type,
                'tanggal' => $record->tanggal,
                'tanggal_formatted' => $record->tanggal_formatted,
                'hari' => $this->getIndonesianDay($record->hari),
                'nama_karyawan' => $record->nama_karyawan,
            );
            
            if ($record->type === 'absensi') {
                $status = 'Tidak Absen';
                if ($record->jam_masuk_only && $record->jam_pulang_only) {
                    $status = 'Masuk';
                } elseif ($record->jam_masuk_only && !$record->jam_pulang_only) {
                    $status = 'Masuk (Belum Pulang)';
                }
                
                $keterangan_parts = array();
                if (!empty($record->keterangan_masuk)) {
                    $keterangan_parts[] = 'Masuk: ' . $record->keterangan_masuk;
                }
                if (!empty($record->keterangan_pulang)) {
                    $keterangan_parts[] = 'Pulang: ' . $record->keterangan_pulang;
                }
                if (!empty($record->durasi_kerja)) {
                    $keterangan_parts[] = 'Durasi: ' . $record->durasi_kerja . ' jam';
                }
                
                // Format lokasi masuk dengan koordinat jika id_lokasi = 999
                $lokasi_masuk = '';
                if (!empty($record->nama_lokasi_masuk)) {
                    $lokasi_masuk = $record->nama_lokasi_masuk;
                    // Jika bukan id_lokasi 999 dan ada koordinat, tambahkan koordinat dalam kurung
                    if ($record->id_lokasi_masuk != 999 && !empty($record->latitude_masuk) && !empty($record->longitude_masuk)) {
                        $lokasi_masuk .= ' (' . $record->latitude_masuk . ', ' . $record->longitude_masuk . ')';
                    }
                }
                
                // Format lokasi pulang dengan koordinat jika id_lokasi = 999
                $lokasi_pulang = '';
                if (!empty($record->nama_lokasi_pulang)) {
                    $lokasi_pulang = $record->nama_lokasi_pulang;
                    // Jika bukan id_lokasi 999 dan ada koordinat, tambahkan koordinat dalam kurung
                    if ($record->id_lokasi_pulang != 999 && !empty($record->latitude_pulang) && !empty($record->longitude_pulang)) {
                        $lokasi_pulang .= ' (' . $record->latitude_pulang . ', ' . $record->longitude_pulang . ')';
                    }
                }
                
                $formatted_record = array_merge($formatted_record, array(
                    'jam_masuk' => $record->jam_masuk_only ? date('H:i', strtotime($record->jam_masuk_only)) : null,
                    'jam_pulang' => $record->jam_pulang_only ? date('H:i', strtotime($record->jam_pulang_only)) : null,
                    'lokasi_masuk' => $lokasi_masuk ?: null,
                    'lokasi_pulang' => $lokasi_pulang ?: null,
                    'status' => $status,
                    'keterangan' => !empty($keterangan_parts) ? implode(' | ', $keterangan_parts) : '',
                    'foto_masuk' => $record->foto_masuk ? $record->foto_masuk : null,
                    'foto_pulang' => $record->foto_pulang ? $record->foto_pulang : null,
                    'alasan' => null,
                    'periode' => null,
                    'foto_dokumen' => null
                ));
                
            } elseif ($record->type === 'cuti') {
                $periode = '';
                if ($record->awal_periode && $record->akhir_periode) {
                    if ($record->awal_periode === $record->akhir_periode) {
                        $periode = date('d M Y', strtotime($record->awal_periode));
                    } else {
                        $periode = date('d M Y', strtotime($record->awal_periode)) . ' - ' . date('d M Y', strtotime($record->akhir_periode));
                    }
                }
                
                $formatted_record = array_merge($formatted_record, array(
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'lokasi_masuk' => null,
                    'lokasi_pulang' => null,
                    'status' => 'Cuti',
                    'keterangan' => null,
                    'foto_masuk' => null,
                    'foto_pulang' => null,
                    'alasan' => $record->alasan,
                    'periode' => $periode,
                    'foto_dokumen' => null
                ));
                
            } elseif ($record->type === 'izin') {
                $periode = '';
                if ($record->awal_periode && $record->akhir_periode) {
                    if ($record->awal_periode === $record->akhir_periode) {
                        $periode = date('d M Y', strtotime($record->awal_periode));
                    } else {
                        $periode = date('d M Y', strtotime($record->awal_periode)) . ' - ' . date('d M Y', strtotime($record->akhir_periode));
                    }
                }
                
                $formatted_record = array_merge($formatted_record, array(
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'lokasi_masuk' => null,
                    'lokasi_pulang' => null,
                    'status' => 'Izin',
                    'keterangan' => null,
                    'foto_masuk' => null,
                    'foto_pulang' => null,
                    'alasan' => $record->alasan,
                    'periode' => $periode,
                    'foto_dokumen' => $record->foto_dokumen ? $record->foto_dokumen : null
                ));
            }
            
            $formatted_data[] = $formatted_record;
        }
        
        return $formatted_data;
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

    public function get_lokasi()
    {
        header('Content-Type: application/json');
        
        try {
            $this->authorize(['admin']);
            
            $this->db->select('id, nama_lokasi');
            $this->db->from('lokasi');
            $this->db->order_by('nama_lokasi', 'ASC');
            $query = $this->db->get();

            if ($query) {
                $results = $query->result_array();
                echo json_encode($results);
            } else {
                echo json_encode(array());
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_lokasi: ' . $e->getMessage());
            echo json_encode(array());
        }
    }

    public function get_absensi_data() 
    {
        header('Content-Type: application/json');
        
        try {
            $this->authorize(['admin']);
            
            $data_type = $this->input->post('data_type');
            $date_range_type = $this->input->post('date_range_type');
            
            if (empty($data_type) || empty($date_range_type)) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Type data dan jarak waktu harus dipilih'
                ));
                return;
            }

            // Validasi dan set date range
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
                $end_date = date('Y-m-t', strtotime($start_date));
            }

            // Query berdasarkan data type
            if ($data_type === 'per_orang') {
                $employee_id = $this->input->post('employee_id');
                
                if (empty($employee_id)) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'ID karyawan tidak valid'
                    ));
                    return;
                }

                // Validasi karyawan exists
                $this->db->where('id', $employee_id);
                $employee = $this->db->get('user')->row();
                
                if (!$employee) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Karyawan tidak ditemukan'
                    ));
                    return;
                }

                $combined_data = $this->getCombinedDataByEmployee($employee_id, $start_date, $end_date);
                
            } elseif ($data_type === 'per_area') {
                $area_id = $this->input->post('area_id');
                
                if (empty($area_id)) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'ID area tidak valid'
                    ));
                    return;
                }

                // Validasi lokasi exists
                $this->db->where('id', $area_id);
                $location = $this->db->get('lokasi')->row();
                
                if (!$location) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Lokasi tidak ditemukan'
                    ));
                    return;
                }

                $combined_data = $this->getCombinedDataByArea($area_id, $start_date, $end_date);
            }

            if (empty($combined_data)) {
                echo json_encode(array(
                    'status' => 'success',
                    'data' => array(),
                    'message' => 'Tidak ada data yang ditemukan',
                    'date_range' => array(
                        'type' => $date_range_type,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'formatted' => $this->formatDateRange($date_range_type, $start_date, $end_date)
                    )
                ));
                return;
            }

            $formatted_data = $this->formatCombinedData($combined_data);
            $summary = $this->generateCombinedSummary($formatted_data);
            
            echo json_encode(array(
                'status' => 'success',
                'data' => $formatted_data,
                'summary' => $summary,
                'message' => 'Data berhasil dimuat',
                'data_type' => $data_type,
                'date_range' => array(
                    'type' => $date_range_type,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'formatted' => $this->formatDateRange($date_range_type, $start_date, $end_date)
                )
            ));
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_absensi_data: ' . $e->getMessage());
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ));
        }
    }

    private function getCombinedDataByEmployee($employee_id, $start_date, $end_date)
    {
        $combined_data = array();
        
        // Get absensi data
        $this->db->select('
            "absensi" as type,
            absensi.tanggal,
            user.nama as nama_karyawan,
            DATE_FORMAT(absensi.tanggal, "%d %M %Y") as tanggal_formatted,
            DAYNAME(absensi.tanggal) as hari,
            TIME(absensi.jam_masuk) as jam_masuk_only,
            TIME(absensi.jam_pulang) as jam_pulang_only,
            CASE 
                WHEN absensi.id_lokasi_masuk = 999 THEN CONCAT("Koordinat: ", absensi.latitude_masuk, ", ", absensi.longitude_masuk)
                ELSE lokasi_masuk.nama_lokasi 
            END as nama_lokasi_masuk,
            CASE 
                WHEN absensi.id_lokasi_pulang = 999 THEN CONCAT("Koordinat: ", absensi.latitude_pulang, ", ", absensi.longitude_pulang)
                ELSE lokasi_pulang.nama_lokasi 
            END as nama_lokasi_pulang,
            absensi.foto_masuk,
            absensi.foto_pulang,
            absensi.latitude_masuk,
            absensi.longitude_masuk,
            absensi.latitude_pulang,
            absensi.longitude_pulang,
            absensi.keterangan_masuk,
            absensi.keterangan_pulang,
            absensi.durasi_kerja,
            absensi.id_lokasi_masuk,
            absensi.id_lokasi_pulang,
            "" as alasan,
            "" as awal_periode,
            "" as akhir_periode,
            "" as foto_dokumen
        ', FALSE);
        $this->db->from('absensi');
        $this->db->join('user', 'user.id = absensi.user_id', 'left');
        $this->db->join('lokasi as lokasi_masuk', 'lokasi_masuk.id = absensi.id_lokasi_masuk AND absensi.id_lokasi_masuk != 999', 'left');
        $this->db->join('lokasi as lokasi_pulang', 'lokasi_pulang.id = absensi.id_lokasi_pulang AND absensi.id_lokasi_pulang != 999', 'left');
        $this->db->where('absensi.user_id', $employee_id);
        $this->db->where('absensi.tanggal >=', $start_date);
        $this->db->where('absensi.tanggal <=', $end_date);
        
        $absensi_query = $this->db->get();
        if ($absensi_query) {
            $combined_data = array_merge($combined_data, $absensi_query->result());
        }
        
        // Get cuti data
        $this->db->select('
            "cuti" as type,
            cuti.awal_cuti as tanggal,
            user.nama as nama_karyawan,
            DATE_FORMAT(cuti.awal_cuti, "%d %M %Y") as tanggal_formatted,
            DAYNAME(cuti.awal_cuti) as hari,
            "" as jam_masuk_only,
            "" as jam_pulang_only,
            "" as nama_lokasi_masuk,
            "" as nama_lokasi_pulang,
            "" as foto_masuk,
            "" as foto_pulang,
            "" as latitude_masuk,
            "" as longitude_masuk,
            "" as latitude_pulang,
            "" as longitude_pulang,
            "" as keterangan_masuk,
            "" as keterangan_pulang,
            "" as durasi_kerja,
            "" as id_lokasi_masuk,
            "" as id_lokasi_pulang,
            cuti.alasan_cuti as alasan,
            cuti.awal_cuti as awal_periode,
            cuti.akhir_cuti as akhir_periode,
            "" as foto_dokumen
        ', FALSE);
        $this->db->from('cuti');
        $this->db->join('user', 'user.id = cuti.user_id', 'left');
        $this->db->where('cuti.user_id', $employee_id);
        $this->db->where('cuti.awal_cuti <=', $end_date);
        $this->db->where('cuti.akhir_cuti >=', $start_date);
        
        $cuti_query = $this->db->get();
        if ($cuti_query) {
            $combined_data = array_merge($combined_data, $cuti_query->result());
        }
        
        // Get izin data
        $this->db->select('
            "izin" as type,
            izin.awal_izin as tanggal,
            user.nama as nama_karyawan,
            DATE_FORMAT(izin.awal_izin, "%d %M %Y") as tanggal_formatted,
            DAYNAME(izin.awal_izin) as hari,
            "" as jam_masuk_only,
            "" as jam_pulang_only,
            "" as nama_lokasi_masuk,
            "" as nama_lokasi_pulang,
            "" as foto_masuk,
            "" as foto_pulang,
            "" as latitude_masuk,
            "" as longitude_masuk,
            "" as latitude_pulang,
            "" as longitude_pulang,
            "" as keterangan_masuk,
            "" as keterangan_pulang,
            "" as durasi_kerja,
            "" as id_lokasi_masuk,
            "" as id_lokasi_pulang,
            izin.alasan_izin as alasan,
            izin.awal_izin as awal_periode,
            izin.akhir_izin as akhir_periode,
            izin.foto_izin as foto_dokumen
        ', FALSE);
        $this->db->from('izin');
        $this->db->join('user', 'user.id = izin.user_id', 'left');
        $this->db->where('izin.user_id', $employee_id);
        $this->db->where('izin.awal_izin <=', $end_date);
        $this->db->where('izin.akhir_izin >=', $start_date);
        
        $izin_query = $this->db->get();
        if ($izin_query) {
            $combined_data = array_merge($combined_data, $izin_query->result());
        }
        
        // Sort by date
        usort($combined_data, function($a, $b) {
            return strtotime($b->tanggal) - strtotime($a->tanggal);
        });
        
        return $combined_data;
    }

    private function getCombinedDataByArea($area_id, $start_date, $end_date)
    {
        $combined_data = array();
        
        // Get absensi data by area
        $this->db->select('
            "absensi" as type,
            absensi.tanggal,
            user.nama as nama_karyawan,
            DATE_FORMAT(absensi.tanggal, "%d %M %Y") as tanggal_formatted,
            DAYNAME(absensi.tanggal) as hari,
            TIME(absensi.jam_masuk) as jam_masuk_only,
            TIME(absensi.jam_pulang) as jam_pulang_only,
            CASE 
                WHEN absensi.id_lokasi_masuk = 999 THEN CONCAT("Koordinat: ", absensi.latitude_masuk, ", ", absensi.longitude_masuk)
                ELSE lokasi_masuk.nama_lokasi 
            END as nama_lokasi_masuk,
            CASE 
                WHEN absensi.id_lokasi_pulang = 999 THEN CONCAT("Koordinat: ", absensi.latitude_pulang, ", ", absensi.longitude_pulang)
                ELSE lokasi_pulang.nama_lokasi 
            END as nama_lokasi_pulang,
            absensi.foto_masuk,
            absensi.foto_pulang,
            absensi.latitude_masuk,
            absensi.longitude_masuk,
            absensi.latitude_pulang,
            absensi.longitude_pulang,
            absensi.keterangan_masuk,
            absensi.keterangan_pulang,
            absensi.durasi_kerja,
            absensi.id_lokasi_masuk,
            absensi.id_lokasi_pulang,
            "" as alasan,
            "" as awal_periode,
            "" as akhir_periode,
            "" as foto_dokumen
        ', FALSE);
        $this->db->from('absensi');
        $this->db->join('user', 'user.id = absensi.user_id', 'left');
        $this->db->join('lokasi as lokasi_masuk', 'lokasi_masuk.id = absensi.id_lokasi_masuk AND absensi.id_lokasi_masuk != 999', 'left');
        $this->db->join('lokasi as lokasi_pulang', 'lokasi_pulang.id = absensi.id_lokasi_pulang AND absensi.id_lokasi_pulang != 999', 'left');
        $this->db->where('user.lokasi_id', $area_id);
        $this->db->where('absensi.tanggal >=', $start_date);
        $this->db->where('absensi.tanggal <=', $end_date);
        
        $absensi_query = $this->db->get();
        if ($absensi_query) {
            $combined_data = array_merge($combined_data, $absensi_query->result());
        }
        
        // Get cuti data by area
        $this->db->select('
            "cuti" as type,
            cuti.awal_cuti as tanggal,
            user.nama as nama_karyawan,
            DATE_FORMAT(cuti.awal_cuti, "%d %M %Y") as tanggal_formatted,
            DAYNAME(cuti.awal_cuti) as hari,
            "" as jam_masuk_only,
            "" as jam_pulang_only,
            "" as nama_lokasi_masuk,
            "" as nama_lokasi_pulang,
            "" as foto_masuk,
            "" as foto_pulang,
            "" as latitude_masuk,
            "" as longitude_masuk,
            "" as latitude_pulang,
            "" as longitude_pulang,
            "" as keterangan_masuk,
            "" as keterangan_pulang,
            "" as durasi_kerja,
            "" as id_lokasi_masuk,
            "" as id_lokasi_pulang,
            cuti.alasan_cuti as alasan,
            cuti.awal_cuti as awal_periode,
            cuti.akhir_cuti as akhir_periode,
            "" as foto_dokumen
        ', FALSE);
        $this->db->from('cuti');
        $this->db->join('user', 'user.id = cuti.user_id', 'left');
        $this->db->where('user.lokasi_id', $area_id);
        $this->db->where('cuti.awal_cuti <=', $end_date);
        $this->db->where('cuti.akhir_cuti >=', $start_date);
        
        $cuti_query = $this->db->get();
        if ($cuti_query) {
            $combined_data = array_merge($combined_data, $cuti_query->result());
        }
        
        // Get izin data by area
        $this->db->select('
            "izin" as type,
            izin.awal_izin as tanggal,
            user.nama as nama_karyawan,
            DATE_FORMAT(izin.awal_izin, "%d %M %Y") as tanggal_formatted,
            DAYNAME(izin.awal_izin) as hari,
            "" as jam_masuk_only,
            "" as jam_pulang_only,
            "" as nama_lokasi_masuk,
            "" as nama_lokasi_pulang,
            "" as foto_masuk,
            "" as foto_pulang,
            "" as latitude_masuk,
            "" as longitude_masuk,
            "" as latitude_pulang,
            "" as longitude_pulang,
            "" as keterangan_masuk,
            "" as keterangan_pulang,
            "" as durasi_kerja,
            "" as id_lokasi_masuk,
            "" as id_lokasi_pulang,
            izin.alasan_izin as alasan,
            izin.awal_izin as awal_periode,
            izin.akhir_izin as akhir_periode,
            izin.foto_izin as foto_dokumen
        ', FALSE);
        $this->db->from('izin');
        $this->db->join('user', 'user.id = izin.user_id', 'left');
        $this->db->where('user.lokasi_id', $area_id);
        $this->db->where('izin.awal_izin <=', $end_date);
        $this->db->where('izin.akhir_izin >=', $start_date);
        
        $izin_query = $this->db->get();
        if ($izin_query) {
            $combined_data = array_merge($combined_data, $izin_query->result());
        }
        
        // Sort by date and name
        usort($combined_data, function($a, $b) {
            $dateComparison = strtotime($b->tanggal) - strtotime($a->tanggal);
            if ($dateComparison === 0) {
                return strcmp($a->nama_karyawan, $b->nama_karyawan);
            }
            return $dateComparison;
        });
        
        return $combined_data;
    }

    private function formatCombinedData($combined_data)
    {
        $formatted_data = array();
        
        foreach ($combined_data as $record) {
            $formatted_record = array(
                'type' => $record->type,
                'tanggal' => $record->tanggal,
                'tanggal_formatted' => $record->tanggal_formatted,
                'hari' => $this->getIndonesianDay($record->hari),
                'nama_karyawan' => $record->nama_karyawan,
            );
            
            if ($record->type === 'absensi') {
                $status = 'Tidak Absen';
                if ($record->jam_masuk_only && $record->jam_pulang_only) {
                    $status = 'Masuk';
                } elseif ($record->jam_masuk_only && !$record->jam_pulang_only) {
                    $status = 'Masuk (Belum Pulang)';
                }
                
                $keterangan_parts = array();
                if (!empty($record->keterangan_masuk)) {
                    $keterangan_parts[] = 'Masuk: ' . $record->keterangan_masuk;
                }
                if (!empty($record->keterangan_pulang)) {
                    $keterangan_parts[] = 'Pulang: ' . $record->keterangan_pulang;
                }
                if (!empty($record->durasi_kerja)) {
                    $keterangan_parts[] = 'Durasi: ' . $record->durasi_kerja . ' jam';
                }
                
                // Format lokasi masuk dengan koordinat jika id_lokasi = 999
                $lokasi_masuk = '';
                if (!empty($record->nama_lokasi_masuk)) {
                    $lokasi_masuk = $record->nama_lokasi_masuk;
                    // Jika bukan id_lokasi 999 dan ada koordinat, tambahkan koordinat dalam kurung
                    if ($record->id_lokasi_masuk != 999 && !empty($record->latitude_masuk) && !empty($record->longitude_masuk)) {
                        $lokasi_masuk .= ' (' . $record->latitude_masuk . ', ' . $record->longitude_masuk . ')';
                    }
                }
                
                // Format lokasi pulang dengan koordinat jika id_lokasi = 999
                $lokasi_pulang = '';
                if (!empty($record->nama_lokasi_pulang)) {
                    $lokasi_pulang = $record->nama_lokasi_pulang;
                    // Jika bukan id_lokasi 999 dan ada koordinat, tambahkan koordinat dalam kurung
                    if ($record->id_lokasi_pulang != 999 && !empty($record->latitude_pulang) && !empty($record->longitude_pulang)) {
                        $lokasi_pulang .= ' (' . $record->latitude_pulang . ', ' . $record->longitude_pulang . ')';
                    }
                }
                
                $formatted_record = array_merge($formatted_record, array(
                    'jam_masuk' => $record->jam_masuk_only ? date('H:i', strtotime($record->jam_masuk_only)) : null,
                    'jam_pulang' => $record->jam_pulang_only ? date('H:i', strtotime($record->jam_pulang_only)) : null,
                    'lokasi_masuk' => $lokasi_masuk ?: null,
                    'lokasi_pulang' => $lokasi_pulang ?: null,
                    'status' => $status,
                    'keterangan' => !empty($keterangan_parts) ? implode(' | ', $keterangan_parts) : '',
                    'foto_masuk' => $record->foto_masuk ? $record->foto_masuk : null,
                    'foto_pulang' => $record->foto_pulang ? $record->foto_pulang : null,
                    'alasan' => null,
                    'periode' => null,
                    'foto_dokumen' => null
                ));
                
            } elseif ($record->type === 'cuti') {
                $periode = '';
                if ($record->awal_periode && $record->akhir_periode) {
                    if ($record->awal_periode === $record->akhir_periode) {
                        $periode = date('d M Y', strtotime($record->awal_periode));
                    } else {
                        $periode = date('d M Y', strtotime($record->awal_periode)) . ' - ' . date('d M Y', strtotime($record->akhir_periode));
                    }
                }
                
                $formatted_record = array_merge($formatted_record, array(
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'lokasi_masuk' => null,
                    'lokasi_pulang' => null,
                    'status' => 'Cuti',
                    'keterangan' => null,
                    'foto_masuk' => null,
                    'foto_pulang' => null,
                    'alasan' => $record->alasan,
                    'periode' => $periode,
                    'foto_dokumen' => null
                ));
                
            } elseif ($record->type === 'izin') {
                $periode = '';
                if ($record->awal_periode && $record->akhir_periode) {
                    if ($record->awal_periode === $record->akhir_periode) {
                        $periode = date('d M Y', strtotime($record->awal_periode));
                    } else {
                        $periode = date('d M Y', strtotime($record->awal_periode)) . ' - ' . date('d M Y', strtotime($record->akhir_periode));
                    }
                }
                
                $formatted_record = array_merge($formatted_record, array(
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'lokasi_masuk' => null,
                    'lokasi_pulang' => null,
                    'status' => 'Izin',
                    'keterangan' => null,
                    'foto_masuk' => null,
                    'foto_pulang' => null,
                    'alasan' => $record->alasan,
                    'periode' => $periode,
                    'foto_dokumen' => $record->foto_dokumen ? base_url($record->foto_dokumen) : null
                ));
            }
            
            $formatted_data[] = $formatted_record;
        }
        
        return $formatted_data;
    }

    private function generateCombinedSummary($data) {
        $total_records = count($data);
        $absensi_count = 0;
        $cuti_count = 0;
        $izin_count = 0;
        $present_days = 0;
        $late_days = 0;
        $incomplete_days = 0;
        
        $standard_work_time = '08:00';
        
        foreach ($data as $record) {
            if ($record['type'] === 'absensi') {
                $absensi_count++;
                
                if ($record['status'] === 'Masuk') {
                    $present_days++;
                    
                    // Cek keterlambatan
                    if ($record['jam_masuk'] && strtotime($record['jam_masuk']) > strtotime($standard_work_time)) {
                        $late_days++;
                    }
                } elseif ($record['status'] === 'Masuk (Belum Pulang)') {
                    $incomplete_days++;
                }
            } elseif ($record['type'] === 'cuti') {
                $cuti_count++;
            } elseif ($record['type'] === 'izin') {
                $izin_count++;
            }
        }
        
        return array(
            'total_records' => $total_records,
            'absensi_count' => $absensi_count,
            'cuti_count' => $cuti_count,
            'izin_count' => $izin_count,
            'present_days' => $present_days,
            'late_days' => $late_days,
            'incomplete_days' => $incomplete_days,
            'attendance_rate' => $absensi_count > 0 ? round(($present_days / $absensi_count) * 100, 1) : 0
        );
    }

    private function getAbsensiByEmployee($employee_id, $start_date, $end_date)
    {
        $this->db->select('
            absensi.*,
            user.nama as nama_karyawan,
            DATE_FORMAT(absensi.tanggal, "%d %M %Y") as tanggal_formatted,
            DAYNAME(absensi.tanggal) as hari,
            TIME(absensi.jam_masuk) as jam_masuk_only,
            TIME(absensi.jam_pulang) as jam_pulang_only,
            CASE 
                WHEN absensi.id_lokasi_masuk = 999 THEN CONCAT("Koordinat: ", absensi.latitude_masuk, ", ", absensi.longitude_masuk)
                ELSE lokasi_masuk.nama_lokasi 
            END as nama_lokasi_masuk,
            CASE 
                WHEN absensi.id_lokasi_pulang = 999 THEN CONCAT("Koordinat: ", absensi.latitude_pulang, ", ", absensi.longitude_pulang)
                ELSE lokasi_pulang.nama_lokasi 
            END as nama_lokasi_pulang
        ');
        $this->db->from('absensi');
        $this->db->join('user', 'user.id = absensi.user_id', 'left');
        $this->db->join('lokasi as lokasi_masuk', 'lokasi_masuk.id = absensi.id_lokasi_masuk AND absensi.id_lokasi_masuk != 999', 'left');
        $this->db->join('lokasi as lokasi_pulang', 'lokasi_pulang.id = absensi.id_lokasi_pulang AND absensi.id_lokasi_pulang != 999', 'left');
        $this->db->where('absensi.user_id', $employee_id);
        $this->db->where('absensi.tanggal >=', $start_date);
        $this->db->where('absensi.tanggal <=', $end_date);
        $this->db->order_by('absensi.tanggal', 'DESC');
        
        $query = $this->db->get();
        return $query ? $query->result() : array();
    }

    // Function: getAbsensiByArea - Modified
    private function getAbsensiByArea($area_id, $start_date, $end_date)
    {
        $this->db->select('
            absensi.*,
            user.nama as nama_karyawan,
            DATE_FORMAT(absensi.tanggal, "%d %M %Y") as tanggal_formatted,
            DAYNAME(absensi.tanggal) as hari,
            TIME(absensi.jam_masuk) as jam_masuk_only,
            TIME(absensi.jam_pulang) as jam_pulang_only,
            CASE 
                WHEN absensi.id_lokasi_masuk = 999 THEN CONCAT("Koordinat: ", absensi.latitude_masuk, ", ", absensi.longitude_masuk)
                ELSE lokasi_masuk.nama_lokasi 
            END as nama_lokasi_masuk,
            CASE 
                WHEN absensi.id_lokasi_pulang = 999 THEN CONCAT("Koordinat: ", absensi.latitude_pulang, ", ", absensi.longitude_pulang)
                ELSE lokasi_pulang.nama_lokasi 
            END as nama_lokasi_pulang
        ');
        $this->db->from('absensi');
        $this->db->join('user', 'user.id = absensi.user_id', 'left');
        $this->db->join('lokasi as lokasi_masuk', 'lokasi_masuk.id = absensi.id_lokasi_masuk AND absensi.id_lokasi_masuk != 999', 'left');
        $this->db->join('lokasi as lokasi_pulang', 'lokasi_pulang.id = absensi.id_lokasi_pulang AND absensi.id_lokasi_pulang != 999', 'left');
        $this->db->where('user.lokasi_id', $area_id);
        $this->db->where('absensi.tanggal >=', $start_date);
        $this->db->where('absensi.tanggal <=', $end_date);
        $this->db->order_by('absensi.tanggal', 'DESC');
        $this->db->order_by('user.nama', 'ASC');
        
        $query = $this->db->get();
        return $query ? $query->result() : array();
    }

    // Function: formatAbsensiData - Modified
    private function formatAbsensiData($absensi_data)
    {
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

            // Format lokasi masuk
            $lokasi_masuk = '';
            if (!empty($absensi->nama_lokasi_masuk)) {
                $lokasi_masuk = $absensi->nama_lokasi_masuk;
                // Jika bukan id_lokasi 999 dan ada koordinat, tambahkan koordinat dalam kurung
                if ($absensi->id_lokasi_masuk != 999 && !empty($absensi->latitude_masuk) && !empty($absensi->longitude_masuk)) {
                    $lokasi_masuk .= ' (' . $absensi->latitude_masuk . ', ' . $absensi->longitude_masuk . ')';
                }
            }
            
            // Format lokasi pulang
            $lokasi_pulang = '';
            if (!empty($absensi->nama_lokasi_pulang)) {
                $lokasi_pulang = $absensi->nama_lokasi_pulang;
                // Jika bukan id_lokasi 999 dan ada koordinat, tambahkan koordinat dalam kurung
                if ($absensi->id_lokasi_pulang != 999 && !empty($absensi->latitude_pulang) && !empty($absensi->longitude_pulang)) {
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
        
        return $formatted_data;
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

    private function generateExcelFile($data, $summary, $start_date, $end_date, $filename_prefix, $data_type, $date_range_type)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set sheet name
            $sheet->setTitle('Laporan Absensi');
            
            // Header Information
            $sheet->setCellValue('A1', 'LAPORAN DATA ABSENSI, CUTI & IZIN');
            $sheet->mergeCells('A1:M1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('A2', 'PT. MANDIRI DAYA ANDALAN');
            $sheet->mergeCells('A2:M2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Period Information
            $period_text = $this->formatDateRange($date_range_type, $start_date, $end_date);
            $sheet->setCellValue('A4', 'Periode: ' . $period_text);
            $sheet->mergeCells('A4:M4');
            $sheet->getStyle('A4')->getFont()->setBold(true);
            
            $sheet->setCellValue('A5', 'Tanggal Export: ' . date('d F Y H:i:s'));
            $sheet->mergeCells('A5:M5');
            
            $sheet->setCellValue('A6', 'Type Data: ' . ($data_type === 'per_orang' ? 'Per Karyawan' : 'Per Area'));
            $sheet->mergeCells('A6:M6');
            
            // Summary Section
            $row = 8;
            $sheet->setCellValue('A' . $row, 'RINGKASAN DATA');
            $sheet->mergeCells('A' . $row . ':M' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0E0E0');
            
            $row++;
            $sheet->setCellValue('A' . $row, 'Total Data: ' . $summary['total_records']);
            $sheet->setCellValue('C' . $row, 'Absensi: ' . $summary['absensi_count']);
            $sheet->setCellValue('E' . $row, 'Cuti: ' . $summary['cuti_count']);
            $sheet->setCellValue('G' . $row, 'Izin: ' . $summary['izin_count']);
            
            $row++;
            $sheet->setCellValue('A' . $row, 'Hari Hadir: ' . $summary['present_days']);
            $sheet->setCellValue('C' . $row, 'Terlambat: ' . $summary['late_days']);
            $sheet->setCellValue('E' . $row, 'Belum Pulang: ' . $summary['incomplete_days']);
            $sheet->setCellValue('G' . $row, 'Tingkat Kehadiran: ' . $summary['attendance_rate'] . '%');
            
            // Table Headers
            $row += 2;
            $headers = [
                'A' => 'No',
                'B' => 'Type',
                'C' => 'Nama Karyawan',
                'D' => 'Tanggal',
                'E' => 'Hari',
                'F' => 'Jam Masuk',
                'G' => 'Lokasi Masuk',
                'H' => 'Jam Pulang',
                'I' => 'Lokasi Pulang',
                'J' => 'Status',
                'K' => 'Alasan',
                'L' => 'Periode',
                'M' => 'Keterangan'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $row, $header);
            }
            
            // Style headers
            $headerRange = 'A' . $row . ':M' . $row;
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD0D0D0');
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Data Rows
            $dataStartRow = $row + 1;
            $no = 1;
            
            foreach ($data as $record) {
                $row++;
                
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, ucfirst($record['type']));
                $sheet->setCellValue('C' . $row, $record['nama_karyawan']);
                $sheet->setCellValue('D' . $row, $record['tanggal_formatted']);
                $sheet->setCellValue('E' . $row, $record['hari']);
                $sheet->setCellValue('F' . $row, $record['jam_masuk'] ?: '-');
                $sheet->setCellValue('G' . $row, $this->cleanLocationText($record['lokasi_masuk']) ?: '-');
                $sheet->setCellValue('H' . $row, $record['jam_pulang'] ?: '-');
                $sheet->setCellValue('I' . $row, $this->cleanLocationText($record['lokasi_pulang']) ?: '-');
                $sheet->setCellValue('J' . $row, $record['status']);
                $sheet->setCellValue('K' . $row, $record['alasan'] ?: '-');
                $sheet->setCellValue('L' . $row, $record['periode'] ?: '-');
                $sheet->setCellValue('M' . $row, $record['keterangan'] ?: '-');
                
                $no++;
            }
            
            // Style data rows
            $dataRange = 'A' . $dataStartRow . ':M' . $row;
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Auto-size columns
            foreach (range('A', 'M') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Set maximum width for location columns to prevent too wide columns
            $sheet->getColumnDimension('G')->setWidth(30);
            $sheet->getColumnDimension('I')->setWidth(30);
            $sheet->getColumnDimension('K')->setWidth(25);
            $sheet->getColumnDimension('M')->setWidth(35);
            
            // Create export directory if not exists
            $export_dir = FCPATH . 'exports/';
            if (!is_dir($export_dir)) {
                mkdir($export_dir, 0755, true);
            }
            
            // Generate filename
            $date_suffix = date('Y-m-d_H-i-s');
            $filename = $filename_prefix . '_' . $date_suffix . '.xlsx';
            $filepath = $export_dir . $filename;
            
            // Save file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filepath);
            
            // Clean up memory
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            return 'exports/' . $filename;
            
        } catch (Exception $e) {
            log_message('error', 'Error generating Excel file: ' . $e->getMessage());
            return false;
        }
    }

    private function cleanLocationText($location_text)
    {
        if (empty($location_text)) {
            return '';
        }
        
        // Remove coordinates from location text for cleaner Excel display
        return preg_replace('/\s*\([^)]*\)$/', '', $location_text);
    }

    public function export_excel()
    {
        try {
            $this->authorize(['admin']);
            
            // Load PhpSpreadsheet library
            require_once APPPATH . '../vendor/autoload.php';
            
            $data_type = $this->input->post('data_type');
            $date_range_type = $this->input->post('date_range_type');
            
            if (empty($data_type) || empty($date_range_type)) {
                $this->output->set_status_header(400);
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Type data dan jarak waktu harus dipilih'
                ));
                return;
            }

            // Validasi dan set date range
            if ($date_range_type === 'daily') {
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                
                if (empty($start_date) || empty($end_date)) {
                    $this->output->set_status_header(400);
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Tanggal mulai dan tanggal akhir harus diisi'
                    ));
                    return;
                }
                
                if (strtotime($start_date) > strtotime($end_date)) {
                    $this->output->set_status_header(400);
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
                    $this->output->set_status_header(400);
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Bulan dan tahun harus diisi'
                    ));
                    return;
                }
                
                $start_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
                $end_date = date('Y-m-t', strtotime($start_date));
            }

            // Query berdasarkan data type
            if ($data_type === 'per_orang') {
                $employee_id = $this->input->post('employee_id');
                
                if (empty($employee_id)) {
                    $this->output->set_status_header(400);
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'ID karyawan tidak valid'
                    ));
                    return;
                }

                $combined_data = $this->getCombinedDataByEmployee($employee_id, $start_date, $end_date);
                
                // Calculate total work hours for employee
                $total_work_hours = $this->calculateTotalWorkHours($employee_id, $start_date, $end_date);
                
                // Get employee name for filename
                $this->db->where('id', $employee_id);
                $employee = $this->db->get('user')->row();
                $filename_prefix = 'Data_Karyawan_' . str_replace(' ', '_', $employee->nama);
                
            } elseif ($data_type === 'per_area') {
                $area_id = $this->input->post('area_id');
                
                if (empty($area_id)) {
                    $this->output->set_status_header(400);
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'ID area tidak valid'
                    ));
                    return;
                }

                $combined_data = $this->getCombinedDataByArea($area_id, $start_date, $end_date);
                
                // Calculate total work hours for area
                $total_work_hours = $this->calculateTotalWorkHoursByArea($area_id, $start_date, $end_date);
                
                // Get area name for filename
                $this->db->where('id', $area_id);
                $location = $this->db->get('lokasi')->row();
                $filename_prefix = 'Data_Area_' . str_replace(' ', '_', $location->nama_lokasi);
            }

            if (empty($combined_data)) {
                $this->output->set_status_header(404);
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Tidak ada data untuk diexport'
                ));
                return;
            }

            $formatted_data = $this->formatCombinedData($combined_data);
            $summary = $this->generateCombinedSummary($formatted_data);
            
            // Add total work hours to summary
            $summary['total_work_hours'] = $total_work_hours;
            
            // Generate and stream Excel file directly
            $this->streamExcelFile($formatted_data, $summary, $start_date, $end_date, $filename_prefix, $data_type, $date_range_type);
            
        } catch (Exception $e) {
            log_message('error', 'Error in export_excel: ' . $e->getMessage());
            $this->output->set_status_header(500);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ));
        }
    }

    private function calculateTotalWorkHours($employee_id, $start_date, $end_date)
    {
        $this->db->select('SUM(durasi_kerja) as total_hours');
        $this->db->from('absensi');
        $this->db->where('user_id', $employee_id);
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $this->db->where('durasi_kerja IS NOT NULL');
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result->total_hours ? round($result->total_hours, 2) : 0;
    }

    private function calculateTotalWorkHoursByArea($area_id, $start_date, $end_date)
    {
        $this->db->select('SUM(absensi.durasi_kerja) as total_hours');
        $this->db->from('absensi');
        $this->db->join('user', 'user.id = absensi.user_id', 'inner');
        $this->db->where('user.lokasi_id', $area_id);
        $this->db->where('absensi.tanggal >=', $start_date);
        $this->db->where('absensi.tanggal <=', $end_date);
        $this->db->where('absensi.durasi_kerja IS NOT NULL');
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result->total_hours ? round($result->total_hours, 2) : 0;
    }

    private function streamExcelFile($data, $summary, $start_date, $end_date, $filename_prefix, $data_type, $date_range_type)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan Absensi');

            // ===== JUDUL UTAMA =====
            $sheet->setCellValue('A1', 'LAPORAN DATA ABSENSI, CUTI & IZIN');
            $sheet->mergeCells('A1:N1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'PT. MANDIRI DAYA ANDALAN');
            $sheet->mergeCells('A2:N2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // ===== INFORMASI PERIODE =====
            $period_text = $this->formatDateRange($date_range_type, $start_date, $end_date);
            $sheet->setCellValue('A4', 'Periode: ' . $period_text);
            $sheet->mergeCells('A4:G4');
            $sheet->getStyle('A4')->getFont()->setBold(true);

            $sheet->setCellValue('H4', 'Tanggal Export: ' . date('d F Y H:i:s'));
            $sheet->mergeCells('H4:N4');
            $sheet->getStyle('H4')->getFont()->setBold(true);

            $sheet->setCellValue('A5', 'Type Data: ' . ($data_type === 'per_orang' ? 'Per Karyawan' : 'Per Area'));
            $sheet->mergeCells('A5:N5');
            $sheet->getStyle('A5')->getFont()->setBold(true);

            // ===== RINGKASAN =====
            $row = 7;
            $sheet->setCellValue('A' . $row, 'RINGKASAN DATA');
            $sheet->mergeCells('A' . $row . ':N' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(13);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $row++;

            $summary_data = [
                ['Total Data:', $summary['total_records'], 'Absensi:', $summary['absensi_count']],
                ['Cuti:', $summary['cuti_count'], 'Izin:', $summary['izin_count']],
                ['Hari Hadir:', $summary['present_days'], 'Terlambat:', $summary['late_days']],
                ['Belum Pulang:', $summary['incomplete_days'], 'Tingkat Kehadiran:', $summary['attendance_rate'] . '%']
            ];

            foreach ($summary_data as $summary_row) {
                $sheet->setCellValue('A' . $row, $summary_row[0]);
                $sheet->setCellValue('B' . $row, $summary_row[1]);
                $sheet->setCellValue('D' . $row, $summary_row[2]);
                $sheet->setCellValue('E' . $row, $summary_row[3]);

                $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':E' . $row)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $row++;
            }

            $sheet->setCellValue('A' . $row, 'TOTAL JAM KERJA: ' . $summary['total_work_hours'] . ' JAM');
            $sheet->mergeCells('A' . $row . ':N' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $row += 2;

            // ===== DATA PER MINGGU =====
            $weekly_data = $this->separateDataByWeeks($data, $start_date, $end_date);

            foreach ($weekly_data as $week_info) {
                // Judul minggu
                $sheet->setCellValue('A' . $row, $week_info['title']);
                $sheet->mergeCells('A' . $row . ':N' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $row++;

                // Ringkasan minggu
                $sheet->setCellValue('A' . $row, 'Ringkasan Minggu: ' . count($week_info['data']) . ' data | Jam Kerja: ' . $week_info['total_hours'] . ' jam');
                $sheet->mergeCells('A' . $row . ':N' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row++;

                if (!empty($week_info['data'])) {
                    // Header tabel
                    $headers = [
                        'A' => 'No',
                        'B' => 'Type',
                        'C' => 'Nama Karyawan',
                        'D' => 'Tanggal',
                        'E' => 'Hari',
                        'F' => 'Jam Masuk',
                        'G' => 'Lokasi Masuk',
                        'H' => 'Jam Pulang',
                        'I' => 'Lokasi Pulang',
                        'J' => 'Status',
                        'K' => 'Jam Kerja',
                        'L' => 'Alasan',
                        'M' => 'Periode',
                        'N' => 'Keterangan'
                    ];
                    foreach ($headers as $col => $header) {
                        $sheet->setCellValue($col . $row, $header);
                    }
                    $headerRange = 'A' . $row . ':N' . $row;
                    $sheet->getStyle($headerRange)->getFont()->setBold(true);
                    $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                    $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $row++;

                    // Isi data
                    $no = 1;
                    foreach ($week_info['data'] as $record) {
                        $jam_kerja_display = '';
                        if ($record['type'] === 'absensi' && !empty($record['keterangan'])) {
                            if (preg_match('/Durasi:\s*([\d.,]+)\s*jam/', $record['keterangan'], $matches)) {
                                $jam_kerja_display = $matches[1] . ' jam';
                            }
                        }

                        $sheet->setCellValue('A' . $row, $no);
                        $sheet->setCellValue('B' . $row, ucfirst($record['type']));
                        $sheet->setCellValue('C' . $row, $record['nama_karyawan']);
                        $sheet->setCellValue('D' . $row, $record['tanggal_formatted']);
                        $sheet->setCellValue('E' . $row, $record['hari']);
                        $sheet->setCellValue('F' . $row, $record['jam_masuk'] ?: '-');
                        $sheet->setCellValue('G' . $row, $this->cleanLocationText($record['lokasi_masuk']) ?: '-');
                        $sheet->setCellValue('H' . $row, $record['jam_pulang'] ?: '-');
                        $sheet->setCellValue('I' . $row, $this->cleanLocationText($record['lokasi_pulang']) ?: '-');
                        $sheet->setCellValue('J' . $row, $record['status']);
                        $sheet->setCellValue('K' . $row, $jam_kerja_display ?: '-');
                        $sheet->setCellValue('L' . $row, $record['alasan'] ?: '-');
                        $sheet->setCellValue('M' . $row, $record['periode'] ?: '-');
                        $sheet->setCellValue('N' . $row, $record['keterangan'] ?: '-');

                        $row++;
                        $no++;
                    }

                    // Border isi data
                    $dataRange = 'A' . ($row - count($week_info['data'])) . ':N' . ($row - 1);
                    $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    // === Tambah background abu-abu soft untuk 1 minggu ===
                    $sheet->getStyle($dataRange)->getFill()->setFillType(
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
                    )->getStartColor()->setARGB('FFEFEFEF'); // abu-abu soft (hex EFEFEF)

                    $row++;
                }
            }

            // Atur lebar kolom
            foreach (range('A', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // ===== OUTPUT FILE =====
            $date_suffix = date('Y-m-d_H-i-s');
            $filename = $filename_prefix . '_' . $date_suffix . '.xlsx';

            if (ob_get_length()) ob_end_clean();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            exit;

        } catch (Exception $e) {
            log_message('error', 'Error streaming Excel file: ' . $e->getMessage());
            if (!headers_sent()) {
                header('Content-Type: application/json');
                http_response_code(500);
            }
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat membuat file: ' . $e->getMessage()]);
            exit;
        }
    }

    private function separateDataByWeeks($data, $start_date, $end_date)
    {
        $weekly_data = [];
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);

        // Cari Senin dari minggu yang mengandung start_date
        $start_of_week = $start_timestamp;
        while (date('N', $start_of_week) != 1) { // N = 1 untuk Senin
            $start_of_week = strtotime('-1 day', $start_of_week);
        }

        $current_week_start = $start_of_week;
        $week_number = 1;

        while ($current_week_start <= $end_timestamp) {
            // Minggu berakhir di hari Minggu (6 hari setelah Senin)
            $current_week_end = strtotime('+6 days', $current_week_start);
            
            // Batasi dengan periode yang diminta
            $actual_start = max($current_week_start, $start_timestamp);
            $actual_end = min($current_week_end, $end_timestamp);

            // Kumpulkan data untuk minggu ini
            $week_data = [];
            $total_work_hours = 0;

            foreach ($data as $record) {
                $record_timestamp = strtotime($record['tanggal']);
                
                // Cek apakah record berada dalam rentang minggu ini
                if ($record_timestamp >= $current_week_start && $record_timestamp <= $current_week_end) {
                    // Urutkan data berdasarkan tanggal dan nama karyawan
                    $week_data[] = $record;
                    
                    // Hitung jam kerja jika ada
                    if ($record['type'] === 'absensi' && !empty($record['keterangan'])) {
                        if (preg_match('/Durasi:\s*([\d.,]+)\s*jam/', $record['keterangan'], $matches)) {
                            $hours = floatval(str_replace(',', '.', $matches[1]));
                            $total_work_hours += $hours;
                        }
                    }
                }
            }

            // Urutkan data dalam minggu berdasarkan tanggal kemudian nama
            usort($week_data, function($a, $b) {
                $dateCompare = strcmp($a['tanggal'], $b['tanggal']);
                if ($dateCompare === 0) {
                    return strcmp($a['nama_karyawan'], $b['nama_karyawan']);
                }
                return $dateCompare;
            });

            // Hanya tambahkan minggu jika ada data atau jika minggu ini intersect dengan periode
            if (!empty($week_data) || ($current_week_start <= $end_timestamp && $current_week_end >= $start_timestamp)) {
                // Format tanggal untuk judul minggu
                $display_start = max($current_week_start, $start_timestamp);
                $display_end = min($current_week_end, $end_timestamp);
                
                $weekly_data[] = [
                    'title' => sprintf('MINGGU %d (%s - %s)',
                        $week_number,
                        $this->formatIndonesianDate($display_start),
                        $this->formatIndonesianDate($display_end)
                    ),
                    'data' => $week_data,
                    'total_hours' => round($total_work_hours, 2),
                    'week_start' => $current_week_start,
                    'week_end' => $current_week_end,
                    'actual_start' => $display_start,
                    'actual_end' => $display_end,
                    'days_info' => $this->getWeekDaysInfo($current_week_start, $current_week_end, $start_timestamp, $end_timestamp)
                ];
            }

            // Lanjut ke minggu berikutnya (Senin berikutnya)
            $current_week_start = strtotime('+7 days', $current_week_start);
            $week_number++;
            
            // Safety break untuk menghindari infinite loop
            if ($week_number > 100) {
                break;
            }
        }

        return $weekly_data;
    }

    /**
     * Helper function untuk format tanggal Indonesia
     */
    private function formatIndonesianDate($timestamp)
    {
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        
        $day = date('d', $timestamp);
        $month = $months[intval(date('m', $timestamp))];
        $year = date('Y', $timestamp);
        
        return $day . ' ' . $month . ' ' . $year;
    }

    /**
     * Helper function untuk mendapatkan info hari dalam minggu
     */
    private function getWeekDaysInfo($week_start, $week_end, $period_start, $period_end)
    {
        $days = [];
        $current_day = $week_start;
        
        $day_names = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        for ($i = 0; $i < 7; $i++) {
            $day_number = intval(date('N', $current_day)); // 1=Senin, 7=Minggu
            $is_in_period = ($current_day >= $period_start && $current_day <= $period_end);
            
            $days[] = [
                'date' => date('Y-m-d', $current_day),
                'day_name' => $day_names[$day_number],
                'day_number' => $day_number,
                'is_in_period' => $is_in_period,
                'formatted_date' => $this->formatIndonesianDate($current_day)
            ];
            
            $current_day = strtotime('+1 day', $current_day);
        }
        
        return $days;
    }

    /**
     * Fungsi tambahan untuk debugging - menampilkan struktur minggu
     */
    private function debugWeekStructure($weekly_data)
    {
        foreach ($weekly_data as $week) {
            echo "=== " . $week['title'] . " ===\n";
            echo "Total jam: " . $week['total_hours'] . "\n";
            echo "Total data: " . count($week['data']) . "\n";
            
            foreach ($week['days_info'] as $day) {
                $status = $day['is_in_period'] ? '✓' : '✗';
                echo $status . " " . $day['day_name'] . " (" . $day['formatted_date'] . ")\n";
            }
            echo "\n";
        }
    }
}