<?php

namespace Database\Seeders;

use App\Models\Odp;
use Illuminate\Database\Seeder;

class OdpSeeder extends Seeder
{
    /**
     * Jalankan penambahan dummy data ODP.
     */
    public function run(): void
    {
        $odpData = [
            [
                'code'            => 'ODP-PSN-001',
                'name'            => 'ODP Banaran RT 01',
                'capacity'        => 8,
                'address'         => 'Tiang Listrik Depan Pos Kamling RT 01 / RW 02',
                'district'        => 'Pesantren',
                'village'         => 'Banaran',
                'google_maps_url' => 'https://maps.google.com/?q=-7.8123,112.0123',
            ],
            [
                'code'            => 'ODP-PSN-002',
                'name'            => 'ODP Pesantren Gang 3',
                'capacity'        => 16,
                'address'         => 'Tiang Telkom Sebelah Masjid Pesantren',
                'district'        => 'Pesantren',
                'village'         => 'Pesantren',
                'google_maps_url' => 'https://maps.google.com/?q=-7.8150,112.0180',
            ],
            [
                'code'            => 'ODP-KDR-001',
                'name'            => 'ODP Mojoroto Indah',
                'capacity'        => 8,
                'address'         => 'Tiang Depan Perumahan Blok C-10',
                'district'        => 'Mojoroto',
                'village'         => 'Sukorame',
                'google_maps_url' => 'https://maps.google.com/?q=-7.8200,112.0050',
            ],
            [
                'code'            => 'ODP-KDR-002',
                'name'            => 'ODP Kota Lapangan',
                'capacity'        => 24,
                'address'         => 'Tiang Fiber Seberang Lapangan Olahraga',
                'district'        => 'Kota',
                'village'         => 'Kampung Dalem',
                'google_maps_url' => 'https://maps.google.com/?q=-7.8110,112.0100',
            ],
        ];

        foreach ($odpData as $data) {
            Odp::updateOrCreate(
                ['code' => $data['code']], // Mencegah duplikasi data jika seeder dijalankan berulang
                $data
            );
        }
    }
}
