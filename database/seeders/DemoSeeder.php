<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Optional sample data: php artisan db:seed --class=DemoSeeder */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $landlord = User::updateOrCreate(['email' => 'landlord@rental.test'], [
            'name' => 'Rahim Uddin', 'phone' => '01711111111', 'password' => Hash::make('Password@123'),
            'role' => 'landlord', 'status' => 'active', 'verification_status' => 'verified',
            'nid_number' => '1234567890123', 'address' => 'Mirpur 2, Dhaka',
        ]);

        User::updateOrCreate(['email' => 'tenant@rental.test'], [
            'name' => 'Nusrat Jahan', 'phone' => '01822222222', 'password' => Hash::make('Password@123'),
            'role' => 'tenant', 'status' => 'active', 'verification_status' => 'unverified',
        ]);

        $rows = [
            ['Sunlit 3-bed flat near Mirpur 10', 'apartment', 'Mirpur', 'Road 4, Block C, Mirpur 2', 3, 2, 1350, 28000, 56000, 'Lift, Generator, Gas, Parking'],
            ['Quiet single room for students', 'room', 'Mirpur', 'Section 6, Mirpur', 1, 1, 220, 6500, 13000, 'Wi-Fi ready, Shared kitchen'],
            ['Family apartment in Dhanmondi', 'apartment', 'Dhanmondi', 'Road 8/A, Dhanmondi', 4, 3, 1800, 55000, 110000, 'Lift, Security, Generator, Parking'],
            ['Modern 2-bed in Uttara Sector 7', 'apartment', 'Uttara', 'Sector 7, Uttara', 2, 2, 1050, 24000, 48000, 'Lift, Gas, CCTV'],
            ['Corner shop space, Mohammadpur', 'commercial', 'Mohammadpur', 'Town Hall Road', 1, 1, 600, 38000, 76000, 'Street facing, Shutter'],
            ['Garden house in Banani', 'house', 'Banani', 'Road 11, Banani', 5, 4, 3200, 120000, 240000, 'Garden, Parking, Security, Generator'],
        ];

        foreach ($rows as [$title, $type, $area, $address, $beds, $baths, $size, $rent, $deposit, $amenities]) {
            Property::updateOrCreate(['title' => $title], [
                'landlord_id' => $landlord->id, 'description' => 'Well maintained and ready to move in. Contact the landlord through a rental request for viewing times.',
                'type' => $type, 'address' => $address, 'area' => $area, 'city' => 'Dhaka',
                'bedrooms' => $beds, 'bathrooms' => $baths, 'size_sqft' => $size,
                'rent' => $rent, 'deposit' => $deposit, 'amenities' => $amenities,
                'status' => 'approved', 'is_available' => true, 'available_from' => now()->addDays(10),
            ]);
        }

        // One listing waiting for admin review, to try the verification screen.
        Property::updateOrCreate(['title' => 'Top-floor flat with terrace, Uttara'], [
            'landlord_id' => $landlord->id, 'description' => 'New listing waiting for admin approval.',
            'type' => 'apartment', 'address' => 'Sector 11, Uttara', 'area' => 'Uttara', 'city' => 'Dhaka',
            'bedrooms' => 3, 'bathrooms' => 2, 'size_sqft' => 1400, 'rent' => 32000, 'deposit' => 64000,
            'amenities' => 'Terrace, Lift', 'status' => 'pending', 'is_available' => true,
        ]);
    }
}
