<?php

namespace Database\Seeders;

use App\Models\ConfigSetting;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Fetch roles
         $adminRole = Role::where('name', 'admin')->first();
         $userRole = Role::where('name', 'user')->first();
         $vendorRole = Role::where('name', 'vendor')->first();

 
         // Create an admin user
         $admin = User::create([
             'user_name' => 'adminuser',
             'name' => 'Admin User',
             'email' => 'admin@ssp.com',
             'password' => bcrypt('password123'),
             'user_type' =>99,
         ]);
         $admin->assignRole($adminRole);
         
         // create vendor data for the above user registered
         $vendordata = [
            'first_name' => "admin",
            'last_name' => "admin_last",
            'phone_1' => '1111111111',
            'phone_2' => '2222222222',
            // 'email' => 'admin@ssp.com',
            'business_name' => 'Shri ShidaPrabhu enterprises',
            'pincode' => '580031',
            'address' => "unkal hubli",
            'latitude' => 0.0,
            'longitude' => 0.0,
            'user_id' => $admin->id,
            'aadhar_number' => 111122224444,
        ];

       $vendor= Vendor::create($vendordata);

        // now update the configsetting by default vendorId
        $configsetting = ConfigSetting::first();
        $configsetting->default_vendor_id = $vendor->id;
        $configsetting->save();
    }
}
