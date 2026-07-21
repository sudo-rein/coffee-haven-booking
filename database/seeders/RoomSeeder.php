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
                'image'       => 'https://images.unsplash.com/photo-1445116572660-236099ec97a0?w=400',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'The Brew Lounge',
                'description' => 'A spacious area for medium-sized groups.',
                'capacity'    => 8,
                'image'       => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=400',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'The Garden Room',
                'description' => 'An open room with a garden view.',
                'capacity'    => 6,
                'image'       => 'https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=400',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'The Private Suite',
                'description' => 'An exclusive private room for special events.',
                'capacity'    => 10,
                'image'       => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}