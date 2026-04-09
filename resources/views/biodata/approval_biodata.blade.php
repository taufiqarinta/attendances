<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Approval Biodata</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Loading --}}
            <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                    <svg class="animate-spin h-8 w-8 text-red-500 mb-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="text-sm text-gray-600">Memproses...</span>
                </div>
            </div>

            <div style="background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:24px;">

                {{-- Tabel --}}
                <div style="overflow-x:auto;">
                    <table id="approvalTable" style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:#f9fafb;">
                                <th style="padding:10px 12px;text-align:center;border-bottom:2px solid #e5e7eb;white-space:nowrap;">No.</th>
                                <th style="padding:10px 12px;text-align:left;border-bottom:2px solid #e5e7eb;">Nama</th>
                                <th style="padding:10px 12px;text-align:center;border-bottom:2px solid #e5e7eb;white-space:nowrap;">Kepala Keluarga</th>
                                <th style="padding:10px 12px;text-align:center;border-bottom:2px solid #e5e7eb;white-space:nowrap;">NPWP Lama</th>
                                <th style="padding:10px 12px;text-align:center;border-bottom:2px solid #e5e7eb;white-space:nowrap;">NPWP Baru</th>
                                <th style="padding:10px 12px;text-align:left;border-bottom:2px solid #e5e7eb;">Email</th>
                                <th style="padding:10px 12px;text-align:center;border-bottom:2px solid #e5e7eb;white-space:nowrap;">Status Kawin</th>
                                <th style="padding:10px 12px;text-align:center;border-bottom:2px solid #e5e7eb;white-space:nowrap;">Jml Anak</th>
                                <th style="padding:10px 12px;text-align:center;border-bottom:2px solid #e5e7eb;white-space:nowrap;">Periode</th>
                                <th style="padding:10px 12px;text-align:center;border-bottom:2px solid #e5e7eb;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="10" style="text-align:center;padding:40px;color:#9ca3af;">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- ── MODAL DETAIL REVIEW ──────────────────────────────────────────── --}}
    <div id="modalReview" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;overflow-y:auto;padding:20px 0;">
        <div style="background:#fff;border-radius:10px;width:100%;max-width:540px;margin:auto;">
            <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-weight:600;font-size:14px;">Detail Perubahan Biodata</span>
                <button onclick="closeReview()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#6b7280;">&times;</button>
            </div>
            <div id="reviewBody" style="padding:20px;font-size:13px;max-height:70vh;overflow-y:auto;">
                Memuat...
            </div>
            <div style="padding:16px 20px;border-top:1px solid #e5e7eb;text-align:center;">
                <p style="font-size:13px;font-weight:600;color:#dc2626;margin-bottom:12px;">
                    Apakah Anda yakin untuk mereview perubahan biodata ini?
                </p>
                <div style="display:flex;justify-content:center;gap:10px;">
                    <button id="btnApprove" onclick="submitApprove(1)"
                        style="background:#22c55e;color:#fff;padding:8px 24px;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
                        ✓ Approve
                    </button>
                    <button id="btnReject" onclick="showRejectForm()"
                        style="background:#ef4444;color:#fff;padding:8px 24px;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
                        ✗ Reject
                    </button>
                    <button onclick="closeReview()"
                        style="background:#6b7280;color:#fff;padding:8px 18px;border:none;border-radius:6px;font-size:13px;cursor:pointer;">
                        Batal
                    </button>
                </div>
                {{-- Form alasan reject --}}
                <div id="rejectForm" style="display:none;margin-top:14px;">
                    <textarea id="rejectReason" placeholder="Masukkan alasan reject..."
                        style="width:100%;box-sizing:border-box;border:1px solid #d1d5db;border-radius:6px;padding:8px;font-size:13px;min-height:80px;resize:vertical;"></textarea>
                    <div style="display:flex;gap:8px;margin-top:8px;justify-content:center;">
                        <button onclick="submitApprove(0)"
                            style="background:#ef4444;color:#fff;padding:8px 24px;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
                            Konfirmasi Reject
                        </button>
                        <button onclick="document.getElementById('rejectForm').style.display='none'"
                            style="background:#6b7280;color:#fff;padding:8px 16px;border:none;border-radius:6px;font-size:13px;cursor:pointer;">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #approvalTable tbody tr:hover { background:#f9fafb; }
        #approvalTable tbody tr td   { padding:10px 12px; border-bottom:1px solid #f3f4f6; }
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
    </style>

    <script>
        const API_SAVE     = 'https://web.kobin.co.id/api/hris/biodata/save_biodata.php';
        const APPROVER_NIK = '{{ session("nik") }}';

        let currentRow = null; // data row yang sedang di-review

        // ── LOAD TABEL ───────────────────────────────────────────────────────
        async function loadTable() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:40px;color:#9ca3af;">Memuat data...</td></tr>';

            let r;
            try {
                const res = await fetch(API_SAVE, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({action:'getApprovalList'})
                });

                // Cek apakah response adalah JSON sebelum di-parse
                const text = await res.text();
                try {
                    r = JSON.parse(text);
                } catch(e) {
                    console.error('Response bukan JSON:', text.substring(0, 300));
                    tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:40px;color:#dc2626;">Error: Response dari server tidak valid. Cek console untuk detail.</td></tr>';
                    return;
                }
            } catch(e) {
                console.error('Fetch error:', e);
                tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:40px;color:#dc2626;">Gagal menghubungi server.</td></tr>';
                return;
            }

            if (!r.success || !r.data?.data?.length) {
                tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:40px;color:#9ca3af;">Tidak ada data yang menunggu approval</td></tr>';
                return;
            }

            let html = '';
            r.data.data.forEach((row, i) => {
                // Escape nama untuk onclick
                const rowJson = JSON.stringify(row).replace(/'/g, "\\'");
                html += `
                <tr>
                    <td style="text-align:center;">${i+1}.</td>
                    <td>${row.nama ?? row.personnelno}</td>
                    <td style="text-align:center;">${row.npwp_milik === 'Y' ? 'Ya' : row.npwp_milik === 'T' ? 'Tidak' : '-'}</td>
                    <td style="text-align:center;">${row.npwp ?? '-'}</td>
                    <td style="text-align:center;">${row.npwp_baru ?? '-'}</td>
                    <td>${row.email ?? '-'}</td>
                    <td style="text-align:center;">${row.status_nikah ?? '-'}</td>
                    <td style="text-align:center;">${row.jumlah_anak ?? '-'}</td>
                    <td style="text-align:center;">${row.periode ?? '-'}</td>
                    <td style="text-align:center;">
                        <button onclick='openReview(${JSON.stringify(row)})'
                            style="background:#f59e0b;color:#fff;border:none;border-radius:5px;padding:5px 14px;font-size:12px;font-weight:600;cursor:pointer;">
                            Review
                        </button>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        // ── OPEN REVIEW MODAL ────────────────────────────────────────────────
        function openReview(row) {
            currentRow = row;
            document.getElementById('rejectForm').style.display = 'none';
            document.getElementById('rejectReason').value = '';

            // Hanya field yang ada di tabel sik_temp_biodata
            const fields = [
                ['Status Kawin',        row.status_nikah ?? '-'],
                ['Jumlah Anak',         row.jumlah_anak  ?? '-'],
                ['Email',               row.email        ?? '-'],
                ['NPWP Lama',           row.npwp         ?? '-'],
                ['NPWP Baru (NIK KTP)', row.npwp_baru    ?? '-'],
                ['Penanggung Keluarga', row.npwp_milik === 'Y' ? 'Ya' : row.npwp_milik === 'T' ? 'Tidak' : '-'],
                ['Periode',             row.periode      ?? '-'],
            ];

            let html = `<p style="font-weight:600;margin:0 0 12px;font-size:14px;">${row.nama ?? row.personnelno} <span style="color:#6b7280;font-weight:400;">(${row.personnelno})</span></p>`;
            html += '<table style="width:100%;border-collapse:collapse;">';
            fields.forEach(([k,v]) => {
                html += `<tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:6px 8px;color:#6b7280;width:45%;">${k}</td>
                    <td style="padding:6px 4px;width:5%;">:</td>
                    <td style="padding:6px 8px;font-weight:500;">${v ?? '-'}</td>
                </tr>`;
            });
            html += '</table>';

            document.getElementById('reviewBody').innerHTML = html;
            document.getElementById('modalReview').style.display = 'flex';
        }

        function closeReview()    { document.getElementById('modalReview').style.display = 'none'; }
        function showRejectForm() { document.getElementById('rejectForm').style.display = 'block'; }

        // ── SUBMIT APPROVE / REJECT ──────────────────────────────────────────
        async function submitApprove(approve) {
            if (!currentRow) return;

            const reason = document.getElementById('rejectReason').value.trim();
            if (approve === 0 && !reason) {
                alert('Alasan reject wajib diisi!');
                return;
            }

            closeReview();
            showLoading();

            const payload = {
                action:       'doApprove',
                nik:          currentRow.personnelno,
                periode:      currentRow.periode,
                approve:      approve,
                reason:       reason,
                approver_nik: APPROVER_NIK,
            };

            try {
                const r = await fetch(API_SAVE, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify(payload)
                }).then(r=>r.json());

                alert(r.message || (approve ? 'Approve berhasil' : 'Reject berhasil'));
                loadTable(); // reload tabel
            } catch(e) {
                alert('Terjadi kesalahan, coba lagi');
            } finally {
                hideLoading();
            }
        }

        function showLoading() { document.getElementById('loadingOverlay').classList.remove('hidden'); }
        function hideLoading() { document.getElementById('loadingOverlay').classList.add('hidden'); }

        // Tidak ada cek hak akses — sama dengan kode lama (approval_biodata hanya cek auth login)
        document.addEventListener('DOMContentLoaded', loadTable);
    </script>
</x-app-layout>