<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Data Peserta Orientation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            background: #FFFFFF;
            padding: 20px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid #7A1113;
            padding-bottom: 15px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .title-section {
            text-align: center;
            flex: 1;
        }

        .title-section h1 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 28px;
            font-weight: bold;
            color: #7A1113;
            letter-spacing: 2px;
        }

        .title-section .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 2px;
        }

        .info-right {
            text-align: right;
            font-size: 11px;
            color: #333;
            min-width: 200px;
        }

        .info-right .label {
            font-weight: bold;
        }

        .program-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 12px;
            padding: 8px 0;
            border-bottom: 1px dashed #ccc;
        }

        .program-info .label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #7A1113;
            color: #FFFFFF;
            font-weight: bold;
            padding: 8px 6px;
            border: 1px solid #7A1113;
            text-align: center;
            font-size: 11px;
            font-family: 'Times New Roman', Times, serif;
        }

        td {
            padding: 6px;
            border: 1px solid #cccccc;
            text-align: center;
            font-size: 11px;
            font-family: 'Times New Roman', Times, serif;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #F9F9F9;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #7A1113;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <div class="logo">
                    <img src="{{ public_path('images/logo_kobin.png') }}" alt="Logo Kobin"
                        onerror="this.style.display='none'">
                </div>
            </div>
            <div class="title-section">
                <h1>KOBIN ORIENTATION PROGRAM</h1>
                <div class="subtitle">{{ $orientation->batch_name }}</div>
            </div>
            <div class="info-right">
                <div><span class="label">Nama:</span> Tim HO</div>
                <div><span class="label">Periode:</span> -</div>
            </div>
        </div>

        <div class="program-info">
            <div class="left">
                <span><span class="label">Lokasi:</span> {{ $plantName ?? '-' }} | Total Peserta:
                    {{ $totalParticipants }} Orang</span>
            </div>
            <div class="right">
                <span><span class="label">Tanggal Cetak:</span> {{ now()->format('d F Y H:i') }} WIB</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:5%">No</th>
                    <th style="width:20%">NIK</th>
                    <th style="width:30%">Nama</th>
                    <th style="width:25%">Jabatan</th>
                    <th style="width:20%">Departemen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $index => $participant)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $participant['nik'] ?? '-' }}</td>
                        <td class="text-left">{{ $participant['nama'] ?? '-' }}</td>
                        <td>{{ $participant['jabatan'] ?? '-' }}</td>
                        <td>{{ $participant['dept'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:30px;color:#999;">
                            Belum ada peserta
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Dokumen ini dicetak dari sistem Kobin Orientation • {{ now()->format('d F Y') }}</p>
        </div>
    </div>
</body>

</html>
