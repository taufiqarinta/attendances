     <div x-data="plantDropdown()" x-init="initPlants({{ json_encode($plants) }})" class="relative">
         <label class="mb-1.5 block text-xs font-semibold text-gray-700">Plant</label>

         {{-- Tombol Dropdown --}}
         <button type="button" @click="toggleDropdown()"
             class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 transition hover:border-red-300 hover:bg-red-50"
             :class="isOpen ? 'border-red-400 ring-2 ring-red-100' : ''">
             <div class="flex items-center gap-3">
                 <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             d="M19 11H5m14-4H5m14 8H5" />
                     </svg>
                 </div>
                 <div class="text-left">
                     <div class="text-sm font-medium text-gray-800"
                         x-text="selectedPlant ? selectedPlant.name_plant : 'Pilih Plant'"></div>
                     <div class="text-xs text-gray-500"
                         x-text="selectedPlant ? 'Kode: ' + selectedPlant.code : 'Pilih plant yang tersedia'">
                     </div>
                 </div>
             </div>
             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 transition-transform duration-200"
                 :class="isOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
             </svg>
         </button>

         {{-- Dropdown --}}
         <div x-show="isOpen" @click.away="closeDropdown()" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
             class="absolute left-0 right-0 z-50 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden"
             style="box-shadow: 0 20px 60px rgba(0,0,0,0.15);">

             {{-- Search Input --}}
             <div class="p-3 border-b border-gray-100">
                 <div class="relative">
                     <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             d="M21 21l-5.2-5.2m1.7-5.3a7 7 0 11-14 0a7 7 0 0114 0z" />
                     </svg>
                     <input type="text" x-model="searchQuery" @input="filterPlants()" placeholder="Cari plant..."
                         class="w-full rounded-lg border border-gray-200 pl-9 pr-4 py-2 text-sm focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:outline-none transition">
                     <button x-show="searchQuery" @click="clearSearch()"
                         class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                         <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M6 18L18 6M6 6l12 12" />
                         </svg>
                     </button>
                 </div>
             </div>

             {{-- List Plant --}}
             <div class="max-h-60 overflow-y-auto">
                 <template x-for="(plant, index) in filteredPlants" :key="index">
                     <button @click="selectPlant(plant)"
                         class="flex w-full items-center gap-3 px-4 py-3 hover:bg-red-50 transition group"
                         :class="selectedPlant && selectedPlant.id === plant.id ? 'bg-red-50/50' : ''">
                         <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl"
                             :class="selectedPlant && selectedPlant.id === plant.id ?
                                 'bg-red-500 text-white' :
                                 'bg-gray-100 text-gray-600 group-hover:bg-red-100 group-hover:text-red-600'">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                     d="M19 11H5m14-4H5m14 8H5" />
                             </svg>
                         </div>
                         <div class="flex-1 text-left">
                             <div class="text-sm font-medium text-gray-800" x-text="plant.name_plant">
                             </div>
                             <div class="text-xs text-gray-500" x-text="'Kode: ' + plant.code"></div>
                         </div>
                         <svg x-show="selectedPlant && selectedPlant.id === plant.id"
                             class="h-5 w-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                         </svg>
                     </button>
                 </template>

                 {{-- No Results --}}
                 <div x-show="filteredPlants.length === 0" class="px-4 py-8 text-center">
                     <svg class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                     </svg>
                     <p class="text-sm text-gray-500">Tidak ada plant ditemukan</p>
                     <p class="text-xs text-gray-400 mt-1">Coba kata kunci lainnya</p>
                 </div>
             </div>
         </div>
     </div>

     {{-- Script Plant Dropdown --}}
     <script>
         function plantDropdown() {
             return {
                 isOpen: false,
                 searchQuery: '',
                 selectedPlant: null,
                 plants: [],
                 filteredPlants: [],

                 initPlants(plants) {
                     this.plants = plants;
                     this.filteredPlants = plants;
                 },

                 toggleDropdown() {
                     this.isOpen = !this.isOpen;
                     if (this.isOpen) {
                         this.filteredPlants = this.plants;
                         this.searchQuery = '';
                     }
                 },

                 closeDropdown() {
                     this.isOpen = false;
                 },

                 filterPlants() {
                     if (!this.searchQuery.trim()) {
                         this.filteredPlants = this.plants;
                         return;
                     }

                     const query = this.searchQuery.toLowerCase().trim();
                     this.filteredPlants = this.plants.filter(plant =>
                         plant.name_plant.toLowerCase().includes(query) ||
                         plant.code.toLowerCase().includes(query)
                     );
                 },

                 clearSearch() {
                     this.searchQuery = '';
                     this.filterPlants();
                 },

                 selectPlant(plant) {
                     this.selectedPlant = plant;
                     this.isOpen = false;
                     console.log('Plant selected:', plant);
                     // Simpan ke global variable untuk akses di submit
                     window.selectedPlantId = plant.id;
                     window.selectedPlantData = plant;
                     window.dispatchEvent(new CustomEvent('orientation-plant-selected', {
                         detail: plant
                     }));
                 }
             }
         }
     </script>
