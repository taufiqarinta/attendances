<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Loading Spinner CSS -->
    <style>
        .loading-spinner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #dc2626;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
    </div>

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: '{{ $errors->first() }}',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Coba Lagi',
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    <form id="loginForm" method="POST">
        @csrf

        <!-- NIK Input -->
        <div style="position: relative; margin-top: 15px;">
            <span style="
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                pointer-events: none;">
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    fill="none"
                    stroke="#dc2626"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </span>

            <input
                type="text"
                id="nik"
                name="nik"
                placeholder="NIK"
                value="{{ old('nik') }}"
                required
                style="
                    width: 100%;
                    padding: 12px 12px 12px 40px;
                    border-radius: 8px;
                    border: 1px solid #ccc;
                    outline: none;
                    box-sizing: border-box;
                    color:red;
                ">
        </div>

        <!-- Password Input -->
        <div style="position: relative; margin-top: 15px;">
            <span style="
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                pointer-events: none;">
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    fill="none"
                    stroke="#dc2626"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="10" rx="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
            </span>

            <input
                id="password"
                type="password"
                name="password"
                placeholder="Password"
                required
                style="
                    width: 100%;
                    padding: 12px 40px 12px 40px;
                    border-radius: 8px;
                    border: 1px solid #ccc;
                    outline: none;
                    box-sizing: border-box;
                    color:red;
                ">

            <!-- Toggle Password -->
            <span id="eyeToggle"
                onclick="togglePassword()"
                style="
                    position: absolute;
                    right: 12px;
                    top: 50%;
                    transform: translateY(-50%);
                    cursor: pointer;">
                <svg id="eyeIcon"
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    fill="none"
                    stroke="#dc2626"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </span>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500" name="remember">
                <span class="text-sm text-white ms-2">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="mt-3">
            <button type="submit" id="loginButton"
                class="w-full flex justify-center text-white"
                style="background-color: #dc2626; padding: 14px 0; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;"
                onmouseover="this.style.backgroundColor='#b91c1c'"
                onmouseout="this.style.backgroundColor='#dc2626'">
                {{ __('Log in') }}
            </button>
        </div>
    </form>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            if (input.type === "password") {
                input.type = "text";
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.94 10.94 0 0112 19c-7 0-11-7-11-7a21.94 21.94 0 015.06-6.94"/>
                    <path d="M1 1l22 22"/>
                `;
            } else {
                input.type = "password";
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                    <circle cx="12" cy="12" r="3"/>
                `;
            }
        }

        // Handle form submission dengan AJAX
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Tampilkan loading
            document.getElementById('loadingSpinner').style.display = 'flex';
            document.getElementById('loginButton').disabled = true;
            
            // Ambil data form
            const formData = {
                nik: document.getElementById('nik').value,
                password: document.getElementById('password').value
            };
            
            // Kirim ke API
            fetch('https://web.kobin.co.id/api/hris/auth/api_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Login sukses - kirim ke server Laravel untuk buat session
                    return fetch('{{ route("login.api-callback") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data.data)
                    });
                } else {
                    throw new Error(data.message || 'Login gagal');
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Redirect berdasarkan level
                    window.location.href = result.redirect;
                } else {
                    throw new Error(result.message || 'Gagal membuat session');
                }
            })
            .catch(error => {
                // Sembunyikan loading
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('loginButton').disabled = false;
                
                // Tampilkan error
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: error.message || 'Terjadi kesalahan koneksi',
                    confirmButtonColor: '#dc2626',
                    timer: 3000
                });
            });
        });
    </script>

</x-guest-layout>