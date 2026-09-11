<?php
namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Seeder;

class NationalitiesTableSeeder extends Seeder
{
    public function run()
    {
        Nationality::query()->delete();

        Nationality::create(['name' => 'Kenyan']);
    }
}