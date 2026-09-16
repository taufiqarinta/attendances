<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kobin Orientation Program</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10px;
            color: #1F2937;
            background: #fff;
            padding: 14px 18px;
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
            padding-bottom: 12px;
            margin-bottom: 12px;
            min-height: 68px;
            gap: 16px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            /* jarak logo ke title — kecil biar rapat */
            flex: 1;
            min-width: 0;
        }

        .logo {
            width: 62px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 4px;
            /* jarak tambahan tipis */
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            width: 58px;
            height: 58px;
            border: 1px dashed #7A1113;
            color: #7A1113;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 10px;
            background: #FEF2F2;
            border-radius: 4px;
            font-weight: bold;
        }

        .title-section {
            flex: 1;
            min-width: 0;
            padding-left: 4px;
            /* sedikit padding biar rapi */
        }

        .title-section h1 {
            font-size: 22px;
            font-weight: bold;
            color: #7A1113;
            letter-spacing: 2px;
            margin: 0;
            line-height: 1.1;
        }

        .title-section .subtitle {
            font-size: 11px;
            color: #6B7280;
            margin-top: 4px;
            font-style: italic;
        }

        .header-right {
            text-align: right;
            font-size: 9px;
            color: #374151;
            min-width: 150px;
            flex-shrink: 0;
            border-left: 2px solid #FEE2E2;
            padding-left: 12px;
        }

        .header-right .row {
            margin-bottom: 3px;
            line-height: 1.3;
        }

        .header-right .label {
            font-weight: bold;
            color: #7A1113;
        }

        /* ================= INFO ================= */
        .program-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: #FEF2F2;
            border-left: 3px solid #7A1113;
            border-radius: 2px;
            font-size: 9px;
        }

        .program-info .left {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }

        .program-info .label {
            font-weight: bold;
            color: #7A1113;
        }

        .program-info .right {
            color: #6B7280;
        }

        /* ================= TABLE ================= */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .schedule-table th {
            background: #7A1113;
            color: #fff;
            border: 1px solid #4A0A0C;
            padding: 6px 4px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .schedule-table td {
            border: 1px solid #D1D5DB;
            padding: 5px 4px;
            font-size: 9px;
            vertical-align: middle;
            color: #1F2937;
        }

        .bg-gray {
            background: #F9FAFB;
        }

        .bg-white {
            background: #ffffff;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .date-cell {
            text-align: center;
            vertical-align: middle;
            font-size: 9px;
            width: 13%;
            font-weight: bold;
            color: #7A1113;
            background: #FEF2F2;
        }

        .place-cell {
            text-align: center;
            vertical-align: middle;
            font-size: 9px;
            width: 13%;
            font-weight: bold;
            color: #1F2937;
        }

        .break-cell {
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            background: #FEE2E2;
            color: #7A1113;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .time-cell {
            text-align: center;
            width: 12%;
            font-weight: 500;
        }

        .activity-cell {
            padding-left: 8px;
            width: 30%;
            font-weight: 500;
        }

        .pic-cell {
            padding-left: 8px;
            width: 18%;
        }

        .position-cell {
            padding-left: 8px;
            width: 14%;
            color: #6B7280;
            font-style: italic;
        }

        /* ================= PARTICIPANTS ================= */
        .participant-section {
            margin-top: 22px;
            page-break-inside: avoid;
        }

        .section-title {
            border-left: 4px solid #7A1113;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-left: 10px;
            color: #7A1113;
            letter-spacing: 0.5px;
        }

        .participant-table {
            width: 100%;
            border-collapse: collapse;
        }

        .participant-table th {
            background: #7A1113;
            border: 1px solid #4A0A0C;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            padding: 7px 6px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .participant-table td {
            border: 1px solid #D1D5DB;
            font-size: 10px;
            padding: 6px 8px;
            color: #1F2937;
        }

        .participant-table tbody tr:nth-child(even) td {
            background: #F9FAFB;
        }

        /* ================= FOOTER ================= */
        .footer {
            margin-top: 22px;
            padding-top: 12px;
            border-top: 2px solid #7A1113;
            text-align: center;
            font-size: 9px;
            color: #6B7280;
        }

        .footer span {
            font-weight: bold;
            color: #7A1113;
        }
    </style>

</head>

<body>

    <div class="container">
        {{-- ================= HEADER ================= --}}
        <div class="header">

            <div class="header-left">

                {{-- LOGO --}}
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
                        <div class="logo-placeholder">LOGO</div>
                    @endif
                </div>

                {{-- TITLE — tepat di sebelah kanan logo --}}
                <div class="title-section">
                    <h1>KOBIN ORIENTATION PROGRAM</h1>
                    <div class="subtitle">{{ $orientation->batch_name ?? 'Batch Name' }}</div>
                </div>

            </div>

            {{-- HEADER RIGHT --}}
            <div class="header-right">
                <div class="row"><span class="label">Nama :</span> Tim HO</div>
                <div class="row"><span class="label">Periode :</span> {{ $period ?? '-' }}</div>
                <div class="row"><span class="label">Lokasi :</span> {{ $plantName ?? '-' }}</div>
            </div>

        </div>


        {{-- ================= PROGRAM INFO ================= --}}
        <div class="program-info">

            <div class="left">
                <span>
                    <span class="label">Total Peserta :</span>
                    {{ $totalParticipants ?? 0 }} Orang
                </span>
                <span>
                    <span class="label">Total Kegiatan :</span>
                    {{ collect($groupedActivities ?? [])->sum(fn($g) => count($g['rows'] ?? [])) }} Materi
                </span>
            </div>

            <div class="right">
                <span class="label">Tanggal Cetak :</span>
                {{ now()->format('d F Y H:i') }} WIB
            </div>

        </div>


        {{-- ================= TABLE ================= --}}
        <table class="schedule-table">

            <thead>
                <tr>
                    <th width="13%">Tanggal</th>
                    <th width="12%">Waktu</th>
                    <th width="13%">Tempat</th>
                    <th width="30%">Kegiatan</th>
                    <th width="18%">PIC</th>
                    <th width="14%">Jabatan</th>
                </tr>
            </thead>

            <tbody>
                @forelse($groupedActivities ?? [] as $group)

                    @php
                        $rows = $group['rows'] ?? [];
                        $rowCount = count($rows);
                    @endphp

                    @foreach ($rows as $index => $row)
                        @php
                            $activity = trim(strtolower($row['activity'] ?? ''));
                            $isBreak = str_contains($activity, 'istirahat') || str_contains($activity, 'break');
                            $isLunch = str_contains($activity, 'makan') || str_contains($activity, 'lunch');
                            $isBreakTime = $isBreak || $isLunch;

                            $bgClass = $index % 2 == 0 ? 'bg-white' : 'bg-gray';
                        @endphp

                        <tr>

                            {{-- TANGGAL (ROWSPAN) --}}
                            @if ($index == 0)
                                <td rowspan="{{ $rowCount }}" class="date-cell">
                                    {{ $group['date_formatted'] ?? '-' }}
                                </td>
                            @endif

                            {{-- WAKTU --}}
                            <td class="time-cell {{ $bgClass }}">
                                {{ $row['time'] ?? '-' }}
                            </td>

                            {{-- TEMPAT (ROWSPAN) --}}
                            @if ($index == 0)
                                <td rowspan="{{ $rowCount }}" class="place-cell">
                                    {{ $plantName ?? '-' }}
                                </td>
                            @endif

                            {{-- ISTIRAHAT / BREAK --}}
                            @if ($isBreakTime)
                                <td colspan="3" class="break-cell">
                                    {{ $isLunch ? 'ISTIRAHAT MAKAN' : 'ISTIRAHAT' }}
                                </td>
                            @else
                                {{-- Kegiatan --}}
                                <td class="activity-cell text-left {{ $bgClass }}">
                                    {{ $row['activity'] ?? '-' }}
                                </td>

                                {{-- PIC --}}
                                <td class="pic-cell text-left {{ $bgClass }}">
                                    {{ $row['pic_name'] ?? '-' }}
                                </td>

                                {{-- Jabatan --}}
                                <td class="position-cell text-left {{ $bgClass }}">
                                    {{ $row['position'] ?? '-' }}
                                </td>
                            @endif

                        </tr>
                    @endforeach

                @empty

                    <tr>
                        <td colspan="6" style="text-align:center;padding:30px;color:#999;">
                            <strong>Belum ada jadwal orientation</strong>
                        </td>
                    </tr>

                @endforelse
            </tbody>

        </table>

        {{-- ================= PARTICIPANTS ================= --}}
        <div class="participant-section">
            <div class="section-title">Daftar Peserta Orientation</div>

            <table class="participant-table">
                <thead>
                    <tr>
                        <th width="6%">No</th>
                        <th width="18%">NIK</th>
                        <th width="31%">Nama</th>
                        <th width="25%">Jabatan</th>
                        <th width="20%">Departemen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participants ?? [] as $index => $participant)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ is_array($participant) ? $participant['nik'] ?? '-' : $participant }}</td>
                            <td>{{ is_array($participant) ? $participant['nama'] ?? '-' : '-' }}</td>
                            <td>{{ is_array($participant) ? $participant['jabatan'] ?? '-' : '-' }}</td>
                            <td>{{ is_array($participant) ? $participant['dept'] ?? '-' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding:20px;color:#999;">
                                <strong>Belum ada peserta</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= FOOTER ================= --}}
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
