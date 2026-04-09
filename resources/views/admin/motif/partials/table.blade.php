<table class="min-w-full text-sm text-gray-500">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr class="text-center">
            <th class="px-6 py-3">No</th>
            <th class="px-6 py-3">Nama Merk</th>
            <th class="px-6 py-3">Ukuran</th>
            <th class="px-6 py-3">Nama Motif</th>
            <th class="px-6 py-3">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-center">
        @forelse ($motifs as $index => $motif)
            <tr>
                <td class="px-6 py-3">{{ $motifs->firstItem() + $index }}</td>
                <td class="px-6 py-3">{{ optional($motif->ukuran->merk)->name ?? '-' }}</td>
                <td class="px-6 py-3">{{ optional($motif->ukuran)->name ?? '-' }}</td>
                <td class="px-6 py-3">{{ $motif->name }}</td>
                <td class="px-6 py-3">
                    <button onclick="editProject({{ $motif->id }}, '{{ $motif->ukuran_id }}', '{{ $motif->name }}')" class="text-blue-600 hover:text-blue-800">Edit</button>
                    <button onclick="deleteProject({{ $motif->id }})" class="text-red-600 hover:text-red-800">Hapus</button>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="py-4 text-center">Data tidak ditemukan</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $motifs->links() }}
</div>
