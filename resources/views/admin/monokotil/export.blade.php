<table>
    <thead>
        <tr>
            <th rowspan="4">DEPARTEMENT</th>
            <th rowspan="4">NIK</th>
            <th rowspan="4">NAMA</th>
            @foreach ($projects as $project)
                @php
                    $subs = $subprojects->where('project_id', $project->id);
                    $colspan = $subs->count() * 5;
                @endphp
                <th colspan="{{ $colspan }}">{{ $project->name }}</th>
            @endforeach
            <th rowspan="3" colspan="5">CONTROL</th>
            <th rowspan="4">TOTAL JAM</th>
        </tr>
        <tr>
            @foreach ($projects as $project)
                @foreach ($subprojects->where('project_id', $project->id) as $sub)
                    <th colspan="5">{{ $sub->nama_pt }}</th>
                @endforeach
            @endforeach
        </tr>
        <tr>
            @foreach ($projects as $project)
                @foreach ($subprojects->where('project_id', $project->id) as $sub)
                    <th colspan="5">{{ $sub->nama_sub_project }}</th>
                @endforeach
            @endforeach
        </tr>
        <tr>
            @foreach ($projects as $project)
                @foreach ($subprojects->where('project_id', $project->id) as $sub)
                    @for ($w = 1; $w <= 5; $w++)
                        <th>W{{ $w }}</th>
                    @endfor
                @endforeach
            @endforeach
            @for ($w = 1; $w <= 5; $w++)
                <th>W{{ $w }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @php $grouped = $users->groupBy(fn($u) => $u->departement->name ?? 'LAINNYA'); @endphp
        @foreach ($grouped as $dept => $employees)
            @foreach ($employees as $i => $user)
                <tr>
                    @if ($i === 0)
                        <td rowspan="{{ $employees->count() }}">{{ $dept }}</td>
                    @endif
                    <td>{{ $user->nik }}</td>
                    <td>{{ $user->name }}</td>

                    @foreach ($projects as $project)
                        @foreach ($subprojects->where('project_id', $project->id) as $sub)
                            @for ($w = 1; $w <= 5; $w++)
                                <td>
                                    {{ $groupedActivities[$user->id][$project->id][$sub->id]['w' . $w] ?? '' }}
                                </td>
                            @endfor
                        @endforeach
                    @endforeach

                    {{-- CONTROL W1–W5 --}}
                    @for ($w = 1; $w <= 5; $w++)
                        @php
                            $control = 0;
                            if (isset($groupedActivities[$user->id])) {
                                foreach ($groupedActivities[$user->id] as $pj) {
                                    foreach ($pj as $sb) {
                                        $control += $sb['w' . $w] ?? 0;
                                    }
                                }
                            }
                        @endphp
                        <td>{{ $control }}</td>
                    @endfor

                    {{-- TOTAL JAM --}}
                    @php
                        $total = 0;
                        foreach ($groupedActivities[$user->id] ?? [] as $pj) {
                            foreach ($pj as $sb) {
                                foreach ($sb as $jam) {
                                    $total += $jam;
                                }
                            }
                        }
                    @endphp
                    <td>{{ $total }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
