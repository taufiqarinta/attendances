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
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            background: #fff;
            padding: 20px 30px;
        }

        .container {
            width: 100%;
        }

        /* ================= HEADER ================= */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #7A1113;
            padding-bottom: 15px;
            margin-bottom: 20px;
            min-height: 80px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .logo {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            width: 75px;
            height: 75px;
            border: 1px dashed #999;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 10px;
            background: #f9f9f9;
        }

        .title-section {
            flex: 1;
        }

        .title-section h1 {
            font-size: 30px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
            margin: 0;
        }

        .title-section .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 2px;
        }

        .header-right {
            text-align: right;
            font-size: 11px;
            color: #333;
            min-width: 200px;
            flex-shrink: 0;
        }

        .header-right .label {
            font-weight: bold;
        }

        /* ================= INFO ================= */
        .program-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px 0;
            border-bottom: 1px dashed #ccc;
            font-size: 12px;
        }

        .program-info .left {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .program-info .label {
            font-weight: bold;
        }

        .program-info .right {
            color: #666;
        }

        /* ================= TABLE ================= */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table th {
            background: #ff4c4c;
            color: #fff;
            border: 1px solid #555;
            padding: 7px 6px;
            font-size: 12px;
            font-weight: normal;
            text-align: center;
        }

        table.data-table td {
            border: 1px solid #555;
            padding: 6px 8px;
            font-size: 11px;
            text-align: center;
            vertical-align: middle;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #d9d9d9;
        }

        table.data-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .text-left {
            text-align: left !important;
            padding-left: 10px;
        }

        .text-center {
            text-align: center;
        }

        /* ================= FOOTER ================= */
        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 2px solid #7A1113;
            text-align: center;
            font-size: 10px;
            color: #888;
        }

        .footer span {
            font-weight: bold;
        }
    </style>

</head>

<body>

    <div class="container">

        <!-- ================= HEADER ================= -->
        <div class="header">

            <div class="header-left">

                <!-- LOGO -->
                <div class="logo">
                    @php
                        try {
                            $logoPath = public_path('icons/kobintiles-logo.png');
                            $logoExists = file_exists($logoPath);

                            if ($logoExists) {
                                $logoData = file_get_contents($logoPath);
                                $logoBase64 = base64_encode($logoData);
                                $logoType = mime_content_type($logoPath) ?: 'png';
                            }
                        } catch (\Exception $e) {
                            $logoExists = false;
                        }
                    @endphp

                    @if (isset($logoExists) && $logoExists && !empty($logoBase64))
                        <img src="data:{{ $logoType }};base64,{{ $logoBase64 }}" alt="Logo Kobin">
                    @else
                        <div class="logo-placeholder">
                            LOGO
                        </div>
                    @endif
                </div>

                <!-- TITLE -->
                <div class="title-section">
                    <h1>KOBIN ORIENTATION PROGRAM</h1>
                    <div class="subtitle">{{ $orientation->batch_name ?? 'Batch Name' }}</div>
                </div>

            </div>

            <!-- HEADER RIGHT -->
            <div class="header-right">
                <div><span class="label">Nama :</span> Tim HO</div>
                <div style="margin-top:4px;"><span class="label">Periode :</span> {{ $period ?? '-' }}</div>
            </div>

        </div>

        <!-- ================= PROGRAM INFO ================= -->
        <div class="program-info">

            <div class="left">
                <span>
                    <span class="label">Batch :</span>
                    {{ $batchNumber ?? '-' }}
                </span>
                <span>
                    <span class="label">Lokasi :</span>
                    {{ $plantName ?? '-' }}
                </span>
                <span>
                    <span class="label">Total Peserta :</span>
                    {{ $totalParticipants ?? 0 }} Orang
                </span>
            </div>

            <div class="right">
                <span class="label">Tanggal Cetak :</span>
                {{ now()->format('d F Y H:i') }} WIB
            </div>

        </div>

        <!-- ================= TABLE ================= -->
        <table class="data-table">

            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">NIK</th>
                    <th width="30%">Nama</th>
                    <th width="25%">Jabatan</th>
                    <th width="20%">Departemen</th>
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
                            <strong>Belum ada peserta</strong>
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

        <!-- ================= FOOTER ================= -->
        <div class="footer">
            <p>
                Dokumen ini dicetak dari sistem
                <span>Kobin Orientation</span> •
                {{ now()->format('d F Y H:i') }} WIB
            </p>
        </div>

    </div>

</body>

</html>
