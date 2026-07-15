<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::insert([
            [
                'name'        => 'The Cozy Nook',
                'description' => 'A quiet corner perfect for small gatherings.',
                'capacity'    => 4,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'The Brew Lounge',
                'description' => 'A spacious area for medium-sized groups.',
                'capacity'    => 8,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'The Garden Room',
                'description' => 'An open room with a garden view.',
                'capacity'    => 6,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'The Private Suite',
                'description' => 'An exclusive private room for special events.',
                'capacity'    => 10,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}