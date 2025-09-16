<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Profil</title>
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

    /* Animasi untuk navbar */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }

    /* Efek glassmorphism untuk navbar */
    .glass-navbar {
        background: rgba(31, 41, 55, 0.85);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Hover effect untuk menu items */
    .menu-item {
        position: relative;
        transition: all 0.3s ease;
    }

    .menu-item::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
        transition: width 0.3s ease;
    }

    .menu-item:hover::after {
        width: 100%;
    }

    /* Efek untuk dropdown */
    .dropdown-enter {
        opacity: 0;
        transform: translateY(-10px);
    }

    .dropdown-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.2s, transform 0.2s;
    }

    /* Efek untuk notifikasi badge */
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    </style>
</head>

<body class="lexend-deca-font bg-gray-100">

    <!-- Enhanced Navbar (desktop) -->
    <nav
        class="glass-navbar hidden md:flex w-full text-white px-6 py-3 items-center justify-between sticky top-0 z-50 shadow-lg">
        <div class="flex items-center space-x-2">
            <div class="bg-blue-600 p-2 rounded-lg">
                <i class="fas fa-calendar-check text-white text-xl"></i>
            </div>
            <div class="font-bold text-xl bg-gradient-to-r from-blue-400 to-blue-600 bg-clip-text text-transparent">
                ABSENSI MDA</div>
        </div>

        <div class="flex items-center space-x-6">

            <!-- User Profile Dropdown -->
            <div class="relative">
                <button id="userDropdownBtn"
                    class="flex items-center space-x-3 bg-gray-700 hover:bg-gray-600 pl-2 pr-4 py-1 rounded-full transition-all duration-200">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                        <span class="text-white font-semibold text-sm"><?php echo substr($username, 0, 1); ?></span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium"><?php echo $username; ?></span>
                        <i class="fas fa-chevron-down text-xs ml-2 transition-transform duration-200"
                            id="dropdownArrow"></i>
                    </div>
                </button>

                <div id="userDropdown"
                    class="hidden absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl py-1 z-50 border border-gray-700 dropdown-enter">
                    <div class="px-4 py-2 border-b border-gray-700">
                        <p class="text-sm text-gray-200">Masuk sebagai</p>
                        <p class="text-sm font-medium truncate"><?php echo $username; ?></p>
                    </div>
                    <a href="<?php echo base_url('user/profil'); ?>"
                        class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                        <i class="fas fa-user-circle mr-3 text-gray-400"></i>Profil Saya
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                        <i class="fas fa-cog mr-3 text-gray-400"></i>Pengaturan
                    </a>
                    <div class="border-t border-gray-700"></div>
                    <button id="logoutBtn"
                        class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 w-full text-left">
                        <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>Keluar
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex min-h-screen">
        <!-- Enhanced Sidebar (desktop) -->
        <aside id="sidebar"
            class="hidden md:block fixed top-0 left-0 h-screen bg-gradient-to-b from-gray-900 to-gray-800 shadow-xl border-r border-gray-700 w-64 z-40">
            <div class="mt-[72px] p-4 font-bold text-gray-100 text-lg border-b border-gray-700 flex items-center">
                <span>Menu Navigasi</span>
            </div>
            <ul class="p-3 space-y-1 mt-3">
                <li>
                    <a href="<?php echo base_url('user'); ?>"
                        class="menu-item w-full flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white group transition-all">
                        <div
                            class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center mr-3 group-hover:bg-blue-500/20 transition-colors">
                            <i class="fas fa-home text-blue-400"></i>
                        </div>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('user/absen'); ?>"
                        class="menu-item w-full flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white group transition-all">
                        <div
                            class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center mr-3 group-hover:bg-green-500/20 transition-colors">
                            <i class="fas fa-calendar-check text-green-400"></i>
                        </div>
                        <span>Absen</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('user/cuti'); ?>"
                        class="menu-item w-full flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white group transition-all">
                        <div
                            class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center mr-3 group-hover:bg-amber-500/20 transition-colors">
                            <i class="fas fa-briefcase text-amber-400"></i>
                        </div>
                        <span>Cuti</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('user/izin'); ?>"
                        class="menu-item w-full flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white group transition-all">
                        <div
                            class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center mr-3 group-hover:bg-purple-500/20 transition-colors">
                            <i class="fas fa-file-alt text-purple-400"></i>
                        </div>
                        <span>Izin</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('user/profil'); ?>"
                        class="menu-item w-full flex items-center px-4 py-3 rounded-lg text-white bg-blue-900/30 group transition-all">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-400"></i>
                        </div>
                        <span>Profil</span>
                        <span class="ml-auto bg-blue-500 text-xs px-2 py-1 rounded-full">Aktif</span>
                    </a>
                </li>
            </ul>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700">
                <div class="flex items-center justify-between text-gray-400 text-xs">
                    <span>Status Sistem</span>
                    <span class="flex items-center">
                        <span class="flex w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                        Online
                    </span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:ml-64 mb-20 md:mb-0">

        </main>
    </div>

    <!-- Enhanced Bottom Navigation (mobile) -->
    <nav
        class="md:hidden fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t border-gray-200 flex justify-around py-3 z-50">
        <a href="<?php echo base_url('user'); ?>"
            class="group flex flex-col items-center text-gray-600 hover:text-blue-600 relative">
            <span
                class="inline-flex items-center justify-center w-10 h-10 rounded-full group-hover:bg-blue-50 transition-colors">
                <i class="fas fa-home text-lg group-hover:text-blue-600"></i>
            </span>
            <span class="text-xs mt-1">Home</span>
        </a>
        <a href="<?php echo base_url('user/absen'); ?>"
            class="group flex flex-col items-center text-gray-600 hover:text-blue-600 relative">
            <span
                class="inline-flex items-center justify-center w-10 h-10 rounded-full group-hover:bg-blue-50 transition-colors">
                <i class="fas fa-calendar-check text-lg group-hover:text-blue-600"></i>
            </span>
            <span class="text-xs mt-1">Absen</span>
        </a>
        <a href="<?php echo base_url('user/cuti'); ?>"
            class="group flex flex-col items-center text-gray-600 hover:text-blue-600 relative">
            <span
                class="inline-flex items-center justify-center w-10 h-10 rounded-full group-hover:bg-blue-50 transition-colors">
                <i class="fas fa-briefcase text-lg group-hover:text-blue-600"></i>
            </span>
            <span class="text-xs mt-1">Cuti</span>
        </a>
        <a href="<?php echo base_url('user/izin'); ?>"
            class="group flex flex-col items-center text-gray-600 hover:text-blue-600 relative">
            <span
                class="inline-flex items-center justify-center w-10 h-10 rounded-full group-hover:bg-blue-50 transition-colors">
                <i class="fas fa-file-alt text-lg group-hover:text-blue-600"></i>
            </span>
            <span class="text-xs mt-1">Izin</span>
        </a>
        <a href="<?php echo base_url('user/profil'); ?>"
            class="group flex flex-col items-center text-blue-600 relative">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100">
                <i class="fas fa-user text-lg text-blue-600"></i>
            </span>
            <span class="text-xs mt-1">Profil</span>
            <span class="absolute -top-1 -right-1 flex w-3 h-3">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full w-3 h-3 bg-blue-500"></span>
            </span>
        </a>
    </nav>

    <script>
    // Enhanced JavaScript for better UI interactions
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // SweetAlert untuk notifikasi flashdata
        <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?php echo $this->session->flashdata('success'); ?>',
            confirmButtonColor: '#3b82f6',
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end'
        });
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: '<?php echo addslashes($this->session->flashdata('error')); ?>',
            confirmButtonColor: '#3b82f6',
            toast: true,
            position: 'top-end'
        });
        <?php endif; ?>

        // Dropdown User dengan animasi
        const userDropdownBtn = document.getElementById('userDropdownBtn');
        const userDropdown = document.getElementById('userDropdown');
        const dropdownArrow = document.getElementById('dropdownArrow');
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutBtnMobile = document.getElementById('logoutBtnMobile');

        if (userDropdownBtn && userDropdown) {
            userDropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
                dropdownArrow.classList.toggle('rotate-180');

                // Tambahkan animasi
                if (!userDropdown.classList.contains('hidden')) {
                    userDropdown.classList.add('animate-slide-in');
                }
            });
        }

        // Fungsi logout
        const logout = () => {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    sessionStorage.clear();
                    localStorage.clear();
                    window.location.href = "<?php echo base_url('auth/logout'); ?>";
                }
            });
        };

        if (logoutBtn) {
            logoutBtn.addEventListener('click', logout);
        }

        if (logoutBtnMobile) {
            logoutBtnMobile.addEventListener('click', logout);
        }

        // Tutup dropdown ketika klik di luar
        document.addEventListener('click', function(event) {
            if (userDropdown && !userDropdown.contains(event.target) &&
                userDropdownBtn && !userDropdownBtn.contains(event.target)) {
                userDropdown.classList.add('hidden');
                dropdownArrow.classList.remove('rotate-180');
            }
        });

        // Prevent dropdown close when clicking inside it
        if (userDropdown) {
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Form validation
        const usernameForm = document.getElementById('usernameForm');
        if (usernameForm) {
            usernameForm.addEventListener('submit', function(e) {
                const usernameInput = document.getElementById('username');
                if (usernameInput.value.trim() === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Username tidak boleh kosong',
                        confirmButtonColor: '#3b82f6',
                    });
                }
            });
        }

        // Add rotation class to dropdown arrow
        if (dropdownArrow) {
            dropdownArrow.classList.add('transition-transform', 'duration-200');
        }
    });
    </script>
</body>

</html>