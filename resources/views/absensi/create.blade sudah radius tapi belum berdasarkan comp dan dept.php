<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Absensi') }}
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet (untuk menampilkan peta radius absen) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-info mb-4 text-blue-600">
                        Nyalakan izin kamera dan lokasi untuk melakukan absensi
                    </div>

                    <form id="attendanceForm" onsubmit="return false;">
                        <!-- Data User -->
                        <input type="hidden" id="userPlant" value="{{ session('plant') ?? '' }}">
                        <input type="hidden" id="userDept" value="{{ session('dept') ?? '' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK</label>
                                <input type="text" id="PersonnelNo" value="{{ session('nik') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100" readonly disabled>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama</label>
                                <input type="text" value="{{ session('username') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100" readonly disabled>
                            </div>
                        </div>

                        <!-- Pilih Check Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Jenis Absensi</label>
                            <select id="CheckType" required onchange="onCheckTypeChange()"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Pilih Jenis Absensi</option>
                                <option value="IN">Masuk</option>
                                <option value="OUT">Pulang</option>
                            </select>
                        </div>

                        <!-- Preview Lokasi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Latitude</label>
                                <input type="text" id="latitude_preview"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Longitude</label>
                                <input type="text" id="longitude_preview"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100" readonly>
                            </div>
                        </div>

                        <!-- Geofence / Radius Section (hanya tampil & wajib untuk CheckType = IN) -->
                        <div id="geofenceSection" class="mb-4 hidden">
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Validasi Lokasi Absen Masuk
                                    </label>
                                    <span id="radiusBadge"
                                        class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">
                                        Mengecek lokasi...
                                    </span>
                                </div>

                                <div id="geofenceMap" style="height: 260px; border-radius: 8px;" class="border border-gray-300"></div>

                                <div class="mt-3 flex flex-col sm:flex-row sm:justify-between text-sm text-gray-600 gap-1">
                                    <span>Lokasi kantor: <strong id="officeNameLabel"></strong></span>
                                    <span>Jarak Anda: <strong id="distanceLabel">-</strong></span>
                                </div>

                                <div id="geofenceWarning" class="hidden mt-3 text-sm text-red-600 font-medium">
                                    Anda berada di luar radius absen masuk. Tombol submit tetap bisa diklik,
                                    namun sistem akan menolak absen masuk jika Anda masih di luar radius.
                                </div>
                            </div>
                        </div>

                        <!-- Camera Section -->
                        <div class="mt-6 p-4 border rounded-lg bg-gray-50">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Selfie <span class="text-red-600">*</span>
                            </label>

                            <div class="flex flex-col items-center">
                                <div class="relative" style="width: 400px; height: 300px;">
                                    <video id="video" autoplay playsinline muted
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                        class="border-2 border-gray-300 rounded-lg shadow-sm"></video>

                                    <canvas id="canvas"
                                        style="display: none; width: 100%; height: 100%;"
                                        class="border-2 border-gray-300 rounded-lg shadow-sm"></canvas>

                                    <img id="preview" src="" alt="Preview"
                                        style="display: none; width: 100%; height: 100%; object-fit: cover;"
                                        class="border-2 border-gray-300 rounded-lg shadow-sm">
                                </div>

                                <div class="mt-4">
                                    <button type="button" id="captureBtn"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Ambil Foto
                                    </button>
                                </div>

                                <div id="cameraStatus" class="mt-2 text-sm text-gray-600"></div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button type="button" id="submitBtn" disabled
                                onclick="submitAttendance()"
                                class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                                Submit Absensi
                            </button>
                            <p id="submitHint" class="mt-2 text-xs text-gray-500 text-center"></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // CEK APAKAH ATURAN RADIUS BERLAKU
        // ============================================================
        function isRadiusRuleApplicable() {
            // Ambil data dari session yang sudah di-pass ke JavaScript
            const plant = document.getElementById('userPlant')?.value || '';
            const dept = document.getElementById('userDept')?.value || '';
            
            // Aturan: berlaku jika plant = 1000 DAN dept BUKAN MERCHANDISER DAN BUKAN SALES JATENG
            if (plant === '1000' && dept !== 'MERCHANDISER' && dept !== 'SALES JATENG') {
                return true;
            }
            return false;
        }

        // ============================================================
        // KONFIGURASI GEOFENCE (RADIUS ABSEN MASUK)
        // ============================================================
        const OFFICE_LOCATION = {
            name: 'Voza Tower',
            lat: -7.282900,   // <-- GANTI dengan latitude presisi
            lng: 112.700500,  // <-- GANTI dengan longitude presisi
            radiusMeters: 50  // Radius absen masuk dalam meter
        };
        
        // const OFFICE_LOCATION = {
        //     name: 'Kim Liong',
        //     lat: -7.564936,  // <-- GANTI dengan latitude presisi
        //     lng: 112.614377,  // <-- GANTI dengan longitude presisi
        //     radiusMeters: 50  // Radius absen masuk dalam meter
        // };

        document.getElementById('officeNameLabel').textContent = OFFICE_LOCATION.name;

        // ============================================================
        // API URL
        // ============================================================
        const API_SUBMIT_URL = 'https://web.kobin.co.id/api/hris/test/absensi/post_absensi.php';

        // State management
        let videoStream = null;
        let videoElement = null;
        let canvasElement = null;
        let previewElement = null;
        let isPhotoTaken = false;
        let capturedPhotoData = null;
        let currentLocation = {
            latitude: null,
            longitude: null
        };
        let currentDistanceMeters = null;
        let isWithinRadius = false;

        // Leaflet map state
        let geofenceMap = null;
        let officeMarker = null;
        let officeCircle = null;
        let userMarker = null;

        // DOM Elements
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const preview = document.getElementById('preview');
        const captureBtn = document.getElementById('captureBtn');
        const submitBtn = document.getElementById('submitBtn');
        const submitHint = document.getElementById('submitHint');
        const cameraStatus = document.getElementById('cameraStatus');
        const latitudePreview = document.getElementById('latitude_preview');
        const longitudePreview = document.getElementById('longitude_preview');
        const checkTypeSelect = document.getElementById('CheckType');
        const geofenceSection = document.getElementById('geofenceSection');
        const radiusBadge = document.getElementById('radiusBadge');
        const distanceLabel = document.getElementById('distanceLabel');
        const geofenceWarning = document.getElementById('geofenceWarning');

        // ============================================================
        // Hitung jarak antar 2 koordinat (Haversine formula) -> meter
        // ============================================================
        function calculateDistanceMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000; // radius bumi dalam meter
            const toRad = (deg) => deg * (Math.PI / 180);

            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);

            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // ============================================================
        // Tampilkan / sembunyikan section geofence berdasar CheckType
        // ============================================================
        function onCheckTypeChange() {
            const checkType = checkTypeSelect.value;

            if (checkType === 'IN') {
                geofenceSection.classList.remove('hidden');
                setTimeout(() => {
                    initOrUpdateMap();
                    if (geofenceMap) geofenceMap.invalidateSize();
                }, 50);
            } else {
                geofenceSection.classList.add('hidden');
            }

            updateSubmitButtonState();
        }

        // ============================================================
        // Inisialisasi / update peta Leaflet
        // ============================================================
        function initOrUpdateMap() {
            if (!geofenceMap) {
                geofenceMap = L.map('geofenceMap').setView([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], 17);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(geofenceMap);

                officeMarker = L.marker([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], {
                    title: OFFICE_LOCATION.name
                }).addTo(geofenceMap).bindPopup(OFFICE_LOCATION.name);

                officeCircle = L.circle([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], {
                    radius: OFFICE_LOCATION.radiusMeters,
                    color: '#ef4444',
                    fillColor: '#ef4444',
                    fillOpacity: 0.15,
                    weight: 2
                }).addTo(geofenceMap);
            }

            updateUserMarker();
        }

        function updateUserMarker() {
            if (!geofenceMap || currentLocation.latitude === null) return;

            const userLatLng = [currentLocation.latitude, currentLocation.longitude];

            if (!userMarker) {
                const userIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:14px;height:14px;border-radius:50%;background:#3b82f6;border:3px solid white;box-shadow:0 0 4px rgba(0,0,0,0.5);"></div>',
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });
                userMarker = L.marker(userLatLng, { icon: userIcon, title: 'Lokasi Anda' })
                    .addTo(geofenceMap)
                    .bindPopup('Lokasi Anda');
            } else {
                userMarker.setLatLng(userLatLng);
            }

            const bounds = L.latLngBounds([
                [OFFICE_LOCATION.lat, OFFICE_LOCATION.lng],
                userLatLng
            ]);
            geofenceMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 18 });
        }

        // ============================================================
        // Update badge status radius + label jarak
        // ============================================================
        function updateRadiusStatus() {
            if (currentLocation.latitude === null) return;

            currentDistanceMeters = calculateDistanceMeters(
                currentLocation.latitude,
                currentLocation.longitude,
                OFFICE_LOCATION.lat,
                OFFICE_LOCATION.lng
            );

            isWithinRadius = currentDistanceMeters <= OFFICE_LOCATION.radiusMeters;

            distanceLabel.textContent = `${Math.round(currentDistanceMeters)} meter`;

            if (isWithinRadius) {
                radiusBadge.textContent = '✅ Dalam Radius';
                radiusBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700';
                geofenceWarning.classList.add('hidden');
            } else {
                radiusBadge.textContent = '❌ Luar Radius';
                radiusBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700';
                geofenceWarning.classList.remove('hidden');
            }

            updateSubmitButtonState();
        }

        // ============================================================
        // Kontrol tombol submit
        // ------------------------------------------------------------
        // PERUBAHAN PENTING:
        // Sebelumnya tombol di-disable total saat checkType = IN dan
        // posisi di luar radius (isWithinRadius = false). Akibatnya
        // klik tidak pernah memicu onclick="submitAttendance()", jadi
        // tidak ada log di console maupun SweetAlert yang muncul.
        //
        // Sekarang tombol hanya butuh foto + lokasi terdeteksi supaya
        // BISA DIKLIK. Validasi radius dipindahkan sepenuhnya ke dalam
        // fungsi submitAttendance(), sehingga saat user di luar radius
        // dan menekan submit, SweetAlert peringatan jarak akan tampil
        // dengan jelas (dan log tetap muncul di console).
        // ============================================================
        function updateSubmitButtonState() {
            const hasPhoto = isPhotoTaken;
            const hasLocation = currentLocation.latitude !== null;
            const checkType = checkTypeSelect.value;

            const allowed = hasPhoto && hasLocation && checkType !== '';
            submitBtn.disabled = !allowed;

            // Hint kecil di bawah tombol supaya user tahu kenapa belum bisa submit
            if (!checkType) {
                submitHint.textContent = 'Pilih jenis absensi terlebih dahulu.';
            } else if (!hasLocation) {
                submitHint.textContent = 'Menunggu lokasi GPS terdeteksi...';
            } else if (!hasPhoto) {
                submitHint.textContent = 'Ambil foto selfie terlebih dahulu.';
            } else if (checkType === 'IN' && !isWithinRadius) {
                submitHint.textContent = 'Anda di luar radius kantor — submit tetap bisa diklik, tapi absen masuk akan ditolak sistem.';
                submitHint.className = 'mt-2 text-xs text-red-600 text-center font-medium';
            } else {
                submitHint.textContent = '';
                submitHint.className = 'mt-2 text-xs text-gray-500 text-center';
            }
        }

        // Initialize camera
        async function initCamera() {
            try {
                videoElement = video;
                canvasElement = canvas;
                previewElement = preview;

                updateStatus('Mengakses kamera...', 'info');

                videoStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    }
                });

                videoElement.srcObject = videoStream;

                await new Promise((resolve) => {
                    videoElement.onloadedmetadata = () => {
                        videoElement.play().then(resolve);
                    };
                });

                updateStatus('Kamera siap!', 'success');
                captureBtn.disabled = false;

            } catch (error) {
                updateStatus('Gagal akses kamera: ' + error.message, 'error');
                captureBtn.disabled = true;

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Akses Kamera',
                    text: error.message + '\n\nPastikan Anda telah mengizinkan akses kamera.',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Capture photo
        function capturePhoto() {
            if (isPhotoTaken) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Foto Sudah Diambil',
                    text: 'Anda sudah mengambil foto. Silakan submit absensi atau refresh halaman untuk mengambil ulang.',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
                return;
            }

            captureBtn.disabled = true;
            updateStatus('Mengambil foto...', 'info');

            try {
                const videoWidth = videoElement.videoWidth;
                const videoHeight = videoElement.videoHeight;

                canvasElement.width = videoWidth;
                canvasElement.height = videoHeight;

                setTimeout(() => {
                    const ctx = canvasElement.getContext('2d');
                    ctx.drawImage(videoElement, 0, 0, videoWidth, videoHeight);

                    const photoDataUrl = canvasElement.toDataURL('image/jpeg', 0.85);

                    videoElement.style.display = 'none';
                    previewElement.src = photoDataUrl;
                    previewElement.style.display = 'block';

                    captureBtn.style.display = 'none';

                    isPhotoTaken = true;
                    capturedPhotoData = photoDataUrl;

                    updateSubmitButtonState();

                    updateStatus('Foto berhasil! Silakan submit', 'success');

                    Swal.close();

                }, 100);

            } catch (error) {
                Swal.close();
                updateStatus('Gagal: ' + error.message, 'error');
                captureBtn.disabled = false;

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengambil Foto',
                    text: error.message,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Get location
        function requestLocation() {
            if (!navigator.geolocation) {
                showAlert('error', 'Browser Anda tidak mendukung geolokasi. Silakan gunakan browser modern.', 'Geolokasi Tidak Didukung');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    Swal.close();

                    currentLocation.latitude = position.coords.latitude;
                    currentLocation.longitude = position.coords.longitude;

                    latitudePreview.value = currentLocation.latitude;
                    longitudePreview.value = currentLocation.longitude;

                    updateRadiusStatus();

                    if (checkTypeSelect.value === 'IN') {
                        initOrUpdateMap();
                    }

                    updateSubmitButtonState();
                },
                function(error) {
                    Swal.close();

                    let errorMessage = '';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Izin lokasi ditolak. Silakan izinkan akses lokasi di pengaturan browser.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Informasi lokasi tidak tersedia. Pastikan GPS aktif.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Waktu permintaan lokasi habis. Silakan coba lagi.';
                            break;
                        default:
                            errorMessage = error.message;
                    }

                    showAlert('error', errorMessage, 'Gagal Mendapatkan Lokasi');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        // Submit to API
        async function submitAttendance() {
            console.log('🟢 submitAttendance() dipanggil');

            // Validasi foto
            if (!isPhotoTaken || !capturedPhotoData) {
                showAlert('error', 'Silakan ambil foto terlebih dahulu', 'Foto Belum Diambil');
                return;
            }

            // Validasi lokasi
            if (!currentLocation.latitude || !currentLocation.longitude) {
                showAlert('error', 'Lokasi belum terdeteksi. Pastikan GPS aktif.', 'Lokasi Tidak Ditemukan');
                return;
            }

            const personnelNo = document.getElementById('PersonnelNo').value;
            const checkType = checkTypeSelect.value;

            if (!personnelNo || !checkType) {
                showAlert('error', 'Data tidak lengkap. Silakan pilih jenis absensi.', 'Data Tidak Lengkap');
                return;
            }

            // Validasi radius KHUSUS untuk absen Masuk (IN)
            // Ini sekarang benar-benar tereksekusi karena tombol tidak lagi
            // di-disable berdasarkan radius.
            if (checkType === 'IN') {
                updateRadiusStatus(); // hitung ulang jarak terbaru sebelum submit

                console.log('📍 Cek radius -> jarak:', Math.round(currentDistanceMeters), 'meter, dalam radius:', isWithinRadius);

                if (!isWithinRadius) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Di Luar Radius Absen Masuk',
                        html: `
                            <div class="text-left">
                                <p>Anda berada <strong>${Math.round(currentDistanceMeters)} meter</strong> dari ${OFFICE_LOCATION.name}.</p>
                                <p>Absen masuk hanya dapat dilakukan dalam radius <strong>${OFFICE_LOCATION.radiusMeters} meter</strong> dari lokasi kantor.</p>
                            </div>
                        `,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }

            // Disable button selama proses submit
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            showLoadingAlert();

            try {
                const localTime = getLocalDeviceTime();

                const data = {
                    PersonnelNo: personnelNo,
                    CheckType: checkType,
                    Latitude: currentLocation.latitude.toString(),
                    Longitude: currentLocation.longitude.toString(),
                    LocalDateTime: localTime.datetime,
                    LocalTimezone: localTime.timezone,
                    UTCTimestamp: localTime.timestamp,
                    photoData: capturedPhotoData,
                    IP: '{{ request()->ip() }}'
                };

                console.log('📤 Submitting data to backend PHP:', data);

                const response = await fetch(API_SUBMIT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                console.log('📥 Response from PHP:', result);

                if (result.success) {
                    closeLoadingAlert();

                    console.log('📤 Saving photo to Laravel storage...');

                    const photoData = {
                        photoData: capturedPhotoData,
                        PersonnelNo: personnelNo,
                        datetime: localTime.datetime
                    };

                    const laravelResponse = await fetch('/absensi/save-photo', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(photoData)
                    });

                    const laravelResult = await laravelResponse.json();

                    if (laravelResult.success) {
                        console.log('✅ Foto berhasil disimpan di:', laravelResult.data.file_path);
                    } else {
                        console.warn('⚠️ Gagal simpan foto di storage:', laravelResult.message);
                    }

                    const checkTypeName = checkType === 'IN' ? 'Masuk' : 'Pulang';
                    Swal.fire({
                        icon: 'success',
                        title: 'Absensi Berhasil!',
                        html: `
                            <div class="text-left">
                                <p><strong>Jenis:</strong> ${checkTypeName}</p>
                                <p><strong>Waktu:</strong> ${localTime.datetime}</p>
                                <p><strong>Lokasi:</strong> ${currentLocation.latitude}, ${currentLocation.longitude}</p>
                                ${checkType === 'IN' ? `<p><strong>Jarak dari kantor:</strong> ${Math.round(currentDistanceMeters)} meter</p>` : ''}
                            </div>
                        `,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true
                    });

                    stopCamera();

                    setTimeout(() => {
                        window.location.href = '{{ route("absensi.index") }}';
                    }, 2000);
                } else {
                    throw new Error(result.message || 'Gagal submit absensi');
                }

            } catch (error) {
                console.error('❌ Error:', error);
                closeLoadingAlert();
                showAlert('error', error.message, 'Submit Gagal');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit Absensi';
            }
        }

        function getLocalDeviceTime() {
            const now = new Date();

            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const timezoneOffset = now.getTimezoneOffset();
            const timezoneOffsetHours = Math.abs(Math.floor(timezoneOffset / 60));
            const timezoneOffsetMinutes = Math.abs(timezoneOffset % 60);
            const timezoneSign = timezoneOffset <= 0 ? '+' : '-';
            const timezoneStr = `UTC${timezoneSign}${String(timezoneOffsetHours).padStart(2, '0')}:${String(timezoneOffsetMinutes).padStart(2, '0')}`;

            return {
                datetime: `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`,
                timezone: timezoneStr,
                timezoneOffset: timezoneOffset,
                timestamp: now.getTime()
            };
        }

        // Stop camera
        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }
        }

        // Update status
        function updateStatus(message, type) {
            cameraStatus.textContent = message;
            cameraStatus.className = `mt-2 text-sm font-medium ${
                type === 'success' ? 'text-green-600' :
                type === 'error' ? 'text-red-600' : 'text-blue-600'
            }`;
        }

        // Show alert
        function showAlert(type, message, title = null) {
            if (type === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: title || 'Berhasil!',
                    text: message,
                    timer: 2000,
                    showConfirmButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            } else if (type === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: title || 'Gagal!',
                    text: message,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            } else if (type === 'warning') {
                Swal.fire({
                    icon: 'warning',
                    title: title || 'Peringatan!',
                    text: message,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            } else if (type === 'info') {
                Swal.fire({
                    icon: 'info',
                    title: title || 'Informasi',
                    text: message,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            }
        }

        function showLoadingAlert(message = 'Memproses absensi...') {
            Swal.fire({
                title: 'Loading...',
                text: message,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function closeLoadingAlert() {
            Swal.close();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            requestLocation();
            captureBtn.disabled = true;
            captureBtn.addEventListener('click', capturePhoto);
            setTimeout(initCamera, 300);
            updateSubmitButtonState();
        });

        // Cleanup
        window.addEventListener('beforeunload', stopCamera);
    </script>

    <style>
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</x-app-layout>