<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <style>
        body::after {
            content: "";
            position: fixed;
            right: 20px;
            bottom: 20px;
            width: 240px;
            height: 240px;
            background-image: url('{{ asset('corner.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.1;
            pointer-events: none;
            z-index: 5;  /* z-index sedang */
        }
        
        /* Pastikan tabel memiliki z-index lebih tinggi */
        .max-w-6xl {
            position: relative;
            z-index: 10;
            background: transparent; /* Tetap transparan */
        }
        
        /* Atau jika perlu, beri background putih pada tabel */
        table {
            background: white;
            position: relative;
            z-index: 15;
        }
    </style>

    <div class="px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-2 mb-4 text-sm text-white bg-green-500 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="p-2 mb-4 text-sm text-white bg-red-500 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <button data-modal-target="add-user-modal" data-modal-toggle="add-user-modal"
                        class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                        Tambah User
                    </button>
                </div>

                <input type="text" id="search-user" placeholder="Cari user..."
                    class="w-1/3 px-4 py-2 ml-4 border rounded" value="{{ request('search') }}">
            </div>

            <div id="user-table">
                @include('users.partials.table', ['users' => $users])
            </div>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div id="add-user-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow w-[600px] max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">Tambah User</h3>
                <button type="button" class="text-xl font-bold text-gray-500 hover:text-gray-700"
                    onclick="closeModal('add-user-modal')">×</button>
            </div>
            
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium">Username *</label>
                        <input type="text" name="id_customer" placeholder="ID Customer"
                            class="w-full p-2 border rounded-lg @error('id_customer') border-red-500 @enderror" required />
                        @error('id_customer')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Nama User *</label>
                        <input type="text" name="name" placeholder="Nama User"
                            class="w-full p-2 border rounded-lg @error('name') border-red-500 @enderror" required />
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Password *</label>
                        <input type="password" name="password" placeholder="Password"
                            class="w-full p-2 border rounded-lg @error('password') border-red-500 @enderror" required />
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block mb-1 text-sm font-medium">Brand</label>
                        <select name="merks[]" multiple class="w-full p-2 border rounded-lg">
                            @foreach ($merks as $merk)
                                <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-gray-500">Tahan CTRL (Windows) / CMD (Mac) untuk memilih lebih dari satu.</small>
                        @error('merks')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit"
                    class="mt-4 w-full text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah User
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="edit-user-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow w-[600px] max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">Edit User</h3>
                <button type="button" class="text-xl font-bold text-gray-500 hover:text-gray-700"
                    onclick="closeModal('edit-user-modal')">×</button>
            </div>
            
            <form id="edit-user-form" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium">Username *</label>
                        <input type="text" name="id_customer" id="edit-id_customer" placeholder="ID Customer"
                            class="w-full p-2 border rounded-lg" readonly />
                    </div>
                    
                    <div>
                        <label class="block mb-1 text-sm font-medium">Nama User *</label>
                        <input type="text" name="name" id="edit-name" placeholder="Nama User"
                            class="w-full p-2 border rounded-lg" required />
                    </div>
                    
                    <div>
                        <label class="block mb-1 text-sm font-medium">Password</label>
                        <input type="password" name="password" placeholder="(Kosongkan jika tidak diubah)"
                            class="w-full p-2 border rounded-lg" />
                    </div>

                    <div class="col-span-2">
                        <label class="block mb-1 text-sm font-medium">Brand</label>
                        <select name="merks[]" id="edit-merks" multiple class="w-full p-2 border rounded-lg">
                            @foreach ($merks as $merk)
                                <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-gray-500">Tahan CTRL (Windows) / CMD (Mac) untuk memilih lebih dari satu.</small>
                    </div>
                </div>

                <button type="submit"
                    class="mt-4 w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Hapus User -->
    <div id="delete-user-modal"
        class="fixed inset-0 z-50 items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow w-[400px] text-center">
            <h3 class="mb-4 text-lg font-semibold">Hapus User?</h3>
            <p class="mb-6 text-sm text-gray-600">Apakah Anda yakin ingin menghapus user ini? Aksi ini tidak dapat dibatalkan.</p>
            <form id="delete-user-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-4">
                    <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                        Ya, Hapus
                    </button>
                    <button type="button" onclick="closeModal('delete-user-modal')"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function editUser(id) {
            $.get(`{{ url('users') }}/${id}/edit`, function(data) {
                $('#edit-id_customer').val(data.id_customer);
                $('#edit-name').val(data.name);
                $('#edit-email').val(data.email || '');
                $('#edit-phone').val(data.phone || '');

                // Reset and set selected merks
                $('#edit-merks option').prop('selected', false);
                if (data.merks && data.merks.length > 0) {
                    data.merks.forEach(function(merkId) {
                        $(`#edit-merks option[value="${merkId}"]`).prop('selected', true);
                    });
                }

                $('#edit-user-form').attr('action', `{{ url('users') }}/${id}`);
                $('#edit-user-modal').removeClass('hidden').addClass('flex');
            }).fail(function() {
                alert('Gagal memuat data user');
            });
        }

        function deleteUser(id) {
            $('#delete-user-form').attr('action', `{{ url('users') }}/${id}`);
            $('#delete-user-modal').removeClass('hidden').addClass('flex');
        }

        function toggleStatus(id) {
            if (confirm('Apakah Anda yakin ingin mengubah status user ini?')) {
                $.ajax({
                    url: `{{ url('users') }}/${id}/toggle-status`,
                    type: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function() {
                        location.reload();
                    },
                    error: function() {
                        alert('Gagal mengubah status user');
                    }
                });
            }
        }

        function closeModal(modalId) {
            $(`#${modalId}`).addClass('hidden').removeClass('flex');
        }

        // Live search
        $('#search-user').on('input', function() {
            let query = $(this).val();
            
            $.ajax({
                url: "{{ route('users.index') }}",
                type: 'GET',
                data: {
                    search: query
                },
                success: function(data) {
                    $('#user-table').html(data);
                },
                error: function() {
                    $('#user-table').html('<p class="p-4 text-red-500">Gagal memuat data</p>');
                }
            });
        });

        // Close modal on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('[id$="-modal"]').addClass('hidden').removeClass('flex');
            }
        });

        // Close modal when clicking outside
        $(document).on('click', function(e) {
            if ($(e.target).hasClass('bg-opacity-50')) {
                $(e.target).addClass('hidden').removeClass('flex');
            }
        });
    </script>
</x-app-layout>