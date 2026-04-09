<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Customer') }}
        </h2>
    </x-slot>

    <div class="px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-2 mb-4 text-sm text-white bg-green-500 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between mb-4">
                <button data-modal-target="add-p2-modal" data-modal-toggle="add-p2-modal"
                    class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Customer
                </button>

                <input type="text" id="search-customer" placeholder="Cari nama customer..."
                    class="w-1/3 px-4 py-2 ml-4 border rounded">
            </div>


            <div id="customer-table">
                @include('admin.customer.partials.table', ['customers' => $customers])
            </div>

        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="add-p2-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow w-[600px]">
            <h3 class="mb-4 text-xl font-semibold">Tambah Customer</h3>
            <form action="{{ route('admin.customer.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        ID_Customer
                        <input type="text" name="id_customer" placeholder="ID Customer"
                            class="w-full p-2 border rounded-lg" required />
                    </div>

                    <div>
                        Nama Customer
                        <input type="text" name="name" placeholder="Nama Customer"
                            class="w-full p-2 border rounded-lg" required />
                    </div>

                    <div>
                        Email
                        <input type="email" name="email" placeholder="Email" class="w-full p-2 border rounded-lg"
                            required />
                    </div>

                    <div>
                        Nomor Whatsapp
                        <input type="text" name="phone" placeholder="Nomor Telepon"
                            class="w-full p-2 border rounded-lg" />
                    </div>

                    <div>
                        Password
                        <input type="password" name="password" placeholder="Password"
                            class="w-full p-2 border rounded-lg" required />
                    </div>
                </div>

                <button type="submit"
                    class="mt-4 w-full text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Customer
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="edit-customer-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow w-[600px]">
            <!-- Header Modal -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">Edit Customer</h3>
                <button type="button" class="text-xl font-bold text-gray-500 hover:text-gray-700"
                    onclick="document.getElementById('edit-customer-modal').classList.add('hidden')">×</button>
            </div>
            <form id="edit-customer-form" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        ID_Customer
                        <input type="text" name="id_customer" id="edit-id_customer" placeholder="ID Customer"
                            class="w-full p-2 border rounded-lg" required />
                    </div>
                    <div>
                        Nama Customer
                        <input type="text" name="name" id="edit-name" placeholder="Nama Customer"
                            class="w-full p-2 border rounded-lg" required />
                    </div>
                    <div>
                        Email
                        <input type="email" name="email" id="edit-email" placeholder="Email"
                            class="w-full p-2 border rounded-lg" required />
                    </div>
                    <div>
                        Nomor Whatsapp
                        <input type="text" name="phone" id="edit-phone" placeholder="Nomor Telepon"
                            class="w-full p-2 border rounded-lg" />
                    </div>
                    <div>
                        Password
                        <input type="password" name="password" placeholder="(Kosongkan jika tidak diubah)"
                            class="w-full p-2 border rounded-lg" />
                    </div>
                </div>

                <button type="submit"
                    class="mt-4 w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div id="delete-confirmation-modal"
        class="fixed inset-0 z-50 items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow w-[400px] text-center">
            <h3 class="mb-4 text-lg font-semibold">Hapus Customer?</h3>
            <p class="mb-6 text-sm text-gray-600">Apakah Anda yakin ingin menghapus customer ini?</p>
            <form id="delete-customer-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-4">
                    <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                        Ya, Hapus
                    </button>
                    <button type="button" onclick="closeModal('delete-confirmation-modal')"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#search-customer').on('input', function() {
            let query = $(this).val();

            $.ajax({
                url: "{{ route('admin.customer') }}",
                type: 'GET',
                data: {
                    search: query
                },
                success: function(data) {
                    $('#customer-table').html(data);
                },
                error: function() {
                    $('#customer-table').html('<p class="p-4 text-red-500">Gagal memuat data</p>');
                }
            });
        });
    </script>

    <script>
        function editCustomer(id) {
            $.get(`/admin/api/customers/${id}`, function(data) {
                $('#edit-id_customer').val(data.id_customer);
                $('#edit-name').val(data.name);
                $('#edit-email').val(data.email);
                $('#edit-phone').val(data.phone);

                $('#edit-customer-form').attr('action', `/admin/customer/${id}`);
                $('#edit-customer-modal').removeClass('hidden').addClass('flex');
            });
        }

        function deleteCustomer(id) {
            $('#delete-customer-form').attr('action', `/admin/customer/${id}`);
            $('#delete-confirmation-modal').removeClass('hidden').addClass('flex');
        }

        function closeModal(modalId) {
            $(`#${modalId}`).addClass('hidden').removeClass('flex');
        }
    </script>
</x-app-layout>
