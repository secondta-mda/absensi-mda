<!-- JANGAN LAH DI BUKA BUKA BANGGG -->
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
                        class="w-full flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 bg-gray-800"><i
                            class="fas fa-user mr-2"></i>Profil</a></li>
            </ul>
        </aside>

        <main class="flex-1 p-6 md:ml-64 mb-20 md:mb-0">
            <h2 class="text-3xl font-bold mb-8 text-gray-800 border-b pb-2">Profil</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <form action="<?= base_url('user/ubah_username'); ?>" method="post" id="usernameForm"
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 flex flex-col h-full">
                    <div>
                        <label for="username" class="block text-gray-700 font-medium mb-1">Username</label>
                        <input type="text" id="username" name="username"
                            value="<?php echo htmlspecialchars($username); ?>" required
                            class="border border-gray-300 w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="flex-grow"></div>
                    <div class="border-t border-gray-200 pt-4 mt-4 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan Username
                        </button>
                    </div>
                </form>

                <form action="<?= base_url('user/ubah_password'); ?>" method="post" id="passwordForm"
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 flex flex-col h-full">
                    <div>
                        <label for="password-lama" class="block text-gray-700 font-medium mb-1">Password Lama</label>
                        <input type="password" id="password-lama" name="password_lama" required
                            class="border border-gray-300 w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password-baru" class="block text-gray-700 font-medium mb-1">Password
                                Baru</label>
                            <input type="password" id="password-baru" name="password_baru" required
                                class="border border-gray-300 w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <div id="password-baru-error" class="text-red-500 text-xs mt-1 hidden">
                                Password harus minimal 8 karakter
                            </div>
                        </div>
                        <div>
                            <label for="konfirmasi-password-baru"
                                class="block text-gray-700 font-medium mb-1">Konfirmasi Password Baru</label>
                            <input type="password" id="konfirmasi-password-baru" name="konfirmasi_password_baru"
                                required
                                class="border border-gray-300 w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <div id="konfirmasi-password-error" class="text-red-500 text-xs mt-1 hidden">
                                Konfirmasi password tidak sesuai
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow"></div>
                    <div class="border-t border-gray-200 pt-4 mt-4 flex justify-end">
                        <button type="submit" id="submitPasswordBtn"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
            <div class="md:hidden w-full mt-6">
                <button id="logoutBtnMobile"
                    class="w-full bg-red-600 text-white px-6 py-2 rounded-lg shadow hover:bg-red-700 transition flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </button>
            </div>

        </main>
    </div>

    <nav
        class="md:hidden rounded-t-3xl fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t border-gray-200 flex justify-around py-2 z-30">
        <a href="<?php echo base_url('user'); ?>"
            class="group flex flex-col items-center text-gray-600 hover:text-blue-600">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full">
                <i class="fas fa-home text-lg text-gray-600 group-hover:text-blue-700"></i>
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
                <i class="fas fa-user text-lg text-blue-600 group-hover:text-blue-700"></i>
            </span>
            <span class="text-xs">Profil</span>
        </a>
    </nav>

    <script>
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const submitBtn = document.getElementById('submitBtn');
    const usernameForm = document.getElementById('usernameForm');

    usernameForm.addEventListener('submit', function(e) {
        if (!validate()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harap perbaiki kesalahan pada form sebelum mengajukan cuti.',
                confirmButtonColor: '#3b82f6',
            });
        }
    });

    <?php if ($this->session->flashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?php echo $this->session->flashdata('success'); ?>',
        confirmButtonColor: '#3b82f6',
        timer: 3000,
        timerProgressBar: true
    });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        html: '<?php echo addslashes($this->session->flashdata('error')); ?>',
        confirmButtonColor: '#3b82f6'
    });
    <?php endif; ?>

    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }

    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdown = document.getElementById('userDropdown');
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutBtnMobile = document.getElementById('logoutBtnMobile');

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

    if (logoutBtnMobile) {
        logoutBtnMobile.addEventListener('click', () => {
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

    const passwordBaruInput = document.getElementById('password-baru');
    const konfirmasiPasswordInput = document.getElementById('konfirmasi-password-baru');
    const passwordBaruError = document.getElementById('password-baru-error');
    const konfirmasiPasswordError = document.getElementById('konfirmasi-password-error');
    const submitPasswordBtn = document.getElementById('submitPasswordBtn');
    const passwordForm = document.getElementById('passwordForm');

    function validatePassword() {
        const passwordBaru = passwordBaruInput.value;
        const konfirmasiPassword = konfirmasiPasswordInput.value;
        let isValid = true;

        if (passwordBaru.length > 0 && passwordBaru.length < 8) {
            passwordBaruError.classList.remove('hidden');
            isValid = false;
        } else {
            passwordBaruError.classList.add('hidden');
        }

        if (konfirmasiPassword.length > 0 && passwordBaru !== konfirmasiPassword) {
            konfirmasiPasswordError.classList.remove('hidden');
            isValid = false;
        } else {
            konfirmasiPasswordError.classList.add('hidden');
        }

        submitPasswordBtn.disabled = !isValid;

        return isValid;
    }

    passwordBaruInput.addEventListener('input', validatePassword);
    konfirmasiPasswordInput.addEventListener('input', validatePassword);

    passwordForm.addEventListener('submit', function(e) {
        if (!validatePassword()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harap periksa kembali input password Anda.',
                confirmButtonColor: '#3b82f6',
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        submitPasswordBtn.disabled = true;
    });
    </script>
</body>

</html>