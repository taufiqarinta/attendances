<table>
    <thead>
        <tr>
            <th>Merk</th>
            <th>Ukuran</th>
            <th>Motif</th>
            @foreach ($bulanHeader as $bulan)
                <th>{{ $bulan }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td>{{ $row['merk'] }}</td>
                <td>{{ $row['ukuran'] }}</td>
                <td>{{ $row['motif'] }}</td>
                @foreach ($bulanHeader as $bulan)
                    <td>{{ $row['bulan'][$bulan] ?? 0 }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
