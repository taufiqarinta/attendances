<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Absensi') }}
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.tailwindcss.com"></script>

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
                            <select id="CheckType" required 
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
                                class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-4 rounded">
                                Submit Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // API URL
        const API_SUBMIT_URL = 'https://web.kobin.co.id/api/hris/absensi/post_absensi.php';
        
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

        // DOM Elements
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const preview = document.getElementById('preview');
        const captureBtn = document.getElementById('captureBtn');
        const submitBtn = document.getElementById('submitBtn');
        const cameraStatus = document.getElementById('cameraStatus');
        const latitudePreview = document.getElementById('latitude_preview');
        const longitudePreview = document.getElementById('longitude_preview');
        const alertSuccess = document.getElementById('alertSuccess');
        const alertError = document.getElementById('alertError');

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
                
                // Tampilkan notifikasi kamera siap
                Swal.fire({
                    icon: 'success',
                    title: 'Kamera Siap!',
                    text: 'Silakan ambil foto selfie Anda',
                    timer: 2500,
                    showConfirmButton: false
                });

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
            
            // Tampilkan loading sebentar
            Swal.fire({
                title: 'Mengambil Foto...',
                text: 'Harap tunggu',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

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
                    
                    submitBtn.disabled = false;
                    
                    updateStatus('Foto berhasil! Silakan submit', 'success');
                    
                    // Tutup loading dan tampilkan success
                    Swal.close();
                    Swal.fire({
                        icon: 'success',
                        title: 'Foto Berhasil!',
                        text: 'Silakan klik tombol Submit Absensi',
                        timer: 2500,
                        showConfirmButton: false
                    });
            
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

            // Show loading location
            Swal.fire({
                title: 'Mendapatkan Lokasi...',
                text: 'Mohon izinkan akses lokasi untuk melanjutkan absensi',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Close loading
                    Swal.close();
                    
                    currentLocation.latitude = position.coords.latitude;
                    currentLocation.longitude = position.coords.longitude;
                    
                    latitudePreview.value = currentLocation.latitude;
                    longitudePreview.value = currentLocation.longitude;
                    
                    // Optional: Tampilkan konfirmasi lokasi
                    Swal.fire({
                        icon: 'success',
                        title: 'Lokasi Terdeteksi!',
                        html: `Latitude: ${currentLocation.latitude}<br>Longitude: ${currentLocation.longitude}`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                function(error) {
                    // Close loading
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
            // Validasi
            if (!isPhotoTaken || !capturedPhotoData) {
                showAlert('error', 'Silakan ambil foto terlebih dahulu', 'Foto Belum Diambil');
                return;
            }

            if (!currentLocation.latitude || !currentLocation.longitude) {
                showAlert('error', 'Lokasi belum terdeteksi. Pastikan GPS aktif.', 'Lokasi Tidak Ditemukan');
                return;
            }

            const personnelNo = document.getElementById('PersonnelNo').value;
            const checkType = document.getElementById('CheckType').value;

            if (!personnelNo || !checkType) {
                showAlert('error', 'Data tidak lengkap. Silakan pilih jenis absensi.', 'Data Tidak Lengkap');
                return;
            }

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            // Show loading
            showLoadingAlert();

            try {
                // Prepare data
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

                // 1. Send to PHP Backend API
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
                    // Close loading
                    closeLoadingAlert();
                    
                    // 2. Jika sukses, kirim foto ke Laravel untuk disimpan di storage
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

                    // Tampilkan notifikasi sukses dengan SweetAlert2
                    const checkTypeName = checkType === 'IN' ? 'Masuk' : 'Pulang';
                    Swal.fire({
                        icon: 'success',
                        title: 'Absensi Berhasil!',
                        html: `
                            <div class="text-left">
                                <p><strong>Jenis:</strong> ${checkTypeName}</p>
                                <p><strong>Waktu:</strong> ${localTime.datetime}</p>
                                <p><strong>Lokasi:</strong> ${currentLocation.latitude}, ${currentLocation.longitude}</p>
                            </div>
                        `,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    // Stop camera
                    stopCamera();
                    
                    // Redirect ke halaman absensi setelah 2 detik
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
            
            // Ambil waktu lokal dari perangkat (otomatis mengikuti timezone perangkat)
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            // Dapatkan informasi timezone
            const timezoneOffset = now.getTimezoneOffset();
            const timezoneOffsetHours = Math.abs(Math.floor(timezoneOffset / 60));
            const timezoneOffsetMinutes = Math.abs(timezoneOffset % 60);
            const timezoneSign = timezoneOffset <= 0 ? '+' : '-'; // Perhatikan: getTimezoneOffset mengembalikan menit *lawan* dari UTC
            const timezoneStr = `UTC${timezoneSign}${String(timezoneOffsetHours).padStart(2, '0')}:${String(timezoneOffsetMinutes).padStart(2, '0')}`;
            
            return {
                datetime: `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`,
                timezone: timezoneStr,
                timezoneOffset: timezoneOffset,
                timestamp: now.getTime() // Unix timestamp dalam milliseconds (UTC)
            };
        }

        // Contoh penggunaan:
        const localTime = getLocalDeviceTime();
        console.log('Waktu lokal:', localTime.datetime);
        console.log('Timezone:', localTime.timezone);
        console.log('UTC Offset:', localTime.timezoneOffset, 'menit');

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

        // Fungsi untuk loading indicator
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