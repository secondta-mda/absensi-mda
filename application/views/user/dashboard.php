<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Cuti</title>
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

    @media (max-width: 768px) {
        .responsive-table thead {
            display: none;
        }

        .responsive-table tr {
            display: block;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .responsive-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            text-align: right;
            border-bottom: 1px solid #e5e7eb;
        }

        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #374151;
            text-align: left;
        }

        .status-badge {
            justify-content: flex-end;
        }
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
                                    Tanggal</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hari</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jam Masuk</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jam Pulang</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keterangan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($riwayat)): ?>
                            <?php foreach ($riwayat as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td data-label="Tanggal" class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                                    <?php echo date('d F Y', strtotime($row->tanggal)); ?>
                                </td>
                                <td data-label="Hari" class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php echo date('l', strtotime($row->tanggal)); ?>
                                </td>
                                <td data-label="Jam Masuk"
                                    class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                    <?php echo $row->jam_masuk ? date('H:i', strtotime($row->jam_masuk)) : '-'; ?>
                                </td>
                                <td data-label="Jam Pulang"
                                    class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                    <?php echo $row->jam_pulang ? date('H:i', strtotime($row->jam_pulang)) : '-'; ?>
                                </td>
                                <td data-label="Status" class="px-4 py-4 whitespace-nowrap">
                                    <?php if ($row->status == 'Masuk'): ?>
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Masuk</span>
                                    <?php elseif ($row->status == 'Izin'): ?>
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Izin</span>
                                    <?php else: ?>
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cuti</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Keterangan" class="px-4 py-4 text-sm text-gray-600">
                                    <?php echo $row->keterangan ?: '-'; ?>
                                </td>
                                <td data-label="Lokasi" class="px-4 py-4 text-sm text-gray-600">
                                    <?php
                                        if ($row->status == 'Masuk') {
                                            $lokasi_masuk = $row->lokasi_masuk ?: '-';
                                            $lokasi_pulang = $row->lokasi_pulang ?: '-';
                                            echo 'Masuk: ' . $lokasi_masuk . '<br>Pulang: ' . $lokasi_pulang;
                                        } else {
                                            echo '-';
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">Belum ada data bulan ini</td>
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

    <script>
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