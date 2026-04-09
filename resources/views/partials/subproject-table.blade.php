<table class="min-w-full divide-y divide-gray-200 text-sm">
    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
        <tr>
            <th class="px-6 py-3">Nama PT</th>
            <th class="px-6 py-3">Project</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($subProjects as $sp)
            <tr>
                <td class="px-6 py-4">{{ $sp->nama_pt }}</td>
                <td class="px-6 py-4">{{ $sp->nama_sub_project }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="px-6 py-4 text-center text-gray-500">Tidak ada data ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="p-4">
    {{ $subProjects->links() }}
</div>
