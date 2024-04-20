<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['name'=>'admin','email'=>'admin@gmail.com','password'=>'admin123','package_id'=>'1','birthday'=>'1111-11-11','contact'=>'1111111111']);
    }
}
