<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            ['name' => 'Super Admin', 'id_customer' => 'SA-925001', 'password' => 'SA-925001',  'phone' => '6287715755505'],
            ['id_customer' => 'C0001', 'name' => 'BUTIK KERAMIK PT.', 'password' => 'C0001', 'phone' => '6281332345483'],
            [
                'id_customer' => 'C0002',
                'name' => 'PERMATA ANUGRAH UTAMA CV.',
                'password' => 'C0002',
                'phone' => '6281252005558'
            ],
            [
                'id_customer' => 'C0003',
                'name' => 'LIE HANJAYA',
                'password' => 'C0003',
                'phone' => null
            ],
            [
                'id_customer' => 'C0004',
                'name' => 'WIRA KEMENANGAN KERAMINDO PT.',
                'password' => 'C0004',
                'phone' => '6282226828899'
            ],
            [
                'id_customer' => 'C0005',
                'name' => 'SURYAPRABHA JATISATYA SMG PT.',
                'password' => 'C0005',
                'phone' => '628156167889'
            ],
            [
                'id_customer' => 'C0006',
                'name' => 'PARIKESIT CV.',
                'password' => 'C0006',
                'phone' => '628122910191'
            ],
            [
                'id_customer' => 'C0007',
                'name' => 'WONG KIE SIONG',
                'password' => 'C0007',
                'phone' => null
            ],
            [
                'id_customer' => 'C0008',
                'name' => 'TJUA POLING',
                'password' => 'C0008',
                'phone' => '6285240804003'
            ],
            [
                'id_customer' => 'C0009',
                'name' => 'MANDIRI INDAH MAKASSAR PT.',
                'password' => 'C0009',
                'phone' => null
            ],
            [
                'id_customer' => 'C0010',
                'name' => 'INTI JAYA PERSADA CV.',
                'password' => 'C0010',
                'phone' => '6282187494690'
            ],
            [
                'id_customer' => 'C0011',
                'name' => 'DEVY',
                'password' => 'C0011',
                'phone' => '6285240804003'
            ],
            [
                'id_customer' => 'C0012',
                'name' => 'GAJAH MADA CV.',
                'password' => 'C0012',
                'phone' => '628121594755'
            ],
            [
                'id_customer' => 'C0015',
                'name' => 'ANUGERAH SENTHOSA AGOENG PT.',
                'password' => 'C0015',
                'phone' => '6287861223610'
            ],
            [
                'id_customer' => 'C0017',
                'name' => 'SURYA PERMATA LESTARI CV.',
                'password' => 'C0017',
                'phone' => '6281237857797'
            ],
            [
                'id_customer' => 'C0019',
                'name' => 'SURYAPRABHA JATISATYA BDG PT.',
                'password' => 'C0019',
                'phone' => '628156167889'
            ],
            [
                'id_customer' => 'C0020',
                'name' => 'IWANATA PT.',
                'password' => 'C0020',
                'phone' => null
            ],
            [
                'id_customer' => 'C0021',
                'name' => 'DUTA KERAMIKINDO CV.',
                'password' => 'C0021',
                'phone' => null
            ],
            [
                'id_customer' => 'C0022',
                'name' => 'SURYAPRABHA JATISATYA JKT PT.',
                'password' => 'C0022',
                'phone' => null
            ],
            [
                'id_customer' => 'C0023',
                'name' => 'INDAH KERAMIK SWADAYA MANDIRI PT.',
                'password' => 'C0023',
                'phone' => '6282125299289'
            ],
            [
                'id_customer' => 'C0024',
                'name' => 'TIGA SEMANGAT MENUNGGAL PT.',
                'password' => 'C0024',
                'phone' => '62811882852'
            ],
            [
                'id_customer' => 'C0025',
                'name' => 'TIGA JAYA MAKMUR CV.',
                'password' => 'C0025',
                'phone' => '6281216767577'
            ],
            [
                'id_customer' => 'C0026',
                'name' => 'INDAH KERAMIK SENTRAL BANGUNAN PT.',
                'password' => 'C0026',
                'phone' => '6282125299289'
            ],
            [
                'id_customer' => 'C0027',
                'name' => 'SURYAPRABHA JATISATYA LPG PT.',
                'password' => 'C0027',
                'phone' => null
            ],
            [
                'id_customer' => 'C0028',
                'name' => 'MODERN DECOR ASRI PT.',
                'password' => 'C0028',
                'phone' => '628125713975'
            ],
            [
                'id_customer' => 'C0029',
                'name' => 'SURYATI SALIM',
                'password' => 'C0029',
                'phone' => '6285100711988'
            ],
            [
                'id_customer' => 'C0030',
                'name' => 'YUFONY CHANDRA',
                'password' => 'C0030',
                'phone' => '6281244165996'
            ],
            [
                'id_customer' => 'C0032',
                'name' => 'CHARISMATA CV.',
                'password' => 'C0032',
                'phone' => '6281244165996'
            ],
            [
                'id_customer' => 'C0033',
                'name' => 'CAHAYA SUKSES CV.',
                'password' => 'C0033',
                'phone' => null
            ],
            [
                'id_customer' => 'C0034',
                'name' => 'INDAH BANGUNAN SEJATI PT.',
                'password' => 'C0034',
                'phone' => null
            ],
            [
                'id_customer' => 'C0035',
                'name' => 'SUKSES JAYA KERAMIK CV.',
                'password' => 'C0035',
                'phone' => null
            ],
            [
                'id_customer' => 'C0047',
                'name' => 'MEGA DEPO PT.',
                'password' => 'C0047',
                'phone' => '6281332388268'
            ],
            [
                'id_customer' => 'C0048',
                'name' => 'ANUGRAH BANGUN CAHAYA PT.',
                'password' => 'C0048',
                'phone' => '6281519164191'
            ],
            [
                'id_customer' => 'C0049',
                'name' => 'CHARLES JULSKILFUL',
                'password' => 'C0049',
                'phone' => null
            ],
            [
                'id_customer' => 'C0050',
                'name' => 'LARIS JAYA KENDARI PT.',
                'password' => 'C0050',
                'phone' => null
            ],
            [
                'id_customer' => 'C0057',
                'name' => 'MEGA DEPO PT.',
                'password' => 'C0057',
                'phone' => '6281332388268'
            ],
            [
                'id_customer' => 'C0069',
                'name' => 'BANYU AGUNG CV.',
                'password' => 'C0069',
                'phone' => '6281232420103'
            ],
            [
                'id_customer' => 'C0074',
                'name' => 'ISTANA KERAMIK PERKASA CV.',
                'password' => 'C0074',
                'phone' => '6285326262424'
            ],
            [
                'id_customer' => 'C0075',
                'name' => 'AGUNG CEMERLANG KERAMINDO PT.',
                'password' => 'C0075',
                'phone' => '6282226828899'
            ],
            [
                'id_customer' => 'C0077',
                'name' => 'BANYU SEGORO AGUNG PT.',
                'password' => 'C0077',
                'phone' => '6281232420103'
            ],
            [
                'id_customer' => 'C0080',
                'name' => 'MEGA DEPO PT.',
                'password' => 'C0080',
                'phone' => '6281332388268'
            ],
            [
                'id_customer' => 'C0082',
                'name' => 'MEGA DEPO PT.',
                'password' => 'C0082',
                'phone' => '6281332388268'
            ],
            [
                'id_customer' => 'C0084',
                'name' => 'SILVIA',
                'password' => 'C0084',
                'phone' => '6285240804003'
            ],
            [
                'id_customer' => 'C0085',
                'name' => 'JAYA RAYA CERAMICA PT.',
                'password' => 'C0085',
                'phone' => '6281281947774'
            ],
            [
                'id_customer' => 'C0093',
                'name' => 'BANGUN MEGA MAKMUR PT.',
                'password' => 'C0093',
                'phone' => '628980395586'
            ],
            [
                'id_customer' => 'C0094',
                'name' => 'DEPO MURAH SENTOSA CV.',
                'password' => 'C0094',
                'phone' => '6281808038088'
            ],
            [
                'id_customer' => 'C0097',
                'name' => 'INDO BAGAN BERSAMA PT.',
                'password' => 'C0097',
                'phone' => '6282157357557'
            ],
            [
                'id_customer' => 'C0098',
                'name' => 'MEGAH BANGUNAN ABADI',
                'password' => 'C0098',
                'phone' => '6285330584930'
            ],
            [
                'id_customer' => 'C0099',
                'name' => 'MEGA DEPO PT.',
                'password' => 'C0099',
                'phone' => '6281332388268'
            ],
            [
                'id_customer' => 'C0102',
                'name' => 'SARANA',
                'password' => 'C0102',
                'phone' => '6289513550768'
            ],
            [
                'id_customer' => 'C0103',
                'name' => 'LOMBOK JAYA PERSADA',
                'password' => 'C0103',
                'phone' => '6281703877899'
            ],
            [
                'id_customer' => 'C0108',
                'name' => 'SEKAWAN EKA TEHNIK',
                'password' => 'C0108',
                'phone' => '6289512307477'
            ],
            [
                'id_customer' => 'C0111',
                'name' => 'MITRA MULIA',
                'password' => 'C0111',
                'phone' => null
            ],
            [
                'id_customer' => 'C0115',
                'name' => 'MEGA DEPO PT.',
                'password' => 'C0115',
                'phone' => '6281332388268'
            ],
            [
                'id_customer' => 'C0116',
                'name' => 'RAHMAT HIDUP RAHARJA PT',
                'password' => 'C0116',
                'phone' => '6281229458255'
            ],
            [
                'id_customer' => 'C0117',
                'name' => 'MITRA MULIA BANGUN PUTERA',
                'password' => 'C0117',
                'phone' => '6282229976109'
            ],
            [
                'id_customer' => 'C0119',
                'name' => 'SUKSES ABADI SEJAHTERA MAROS CV.',
                'password' => 'C0119',
                'phone' => null
            ],
            [
                'id_customer' => 'C0122',
                'name' => 'UNION BANGUNAN',
                'password' => 'C0122',
                'phone' => '6281234998889'
            ],
            [
                'id_customer' => 'C0125',
                'name' => 'JAYA RAYA KERAMIKA',
                'password' => 'C0125',
                'phone' => null
            ],
            [
                'id_customer' => 'C0126',
                'name' => 'HARAPAN SRI JAYA PT.',
                'password' => 'C0126',
                'phone' => '6285271916868'
            ],
            [
                'id_customer' => 'C0132',
                'name' => 'NENGAH PARNITA YASA',
                'password' => 'C0132',
                'phone' => '6285738360554'
            ],
            [
                'id_customer' => 'C0143',
                'name' => 'JAYA KERAMIK CV.',
                'password' => 'C0143',
                'phone' => '6285240804003'
            ],
            [
                'id_customer' => 'C0146',
                'name' => 'TEKAD JAYA MAKMUR',
                'password' => 'C0146',
                'phone' => '6281242286668'
            ],
            ['name' => 'User Dummy', 'id_customer' => '123456', 'password' => '123456',  'phone' => null],

            // Tambahkan data lainnya...
        ];

        foreach ($data as $user) {
            DB::table('users')->insert([
                'id_customer' => $user['id_customer'],
                'name' => $user['name'],
                'password' => Hash::make($user['password']),
                'phone' => $user['phone'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
