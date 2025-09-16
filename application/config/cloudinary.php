<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| CLOUDINARY CONFIGURATION
| -------------------------------------------------------------------
| File ini harus disimpan di: application/config/cloudinary.php
|
| Konfigurasi untuk upload gambar ke Cloudinary
| Ganti dengan kredensial Cloudinary Anda
*/

$config['cloudinary'] = [
    'cloud_name' => 'dkcpmhcbr',
    'api_key' => '346428791992843', 
    'api_secret' => '1ZOPLqMx6fx5HdKzRAMJ0HtiU4M',
    'folder' => 'absen' // Folder untuk menyimpan foto absensi
];

/*
| -------------------------------------------------------------------
| CARA MENDAPATKAN KREDENSIAL CLOUDINARY:
| -------------------------------------------------------------------
| 1. Daftar di https://cloudinary.com/
| 2. Login ke dashboard
| 3. Salin Cloud Name, API Key, dan API Secret dari dashboard
| 4. Ganti nilai di atas dengan kredensial Anda
| 5. Simpan file ini sebagai application/config/cloudinary.php
*/