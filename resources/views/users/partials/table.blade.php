<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full text-sm text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr class="text-center">
                <th class="px-6 py-3">No</th>
                <th class="px-6 py-3">Username</th>
                <th class="px-6 py-3">Nama User</th>
                <th class="px-6 py-3">Brand</th>
                <th class="px-6 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @forelse ($users as $index => $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $users->firstItem() + $index }}</td>
                    <td class="px-6 py-4">{{ $user->id_customer }}</td>
                    <td class="px-6 py-4 text-left">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->brands }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <button onclick="editUser({{ $user->id }})" 
                                class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded"
                                title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5m-5-11l5 5M18 2l4 4m-2 2L9 19H4v-5L16 4z" />
                                </svg>
                            </button>
                            
                            <!-- <button onclick="toggleStatus({{ $user->id }})" 
                                class="p-1 {{ $user->status == 1 ? 'text-yellow-600 hover:text-yellow-800 hover:bg-yellow-100' : 'text-green-600 hover:text-green-800 hover:bg-green-100' }} rounded"
                                title="{{ $user->status == 1 ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    @if($user->status == 1)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    @endif
                                </svg>
                            </button> -->
                            
                            <!-- <button onclick="deleteUser({{ $user->id }})" 
                                class="p-1 text-red-600 hover:text-red-800 hover:bg-red-100 rounded"
                                title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4m16 0h-4" />
                                </svg>
                            </button> -->
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-8 text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Tidak ada data user ditemukan</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>