<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Change Password') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div style="background:#fff; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,.08); padding:28px 32px;">

                <h3 style="font-size:15px; font-weight:600; color:#111827; margin:0 0 6px;">Ganti Password</h3>
                <p style="font-size:13px; color:#6b7280; margin:0 0 24px;">
                    Masukkan password lama kamu, lalu tentukan password baru.
                </p>

                {{-- Alert error --}}
                <div id="alertError" style="display:none; margin-bottom:16px; padding:10px 14px; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; font-size:13px; color:#991b1b;">
                    <span id="errorMsg"></span>
                </div>

                {{-- Alert sukses --}}
                <div id="alertSuccess" style="display:none; margin-bottom:16px; padding:10px 14px; background:#dcfce7; border:1px solid #86efac; border-radius:6px; font-size:13px; color:#166534;">
                    Password berhasil diubah. Mengalihkan ke halaman dashboard...
                </div>

                {{-- Form --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:4px;">
                        Password Lama
                    </label>
                    <input type="password" id="oldpass" placeholder="Masukkan password lama"
                        style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px; padding:8px 12px; font-size:13px; color:#111827; outline:none;">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:4px;">
                        Password Baru
                    </label>
                    <input type="password" id="newpass" placeholder="Masukkan password baru"
                        style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px; padding:8px 12px; font-size:13px; color:#111827; outline:none;">
                </div>

                <div style="margin-bottom:24px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:4px;">
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" id="confirmpass" placeholder="Ulangi password baru"
                        style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px; padding:8px 12px; font-size:13px; color:#111827; outline:none;">
                </div>

                <button id="btnSubmit" onclick="submitChangePassword()"
                    style="width:100%; background:#ef4444; color:#fff; font-size:13px; font-weight:600; padding:10px; border:none; border-radius:6px; cursor:pointer;"
                    onmouseover="this.style.background='#dc2626'"
                    onmouseout="this.style.background='#ef4444'">
                    Simpan Password
                </button>

            </div>
        </div>
    </div>

    <script>
        const API_URL  = 'https://web.kobin.co.id/api/hris/auth/change_password.php';
        const NIK      = '{{ session("nik") }}';
        const LOGOUT   = '{{ route("logout") }}';
        const CSRF     = '{{ csrf_token() }}';

        async function submitChangePassword() {
            const oldpass     = document.getElementById('oldpass').value.trim();
            const newpass     = document.getElementById('newpass').value.trim();
            const confirmpass = document.getElementById('confirmpass').value.trim();

            hideAlerts();

            // Validasi sisi klien
            if (!oldpass || !newpass || !confirmpass) {
                showError('Semua field wajib diisi.');
                return;
            }
            if (newpass.length < 6) {
                showError('Password baru minimal 6 karakter.');
                return;
            }
            if (newpass !== confirmpass) {
                showError('Konfirmasi password tidak cocok.');
                return;
            }

            // Disable tombol saat proses
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
            btn.style.background = '#9ca3af';

            try {
                const res    = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nik: NIK, oldpass, newpass })
                });
                const result = await res.json();

                if (result.success) {
                    document.getElementById('alertSuccess').style.display = 'block';

                    // Logout lalu redirect ke login (sama seperti kode lama)
                    setTimeout(async () => {
                        await fetch(LOGOUT, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': CSRF,
                                'Content-Type': 'application/json'
                            }
                        });
                        window.location.href = '/login';
                    }, 1500);

                } else {
                    showError(result.message || 'Gagal mengubah password.');
                    resetBtn();
                }
            } catch (err) {
                showError('Terjadi kesalahan. Silakan coba lagi.');
                resetBtn();
            }
        }

        function showError(msg) {
            document.getElementById('errorMsg').textContent = msg;
            document.getElementById('alertError').style.display = 'block';
        }

        function hideAlerts() {
            document.getElementById('alertError').style.display  = 'none';
            document.getElementById('alertSuccess').style.display = 'none';
        }

        function resetBtn() {
            const btn = document.getElementById('btnSubmit');
            btn.disabled = false;
            btn.textContent = 'Simpan Password';
            btn.style.background = '#ef4444';
        }

        // Submit dengan Enter
        document.addEventListener('keydown', e => {
            if (e.key === 'Enter') submitChangePassword();
        });
    </script>

</x-app-layout>