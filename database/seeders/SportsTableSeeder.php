<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sports;
class SportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $sports=
       [ 
        [
            'name' => 'BADMINTON'
        ],
        [
            'name' => 'FUTSAL'
        ],
        [
            'name' => 'ARCHERY'
        ],
        [
            'name' => 'CHESS'
        ],
        [
            'name' => 'INDOOR RUGBY'
        ],
        [
            'name' => 'TABLE TENNIS'
        ],
        [
            'name' => 'INDOOR HOCKEY'
        ],
        [
            'name' => 'KABADI'
        ],
        [
            'name' => 'HANDBALL'
        ],
        [
            'name' => 'BASKETBALL'
        ],
        [
            'name' => 'VOLLEYBALL'
        ],
        [
            'name' => 'GYMNASTIC'
        ],
        [
            'name' => 'CRICKET'
        ],
        [
            'name' => 'BOXING'
        ],
        [
            'name' => 'MARTIAL ARTS'
        ]
      ];
        
        foreach($sports as $sport){
            Sports::create($sport);
        }

    }
}
