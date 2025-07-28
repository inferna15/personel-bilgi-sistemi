<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'name' => 'İnsan Kaynakları',
                'description' => 'Personel işleri ve insan kaynakları yönetimi'
            ],
            [
                'name' => 'Bilgi İşlem',
                'description' => 'Teknoloji altyapısı ve yazılım geliştirme'
            ],
            [
                'name' => 'Muhasebe',
                'description' => 'Mali işler ve muhasebe departmanı'
            ],
            [
                'name' => 'Pazarlama',
                'description' => 'Pazarlama ve satış departmanı'
            ],
            [
                'name' => 'Üretim',
                'description' => 'Üretim ve kalite kontrol departmanı'
            ],
            [
                'name' => 'Satış',
                'description' => 'Satış ve müşteri ilişkileri departmanı'
            ]
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
