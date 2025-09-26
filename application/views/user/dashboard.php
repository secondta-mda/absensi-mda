<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap');

    .lexend-deca-font {
        font-family: "Lexend Deca", sans-serif;
        font-optical-sizing: auto;
        font-weight: 400;
        font-style: normal;
    }

    /* Responsive table enhancements untuk kolom tambahan */
    @media (max-width: 768px) {
        .responsive-table thead {
            display: none;
        }

        .responsive-table tbody tr {
            display: block;
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
            background: white;
            border: 1px solid #e5e7eb;
        }

        .responsive-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
            text-align: right;
            min-height: 2rem;
        }

        .responsive-table tbody td:last-child {
            border-bottom: none;
        }

        .responsive-table tbody td::before {
            content: attr(data-label) ": ";
            font-weight: 600;
            color: #374151;
            text-align: left;
            min-width: 100px;
            flex-shrink: 0;
        }

        /* Khusus untuk type badge */
        .responsive-table tbody td[data-label="Type"] {
            justify-content: flex-end;
        }

        /* Khusus untuk status badge */
        .responsive-table tbody td[data-label="Status"] {
            justify-content: flex-end;
        }

        /* Action buttons styling */
        .responsive-table tbody td[data-label="Aksi"] {
            flex-direction: column;
            align-items: stretch;
            gap: 0.25rem;
        }

        .responsive-table tbody td[data-label="Aksi"] button {
            width: 100%;
            margin-bottom: 0;
        }
    }

    /* Badge variations */
    .type-badge {
        font-weight: 600;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
    }

    /* Hover effects untuk rows */
    .hover\:bg-gray-50:hover {
        background-color: #f9fafb;
        transition: background-color 0.15s ease-in-out;
    }

    /* Enhanced button styling */
    .btn-foto-absensi {
        background: linear-gradient(45deg, #3b82f6, #1d4ed8);
        transition: all 0.2s ease;
    }

    .btn-foto-absensi:hover {
        background: linear-gradient(45deg, #1d4ed8, #1e40af);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .btn-foto-izin {
        background: linear-gradient(45deg, #f97316, #ea580c);
        transition: all 0.2s ease;
    }

    .btn-foto-izin:hover {
        background: linear-gradient(45deg, #ea580c, #dc2626);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
    }
    </style>
</head>

<body class="lexend-deca-font bg-gray-100">

    <nav
        class="hidden md:flex w-full bg-gray-800 text-white px-4 py-3 items-center justify-between shadow-md sticky top-0 z-30">
        <div class="font-bold text-lg">ABSENSI MDA</div>
        <div class="relative">
            <button id="userDropdownBtn" class="flex items-center space-x-2">
                <i class="fas fa-user-circle text-lg"></i><span><?php echo $username; ?></span>
            </button>
            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-gray-700 rounded-md shadow-lg py-1 z-40">
                <button id="logoutBtn"
                    class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-600 w-full text-left">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </button>
            </div>
        </div>
    </nav>

    <div class="flex min-h-screen">
        <aside id="sidebar"
            class="hidden md:block fixed top-0 left-0 h-screen bg-gray-900 shadow-lg border-r border-gray-800 w-64 z-20">
            <div class="mt-[50px] p-4 font-bold text-gray-100 text-xl border-b border-gray-800">Menu</div>
            <ul class="p-2 space-y-2">
                <li><a href="<?php echo base_url('user'); ?>"
                        class="w-full flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 bg-gray-800"><i
                            class="fas fa-home mr-2"></i>Home</a></li>
                <li><a href="<?php echo base_url('user/absen'); ?>"
                        class="w-full flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800"><i
                            class="fas fa-calendar-check mr-2"></i>Absensi</a></li>
                <li><a href="<?php echo base_url('user/cuti'); ?>"
                        class="w-full flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800"><i
                            class="fas fa-briefcase mr-2"></i>Cuti</a></li>
                <li><a href="<?php echo base_url('user/izin'); ?>"
                        class="w-full flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800"><i
                            class="fas fa-file-alt mr-2"></i>Izin</a></li>
                <li><a href="<?php echo base_url('user/profil'); ?>"
                        class="w-full flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800"><i
                            class="fas fa-user mr-2"></i>Profil</a></li>
            </ul>
        </aside>

        <main class="flex-1 p-6 md:ml-64 mb-20 md:mb-0">
            <h2 class="text-2xl md:text-3xl font-bold mb-8 text-gray-800 border-b pb-2">Dashboard</h2>

            <?php 
                $role = $this->session->userdata('role');
                $gridClass = ($role === 'admin') ? 'grid-cols-4' : 'grid-cols-3';
            ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:<?php echo $gridClass; ?> gap-4 md:gap-6 w-full">
                <a href="<?php echo base_url('user/absen'); ?>"
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 flex items-center justify-between hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="flex flex-col">
                        <h3 class="text-base md:text-lg font-semibold text-gray-700 mb-2">Absensi Masuk</h3>
                        <p class="text-2xl md:text-3xl font-bold text-blue-600">
                            <?php echo $absensi_masuk; ?>
                        </p>
                    </div>
                    <i class="fas fa-calendar-check text-xl md:text-2xl text-blue-600"></i>
                </a>

                <a href="<?php echo base_url('user/cuti'); ?>"
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 flex items-center justify-between hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="flex flex-col">
                        <h3 class="text-base md:text-lg font-semibold text-gray-700 mb-2">Cuti</h3>
                        <p class="text-2xl md:text-3xl font-bold text-green-600">
                            <?php echo $cuti; ?>
                        </p>
                    </div>
                    <i class="fas fa-home text-xl md:text-2xl text-green-600"></i>
                </a>

                <a href="<?php echo base_url('user/izin'); ?>"
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 flex items-center justify-between hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="flex flex-col">
                        <h3 class="text-base md:text-lg font-semibold text-gray-700 mb-2">Izin</h3>
                        <p class="text-2xl md:text-3xl font-bold text-yellow-600">
                            <?php echo $izin; ?>
                        </p>
                    </div>
                    <i class="fas fa-user text-xl md:text-2xl text-yellow-600"></i>
                </a>

                <?php if ($role === 'admin'): ?>
                <a href="<?php echo base_url('user/riwayat_absensi'); ?>"
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 flex items-center justify-between hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="flex flex-col">
                        <h3 class="text-base md:text-lg font-semibold text-gray-700 mb-2">Riwayat Absensi Karyawan</h3>
                        <p class="text-2xl md:text-3xl font-bold text-purple-600">
                            -
                        </p>
                    </div>
                    <i class="fas fa-history text-xl md:text-2xl text-purple-600"></i>
                </a>
                <?php endif; ?>
            </div>

            <div class="mt-8 md:mt-12">
                <h3 class="text-xl font-bold mb-4 md:mb-6 text-gray-800">Riwayat Absen Bulan Ini</h3>

                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 overflow-x-auto">
                    <table class="responsive-table w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hari</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jam Masuk</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lokasi Masuk</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jam Pulang</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lokasi Pulang</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Alasan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Periode</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keterangan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($riwayat)): ?>
                            <?php foreach ($riwayat as $row): ?>
                            <?php
                                // Tentukan class dan badge berdasarkan type dan status
                                $statusClass = '';
                                $typeClass = '';
                                $typeBadge = '';
                                
                                if ($row['type'] === 'absensi') {
                                    $typeClass = 'bg-blue-100 text-blue-800';
                                    $typeBadge = 'Absensi';
                                    
                                    $statusClass = $row['status'] === 'Masuk' ? 'bg-green-100 text-green-800' :
                                        (strpos($row['status'], 'Belum Pulang') !== false ? 'bg-yellow-100 text-yellow-800' :
                                        'bg-red-100 text-red-800');
                                } elseif ($row['type'] === 'cuti') {
                                    $typeClass = 'bg-purple-100 text-purple-800';
                                    $typeBadge = 'Cuti';
                                    $statusClass = 'bg-purple-100 text-purple-800';
                                } elseif ($row['type'] === 'izin') {
                                    $typeClass = 'bg-orange-100 text-orange-800';
                                    $typeBadge = 'Izin';
                                    $statusClass = 'bg-orange-100 text-orange-800';
                                }
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td data-label="Type" class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $typeClass; ?>">
                                        <?php echo $typeBadge; ?>
                                    </span>
                                </td>
                                <td data-label="Tanggal" class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                                    <?php echo $row['tanggal_formatted']; ?>
                                </td>
                                <td data-label="Hari" class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php echo $row['hari']; ?>
                                </td>
                                <td data-label="Jam Masuk"
                                    class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                    <?php echo $row['jam_masuk'] ?: '-'; ?>
                                </td>
                                <td data-label="Lokasi Masuk" class="px-4 py-4 text-sm text-gray-600">
                                    <div class="max-w-32">
                                        <?php echo $row['lokasi_masuk'] ?: '-'; ?>
                                    </div>
                                </td>
                                <td data-label="Jam Pulang"
                                    class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                    <?php echo $row['jam_pulang'] ?: '-'; ?>
                                </td>
                                <td data-label="Lokasi Pulang" class="px-4 py-4 text-sm text-gray-600">
                                    <div class="max-w-32">
                                        <?php echo $row['lokasi_pulang'] ?: '-'; ?>
                                    </div>
                                </td>
                                <td data-label="Status" class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td data-label="Alasan" class="px-4 py-4 text-sm text-gray-600">
                                    <div class="max-w-40">
                                        <?php echo $row['alasan'] ?: '-'; ?>
                                    </div>
                                </td>
                                <td data-label="Periode" class="px-4 py-4 text-sm text-gray-600">
                                    <div class="max-w-32">
                                        <?php echo $row['periode'] ?: '-'; ?>
                                    </div>
                                </td>
                                <td data-label="Keterangan" class="px-4 py-4 text-sm text-gray-600">
                                    <div class="max-w-32">
                                        <?php echo $row['keterangan'] ?: '-'; ?>
                                    </div>
                                </td>
                                <td data-label="Aksi" class="px-4 py-4 text-sm text-gray-600">
                                    <?php
                                    $buttons = [];
                                    
                                    // Button untuk foto absensi
                                    if ($row['type'] === 'absensi' && ($row['foto_masuk'] || $row['foto_pulang'])) {
                                        $buttons[] = '<button onclick="showFoto(\'' . ($row['foto_masuk'] ?: '') . '\',\'' . ($row['foto_pulang'] ?: '') . '\')" class="mb-1 w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-1 px-2 rounded text-xs font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200"><i class="fas fa-images mr-1"></i>Foto Absensi</button>';
                                    }
                                    
                                    // Button untuk foto izin
                                    if ($row['type'] === 'izin' && $row['foto_dokumen']) {
                                        $buttons[] = '<button onclick="showFotoDokumen(\'' . $row['foto_dokumen'] . '\')" class="mb-1 w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-1 px-2 rounded text-xs font-semibold hover:from-orange-600 hover:to-orange-700 transition-all duration-200"><i class="fas fa-file-image mr-1"></i>Foto Izin</button>';
                                    }
                                    
                                    // Jika tidak ada foto
                                    if (empty($buttons)) {
                                        echo '<span class="text-gray-400">-</span>';
                                    } else {
                                        echo implode('', $buttons);
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="12" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                                    <p class="text-lg">Belum ada data bulan ini</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <nav
        class="md:hidden rounded-t-3xl fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t border-gray-200 flex justify-around py-2 z-30">
        <a href="<?php echo base_url('user'); ?>"
            class="group flex flex-col items-center text-gray-600 hover:text-blue-600">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full">
                <i class="fas fa-home text-lg text-blue-600 group-hover:text-blue-700"></i>
            </span>
            <span class="text-xs">Home</span>
        </a>
        <a href="<?php echo base_url('user/cuti'); ?>"
            class="group flex flex-col items-center text-gray-600 hover:text-blue-600">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full">
                <i class="fas fa-briefcase text-lg text-gray-600 group-hover:text-blue-700"></i>
            </span>
            <span class="text-xs">Cuti</span>
        </a>
        <a href="<?php echo base_url('user/absen'); ?>"
            class="group flex flex-col items-center text-gray-600 mt-[-35px]">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-100">
                <i class="fas fa-calendar-check text-4xl text-blue-600 group-hover:text-blue-700"></i>
            </span>
            <span class="text-xs mt-[11px]">Absensi</span>
        </a>
        <a href="<?php echo base_url('user/izin'); ?>"
            class="group flex flex-col items-center text-gray-600 hover:text-blue-600">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full">
                <i class="fas fa-file-alt text-lg text-gray-600 group-hover:text-blue-700"></i>
            </span>
            <span class="text-xs">Izin</span>
        </a>
        <a href="<?php echo base_url('user/profil'); ?>"
            class="group flex flex-col items-center text-gray-600 hover:text-blue-600">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full">
                <i class="fas fa-user text-lg text-gray-600 group-hover:text-blue-700"></i>
            </span>
            <span class="text-xs">Profil</span>
        </a>
    </nav>

    <!-- Modal untuk Foto Absensi -->
    <div id="fotoModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Foto Absensi</h3>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-center font-semibold text-gray-700 mb-2">Foto Masuk</p>
                        <div class="text-center">
                            <img id="modalFotoMasuk" src="" alt="Foto Masuk"
                                class="max-w-full h-auto rounded-lg border mx-auto" style="display: none;">
                            <p id="noFotoMasuk" class="text-gray-500 py-8">Tidak ada foto masuk</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-center font-semibold text-gray-700 mb-2">Foto Pulang</p>
                        <div class="text-center">
                            <img id="modalFotoPulang" src="" alt="Foto Pulang"
                                class="max-w-full h-auto rounded-lg border mx-auto" style="display: none;">
                            <p id="noFotoPulang" class="text-gray-500 py-8">Tidak ada foto pulang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Fungsi untuk menampilkan foto absensi
    function showFoto(fotoMasuk, fotoPulang) {
        if (fotoMasuk && fotoMasuk !== '') {
            document.getElementById('modalFotoMasuk').src = fotoMasuk;
            document.getElementById('modalFotoMasuk').style.display = 'block';
            document.getElementById('noFotoMasuk').style.display = 'none';
        } else {
            document.getElementById('modalFotoMasuk').style.display = 'none';
            document.getElementById('noFotoMasuk').style.display = 'block';
        }

        if (fotoPulang && fotoPulang !== '') {
            document.getElementById('modalFotoPulang').src = fotoPulang;
            document.getElementById('modalFotoPulang').style.display = 'block';
            document.getElementById('noFotoPulang').style.display = 'none';
        } else {
            document.getElementById('modalFotoPulang').style.display = 'none';
            document.getElementById('noFotoPulang').style.display = 'block';
        }

        document.getElementById('fotoModal').classList.remove('hidden');
    }

    // Fungsi untuk menampilkan foto dokumen izin
    function showFotoDokumen(fotoDokumen) {
        Swal.fire({
            title: 'Foto Dokumen Izin',
            imageUrl: fotoDokumen,
            imageAlt: 'Foto Dokumen Izin',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#3b82f6',
            imageWidth: 'auto',
            imageHeight: 'auto',
            customClass: {
                image: 'max-w-full max-h-96 object-contain'
            }
        });
    }

    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdown = document.getElementById('userDropdown');
    const logoutBtn = document.getElementById('logoutBtn');

    if (userDropdownBtn && userDropdown) {
        userDropdownBtn.addEventListener('click', () => {
            userDropdown.classList.toggle('hidden');
        });
    }

    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    sessionStorage.clear();
                    localStorage.clear();
                    window.location.href = "<?php echo base_url('auth/logout'); ?>";
                }
            });
        });
    }

    document.addEventListener('click', function(event) {
        if (userDropdownBtn && userDropdown &&
            !userDropdownBtn.contains(event.target) &&
            !userDropdown.contains(event.target)) {
            userDropdown.classList.add('hidden');
        }
    });

    // Modal close handlers
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('fotoModal').classList.add('hidden');
    });

    window.addEventListener('click', function(e) {
        if (e.target.id === 'fotoModal') {
            document.getElementById('fotoModal').classList.add('hidden');
        }
    });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                console.log("Lat:", pos.coords.latitude, "Lng:", pos.coords.longitude);
            },
            (err) => {
                console.error("Error:", err.message);
            }, {
                enableHighAccuracy: true,
                timeout: 10000
            }
        );
    }
    </script>
</body>

</html>