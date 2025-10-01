<!-- JANGAN LAH DI BUKA BUKA BANGGG -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Riwayat Data</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
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

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 48px;
        display: flex;
        align-items: center;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 12px;
    }

    .date-input-group {
        transition: all 0.3s ease;
    }

    /* Styling untuk summary cards */
    .summary-cards {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
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
                        class="w-full flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800"><i
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

        <main class="flex-1 p-6 md:ml-64 mb-20 md:mb-0 overflow-x-hidden">
            <h2 class="text-3xl font-bold mb-8 text-gray-800 border-b pb-2">Riwayat Data Karyawan</h2>

            <div class="mb-6 bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-xl font-bold mb-6 text-gray-800">Filter Data</h3>

                <form id="searchForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Type Data Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Type Data</label>
                            <select id="dataType"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Type Data --</option>
                                <option value="per_orang">Data Per Orang</option>
                                <option value="per_area">Data Per Area</option>
                            </select>
                        </div>

                        <!-- Jarak Waktu Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jarak Waktu</label>
                            <select id="dateRangeType"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                disabled>
                                <option value="">-- Pilih Jarak Waktu --</option>
                                <option value="daily">Harian (Rentang Tanggal)</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Selection Filters -->
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Employee Select (for per_orang) -->
                        <div id="employeeSelectDiv" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Karyawan</label>
                            <select id="employeeSelect" class="w-full">
                                <option value="">-- Pilih Karyawan --</option>
                            </select>
                        </div>

                        <!-- Area Select (for per_area) -->
                        <div id="areaSelectDiv" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Area/Lokasi</label>
                            <select id="areaSelect"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Area --</option>
                            </select>
                        </div>
                    </div>

                    <div id="dateInputs" class="hidden date-input-group">
                        <div id="dailyInputs" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                                <input type="date" id="startDate"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Akhir</label>
                                <input type="date" id="endDate"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div id="monthlyInputs" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                                <select id="selectedMonth"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Pilih Bulan --</option>
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                                <select id="selectedYear"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Pilih Tahun --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <button type="submit" id="submitBtn"
                            class="hidden bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-200 shadow-md">
                            <i class="fas fa-search mr-2"></i>Tampilkan Data
                        </button>
                        <button type="button" id="exportBtn"
                            class="hidden bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-200 shadow-md">
                            <i class="fas fa-file-excel mr-2"></i>Export Excel
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-8 md:mt-12">
                <h3 class="text-xl font-bold mb-4 md:mb-6 text-gray-800">Data Riwayat Absensi, Cuti & Izin</h3>

                <!-- Summary Cards akan diinsert di sini oleh JavaScript -->

                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 overflow-x-auto">
                    <table class="responsive-table w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama</th>
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
                            <tr>
                                <td colspan="13" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-search text-4xl mb-3 opacity-50"></i>
                                    <p class="text-lg">Silakan pilih filter untuk menampilkan data</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Bottom Navigation for Mobile -->
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

    <div id="exportLoadingModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-1/2 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform -translate-y-1/2">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <i class="fas fa-file-excel text-green-600 text-2xl animate-pulse"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Processing Export</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Sedang memproses data untuk export Excel...<br>
                        <span class="text-xs">Mohon tunggu beberapa saat</span>
                    </p>
                    <div class="mt-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full animate-pulse" style="width: 70%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        generateYearOptions();
        setDefaultDates();

        // Data Type change handler
        $('#dataType').on('change', function() {
            const selectedDataType = $(this).val();

            // Reset and hide all dependent fields
            $('#dateRangeType').val('').prop('disabled', true);
            $('#dateInputs, #dailyInputs, #monthlyInputs, #employeeSelectDiv, #areaSelectDiv, #submitBtn')
                .addClass('hidden');

            // Reset selection fields
            $('#employeeSelect').val('').trigger('change');
            $('#areaSelect').val('');

            // Remove previous summary cards
            $('.summary-cards').remove();

            if (selectedDataType) {
                // Enable date range type selector
                $('#dateRangeType').prop('disabled', false);

                // Check if both selections are made
                checkSelections();
            }
        });

        // Date Range Type change handler
        $('#dateRangeType').on('change', function() {
            const selectedType = $(this).val();

            $('#dateInputs, #dailyInputs, #monthlyInputs, #submitBtn').addClass('hidden');

            if (selectedType) {
                // Check if both selections are made
                checkSelections();
            }
        });

        // Function to check if both selections are made
        function checkSelections() {
            const dataType = $('#dataType').val();
            const dateRangeType = $('#dateRangeType').val();

            // Only proceed if both selections are made
            if (dataType && dateRangeType) {
                // Show appropriate selection div based on data type
                if (dataType === 'per_orang') {
                    $('#employeeSelectDiv').removeClass('hidden');
                    initializeEmployeeSelect();
                } else if (dataType === 'per_area') {
                    $('#areaSelectDiv').removeClass('hidden');
                    loadAreaOptions();
                }

                // Show appropriate date inputs based on date range type
                $('#dateInputs').removeClass('hidden');
                if (dateRangeType === 'daily') {
                    $('#dailyInputs').removeClass('hidden');
                } else if (dateRangeType === 'monthly') {
                    $('#monthlyInputs').removeClass('hidden');
                }

                // Show submit button and export button
                $('#submitBtn, #exportBtn').removeClass('hidden');
            }
        }

        $('#exportBtn').on('click', function(e) {
            e.preventDefault();

            if (validateForm()) {
                exportToExcel();
            }
        });

        function exportToExcel() {
            // Tampilkan dialog progress dengan estimasi waktu
            const exportPromise = new Promise((resolve, reject) => {
                Swal.fire({
                    title: 'Mempersiapkan Laporan Excel',
                    html: `
                <div class="export-progress">
                    <div class="progress-bar">
                        <div class="progress"></div>
                    </div>
                    <div class="progress-text">Mengumpulkan data... 0%</div>
                    <div class="time-remaining">Estimasi waktu: <span class="time">15</span> detik</div>
                </div>
            `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        // Simulasi progres dengan estimasi waktu lebih realistis
                        const progressBar = Swal.getHtmlContainer().querySelector(
                            '.progress');
                        const progressText = Swal.getHtmlContainer().querySelector(
                            '.progress-text');
                        const timeRemaining = Swal.getHtmlContainer().querySelector(
                            '.time');

                        let progress = 0;
                        let timeEstimate = 5; // Estimasi awal 5 detik

                        const interval = setInterval(() => {
                            progress +=
                                20; // Naik 20% tiap 500ms -> 5 detik selesai
                            if (progress >= 100) {
                                progress = 100;
                                clearInterval(interval);
                                resolve();
                            }

                            // Update progress bar dan teks
                            progressBar.style.width = `${progress}%`;
                            progressText.textContent =
                                `Mempersiapkan file... ${Math.round(progress)}%`;

                            // Perbarui estimasi waktu
                            timeEstimate = Math.max(1, Math.round((100 - progress) /
                                20 * 0.5));
                            timeRemaining.textContent = timeEstimate;

                        }, 500); // Update setiap 500ms
                        // Update setiap 500ms
                    }
                });
            });

            // Setelah simulasi progres selesai, lakukan export sebenarnya
            exportPromise.then(() => {
                // Sembunyikan Swal progres
                Swal.close();

                // Tampilkan notifikasi bahwa download akan segera dimulai
                Swal.fire({
                    title: 'File Siap!',
                    text: 'Download akan segera dimulai...',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Lakukan request export sebenarnya
                const formData = getFormData();
                const form = new FormData();

                for (const [key, value] of Object.entries(formData)) {
                    form.append(key, value);
                }

                // Gunakan pendekatan fetch dengan Blob untuk handling yang lebih baik
                fetch('<?php echo base_url('user/export_excel'); ?>', {
                        method: 'POST',
                        body: form
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        // Buat URL untuk blob dan trigger download
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download =
                            `Laporan_Absensi_${new Date().toISOString().split('T')[0]}.xlsx`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        // Tampilkan notifikasi sukses
                        Swal.fire({
                            title: 'Download Berhasil!',
                            text: 'File Excel telah berhasil diunduh',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    })
                    .catch(error => {
                        console.error('Error exporting to Excel:', error);
                        Swal.fire({
                            title: 'Export Gagal',
                            text: 'Terjadi kesalahan saat mengekspor data: ' + error
                                .message,
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    });
            });
        }

        // Tambahkan CSS untuk progress bar yang lebih menarik
        const exportStyles = `
            .export-progress {
                width: 100%;
                padding: 10px 0;
            }
            .progress-bar {
                width: 100%;
                height: 20px;
                background-color: #f3f4f6;
                border-radius: 10px;
                overflow: hidden;
                margin-bottom: 10px;
            }
            .progress {
                height: 100%;
                background-color: #10b981;
                border-radius: 10px;
                width: 0%;
                transition: width 0.5s ease;
            }
            .progress-text {
                text-align: center;
                margin-bottom: 5px;
                font-weight: bold;
            }
            .time-remaining {
                text-align: center;
                font-size: 0.9em;
                color: #6b7280;
            }
        `;

        // Inject styles
        const styleSheet = document.createElement('style');
        styleSheet.textContent = exportStyles;
        document.head.appendChild(styleSheet);


        // Alternative simpler version using XMLHttpRequest for blob handling
        function exportToExcelAlt() {
            Swal.fire({
                title: 'Export ke Excel',
                text: 'Sedang memproses data untuk export...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            const formData = getFormData();

            // Convert object to FormData
            const form = new FormData();
            for (const [key, value] of Object.entries(formData)) {
                form.append(key, value);
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo base_url('user/export_excel'); ?>', true);
            xhr.responseType = 'blob';

            xhr.onload = function() {
                Swal.close();

                if (xhr.status === 200) {
                    // Check if response is actually a blob (Excel file)
                    if (xhr.response.type ===
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                        // Create blob URL and trigger download
                        const blob = new Blob([xhr.response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'Laporan_Absensi_' + new Date().toISOString().split('T')[0] +
                            '.xlsx';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);

                        Swal.fire({
                            title: 'Export Berhasil!',
                            text: 'File Excel berhasil didownload',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // Response is likely JSON error message
                        const reader = new FileReader();
                        reader.onload = function() {
                            try {
                                const errorData = JSON.parse(reader.result);
                                Swal.fire({
                                    title: 'Export Gagal',
                                    text: errorData.message || 'Gagal membuat file Excel',
                                    icon: 'error',
                                    confirmButtonColor: '#dc2626'
                                });
                            } catch (e) {
                                Swal.fire({
                                    title: 'Export Gagal',
                                    text: 'Terjadi kesalahan saat export data',
                                    icon: 'error',
                                    confirmButtonColor: '#dc2626'
                                });
                            }
                        };
                        reader.readAsText(xhr.response);
                    }
                } else {
                    Swal.fire({
                        title: 'Export Error',
                        text: 'Terjadi kesalahan saat export data (Status: ' + xhr.status + ')',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                }
            };

            xhr.onerror = function() {
                Swal.close();
                Swal.fire({
                    title: 'Export Error',
                    text: 'Terjadi kesalahan koneksi saat export data',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            };

            xhr.send(form);
        }

        // 6. Tambahkan notifikasi loading yang lebih informatif
        function showExportProgress() {
            let timerInterval;
            Swal.fire({
                title: 'Memproses Export Excel',
                html: 'Sedang mengumpulkan data... <b></b> detik',
                timer: 30000,
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    const b = Swal.getHtmlContainer().querySelector('b');
                    timerInterval = setInterval(() => {
                        b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            });
        }

        $('#searchForm').on('submit', function(e) {
            e.preventDefault();

            if (validateForm()) {
                loadAttendanceData();
            }
        });
    });

    function generateYearOptions() {
        const currentYear = new Date().getFullYear();
        const yearSelect = $('#selectedYear');

        for (let year = currentYear; year >= currentYear - 5; year--) {
            yearSelect.append(`<option value="${year}"${year === currentYear ? ' selected' : ''}>${year}</option>`);
        }
    }

    function setDefaultDates() {
        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        $('#startDate').val(firstDayOfMonth.toISOString().split('T')[0]);
        $('#endDate').val(lastDayOfMonth.toISOString().split('T')[0]);

        $('#selectedMonth').val(String(today.getMonth() + 1).padStart(2, '0'));
        $('#selectedYear').val(today.getFullYear());
    }

    function loadAreaOptions() {
        $.ajax({
            url: '<?php echo base_url('user/get_lokasi'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const areaSelect = $('#areaSelect');
                areaSelect.empty().append('<option value="">-- Pilih Area --</option>');

                if (response && response.length > 0) {
                    response.forEach(function(area) {
                        areaSelect.append(
                            `<option value="${area.id}">${area.nama_lokasi}</option>`);
                    });
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data lokasi', 'error');
            }
        });
    }

    function initializeEmployeeSelect() {
        if (!$('#employeeSelect').hasClass('select2-hidden-accessible')) {
            $('#employeeSelect').select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketikan minimal 3 karakter untuk mencari...',
                minimumInputLength: 3,
                allowClear: true,
                ajax: {
                    url: '<?php echo base_url('user/get_karyawan'); ?>',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });
        }
    }

    function validateForm() {
        const dataType = $('#dataType').val();
        const dateRangeType = $('#dateRangeType').val();

        if (!dataType) {
            Swal.fire('Peringatan', 'Silakan pilih type data terlebih dahulu', 'warning');
            return false;
        }

        if (!dateRangeType) {
            Swal.fire('Peringatan', 'Silakan pilih jarak waktu terlebih dahulu', 'warning');
            return false;
        }

        // Validate selection based on data type
        if (dataType === 'per_orang') {
            const selectedEmployee = $('#employeeSelect').val();
            if (!selectedEmployee) {
                Swal.fire('Peringatan', 'Silakan pilih karyawan terlebih dahulu', 'warning');
                return false;
            }
        } else if (dataType === 'per_area') {
            const selectedArea = $('#areaSelect').val();
            if (!selectedArea) {
                Swal.fire('Peringatan', 'Silakan pilih area terlebih dahulu', 'warning');
                return false;
            }
        }

        // Validate date inputs
        if (dateRangeType === 'daily') {
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();

            if (!startDate || !endDate) {
                Swal.fire('Peringatan', 'Silakan pilih tanggal mulai dan tanggal akhir', 'warning');
                return false;
            }

            if (new Date(startDate) > new Date(endDate)) {
                Swal.fire('Peringatan', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir', 'warning');
                return false;
            }
        } else if (dateRangeType === 'monthly') {
            const selectedMonth = $('#selectedMonth').val();
            const selectedYear = $('#selectedYear').val();

            if (!selectedMonth || !selectedYear) {
                Swal.fire('Peringatan', 'Silakan pilih bulan dan tahun', 'warning');
                return false;
            }
        }

        return true;
    }

    function loadAttendanceData() {
        Swal.fire({
            title: 'Memuat data',
            text: 'Sedang mengambil data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        const formData = getFormData();

        $.ajax({
            url: '<?php echo base_url('user/get_absensi_data'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                Swal.close();

                if (response && response.status === 'success') {
                    // Remove previous summary cards if exist
                    $('.summary-cards').remove();

                    updateAttendanceTable(response.data);

                    // Show summary cards if available
                    if (response.summary) {
                        showSummaryCards(response.summary);
                    }

                    Swal.fire({
                        title: 'Berhasil',
                        text: `Data berhasil dimuat (${response.data.length} record)`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    // Remove previous summary cards if exist
                    $('.summary-cards').remove();
                    updateAttendanceTable([]);
                    Swal.fire('Info', response.message || 'Tidak ada data yang ditemukan', 'info');
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('AJAX Error:', xhr.responseText);

                let errorMessage = 'Terjadi kesalahan saat memuat data';
                if (xhr.responseText) {
                    try {
                        let errorResponse = JSON.parse(xhr.responseText);
                        errorMessage = errorResponse.message || errorMessage;
                    } catch (e) {
                        errorMessage += ': ' + xhr.responseText;
                    }
                }

                Swal.fire('Error', errorMessage, 'error');
            }
        });
    }

    function getFormData() {
        const dataType = $('#dataType').val();
        const dateRangeType = $('#dateRangeType').val();

        let formData = {
            data_type: dataType,
            date_range_type: dateRangeType
        };

        // Add selection based on data type
        if (dataType === 'per_orang') {
            formData.employee_id = $('#employeeSelect').val();
        } else if (dataType === 'per_area') {
            formData.area_id = $('#areaSelect').val();
        }

        // Add date range data
        if (dateRangeType === 'daily') {
            formData.start_date = $('#startDate').val();
            formData.end_date = $('#endDate').val();
        } else if (dateRangeType === 'monthly') {
            formData.month = $('#selectedMonth').val();
            formData.year = $('#selectedYear').val();
        }

        return formData;
    }

    function getTwoWords(name) {
        if (!name) return '';

        // Pisahkan nama menjadi array kata
        const words = name.trim().split(/\s+/);

        // Ambil 2 kata pertama
        if (words.length >= 2) {
            return words[0] + ' ' + words[1];
        }

        // Jika hanya ada 1 kata, kembalikan kata tersebut
        return words[0];
    }

    function updateAttendanceTable(data) {
        const tbody = $('tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append(`
                    <tr>
                        <td colspan="13" class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                            <p class="text-lg">Tidak ada data yang ditemukan</p>
                        </td>
                    </tr>
                `);
            return;
        }

        data.forEach(row => {
            // Tentukan class dan badge berdasarkan type dan status
            let statusClass = '';
            let typeClass = '';
            let typeBadge = '';
            const shortName = getTwoWords(row.nama_karyawan);

            if (row.type === 'absensi') {
                typeClass = 'bg-blue-100 text-blue-800';
                typeBadge = 'Absensi';

                statusClass = row.status === 'Masuk' ? 'bg-green-100 text-green-800' :
                    row.status.includes('Belum Pulang') ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800';
            } else if (row.type === 'cuti') {
                typeClass = 'bg-purple-100 text-purple-800';
                typeBadge = 'Cuti';
                statusClass = 'bg-purple-100 text-purple-800';
            } else if (row.type === 'izin') {
                typeClass = 'bg-orange-100 text-orange-800';
                typeBadge = 'Izin';
                statusClass = 'bg-orange-100 text-orange-800';
            }

            const tr = `
                <tr class="hover:bg-gray-50">
                    <td data-label="Type" class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${typeClass}">
                            ${typeBadge}
                        </span>
                    </td>
                    <td data-label="Nama" class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                        ${shortName}
                    </td>
                    <td data-label="Tanggal" class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                        ${row.tanggal_formatted}
                    </td>
                    <td data-label="Hari" class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                        ${row.hari}
                    </td>
                    <td data-label="Jam Masuk" class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                        ${row.jam_masuk || '-'}
                    </td>
                    <td data-label="Lokasi Masuk" class="px-4 py-4 text-sm text-gray-600">
                        <div class="max-w-32">
                            ${row.lokasi_masuk || '-'}
                        </div>
                    </td>
                    <td data-label="Jam Pulang" class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                        ${row.jam_pulang || '-'}
                    </td>
                    <td data-label="Lokasi Pulang" class="px-4 py-4 text-sm text-gray-600">
                        <div class="max-w-32">
                            ${row.lokasi_pulang || '-'}
                        </div>
                    </td>
                    <td data-label="Status" class="px-4 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${row.status}
                        </span>
                    </td>
                    <td data-label="Alasan" class="px-4 py-4 text-sm text-gray-600">
                        <div class="max-w-40">
                            ${row.alasan || '-'}
                        </div>
                    </td>
                    <td data-label="Periode" class="px-4 py-4 text-sm text-gray-600">
                        <div class="max-w-32">
                            ${row.periode || '-'}
                        </div>
                    </td>
                    <td data-label="Keterangan" class="px-4 py-4 text-sm text-gray-600">
                        <div class="max-w-32">
                            ${row.keterangan || '-'}
                        </div>
                    </td>
                    <td data-label="Aksi" class="px-4 py-4 text-sm text-gray-600">
                        ${getActionButtons(row)}
                    </td>
                </tr>
                `;

            tbody.append(tr);
        });
    }

    function getActionButtons(row) {
        let buttons = [];

        // Button untuk foto absensi
        if (row.type === 'absensi' && (row.foto_masuk || row.foto_pulang)) {
            buttons.push(`
                    <button onclick="showFoto('${row.foto_masuk || ''}','${row.foto_pulang || ''}')" 
                        class="mb-1 w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-1 px-2 rounded text-xs font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                        <i class="fas fa-images mr-1"></i>Foto Absensi
                    </button>
                `);
        }

        // Button untuk foto izin
        if (row.type === 'izin' && row.foto_dokumen) {
            buttons.push(`
                    <button onclick="showFotoDokumen('${row.foto_dokumen}')" 
                        class="mb-1 w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-1 px-2 rounded text-xs font-semibold hover:from-orange-600 hover:to-orange-700 transition-all duration-200">
                        <i class="fas fa-file-image mr-1"></i>Foto Izin
                    </button>
                `);
        }

        // Jika tidak ada foto
        if (buttons.length === 0) {
            return '<span class="text-gray-400">-</span>';
        }

        return buttons.join('');
    }

    // Fungsi untuk menampilkan foto absensi
    function showFoto(fotoMasuk, fotoPulang) {
        if (fotoMasuk && fotoMasuk !== '') {
            $('#modalFotoMasuk').attr('src', fotoMasuk).show();
            $('#noFotoMasuk').hide();
        } else {
            $('#modalFotoMasuk').hide();
            $('#noFotoMasuk').show();
        }

        if (fotoPulang && fotoPulang !== '') {
            $('#modalFotoPulang').attr('src', fotoPulang).show();
            $('#noFotoPulang').hide();
        } else {
            $('#modalFotoPulang').hide();
            $('#noFotoPulang').show();
        }

        $('#fotoModal').removeClass('hidden');
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

    // Fungsi untuk menampilkan summary cards
    function showSummaryCards(summary) {
        const summaryHtml = `
                <div class="summary-cards grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <div class="text-2xl font-bold text-gray-800">${summary.total_records}</div>
                        <div class="text-xs text-gray-600">Total Data</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">${summary.absensi_count}</div>
                        <div class="text-xs text-gray-600">Absensi</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600">${summary.cuti_count}</div>
                        <div class="text-xs text-gray-600">Cuti</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <div class="text-2xl font-bold text-orange-600">${summary.izin_count}</div>
                        <div class="text-xs text-gray-600">Izin</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">${summary.present_days}</div>
                        <div class="text-xs text-gray-600">Hadir</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-600">${summary.late_days}</div>
                        <div class="text-xs text-gray-600">Terlambat</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <div class="text-2xl font-bold text-red-600">${summary.incomplete_days}</div>
                        <div class="text-xs text-gray-600">Belum Pulang</div>
                    </div>
                </div>
            `;

        // Insert summary before table
        $('.bg-white.rounded-2xl.shadow-lg.p-6.border.border-gray-200.overflow-x-auto').before(summaryHtml);
    }

    // Modal close handlers
    $('#closeModal').on('click', function() {
        $('#fotoModal').addClass('hidden');
    });

    $(window).on('click', function(e) {
        if ($(e.target).is('#fotoModal')) {
            $('#fotoModal').addClass('hidden');
        }
    });

    // User dropdown and logout handlers
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
    </script>
</body>

</html>