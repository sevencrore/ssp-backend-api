<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConfigSetting;

class ConfigSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ConfigSetting::updateOrCreate(
            ['id' => 1], // unique key
            [
                'referal_incentive' => 30,
                'max_level' => 2,
                // 'default_vendor_id' => 1,
                'admin_comission_percentage' => 8,
                'tds_charges_percentage' => 2.0,
                'minimum_basepay_amount' => 100,
                'slideImage_displayCount' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
