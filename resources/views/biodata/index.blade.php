<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Biodata</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Loading --}}
            <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                    <svg class="animate-spin h-8 w-8 text-red-500 mb-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="text-sm text-gray-600">Memuat data...</span>
                </div>
            </div>

            {{-- Alert area --}}
            <div id="alertPending" style="display:none;margin-bottom:16px;padding:12px 16px;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;font-size:13px;color:#991b1b;">
                <b>⚠ Data Anda masih dalam proses approval oleh HRD</b>
            </div>
            <div id="alertReject" style="display:none;margin-bottom:16px;padding:12px 16px;background:#fef9c3;border:1px solid #fde047;border-radius:8px;font-size:13px;color:#854d0e;">
                <b>⚠ Perubahan Biodata Anda direject</b> — Alasan: <span id="rejectReason" style="color:#dc2626;font-weight:600;"></span>
            </div>
            <div id="alertSuccess" style="display:none;margin-bottom:16px;padding:12px 16px;background:#dcfce7;border:1px solid #86efac;border-radius:8px;font-size:13px;color:#166534;">
                ✅ Data berhasil dikirim, menunggu approval HRD.
            </div>
            <div id="alertError" style="display:none;margin-bottom:16px;padding:12px 16px;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;font-size:13px;color:#991b1b;">
                <span id="errorMsg"></span>
            </div>

            <div style="background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:24px 28px;">

                {{-- ── SECTION: Data HR ──────────────────────────────────── --}}
                <p style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin:0 0 12px;border-bottom:1px solid #e5e7eb;padding-bottom:6px;">Data HR</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px 24px;margin-bottom:20px;">
                    <x-bio-field label="NIK"           id="nik"      readonly />
                    <x-bio-field label="Jabatan"       id="jabatan"  readonly />
                    <x-bio-field label="Nama"          id="firstname" readonly />
                    <x-bio-field label="Divisi"        id="divisi"   readonly />
                    <x-bio-field label="Plant"         id="plant"    readonly />
                    <x-bio-field label="Tanggal Join"  id="tgljoin"  readonly />
                </div>

                {{-- ── SECTION: Personal Info ────────────────────────────── --}}
                <p style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin:16px 0 12px;border-bottom:1px solid #e5e7eb;padding-bottom:6px;">Personal Info</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px 24px;margin-bottom:20px;">
                    <div>
                        <label style="font-size:12px;font-weight:500;color:#374151;display:block;margin-bottom:3px;">TTL</label>
                        <div style="display:flex;gap:8px;">
                            <input id="tempatlahir" readonly class="bio-input" style="flex:1;" placeholder="Tempat Lahir">
                            <input id="tgllahir"    readonly class="bio-input" style="width:120px;" placeholder="DD/MM/YYYY">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:500;color:#374151;display:block;margin-bottom:3px;">Jenis Kelamin</label>
                        <div style="display:flex;gap:16px;align-items:center;height:34px;">
                            <label style="font-size:13px;cursor:pointer;"><input type="radio" id="jk_l" name="jk" value="Male" disabled> Laki-laki</label>
                            <label style="font-size:13px;cursor:pointer;"><input type="radio" id="jk_p" name="jk" value="Female" disabled> Perempuan</label>
                        </div>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:500;color:#374151;display:block;margin-bottom:3px;">Status Kawin</label>
                        <select id="statuskawin" class="bio-input" disabled>
                            <option value="Lajang">Lajang</option>
                            <option value="Nikah">Nikah</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>
                    <x-bio-field label="Jumlah Anak"  id="jmlanak"    type="number" />
                    <div>
                        <label style="font-size:12px;font-weight:500;color:#374151;display:block;margin-bottom:3px;">Pendidikan Terakhir</label>
                        <select id="pendidikan" class="bio-input" disabled>
                            <option value="">-- Pilih Pendidikan --</option>
                        </select>
                    </div>
                    <x-bio-field label="Jurusan"      id="jurusan" />
                </div>

                {{-- ── SECTION: Alamat KTP ───────────────────────────────── --}}
                <p style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin:16px 0 12px;border-bottom:1px solid #e5e7eb;padding-bottom:6px;">Alamat Sesuai KTP</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px 24px;margin-bottom:20px;">
                    <x-bio-field label="Alamat KTP"  id="alamatktp" />
                    <x-bio-field label="RT/RW"       id="rtrwktp" />
                    <x-bio-field label="Kelurahan"   id="kelurahanktp" />
                    <x-bio-field label="Kecamatan"   id="kecamatanktp" />
                    <x-bio-field label="Kab/Kota"    id="kabupatenktp" />
                    <x-bio-field label="Provinsi"    id="provinsiktp" />
                </div>

                {{-- ── SECTION: Alamat Domisili ─────────────────────────── --}}
                <p style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin:16px 0 8px;border-bottom:1px solid #e5e7eb;padding-bottom:6px;">Alamat Domisili</p>
                <div style="margin-bottom:10px;">
                    <label style="font-size:13px;cursor:pointer;font-weight:500;">
                        <input type="checkbox" id="samakan_ktp"> Samakan dengan Alamat KTP
                    </label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px 24px;margin-bottom:20px;">
                    <x-bio-field label="Alamat Tinggal" id="alamatdom" />
                    <x-bio-field label="RT/RW"          id="rtrwdom" />
                    <x-bio-field label="Kelurahan"      id="kelurahandom" />
                    <x-bio-field label="Kecamatan"      id="kecamatandom" />
                    <x-bio-field label="Kab/Kota"       id="kabupatendom" />
                    <x-bio-field label="Provinsi"       id="provinsidom" />
                </div>

                {{-- ── SECTION: Additional Info ─────────────────────────── --}}
                <p style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin:16px 0 12px;border-bottom:1px solid #e5e7eb;padding-bottom:6px;">Additional Info</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px 24px;margin-bottom:20px;">
                    <x-bio-field label="No. HP"         id="nohp" />
                    <x-bio-field label="Email (Gaji)"   id="email" />
                    <x-bio-field label="No. HP Darurat" id="nohp_darurat" />
                    <div>
                        <label style="font-size:12px;font-weight:500;color:#374151;display:block;margin-bottom:3px;">Hubungan Darurat</label>
                        <select id="hubungan_darurat" class="bio-input" disabled>
                            <option value="">-- Pilih Hubungan --</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Suami/Istri">Suami/Istri</option>
                            <option value="Saudara Kandung">Saudara Kandung</option>
                            <option value="Family Lain">Family Lain</option>
                        </select>
                    </div>
                    <x-bio-field label="No. KTP"        id="noktp" />
                    <x-bio-field label="BPJS Kesehatan" id="bpjs_kes" readonly />
                    <x-bio-field label="BPJS TK"        id="bpjs_tk" readonly />
                    <div>
                        <label style="font-size:12px;font-weight:500;color:#374151;display:block;margin-bottom:3px;">Kepala Keluarga</label>
                        <select id="npwp_milik" class="bio-input" disabled>
                            <option value="">-- Pilih --</option>
                            <option value="Y">Ya</option>
                            <option value="T">Tidak</option>
                        </select>
                    </div>
                    <x-bio-field label="NPWP Lama"      id="npwp" />
                    <x-bio-field label="NPWP Baru (NIK KTP)" id="npwp_baru" />
                </div>

                {{-- ── Checkbox Agreement & Submit ──────────────────────── --}}
                <!-- <div style="margin-top:8px;padding-top:16px;border-top:1px solid #e5e7eb;">
                    <label id="agreement_wrap" style="font-size:13px;cursor:pointer;font-weight:500;">
                        <input type="checkbox" id="agreement_check" disabled>
                        Dengan ini saya menyatakan bahwa informasi yang saya berikan adalah benar beserta lampiran-lampirannya.
                    </label>
                </div>
                <div style="margin-top:16px;text-align:center;">
                    <button id="btn_confirm" onclick="showModalKonfirmasi()"
                        style="background:#ef4444;color:#fff;font-size:13px;font-weight:600;padding:10px 32px;border:none;border-radius:6px;cursor:not-allowed;opacity:.5;"
                        disabled
                        onmouseover="if(!this.disabled){this.style.background='#dc2626';this.style.cursor='pointer'}"
                        onmouseout="this.style.background='#ef4444'">
                        Update Data
                    </button>
                </div> -->
            </div>

        </div>
    </div>

    {{-- ── MODAL KONFIRMASI PAJAK ────────────────────────────────────── --}}
    <div id="modalKonfirmasi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:10px;width:100%;max-width:480px;margin:0 16px;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;font-weight:600;font-size:14px;text-align:center;">
                STATUS PAJAK TAHUN <span id="modal_year"></span>
            </div>
            <div style="padding:20px;" id="modal_pajak_body">
                <div style="text-align:center;color:#6b7280;font-size:13px;">Memuat data pajak...</div>
            </div>
            <div style="padding:16px 20px;border-top:1px solid #e5e7eb;text-align:center;">
                <p style="font-size:13px;color:#dc2626;font-weight:600;margin-bottom:12px;">Apakah data tersebut sudah sesuai dengan status pajak Anda?</p>
                <div style="display:flex;justify-content:center;gap:10px;">
                    <button onclick="doSave()"
                        style="background:#3b82f6;color:#fff;padding:8px 24px;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
                        Ya, Simpan
                    </button>
                    <button onclick="closeModal()"
                        style="background:#6b7280;color:#fff;padding:8px 20px;border:none;border-radius:6px;font-size:13px;cursor:pointer;">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bio-input {
            width: 100%; box-sizing: border-box;
            border: 1px solid #d1d5db; border-radius: 6px;
            padding: 7px 10px; font-size: 13px; color: #111827;
            background: #fff; outline: none;
        }
        .bio-input:disabled, .bio-input[readonly] { background: #f9fafb; color: #6b7280; }
        .bio-input:focus { border-color: #f87171; }
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
    </style>

    <script>
        const API_GET  = 'https://web.kobin.co.id/api/hris/biodata/get_biodata.php';
        const API_SAVE = 'https://web.kobin.co.id/api/hris/biodata/save_biodata.php';
        const NIK      = '{{ session("nik") }}';
        const PLANT    = '{{ session("plant") ?? "0001" }}';
        const YEAR     = '{{ date("Y") }}';

        let bioEnabled  = false;  // boleh edit atau tidak
        // DEBUG: set true sementara untuk test tampilan tombol
        // Hapus baris ini setelah konfirmasi tombol muncul
        // bioEnabled = true;
        let dataPajak   = null;

        // ── INIT ────────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', async () => {
            showLoading();
            document.getElementById('modal_year').textContent = YEAR;

            try {
                await loadBiodata();
                await loadProgramStudi();
                await checkPeriode();
                await loadPendingStatus();
            } catch(e) {
                console.error('Init error:', e);
            } finally {
                hideLoading(); // SELALU hilangkan loading apapun yang terjadi
            }

            loadDataPajak(); // background, tidak perlu await
        });

        // ── LOAD BIODATA ────────────────────────────────────────────────────
        async function loadBiodata() {
            const r = await fetch(`${API_GET}?action=getBiodata&nik=${NIK}`).then(r=>r.json());
            if (!r.success || !r.data) return;
            const d = r.data;

            setVal('nik',        d.PersonnelNo);
            setVal('jabatan',    d.JobDesc);
            setVal('firstname',  d.FirstName);
            setVal('divisi',     d.DivDesc);
            setVal('plant',      d.plant);
            setVal('tgljoin',    d.MarFrom);
            setVal('tempatlahir',d.CityOfBirth);
            setVal('tgllahir',   d.BirthDate);
            setVal('jmlanak',    d.jumlah_anak);
            setVal('jurusan',    d.JurusanAkademik);
            setVal('alamatktp',  d.ktp_address);
            setVal('rtrwktp',    d.ktp_rtrw);
            setVal('kelurahanktp',d.ktp_kelurahan);
            setVal('kecamatanktp',d.ktp_kecamatan);
            setVal('kabupatenktp',d.ktp_kabupaten);
            setVal('provinsiktp', d.ktp_provinsi);
            setVal('alamatdom',  d.dom_address);
            setVal('rtrwdom',    d.dom_rtrw);
            setVal('kelurahandom',d.dom_kelurahan);
            setVal('kecamatandom',d.dom_kecamatan);
            setVal('kabupatendom',d.dom_kabupaten);
            setVal('provinsidom', d.dom_provinsi);
            setVal('nohp',       d.NumberType1);
            setVal('email',      d.Email);
            setVal('nohp_darurat',d.nohp_darurat);
            setVal('noktp',      d.KTPNumber);
            setVal('bpjs_kes',   d.bpjs_kes);
            setVal('bpjs_tk',    d.bpjs_tk);
            setVal('npwp',       d.NPWPNumber);
            setVal('npwp_baru',  d.npwp_baru);

            setSelect('statuskawin',     d.MarStatus);
            setSelect('npwp_milik',      d.npwp_milik);
            setSelect('hubungan_darurat',d.hubungan_darurat);
            setSelect('pendidikan',      d.ProgramStudi);

            if (d.Gender === 'Male')   document.getElementById('jk_l').checked = true;
            if (d.Gender === 'Female') document.getElementById('jk_p').checked = true;
        }

        // ── LOAD PROGRAM STUDI ───────────────────────────────────────────────
        async function loadProgramStudi() {
            const r = await fetch(`${API_GET}?action=getProgramStudi`).then(r=>r.json());
            if (!r.success) return;
            const sel = document.getElementById('pendidikan');
            r.data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.Nama; opt.textContent = p.Nama;
                sel.appendChild(opt);
            });
        }

        // ── CEK PERIODE PAYROLL (boleh edit?) ────────────────────────────────
        async function checkPeriode() {
            try {
                const r = await fetch(`${API_GET}?action=checkPeriode&plant=${PLANT}`).then(r=>r.json());
                // PHP bisa return true (boolean), 1 (int), atau "1" (string) — handle semua
                bioEnabled = r.enable == true || r.enable === 1 || r.enable === '1' || r.enable === 'true';
                console.log('checkPeriode response:', r, '→ bioEnabled:', bioEnabled);
            } catch(e) {
                console.warn('checkPeriode gagal, default bioEnabled=false', e);
                bioEnabled = false;
            }
        }

        // ── CEK STATUS PENDING / REJECT ──────────────────────────────────────
        async function loadPendingStatus() {
            const p = await fetch(`${API_SAVE}?action=getPending&nik=${NIK}`)
                .then(r => r.json())
                .catch(() => ({ success: false, data: null }));

            if (p.data) {
                // Ada pending → alert muncul, tombol tetap tampil tapi disabled + tooltip
                document.getElementById('alertPending').style.display = 'block';
                setTombolState('pending');
                return;
            }

            // Cek reject terakhir
            const rj = await fetch(`${API_SAVE}?action=getLastReject&nik=${NIK}`)
                .then(r => r.json())
                .catch(() => ({ success: false, data: null }));

            if (rj.data) {
                try {
                    const detail = JSON.parse(rj.data.detail_approval || '{}');
                    document.getElementById('rejectReason').textContent = detail.reason || '-';
                } catch(e) {}
                document.getElementById('alertReject').style.display = 'block';
            }

            if (bioEnabled) {
                // Periode aktif & tidak ada pending → form bisa diedit
                setTombolState('enabled');
                enableForm();
            } else {
                // Periode tidak aktif → tombol tampil tapi disabled
                setTombolState('disabled');
            }

            // Checkbox agreement aktifkan tombol
            document.getElementById('agreement_check').addEventListener('change', function() {
                if (!bioEnabled) return;
                const btn = document.getElementById('btn_confirm');
                btn.disabled = !this.checked;
                btn.style.opacity  = this.checked ? '1'   : '.5';
                btn.style.cursor   = this.checked ? 'pointer' : 'not-allowed';
            });
        }

        // ── SET STATE TOMBOL ─────────────────────────────────────────────────
        function setTombolState(state) {
            const btn   = document.getElementById('btn_confirm');
            const chk   = document.getElementById('agreement_check');
            const label = document.getElementById('agreement_wrap');

            if (state === 'enabled') {
                // Aktif — tunggu checkbox dicentang
                chk.disabled      = false;
                btn.disabled      = true;   // aktif setelah checkbox
                btn.style.opacity = '.5';
                btn.style.cursor  = 'not-allowed';
                btn.title         = 'Centang pernyataan di atas terlebih dahulu';
            } else if (state === 'pending') {
                chk.disabled      = true;
                btn.disabled      = true;
                btn.style.opacity = '.4';
                btn.style.cursor  = 'not-allowed';
                btn.title         = 'Data Anda sedang dalam proses approval HRD';
            } else {
                // disabled — periode tidak aktif
                chk.disabled      = true;
                btn.disabled      = true;
                btn.style.opacity = '.4';
                btn.style.cursor  = 'not-allowed';
                btn.title         = 'Perubahan data tidak diizinkan pada periode ini';
            }
        }

        // ── LOAD DATA PAJAK ──────────────────────────────────────────────────
        async function loadDataPajak() {
            const r = await fetch(`${API_GET}?action=getDataPajak&nik=${NIK}&year=${YEAR}`).then(r=>r.json());
            if (r.success && r.data) dataPajak = r.data;
        }

        // ── ENABLE FORM ──────────────────────────────────────────────────────
        function enableForm() {
            const fields = ['statuskawin','jmlanak','pendidikan','jurusan',
                'alamatktp','rtrwktp','kelurahanktp','kecamatanktp','kabupatenktp','provinsiktp',
                'alamatdom','rtrwdom','kelurahandom','kecamatandom','kabupatendom','provinsidom',
                'nohp','email','nohp_darurat','hubungan_darurat','noktp',
                'npwp_milik','npwp','npwp_baru'];
            fields.forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.disabled = false; el.removeAttribute('readonly'); }
            });
            document.getElementById('samakan_ktp').disabled = false;
        }

        // ── SAMAKAN KTP ──────────────────────────────────────────────────────
        document.getElementById('samakan_ktp').addEventListener('change', function() {
            if (!this.checked) return;
            ['alamat','rtrw','kelurahan','kecamatan','kabupaten','provinsi'].forEach(f => {
                const el = document.getElementById(f+'dom');
                if (el) el.value = document.getElementById(f+'ktp')?.value || '';
            });
        });

        // ── MODAL KONFIRMASI PAJAK ───────────────────────────────────────────
        function showModalKonfirmasi() {
            document.getElementById('modalKonfirmasi').style.display = 'flex';
            if (dataPajak) renderPajakTable(dataPajak);
        }
        function closeModal() {
            document.getElementById('modalKonfirmasi').style.display = 'none';
        }
        function renderPajakTable(d) {
            const rows = [
                ['Personnel No', d.Personnelno],['Nama', d.Nama],['Company', d.CompDesc],
                ['Area', d.PlantDesc],['NPWP', d.NPWP],['Alamat', d.Alamat],
                ['Gender', d.GenderDesc],['Jabatan', d.Jabatan],
                ['Tanggungan', d.Tanggungan],
                ['Status Pajak', `<span id="status_pajak_confirm">${d.Keluarga??'-'}</span>`],
                ['Kategori', d.Kategori],
            ];
            let html = '<table style="width:100%;font-size:13px;border-collapse:collapse;">';
            rows.forEach(([k,v]) => {
                html += `<tr><td style="padding:4px 8px;width:40%;color:#6b7280;">${k}</td><td style="padding:4px 2px;width:4%;">:</td><td style="padding:4px 8px;">${v??'-'}</td></tr>`;
            });
            html += '</table>';
            if (d.SrtKetSuamiDesc || d.KantorLamaDesc) {
                html += `<div style="margin-top:12px;font-size:12px;color:#374151;">
                    <b>Keterangan</b>
                    ${d.SrtKetSuamiDesc ? `<p style="margin:4px 0;">${d.SrtKetSuamiDesc}</p>` : ''}
                    ${d.KantorLamaDesc  ? `<p style="margin:4px 0;">${d.KantorLamaDesc}</p>`  : ''}
                </div>`;
            }
            document.getElementById('modal_pajak_body').innerHTML = html;
        }

        // ── DO SAVE ──────────────────────────────────────────────────────────
        async function doSave() {
            closeModal();
            showLoading();

            // Hanya kirim field yang ada di tabel sik_temp_biodata
            const payload = {
                action:      'saveBiodata',
                nik:         NIK,
                crby:        NIK,
                statuskawin: getVal('statuskawin'),
                jmlanak:     getVal('jmlanak'),
                email:       getVal('email'),
                npwp_milik:  getVal('npwp_milik'),
                npwp:        getVal('npwp'),
                npwp_baru:   getVal('npwp_baru'),
            };

            try {
                const r = await fetch(API_SAVE, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify(payload)
                }).then(r=>r.json());

                if (r.success) {
                    document.getElementById('alertSuccess').style.display = 'block';
                    setTombolState('pending'); // disable setelah submit
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showError(r.message || 'Gagal menyimpan data');
                }
            } catch(e) {
                showError('Terjadi kesalahan jaringan');
            } finally {
                hideLoading();
            }
        }

        // ── HELPERS ──────────────────────────────────────────────────────────
        function setVal(id, val)    { const el=document.getElementById(id); if(el) el.value = val??''; }
        function getVal(id)         { return document.getElementById(id)?.value??''; }
        function setSelect(id, val) { const el=document.getElementById(id); if(el) el.value = val??''; }
        function showLoading()      { document.getElementById('loadingOverlay').classList.remove('hidden'); }
        function hideLoading()      { document.getElementById('loadingOverlay').classList.add('hidden'); }
        function showError(msg)     {
            document.getElementById('errorMsg').textContent = msg;
            document.getElementById('alertError').style.display = 'block';
            setTimeout(()=>document.getElementById('alertError').style.display='none', 5000);
        }

        // Title case on blur (semua input text)
        document.querySelectorAll('input.bio-input:not(#email):not(#nohp):not(#nohp_darurat)').forEach(el => {
            el.addEventListener('blur', function() {
                if (this.readOnly || this.disabled) return;
                this.value = this.value.trim().toLowerCase().replace(/\b\w/g, c=>c.toUpperCase());
            });
        });
        document.getElementById('email')?.addEventListener('blur', function() {
            this.value = this.value.trim().toLowerCase();
        });
    </script>
</x-app-layout>