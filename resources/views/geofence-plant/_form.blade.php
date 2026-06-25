<style>
    /* Batasi z-index Leaflet agar tidak melebihi navbar */
    .leaflet-control-container,
    .leaflet-top,
    .leaflet-bottom,
    .leaflet-pane {
        z-index: 40 !important;
    }
    
    /* Pastikan navbar tetap di atas */
    nav {
        z-index: 50 !important;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Kode Plant <span class="text-red-600">*</span>
        </label>
        <input type="text" name="plant" value="{{ old('plant', $geofencePlant->plant) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 @error('plant') border-red-500 @enderror">
        @error('plant')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Nama Plant <span class="text-red-600">*</span>
        </label>
        <input type="text" name="plant_name" value="{{ old('plant_name', $geofencePlant->plant_name) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 @error('plant_name') border-red-500 @enderror">
        @error('plant_name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Jenis Absensi <span class="text-red-600">*</span>
        </label>
        <select name="type" required
            class="p-2 block mt-1 w-full h-[42px] rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
            <option value="">Pilih Jenis</option>
            @foreach($types as $type)
                <option value="{{ $type }}" {{ old('type', $geofencePlant->type) === $type ? 'selected' : '' }}>
                    {{ $type == 'in' ? 'Masuk (IN)' : 'Pulang (OUT)' }}
                </option>
            @endforeach
        </select>
        @error('type')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Radius (meter) <span class="text-red-600">*</span>
        </label>
        <input type="number" step="0.01" min="0" name="radius" id="radiusInput"
            value="{{ old('radius', $geofencePlant->radius) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 @error('radius') border-red-500 @enderror">
        @error('radius')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Peta untuk memilih koordinat -->
<div class="mb-4 p-4 border rounded-lg bg-gray-50">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Titik Lokasi Plant <span class="text-red-600">*</span>
    </label>
    <p class="text-xs text-gray-500 mb-3">Cari alamat di bawah, atau klik langsung pada peta untuk menentukan titik koordinat.</p>

    <!-- Search alamat dengan autocomplete, mirip pencarian lokasi di Gojek/Grab -->
    <div class="relative mb-3">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <input type="text" id="addressSearchInput" autocomplete="off"
                placeholder="Cari nama jalan, gedung, atau alamat..."
                class="block w-full pl-9 pr-9 py-2 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
            <svg id="addressSearchSpinner" class="hidden animate-spin h-4 w-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Dropdown hasil pencarian -->
        <div id="addressSearchResults"
            class="hidden absolute z-[1000] mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-64 overflow-y-auto">
        </div>
    </div>

    <div id="locationPickerMap" style="height: 300px; border-radius: 8px;" class="border border-gray-300 mb-3"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500">Latitude</label>
            <input type="text" name="latitude" id="latitudeInput"
                value="{{ old('latitude', $geofencePlant->latitude) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 @error('latitude') border-red-500 @enderror">
            @error('latitude')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500">Longitude</label>
            <input type="text" name="longitude" id="longitudeInput"
                value="{{ old('longitude', $geofencePlant->longitude) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 @error('longitude') border-red-500 @enderror">
            @error('longitude')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- Exclude Department -->
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Exclude Department</label>
    <p class="text-xs text-gray-500 mb-2">Departemen yang DIKECUALIKAN dari validasi radius ini. Tekan Enter untuk menambah.</p>
    <div id="excludeDepartmentTags" class="flex flex-wrap gap-2 p-2 border rounded-md min-h-[44px] bg-white">
        <input type="text" id="excludeDepartmentTagInput" placeholder="Ketik nama departemen lalu Enter..."
            class="flex-1 min-w-[140px] border-0 focus:ring-0 text-sm py-1">
    </div>
    <div id="excludeDepartmentHiddenInputs"></div>
</div>

<!-- Exclude NIK -->
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Exclude NIK</label>
    <p class="text-xs text-gray-500 mb-2">NIK karyawan yang DIKECUALIKAN dari validasi radius ini. Tekan Enter untuk menambah.</p>
    <div id="excludeNikTags" class="flex flex-wrap gap-2 p-2 border rounded-md min-h-[44px] bg-white">
        <input type="text" id="excludeNikTagInput" placeholder="Ketik NIK lalu Enter..."
            class="flex-1 min-w-[140px] border-0 focus:ring-0 text-sm py-1">
    </div>
    <div id="excludeNikHiddenInputs"></div>
</div>

<!-- Status -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Status <span class="text-red-600">*</span>
    </label>
    <div class="flex gap-4">
        <label class="inline-flex items-center">
            <input type="radio" name="status" value="1"
                {{ old('status', $geofencePlant->status ?? 1) == 1 ? 'checked' : '' }}
                class="text-red-600 focus:ring-red-500">
            <span class="ml-2 text-sm text-gray-700">Aktif</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="status" value="0"
                {{ old('status', $geofencePlant->status ?? 1) == 0 ? 'checked' : '' }}
                class="text-red-600 focus:ring-red-500">
            <span class="ml-2 text-sm text-gray-700">Nonaktif</span>
        </label>
    </div>
    @error('status')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
    // ============================================================
    // Tag input sederhana untuk exclude_department & exclude_nik
    // ------------------------------------------------------------
    // Setiap tag yang ditambahkan akan membuat <input type="hidden">
    // bernama exclude_department[] / exclude_nik[] supaya saat form
    // di-submit, Laravel menerimanya sebagai array (sesuai validasi
    // di GeofencePlantRequest), lalu di controller di-convert ke
    // string dipisah koma sebelum disimpan ke database.
    // ============================================================
    function setupTagInput(containerId, inputId, hiddenContainerId, fieldName, initialValues) {
        const container = document.getElementById(containerId);
        const input = document.getElementById(inputId);
        const hiddenContainer = document.getElementById(hiddenContainerId);

        function renderTag(value) {
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-medium px-2 py-1 rounded-full';
            tag.innerHTML = `${value} <button type="button" class="ml-1 text-red-500 hover:text-red-800 font-bold">&times;</button>`;

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `${fieldName}[]`;
            hiddenInput.value = value;
            hiddenContainer.appendChild(hiddenInput);

            tag.querySelector('button').addEventListener('click', () => {
                tag.remove();
                hiddenInput.remove();
            });

            container.insertBefore(tag, input);
        }

        initialValues.forEach((value) => {
            if (value) renderTag(value);
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = input.value.trim();
                if (value) {
                    renderTag(value);
                    input.value = '';
                }
            }
        });
    }

    setupTagInput(
        'excludeDepartmentTags', 'excludeDepartmentTagInput', 'excludeDepartmentHiddenInputs',
        'exclude_department',
        {{ json_encode(old('exclude_department', $geofencePlant->exclude_department_array ?? [])) }}
    );

    setupTagInput(
        'excludeNikTags', 'excludeNikTagInput', 'excludeNikHiddenInputs',
        'exclude_nik',
        {{ json_encode(old('exclude_nik', $geofencePlant->exclude_nik_array ?? [])) }}
    );

    // ============================================================
    // Leaflet map untuk memilih titik koordinat plant
    // ============================================================
    function initLocationPickerMap() {
        const latInput = document.getElementById('latitudeInput');
        const lngInput = document.getElementById('longitudeInput');
        const radiusInput = document.getElementById('radiusInput');

        // Default ke koordinat existing, atau ke Surabaya jika data baru/kosong
        const initialLat = parseFloat(latInput.value) || -7.2575;
        const initialLng = parseFloat(lngInput.value) || 112.7521;

        const map = L.map('locationPickerMap').setView([initialLat, initialLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;
        let circle = null;

        function setPoint(lat, lng) {
            latInput.value = lat.toFixed(8);
            lngInput.value = lng.toFixed(8);

            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    setPoint(pos.lat, pos.lng);
                });
            }

            updateCircle();
        }

        function updateCircle() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            const radius = parseFloat(radiusInput.value) || 0;

            if (isNaN(lat) || isNaN(lng)) return;

            if (circle) {
                circle.setLatLng([lat, lng]);
                circle.setRadius(radius);
            } else {
                circle = L.circle([lat, lng], {
                    radius: radius,
                    color: '#ef4444',
                    fillColor: '#ef4444',
                    fillOpacity: 0.15,
                    weight: 2
                }).addTo(map);
            }
        }

        // Jika sudah ada koordinat tersimpan (mode edit), tampilkan marker awal
        if (latInput.value && lngInput.value) {
            setPoint(parseFloat(latInput.value), parseFloat(lngInput.value));
        }

        map.on('click', (e) => {
            setPoint(e.latlng.lat, e.latlng.lng);
        });

        // Update lingkaran radius saat user mengetik manual di field radius
        radiusInput.addEventListener('input', updateCircle);

        // Update marker saat user mengetik manual di field lat/long
        latInput.addEventListener('change', () => {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                map.setView([lat, lng], map.getZoom());
                setPoint(lat, lng);
            }
        });
        lngInput.addEventListener('change', () => {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                map.setView([lat, lng], map.getZoom());
                setPoint(lat, lng);
            }
        });

        // Expose fungsi yang dibutuhkan fitur search alamat (di bawah)
        return {
            setPoint: setPoint,
            flyTo: (lat, lng, zoom) => map.setView([lat, lng], zoom || 17),
        };
    }

    const locationPickerMapController = initLocationPickerMap();

    // ============================================================
    // Search alamat dengan autocomplete (mirip pencarian lokasi di
    // Gojek/Grab/Uber) — memakai Nominatim (OpenStreetMap), gratis
    // dan tanpa API key. Saat user pilih salah satu hasil, peta akan
    // pan ke lokasi tersebut dan field latitude/longitude otomatis
    // terisi dari koordinat hasil pencarian.
    // ------------------------------------------------------------
    // Catatan: Nominatim membatasi rate ±1 request/detik untuk
    // pemakaian wajar (usage policy). Debounce 400ms di bawah ini
    // membantu menghindari permintaan berlebihan saat user mengetik.
    // ============================================================
    (function initAddressSearch(mapController) {
        const searchInput = document.getElementById('addressSearchInput');
        const resultsBox = document.getElementById('addressSearchResults');
        const spinner = document.getElementById('addressSearchSpinner');

        let debounceTimer = null;
        let activeController = null; // AbortController utk request yg sedang berjalan

        function hideResults() {
            resultsBox.classList.add('hidden');
            resultsBox.innerHTML = '';
        }

        function showLoading(isLoading) {
            spinner.classList.toggle('hidden', !isLoading);
        }

        async function searchAddress(query) {
            if (activeController) activeController.abort();
            activeController = new AbortController();

            showLoading(true);

            try {
                // Bias hasil ke area Indonesia (countrycodes=id) — hapus parameter
                // ini jika plant Anda ada di luar Indonesia juga.
                const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=6&countrycodes=id&q=${encodeURIComponent(query)}`;

                const response = await fetch(url, {
                    signal: activeController.signal,
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('Gagal mencari alamat');

                const results = await response.json();
                renderResults(results);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('❌ Gagal mencari alamat:', error);
                    resultsBox.innerHTML = `<div class="px-3 py-2 text-sm text-gray-500">Gagal memuat hasil pencarian.</div>`;
                    resultsBox.classList.remove('hidden');
                }
            } finally {
                showLoading(false);
            }
        }

        function renderResults(results) {
            if (!results || results.length === 0) {
                resultsBox.innerHTML = `<div class="px-3 py-2 text-sm text-gray-500">Alamat tidak ditemukan.</div>`;
                resultsBox.classList.remove('hidden');
                return;
            }

            resultsBox.innerHTML = '';

            results.forEach((place) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'w-full text-left px-3 py-2 hover:bg-red-50 text-sm border-b last:border-b-0 border-gray-100 flex items-start gap-2';
                item.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 mt-0.5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-700">${escapeHtml(place.display_name)}</span>
                `;

                item.addEventListener('click', () => {
                    const lat = parseFloat(place.lat);
                    const lng = parseFloat(place.lon);

                    mapController.flyTo(lat, lng, 17);
                    mapController.setPoint(lat, lng);

                    searchInput.value = place.display_name;
                    hideResults();
                });

                resultsBox.appendChild(item);
            });

            resultsBox.classList.remove('hidden');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim();

            clearTimeout(debounceTimer);

            if (query.length < 3) {
                hideResults();
                return;
            }

            debounceTimer = setTimeout(() => searchAddress(query), 400);
        });

        // Sembunyikan dropdown saat klik di luar search box
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                hideResults();
            }
        });

        // Tekan Enter -> langsung cari tanpa menunggu debounce
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(debounceTimer);
                const query = searchInput.value.trim();
                if (query.length >= 3) searchAddress(query);
            }
        });
    })(locationPickerMapController);
</script>