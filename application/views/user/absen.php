<!-- JANGAN LAH DI BUKA BUKA BANGGG -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Absensi</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap');

    .lexend-deca-font {
        font-family: "Lexend Deca", sans-serif;
        font-optical-sizing: auto;
        font-weight: 400;
        font-style: normal;
    }

    video {
        transform: scaleX(-1);
        -webkit-transform: scaleX(-1);
    }

    canvas {
        transform: scaleX(-1);
        -webkit-transform: scaleX(-1);
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
                        class="w-full flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 bg-gray-800"><i
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
            <h2 class="text-3xl font-bold mb-8 text-gray-800 border-b pb-2">Absensi</h2>

            <!-- GPS Info Panel -->
            <div id="gpsInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas fa-satellite-dish text-blue-600"></i>
                        <span class="font-semibold ml-2">Status GPS:</span>
                    </div>
                    <div class="text-right">
                        <div class="text-blue-600 font-bold">
                            Mencari sinyal...
                        </div>
                    </div>
                </div>
            </div>

            <div id="statusAbsensi" class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold mb-2">Status Absensi Hari Ini</h3>

                <?php if (isset($is_continuing_yesterday) && $is_continuing_yesterday): ?>
                <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                    <i class="fas fa-moon mr-1"></i>
                    Anda sedang melanjutkan shift dari tanggal
                    <?php echo date('d M Y', strtotime($absensi_today->tanggal)); ?>
                </div>
                <?php endif; ?>

                <div class="block md:flex gap-4">
                    <div id="statusMasuk"
                        class="flex items-center gap-2 <?php echo ($absensi_today && $absensi_today->jam_masuk) ? 'text-green-600' : 'text-gray-400'; ?>">
                        <i
                            class="fas <?php echo ($absensi_today && $absensi_today->jam_masuk) ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                        Masuk:
                        <?php echo ($absensi_today && $absensi_today->jam_masuk) ? $absensi_today->jam_masuk : 'Belum'; ?>
                    </div>
                    <div id="statusPulang"
                        class="mt-1 md:mt-0 flex items-center gap-2 <?php echo ($absensi_today && $absensi_today->jam_pulang) ? 'text-green-600' : 'text-gray-400'; ?>">
                        <i
                            class="fas <?php echo ($absensi_today && $absensi_today->jam_pulang) ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                        Pulang:
                        <?php echo ($absensi_today && $absensi_today->jam_pulang) ? $absensi_today->jam_pulang : 'Belum'; ?>
                    </div>
                </div>
                <?php if ($absensi_today && $absensi_today->jam_masuk && $absensi_today->jam_pulang): ?>
                <div class="mt-2 text-sm text-blue-600">
                    <i class="fas fa-clock mr-1"></i>
                    Durasi kerja: <?php echo $absensi_today->durasi_kerja; ?> jam
                </div>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 col-span-2 mt-6 text-center">
                <div class="flex flex-col items-center">
                    <?php if ($absensi_today && $absensi_today->jam_masuk && $absensi_today->jam_pulang): ?>
                    <?php else: ?>
                    <div
                        class="relative mb-4 rounded-xl overflow-hidden w-full max-w-2xl min-h-[300px] flex items-center justify-center">
                        <div id="cameraLoading" class="flex flex-col items-center">
                            <svg width="40" height="40" viewBox="0 0 50 50" class="animate-spin">
                                <circle cx="25" cy="25" r="20" fill="none" stroke="#ff0000" stroke-width="5"
                                    stroke-dasharray="90" stroke-dashoffset="60" />
                            </svg>
                            <span class="text-[#ff0000] font-bold animate-pulse">Memuat Kamera...</span>
                        </div>
                        <video id="video" autoplay playsinline muted class="w-full h-auto hidden"></video>
                        <canvas id="canvas" class="hidden w-full h-auto"></canvas>
                    </div>
                    <?php endif; ?>

                    <div id="currentDate" class="text-lg font-semibold text-gray-600 mb-2"></div>
                    <div id="currentTime" class="text-3xl font-bold mb-2"></div>
                    <div id="username" class="text-xl font-semibold text-gray-600 mb-2 capitalize">
                        <?php echo $username; ?></div>

                    <div class="flex gap-4 w-full max-w-md" id="buttonGroup">
                        <?php if ($absensi_today && $absensi_today->jam_masuk && $absensi_today->jam_pulang): ?>
                        <div class="w-full text-center text-gray-500 py-3">
                            <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                            <p>Absensi hari ini sudah selesai</p>
                        </div>
                        <?php else: ?>
                        <button id="captureBtn"
                            class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-3 rounded-xl font-semibold hover:from-red-600 hover:to-red-700">
                            <?php if (!$absensi_today || !$absensi_today->jam_masuk): ?>
                            Ambil Foto - Absen Masuk
                            <?php else: ?>
                            Ambil Foto - Absen Pulang
                            <?php endif; ?>
                        </button>
                        <?php endif; ?>
                    </div>

                    <?php if (!($absensi_today && $absensi_today->jam_masuk && $absensi_today->jam_pulang)): ?>
                    <button onclick="showGPSTips()" class="mt-3 text-blue-600 text-sm hover:underline">
                        <i class="fas fa-info-circle"></i> Tips Meningkatkan Akurasi GPS
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 flex flex-col">
                    <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100">
                            <i class="fas fa-arrow-right-to-bracket text-blue-500 text-2xl"></i>
                        </span>
                        Riwayat Masuk
                    </h3>
                    <div class="space-y-4 max-h-80 overflow-y-auto">
                        <?php
                        $masukList = array_filter($riwayat_masuk, function($item) {
                            return !empty($item->jam_masuk);
                        });
                        $masukList = array_slice($masukList, 0, 5);
                        ?>
                        <?php if (count($masukList) > 0): ?>
                        <?php foreach ($masukList as $item): ?>
                        <div class="p-4 rounded-xl flex flex-col gap-2 hover:shadow transition">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-lg text-gray-700">
                                    <?php echo date('H:i', strtotime($item->jam_masuk)); ?>
                                </span>
                                <span class="flex items-center gap-1 text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $item->nama_lokasi_masuk; ?>
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-sm text-gray-500">
                                <span><?php echo date('d M Y', strtotime($item->jam_masuk)); ?></span>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium <?php echo ($item->keterangan_masuk === 'tepat waktu') ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                    <?php echo ucfirst($item->keterangan_masuk); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                            <i class="fas fa-calendar-times text-3xl mb-2"></i>
                            <p>Belum ada riwayat masuk</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 flex flex-col">
                    <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100">
                            <i class="fas fa-arrow-right-from-bracket text-green-500 text-2xl"></i>
                        </span>
                        Riwayat Pulang
                    </h3>
                    <div class="space-y-4 max-h-80 overflow-y-auto">
                        <?php
                        $pulangList = array_filter($riwayat_pulang, function($item) {
                            return !empty($item->jam_pulang);
                        });
                        $pulangList = array_slice($pulangList, 0, 5);
                        ?>
                        <?php if (count($pulangList) > 0): ?>
                        <?php foreach ($pulangList as $item): ?>
                        <div class="p-4 rounded-xl flex flex-col gap-2 hover:shadow transition">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-lg text-gray-700">
                                    <?php echo date('H:i', strtotime($item->jam_pulang)); ?>
                                </span>
                                <span class="flex items-center gap-1 text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $item->nama_lokasi_pulang; ?>
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-sm text-gray-500">
                                <span><?php echo date('d M Y', strtotime($item->jam_pulang)); ?></span>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium <?php echo ($item->keterangan_pulang === 'tepat waktu') ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'; ?>">
                                    <?php echo ucfirst($item->keterangan_pulang); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                            <i class="fas fa-calendar-times text-3xl mb-2"></i>
                            <p>Belum ada riwayat pulang</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="uploadOverlay"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg shadow-xl text-center max-w-md">
                    <p class="text-lg font-semibold">Mengupload gambar...</p>
                    <div class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full animate-pulse"></div>
                    </div>
                </div>
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
                <i class="fas fa-user text-lg text-gray-600 group-hover:text-blue-700"></i>
            </span>
            <span class="text-xs">Profil</span>
        </a>
    </nav>

    <script>
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

    function updateClock() {
        const now = new Date();
        document.getElementById('currentTime').innerText = now.toLocaleTimeString('id-ID');
        document.getElementById('currentDate').innerText = now.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    setInterval(updateClock, 1000);
    updateClock();

    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const buttonGroup = document.getElementById('buttonGroup');
    const cameraLoadingDiv = document.getElementById('cameraLoading');

    let stream = null;
    let gpsWatchId = null;

    // ========== FUNGSI GPS AKURASI TINGGI ==========

    async function getHighAccuracyLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Perangkat tidak mendukung geolokasi.'));
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            };

            let bestPosition = null;
            let attempts = 0;
            const maxAttempts = 3;

            const tryGetPosition = () => {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        attempts++;

                        if (!bestPosition || position.coords.accuracy < bestPosition.coords
                            .accuracy) {
                            bestPosition = position;
                        }

                        if (bestPosition.coords.accuracy < 20 || attempts >= maxAttempts) {
                            resolve(bestPosition);
                        } else {
                            setTimeout(tryGetPosition, 1000);
                        }
                    },
                    (error) => {
                        if (attempts === 0) {
                            reject(error);
                        } else if (bestPosition) {
                            resolve(bestPosition);
                        } else {
                            reject(error);
                        }
                    },
                    options
                );
            };

            tryGetPosition();
        });
    }

    function watchLocation(callback) {
        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                callback({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: position.timestamp
                });
            },
            (error) => {
                console.error('Error watching position:', error);
            },
            options
        );

        return watchId;
    }

    function displayGPSInfo() {
        const gpsInfoDiv = document.getElementById('gpsInfo');
        gpsInfoDiv.classList.remove('hidden');

        if (gpsWatchId) {
            navigator.geolocation.clearWatch(gpsWatchId);
        }

        gpsWatchId = watchLocation((location) => {
            const accuracyClass = location.accuracy < 20 ? 'text-green-600' :
                location.accuracy < 50 ? 'text-yellow-600' : 'text-red-600';

            const statusText = location.accuracy < 20 ? 'Sangat Baik ✓' :
                location.accuracy < 50 ? 'Baik' : 'Kurang Baik';

            gpsInfoDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas fa-satellite-dish ${accuracyClass}"></i>
                        <span class="font-semibold ml-2">Status GPS:</span>
                    </div>
                    <div class="text-right">
                        <div class="${accuracyClass} font-bold">
                            Akurasi: ${Math.round(location.accuracy)}m
                        </div>
                        <div class="text-xs text-gray-500">
                            ${statusText}
                        </div>
                    </div>
                </div>
            `;
        });
    }

    function showGPSTips() {
        Swal.fire({
            title: 'Tips Meningkatkan Akurasi GPS',
            html: `
                <div class="text-left space-y-3">
                    <p class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <span>Pastikan <b>GPS/Lokasi aktif</b> di perangkat Anda</span>
                    </p>
                    <p class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <span>Berada di <b>area terbuka</b>, hindari gedung tinggi</span>
                    </p>
                    <p class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <span>Pastikan <b>koneksi internet stabil</b></span>
                    </p>
                    <p class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <span><b>Tunggu 5-10 detik</b> sebelum ambil foto untuk GPS lock</span>
                    </p>
                    <p class="flex items-start gap-2">
                        <i class="fas fa-star text-yellow-500 mt-1"></i>
                        <span>Akurasi terbaik: <b>&lt; 20 meter</b></span>
                    </p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#3b82f6'
        });
    }

    async function startCamera() {
        try {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }

            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    },
                    facingMode: {
                        ideal: "user"
                    }
                },
                audio: false
            });

            video.srcObject = stream;
            video.classList.remove("hidden");
            cameraLoadingDiv.classList.add("hidden");

            video.onloadedmetadata = () => {
                video.play();
            };
        } catch (err) {
            console.error("Error mengakses kamera:", err);
            cameraLoadingDiv.innerHTML = `
                <span class="text-red-500 font-bold">Tidak dapat mengakses kamera</span>
                <button id="retryCamera" class="mt-2 px-4 py-2 bg-red-500 text-white rounded-md">
                  Coba Lagi
                </button>
            `;
            document.getElementById("retryCamera").addEventListener("click", startCamera);
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        async function requestLocationPermission() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('Perangkat tidak mendukung geolokasi.'));
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (position) => resolve(position),
                    (error) => reject(error), {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });
        }

        try {
            await requestLocationPermission();
            const isComplete =
                <?php echo ($absensi_today && $absensi_today->jam_masuk && $absensi_today->jam_pulang) ? 'true' : 'false'; ?>;
            if (!isComplete) {
                startCamera();
                displayGPSInfo();
            } else {
                stopCamera();
                if (document.getElementById('video')) {
                    document.getElementById('video').classList.add('hidden');
                }
                if (document.getElementById('canvas')) {
                    document.getElementById('canvas').classList.add('hidden');
                }
                if (document.getElementById('cameraLoading')) {
                    document.getElementById('cameraLoading').classList.add('hidden');
                }
            }
        } catch (err) {
            Swal.fire({
                title: 'Izin Lokasi Diperlukan',
                text: 'Anda harus mengizinkan akses lokasi untuk melakukan absensi.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            if (document.getElementById('buttonGroup')) {
                document.getElementById('buttonGroup').innerHTML = `
                <div class="w-full text-center text-red-500 py-3">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>Izin lokasi belum diberikan. Silakan aktifkan lokasi pada perangkat Anda.</p>
                </div>
            `;
            }
            if (document.getElementById('cameraLoading')) {
                document.getElementById('cameraLoading').innerHTML = `
                <span class="text-red-500 font-bold">Izin lokasi belum diberikan</span>
            `;
            }
        }
    });

    if (captureBtn) {
        captureBtn.addEventListener('click', captureImage);
    }

    function captureImage() {
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.classList.remove('hidden');
        video.classList.add('hidden');

        stopCamera();

        if (gpsWatchId) {
            navigator.geolocation.clearWatch(gpsWatchId);
            gpsWatchId = null;
        }

        const isAbsenMasuk = <?php echo (!$absensi_today || !$absensi_today->jam_masuk) ? 'true' : 'false'; ?>;
        const buttonText = isAbsenMasuk ? 'Kirim Absen Masuk' : 'Kirim Absen Pulang';

        buttonGroup.innerHTML = `
        <button id="resetBtn" class="flex-1 bg-gray-500 text-white py-3 rounded-xl font-semibold hover:bg-gray-600">Ulangi</button>
        <button id="submitBtn" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white py-3 rounded-xl font-semibold hover:from-green-600 hover:to-green-700">${buttonText}</button>
      `;

        document.getElementById('resetBtn').addEventListener('click', resetCamera);
        document.getElementById('submitBtn').addEventListener('click', handleSubmitWithAccurateGPS);
    }

    async function resetCamera() {
        canvas.classList.add('hidden');
        video.classList.remove('hidden');
        cameraLoadingDiv.classList.remove('hidden');
        await startCamera();
        displayGPSInfo();

        const isAbsenMasuk = <?php echo (!$absensi_today || !$absensi_today->jam_masuk) ? 'true' : 'false'; ?>;
        const buttonText = isAbsenMasuk ? 'Ambil Foto - Absen Masuk' : 'Ambil Foto - Absen Pulang';

        buttonGroup.innerHTML = `
        <button id="captureBtn" class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-3 rounded-xl font-semibold hover:from-red-600 hover:to-red-700">
          ${buttonText}
        </button>
      `;

        document.getElementById('captureBtn').addEventListener('click', captureImage);
    }

    async function handleSubmitWithAccurateGPS() {
        const uploadOverlay = document.getElementById('uploadOverlay');
        uploadOverlay.classList.remove('hidden');

        const loadingDiv = uploadOverlay.querySelector('div');
        loadingDiv.innerHTML = `
            <p class="text-lg font-semibold">Mendapatkan lokasi akurat...</p>
            <div class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full animate-pulse"></div>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                <i class="fas fa-satellite-dish animate-pulse"></i> 
                Mohon tunggu, sedang mencari sinyal GPS terbaik
            </p>
        `;

        try {
            const position = await getHighAccuracyLocation();

            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            loadingDiv.innerHTML = `
                <p class="text-lg font-semibold">Mengupload absensi...</p>
                <p class="text-sm text-green-600 mt-2">
                    <i class="fas fa-check-circle"></i> 
                    Lokasi didapat (Akurasi: ${Math.round(accuracy)}m)
                </p>
                <div class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full animate-pulse"></div>
                </div>
            `;

            if (accuracy > 50) {
                uploadOverlay.classList.add('hidden');

                const confirm = await Swal.fire({
                    title: 'Akurasi GPS Rendah',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-3"></i>
                            <p class="mb-2">Akurasi lokasi Anda saat ini <b class="text-red-600">${Math.round(accuracy)} meter</b>.</p>
                            <p class="text-sm text-gray-600">Untuk hasil terbaik, disarankan akurasi < 20 meter.</p>
                            <br>
                            <p class="font-semibold">Apakah Anda tetap ingin melanjutkan?</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Coba Lagi',
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#6b7280'
                });

                if (!confirm.isConfirmed) {
                    resetCamera();
                    return;
                }

                uploadOverlay.classList.remove('hidden');
            }

            const imageData = canvas.toDataURL('image/jpeg', 0.8);

            const formData = new FormData();
            formData.append('image_data', imageData);
            formData.append('latitude', latitude);
            formData.append('longitude', longitude);
            formData.append('accuracy', accuracy);
            formData.append('timestamp', position.timestamp);

            const response = await fetch('<?php echo base_url("user/submit_absensi"); ?>', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                Swal.fire({
                    title: 'Berhasil!',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-check-circle text-green-500 text-5xl mb-3"></i>
                            <p class="text-lg mb-2">${result.message}</p>
                            <div class="bg-gray-100 rounded-lg p-3 mt-3">
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt text-blue-500"></i> 
                                    Akurasi GPS: <b>${Math.round(accuracy)}m</b>
                                </p>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(result.message || 'Gagal mengirim absensi');
            }

        } catch (error) {
            console.error('Error:', error);
            let errorMessage = 'Gagal mengirim absensi: ' + error.message;

            if (error.code === 1) {
                errorMessage = 'Akses lokasi ditolak. Mohon izinkan akses lokasi pada browser Anda.';
            } else if (error.code === 2) {
                errorMessage = 'Lokasi tidak tersedia. Pastikan GPS Anda aktif dan berada di area terbuka.';
            } else if (error.code === 3) {
                errorMessage =
                    'Timeout mendapatkan lokasi. Pastikan Anda berada di area dengan sinyal GPS yang baik.';
            }

            Swal.fire({
                title: 'Error!',
                html: `
                    <div class="text-center">
                        <i class="fas fa-times-circle text-red-500 text-4xl mb-3"></i>
                        <p>${errorMessage}</p>
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg text-left text-sm">
                            <p class="font-semibold mb-2">Tips:</p>
                            <p>• Pastikan GPS aktif</p>
                            <p>• Berada di area terbuka</p>
                            <p>• Koneksi internet stabil</p>
                        </div>
                    </div>
                `,
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#ef4444'
            });
            resetCamera();
        } finally {
            uploadOverlay.classList.add('hidden');
        }
    }

    window.addEventListener('beforeunload', () => {
        stopCamera();
        if (gpsWatchId) {
            navigator.geolocation.clearWatch(gpsWatchId);
        }
    });
    </script>
</body>

</html>