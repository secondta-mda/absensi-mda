<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Cuti</title>
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

        <main class="flex-1 p-6 md:ml-64 mb-20 md:mb-0">
            <h2 class="text-3xl font-bold mb-8 text-gray-800 border-b pb-2">Riwayat Absensi Karyawan</h2>

            <div class="mb-6 bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-xl font-bold mb-6 text-gray-800">Filter Data Absensi</h3>

                <form id="searchForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jarak Waktu</label>
                            <select id="dateRangeType"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Jarak Waktu --</option>
                                <option value="daily">Harian (Rentang Tanggal)</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        <div id="employeeSelectDiv" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Karyawan</label>
                            <select id="employeeSelect" class="w-full">
                                <option value="">-- Pilih Karyawan --</option>
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

                    <div class="flex justify-end">
                        <button type="submit" id="submitBtn"
                            class="hidden bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>Tampilkan Data
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-8 md:mt-12">
                <h3 class="text-xl font-bold mb-4 md:mb-6 text-gray-800">Data Riwayat Absensi</h3>

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
                                    Keterangan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-500">
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

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        generateYearOptions();

        setDefaultDates();

        $('#dateRangeType').on('change', function() {
            const selectedType = $(this).val();

            $('#dateInputs, #dailyInputs, #monthlyInputs, #employeeSelectDiv, #submitBtn').addClass(
                'hidden');

            if (selectedType) {
                $('#dateInputs, #employeeSelectDiv').removeClass('hidden');

                if (selectedType === 'daily') {
                    $('#dailyInputs').removeClass('hidden');
                } else if (selectedType === 'monthly') {
                    $('#monthlyInputs').removeClass('hidden');
                }

                initializeEmployeeSelect();

                $('#submitBtn').removeClass('hidden');
            }
        });

        $('#searchForm').on('submit', function(e) {
            e.preventDefault();

            if (validateForm()) {
                loadEmployeeAttendance();
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
        const dateRangeType = $('#dateRangeType').val();
        const selectedEmployee = $('#employeeSelect').val();

        if (!dateRangeType) {
            Swal.fire('Peringatan', 'Silakan pilih jarak waktu terlebih dahulu', 'warning');
            return false;
        }

        if (!selectedEmployee) {
            Swal.fire('Peringatan', 'Silakan pilih karyawan terlebih dahulu', 'warning');
            return false;
        }

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

    function loadEmployeeAttendance() {
        Swal.fire({
            title: 'Memuat data',
            text: 'Sedang mengambil data absensi...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        const formData = getFormData();

        $.ajax({
            url: '<?php echo base_url('user/get_absensi_karyawan'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                Swal.close();

                if (response && response.status === 'success') {
                    updateAttendanceTable(response.data);
                    Swal.fire({
                        title: 'Berhasil',
                        text: `Data absensi berhasil dimuat (${response.data.length} record)`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
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
        const dateRangeType = $('#dateRangeType').val();
        const employeeId = $('#employeeSelect').val();

        let formData = {
            employee_id: employeeId,
            date_range_type: dateRangeType
        };

        if (dateRangeType === 'daily') {
            formData.start_date = $('#startDate').val();
            formData.end_date = $('#endDate').val();
        } else if (dateRangeType === 'monthly') {
            formData.month = $('#selectedMonth').val();
            formData.year = $('#selectedYear').val();
        }

        return formData;
    }

    function updateAttendanceTable(data) {
        const tbody = $('tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="9" class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                        <p class="text-lg">Tidak ada data absensi yang ditemukan</p>
                    </td>
                </tr>
            `);
            return;
        }

        data.forEach(row => {
            const statusClass = row.status === 'Masuk' ? 'bg-green-100 text-green-800' :
                row.status.includes('Belum Pulang') ? 'bg-yellow-100 text-yellow-800' :
                row.status === 'Izin' ? 'bg-blue-100 text-blue-800' :
                'bg-red-100 text-red-800';

            const tr = `
            <tr class="hover:bg-gray-50">
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
                <td data-label="Keterangan" class="px-4 py-4 text-sm text-gray-600">
                    <div class="max-w-32">
                        ${row.keterangan || '-'}
                    </div>
                </td>
                <td data-label="Aksi" class="px-4 py-4 text-sm text-gray-600">
                    ${(row.foto_masuk || row.foto_pulang) ? 
                    `<button onclick="showFoto('${row.foto_masuk || ''}','${row.foto_pulang || ''}')" 
                        class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-2 px-3 rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transition-all duration-200">
                        <i class="fas fa-images mr-1"></i>Lihat Foto
                    </button>` : 
                    '<span class="text-gray-400">-</span>'}
                </td>
            </tr>
        `;

            tbody.append(tr);
        });
    }

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

    $('#closeModal').on('click', function() {
        $('#fotoModal').addClass('hidden');
    });

    $(window).on('click', function(e) {
        if ($(e.target).is('#fotoModal')) {
            $('#fotoModal').addClass('hidden');
        }
    });

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