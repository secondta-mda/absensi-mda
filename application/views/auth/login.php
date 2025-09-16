<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Absensi</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome CDN -->
    <script src="https://kit.fontawesome.com/your-fa-kit.js" crossorigin="anonymous"></script>
    <!-- ganti "your-fa-kit.js" dengan kit asli atau pakai link cdnjs -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap');

    .lexend-deca-font {
        font-family: "Lexend Deca", sans-serif;
        font-optical-sizing: auto;
        font-weight: 400;
        font-style: normal;
    }
    
    /* Spinner sederhana */
    .spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-top: 4px solid #ff0000;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-200 px-4 lexend-deca-font">

    <div
        class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl border border-gray-200 flex flex-col md:flex-row overflow-hidden">

        <!-- BAGIAN GAMBAR -->
        <div class="hidden md:flex w-1/2 items-center justify-center p-10 bg-gray-50">
            <img src="<?= base_url('assets/img/undraw_booking_1ztt.svg'); ?>" alt="Absensi Ilustrasi"
                class="w-full h-auto object-contain" />
        </div>

        <!-- BAGIAN FORM LOGIN -->
        <form action="<?= base_url('auth/login'); ?>" method="post"
            class="w-full md:w-1/2 p-8 space-y-7 flex flex-col items-center bg-white"
            style="backdrop-filter: blur(2px); box-shadow: 0 8px 32px 0 rgba(0,0,0,0.08)">

            <img src="<?= base_url('assets/img/logo-mda.png'); ?>" alt="Logo" width="200" class="mb-4" />
            <?php if ($this->session->flashdata('error')): ?>
            <div
                class="w-full mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded-xl text-center font-semibold">
                <?= $this->session->flashdata('error'); ?>
            </div>
            <?php endif; ?>
            <h2 class="text-2xl font-extrabold text-gray-800 text-center mb-2 tracking-wide drop-shadow">
                Login
            </h2>

            <!-- Username -->
            <div class="flex flex-col gap-2 w-full">
                <label for="username" class="text-sm font-semibold text-gray-700">Username</label>
                <input id="username" name="username" type="text" required
                    class="w-full border border-gray-300 p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#ff0000] transition bg-gray-50 text-gray-800 font-medium shadow"
                    placeholder="Masukkan username" autocomplete="username">
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-2 w-full relative">
                <label for="password" class="text-sm font-semibold text-gray-700">Password</label>
                <input id="password" name="password" type="password" required
                    class="w-full border border-gray-300 p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#ff0000] transition bg-gray-50 text-gray-800 font-medium shadow pr-12"
                    placeholder="Masukkan password" autocomplete="current-password">
                <button type="button" onclick="togglePassword()"
                    class="absolute right-4 top-[53px] transform -translate-y-1/2 text-[#ff0000] hover:text-gray-700 focus:outline-none">
                    <i id="eyeIcon" class="fa-solid fa-eye-slash text-xl"></i>
                </button>
            </div>

            <!-- Tombol Login -->
            <button type="submit"
                class="w-full bg-gradient-to-r from-[#ff0000] to-gray-700 text-white px-4 py-3 rounded-xl font-bold shadow-lg hover:scale-105 active:scale-95 transition-all duration-150 tracking-wide flex items-center justify-center gap-2">
                Login
            </button>

        </form>
    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById("password");
        const icon = document.getElementById("eyeIcon");
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }
    </script>
</body>

</html>