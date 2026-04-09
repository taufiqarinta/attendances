<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Perjalanan Dinas</title>
    <link rel="icon" type="image/png" sizes="16x16" href="https://sso.tanobel.net/asset/img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @media print, screen {
            table, .tbl {
                width: 100%;
                border: none;
            }
            body { 
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            .header-table {
                width: 100%;
                margin-bottom: 20px;
            }
            .info-table {
                width: 100%;
                margin-bottom: 30px;
            }
            .info-table th {
                width: 25%;
                text-align: left;
                font-weight: bold;
            }
            .info-table td {
                width: 1%;
                padding: 0 5px;
            }
            .info-table td:last-child {
                width: auto;
            }
            hr {
                border: 1px solid #000;
                margin: 20px 0;
            }
            .approval {
                margin-top: 40px;
                text-align: center;
            }
            .loading {
                text-align: center;
                padding: 50px;
                font-size: 18px;
            }
        }
    </style>
</head>
<body class="m-3">
    <div id="loading" class="loading">
        <img src="https://i.gifer.com/ZZ5H.gif" width="50"> Memuat data...
    </div>
    
    <div id="content" style="display:none;">
        <div class="row">
            <div class="col-md-9">
                <table class="header-table">
                    <tr>
                        <td rowspan="4" class="text-center" style="width:20%">
                            <img src="{{ asset('logo-kobin-one.png') }}" alt="Logo Kobin" width="100">
                        </td>
                        <td rowspan="4" class="text-center">
                            <h5><u>SURAT PERINTAH PERJALANAN DINAS</u></h5>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-3">
                <table style="border:1px solid black; font-size:8pt; width:100%">
                    <tr>
                        <td>No. Dokumen</td>
                        <td>:</td>
                        <td>FR-HRD-03-02</td>
                    </tr>
                    <tr>
                        <td>Tgl. Efektif</td>
                        <td>:</td>
                        <td>02-Februari-2017</td>
                    </tr>
                    <tr>
                        <td>Rev</td>
                        <td>:</td>
                        <td>0</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <br><br>
        
        <div class="row">
            <div class="col-md-12">
                <table class="info-table" id="data-table">
                    <tr>
                        <th>Tanggal</th><td>:</td>
                        <td id="crtd"></td>
                    </tr>
                    <tr>
                        <th>Nama</th><td>:</td>
                        <td id="firstname"></td>
                    </tr>
                    <tr>
                        <th>NIK</th><td>:</td>
                        <td id="personnelno"></td>
                    </tr>
                    <tr>
                        <th>Jabatan</th><td>:</td>
                        <td id="jabatan"></td>
                    </tr>
                    <tr>
                        <th>Divisi/Dept./Seksi</th><td>:</td>
                        <td id="divisi"></td>
                    </tr>
                </table>
                
                <hr>
                
                <table class="info-table" style="width:100%;">
                    <tr>
                        <th style="width:15%;">Dari Tanggal</th>
                        <td style="width:2%;">:</td>
                        <td style="width:25%;" id="validfrom"></td>
                        <td style="width:5%;">s/d</td>
                        <td style="width:25%;" id="enddate"></td>
                        <td style="width:28%;"></td> <!-- Spacer -->
                    </tr>
                    <tr>
                        <th>Dengan Tugas Utama</th>
                        <td>:</td>
                        <td colspan="4" id="keterangan"></td>
                    </tr>
                </table>
                
                <br><br>
                
                <p>Demikian untuk dilaksanakan sebaik - baiknya.</p>
                
                <hr>
                
                <div class="approval">
                    <b>Approval</b>
                    <br><br><br>
                    Approved by <span id="nama_approval_2"></span><br>
                    <span id="approval_date"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Ambil data dari URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const encodedData = urlParams.get('data');
            
            if (!encodedData) {
                $('#loading').html('Data tidak ditemukan');
                return;
            }
            
            // Decode data
            try {
                const decoded = atob(encodedData);
                const params = JSON.parse(decoded);
                console.log('Params:', params);
                
                // Ambil tanggal dalam format yang benar
                let v_date = params.v_date;
                let e_date = params.e_date;
                
                // Handle jika v_date adalah object (dari hasil encode)
                if (typeof v_date === 'object' && v_date.date) {
                    v_date = v_date.date.split(' ')[0]; // Ambil YYYY-MM-DD
                }
                if (typeof e_date === 'object' && e_date.date) {
                    e_date = e_date.date.split(' ')[0];
                }
                
                // FORMAT UNTUK API: gunakan format Inggris (Dec, Mar, dll)
                const apiVDate = formatDateToAPI(v_date);
                const apiEDate = formatDateToAPI(e_date);
                
                console.log('API dates:', {v_date: apiVDate, e_date: apiEDate});
                
                // Panggil API backend untuk ambil data
                $.ajax({
                    url: 'https://web.kobin.co.id/api/hris/izin/get_print_data.php',
                    method: 'GET',
                    data: {
                        nik: params.nik,
                        v_date: apiVDate,      // Kirim format Inggris ke API
                        e_date: apiEDate        // Kirim format Inggris ke API
                    },
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {
                        console.log('Response:', response);
                        
                        if (response.success && response.data && response.data.length > 0) {
                            const data = response.data[0];
                            
                            // Fungsi untuk TAMPILAN: ubah ke bulan Indonesia
                            function formatDateForDisplay(dateStr) {
                                if (!dateStr) return '-';
                                
                                const months = {
                                    'Jan': 'Januari', 'Feb': 'Februari', 'Mar': 'Maret', 'Apr': 'April',
                                    'May': 'Mei', 'Jun': 'Juni', 'Jul': 'Juli', 'Aug': 'Agustus',
                                    'Sep': 'September', 'Oct': 'Oktober', 'Nov': 'November', 'Dec': 'Desember'
                                };
                                
                                // Parse format "18 Mar 2026"
                                const parts = dateStr.split(' ');
                                if (parts.length === 3) {
                                    const day = parts[0];
                                    const month = months[parts[1]] || parts[1];
                                    const year = parts[2];
                                    return `${day} ${month} ${year}`;
                                }
                                
                                return dateStr;
                            }
                            
                            // Isi data ke HTML dengan format Indonesia untuk tampilan
                            $('#crtd').text(formatDateForDisplay(data.crtd) || '-');
                            $('#firstname').text(data.firstname || '-');
                            $('#personnelno').text(data.personnelno || '-');
                            $('#jabatan').text(data.jabatan || '-');
                            $('#divisi').text(data.divisi || '-');
                            $('#validfrom').text(formatDateForDisplay(data.validfrom) || '-');
                            $('#enddate').text(formatDateForDisplay(data.enddate) || '-');
                            $('#keterangan').text(data.keterangan || '-');
                            $('#nama_approval_2').text(data.nama_approval_2 || '-');
                            $('#approval_date').text(formatDateForDisplay(data.approval_date) || '-');
                            
                            // Sembunyikan loading, tampilkan konten
                            $('#loading').hide();
                            $('#content').show();
                            
                            // Auto print
                            window.print();
                        } else {
                            $('#loading').html('Data tidak ditemukan');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error details:', {
                            status: status,
                            error: error,
                            response: xhr.responseText,
                            statusCode: xhr.status
                        });
                        
                        let errorMsg = 'Gagal memuat data: ';
                        if (xhr.status === 404) {
                            errorMsg += 'API tidak ditemukan (404)';
                        } else if (xhr.status === 500) {
                            errorMsg += 'Error server (500)';
                        } else if (xhr.responseText) {
                            errorMsg += xhr.responseText.substring(0, 100);
                        } else {
                            errorMsg += error;
                        }
                        
                        $('#loading').html(errorMsg);
                    }
                });
            } catch (e) {
                console.error('Error decoding:', e);
                $('#loading').html('Data tidak valid: ' + e.message);
            }
        });

        // Fungsi untuk format tanggal ke dd Mmm yyyy (INGGRIS - untuk API)
        function formatDateToAPI(dateStr) {
            if (!dateStr) return '';
            
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            let date;
            
            if (typeof dateStr === 'string') {
                if (dateStr.includes('-')) {
                    const parts = dateStr.split('-');
                    if (parts.length === 3) {
                        date = new Date(parts[0], parts[1] - 1, parts[2]);
                    }
                } else if (dateStr.includes('/')) {
                    const parts = dateStr.split('/');
                    if (parts.length === 3) {
                        date = new Date(parts[2], parts[1] - 1, parts[0]);
                    }
                } else {
                    date = new Date(dateStr);
                }
            } else {
                date = new Date(dateStr);
            }
            
            if (date && !isNaN(date.getTime())) {
                const day = date.getDate();
                const month = months[date.getMonth()];
                const year = date.getFullYear();
                return `${day} ${month} ${year}`;
            }
            
            return dateStr;
        }
    </script>
</body>
</html>