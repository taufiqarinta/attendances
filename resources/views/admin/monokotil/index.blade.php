<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Report') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center mb-2">TIME REPORT</h1>
        <h2 class="text-2xl font-semibold text-center mb-2">PERIODE JANUARI 2025</h2>
        <h3 class="text-xl font-medium text-center mb-4">FINANCE ACCOUNTING TAX TANRISE</h3>
        
        <div class="max-h-[600px] overflow-auto border rounded-lg bg-gray-100">
            <table class="table-auto w-full border-collapse border-none text-sm">
                <thead class="sticky top-0 z-50 bg-white">
                    <tr class="text-white">
                        <th class="sticky left-0 bg-white"></th>
                        <th class="sticky left-[113px] border border-gray-300 p-2 bg-green-700" rowspan="4">FINANCE ACCOUNTING & TAX</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="55">PROPERTY PROJECTS</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="65">HOTEL PROJECTS</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="225"></th>
                        <th class="border border-gray-300 p-2 bg-white text-green-700" rowspan="3" colspan="5">CONTROL</th>
                        <th class="border border-gray-300 p-2 bg-gray-500" rowspan="4">TOTAL JAM</th>
                    </tr>
                        @php
                        $propertyDivisions = ['TI','TJI','TMS','VIP','MMM','MMM','TPI','MMM','JSMS','SSS','TJI'];
                        $hotelDivisions = ['GBR','GBR','SPI','VSI','GBR','BBQ','TI','VIP','BAS','TMS','PSAS','TD','TI'];
                        $divisions = ['TIM','TWN','TWI','WRJ','GWP','KSMS','MBS','SEA','SIPS','TMI','TSP','TPP','TMP','TXI','GION','TD ESTATE','SI','BRW','VPI','PAB','ASMS','BTG','SRS','KUTA','SSS','RR','SKI','ACE','EAJ','NES','MBK','PTA','AAA','TRM','TGI','SSI','USK','JSMS','BSMS','RI','MMM','BSK','BEL','MEL','H5F4A4']
                        @endphp
                    <tr class="text-white">
                        <td class="sticky left-0 bg-white"></td>
                        @foreach ($propertyDivisions as $division)
                        <th class="border border-gray-300 px-2 bg-green-700" colspan="5">{{ $division }}</th>
                        @endforeach
                        @foreach ($hotelDivisions as $division)
                        <th class="border border-gray-300 px-2 bg-gray-700" colspan="5">{{ $division }}</th>
                        @endforeach
                        @foreach ($divisions as $division)
                        <th class="border border-gray-300 px-2 bg-green-700" colspan="5">{{ $division }}</th>
                        @endforeach
                    </tr>
                    <tr class="bg-green-700 text-white text-xs uppercase">
                        <td class="sticky left-0 bg-white"></td>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">IDEAHUB</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">KYO APT</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">VASTU RES - ubud</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">NAWA canggu</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">JUMANA</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">GRESIK manyar</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">MAlang project</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">MASTERPLAN banjar baru</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">MASTERPLAN baru</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">KARANGPLOSo malang</th>
                        <th class="border border-gray-300 p-2 bg-green-700" colspan="5">MIDPOINT kayoon</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">SETIABUDI (VILLA)</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">SETIABUDI (NAGANO & CHAMAS)</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">SOLARIS MALANG (VILLA)</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">VASA SUITE (ARC 100)</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">TEMU KAMU SETIABUDI</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">BRAZILIAN AUSSIE BBQ</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">RENOVASI XFH VASA</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">CANGGU PROJECT</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">VASA BROMO</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">VASA UBUD</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">GOED BENOA</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">TAMAN DAYU</th>
                        <th class="border border-gray-300 p-2 bg-gray-700" colspan="5">VOZA HOTEL LT. 12 & 26</th>
                        @for ($i = 0; $i < 37; $i++)
                            <th class="border border-gray-300 p-2" colspan="5"></th>
                        @endfor
                        <th class="border border-gray-300 p-2" colspan="5">TP Medan, GSE, TP Southgate</th>
                        <th class="border border-gray-300 p-2" colspan="5"></th>
                        <th class="border border-gray-300 p-2" colspan="5">ARC100</th>
                        <th class="border border-gray-300 p-2" colspan="5">TH, TPW, TPT, TP Ngoro</th>
                        @for ($i = 0; $i < 4; $i++)
                            <th class="border border-gray-300 p-2" colspan="5"></th>
                        @endfor
                    </tr>
                    <tr class="bg-green-700 text-white text-xs uppercase">
                        <td class="sticky left-0 bg-white"></td>
                        @foreach ($propertyDivisions as $division)
                        <th class="border border-gray-300 px-2">w1</th>
                        <th class="border border-gray-300 px-2">w2</th>
                        <th class="border border-gray-300 px-2">w3</th>
                        <th class="border border-gray-300 px-2">w4</th>
                        <th class="border border-gray-300 px-2">w5</th>
                        @endforeach
                        @foreach ($hotelDivisions as $division)
                        <th class="border border-gray-300 px-2 bg-gray-700">w1</th>
                        <th class="border border-gray-300 px-2 bg-gray-700">w2</th>
                        <th class="border border-gray-300 px-2 bg-gray-700">w3</th>
                        <th class="border border-gray-300 px-2 bg-gray-700">w4</th>
                        <th class="border border-gray-300 px-2 bg-gray-700">w5</th>
                        @endforeach
                        @foreach ($divisions as $division)
                        <th class="border border-gray-300 px-2">w1</th>
                        <th class="border border-gray-300 px-2">w2</th>
                        <th class="border border-gray-300 px-2">w3</th>
                        <th class="border border-gray-300 px-2">w4</th>
                        <th class="border border-gray-300 px-2">w5</th>
                        @endforeach
                        <th class="border border-gray-300 px-2 bg-white text-green-700">w1</th>
                        <th class="border border-gray-300 px-2 bg-white text-green-700">w2</th>
                        <th class="border border-gray-300 px-2 bg-white text-green-700">w3</th>
                        <th class="border border-gray-300 px-2 bg-white text-green-700">w4</th>
                        <th class="border border-gray-300 px-2 bg-white text-green-700">w5</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $employees = [
                            ["jabatan" => "DIRECTOR", "nama" => "Go Herliani Prayogo"],
                            ["jabatan" => "FAT", "nama" => "Ruth Felisia"],
                            ["jabatan" => "FINANCE", "nama" => "Silvia Stefani Tjuwandi"],
                            ["jabatan" => "FINANCE", "nama" => "Febriyani Santoso"],
                            ["jabatan" => "FINANCE", "nama" => "Cynthia Halim"],
                            ["jabatan" => "FINANCE", "nama" => "Jessica Giovanni Matoke"],
                            ["jabatan" => "FINANCE", "nama" => "Heni Suswanti"],
                            ["jabatan" => "ACCOUNTING", "nama" => "Desi Nur Komarosari"],
                            ["jabatan" => "ACCOUNTING", "nama" => "Vincensia Sarwendah"],
                            ["jabatan" => "ACCOUNTING", "nama" => "Nabila Surya Kurniasari"],
                            ["jabatan" => "ACCOUNTING", "nama" => "Aulia Valencia Djunawan"],
                            ["jabatan" => "ACCOUNTING", "nama" => "Nanda Putri Ediningtyas"],
                            ["jabatan" => "ACCOUNTING", "nama" => "Savira Kristiany"],
                            ["jabatan" => "ACCOUNTING", "nama" => "Dian Ari Widyanti"],
                            ["jabatan" => "ACCOUNTING", "nama" => "Arista Dharmayanti"],
                            ["jabatan" => "TAX", "nama" => "Aprilia Indah Kartika Sari"],
                            ["jabatan" => "PURCHASING", "nama" => "Dewi Wulan Sari"],
                            ["jabatan" => "IT", "nama" => "Winata Sukiman"],
                            ["jabatan" => "", "nama" => ""]
                        ];

                        $grouped = [];
                        foreach ($employees as $item) {
                            $grouped[$item['jabatan']][] = $item['nama'];
                        }
                    @endphp
                    
                    @foreach ($grouped as $jabatan => $names)
                        @php $rowspan = count($names); @endphp
                        @foreach ($names as $index => $nama)
                            <tr>
                                @if ($index === 0)
                                    <td class="sticky left-0 bg-white border border-gray-300 p-2 font-semibold uppercase" rowspan="{{ $rowspan }}">
                                        {{ $jabatan }}
                                    </td>
                                @endif
                                <td class="sticky left-[113px] bg-white border border-gray-300 p-2 font-semibold">{{ $nama }}</td>
                                @for ($j = 0; $j < 351; $j++)
                                    <td class="border border-gray-300 text-center">
                                        {{-- <input type="number" class="w-full text-center bg-white border border-gray-300 rounded"> --}}
                                        <div class="w-10" contenteditable="true"></div>
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
