<table class="min-w-full text-sm text-gray-500">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr class="text-center">
            <th class="px-6 py-3">No</th>
            <th class="px-6 py-3">Nama Customer</th>
            <th class="px-6 py-3">Email Customer</th>
            <th class="px-6 py-3">Nomor Customer</th>
            <th class="px-6 py-3">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-center">
        @forelse ($customers as $index => $customer)
            <tr>
                <td class="px-6 py-3">{{ $customers->firstItem() + $index }}</td>
                <td class="px-6 py-3 text-left">{{ $customer->name }}</td>
                <td class="px-6 py-3">{{ $customer->email }}</td>
                <td class="px-6 py-3">{{ $customer->phone }}</td>
                <td class="px-6 py-3">
                    <button onclick="editCustomer({{ $customer->id }})" class="text-blue-600 hover:text-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <!-- Edit Path -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5m-5-11l5 5M18 2l4 4m-2 2L9 19H4v-5L16 4z" />
                        </svg>
                    </button>
                    <button onclick="deleteCustomer({{ $customer->id }})" class="text-red-600 hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <!-- Trash Icon Path -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4m16 0h-4" />
                        </svg>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-4 text-gray-400">Data tidak ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $customers->links() }}
</div>
