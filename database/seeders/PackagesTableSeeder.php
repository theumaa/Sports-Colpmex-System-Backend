<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $packages=
       [
        [
           'name'=>'silver',
           'hours'=>2,
           'days'=>2,
           'price'=> 8000
        ],
        [
            'name'=>'gold',
            'hours'=>2,
            'days'=>3,
            'price'=> 12000
        ],
        [
            'name'=>'premium',
            'hours'=>2,
            'days'=>7,
            'price'=> 28000
         ]
        ]; 


        foreach($packages as $package){
            Package::create($package);
        }
    }
}
