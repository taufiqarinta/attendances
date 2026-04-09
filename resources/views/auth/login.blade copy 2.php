<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: '{{ $errors->first() }}',
                    // text: 'Username atau Password anda Salah. Silahkan Coba Lagi',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Coba Lagi',
                    timer: 3000,
                    timerProgressBar: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
            });
        </script>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
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
                name="id_customer"
                placeholder="Username"
                value="{{ old('id_customer') }}"
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


        <!-- Password -->
        <div style="position: relative; margin-top: 15px;">

            <!-- Icon Kiri -->
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


            <!-- Input -->
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

            <!-- Tombol Mata -->
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

        <div class="flex items-center justify-end mt-4">
            {{-- @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif --}}

            <!-- <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button> -->
        </div>

        <div class="mt-3">
            <x-primary-button 
                class="w-full flex justify-center text-white"
                style="background-color: #dc2626; padding: 14px 0;"
                onmouseover="this.style.backgroundColor='#b91c1c'"
                onmouseout="this.style.backgroundColor='#dc2626'">
                {{ __('Log in') }}
            </x-primary-button>
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
    </script>

</x-guest-layout>
