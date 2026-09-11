<?php

namespace Tests\Unit;

use App\Models\Nationality;
use App\Models\State;
use Database\Seeders\NationalitiesTableSeeder;
use Database\Seeders\StatesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KenyaSeedDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_nationalities_and_states_are_kenya_only()
    {
        (new NationalitiesTableSeeder)->run();
        (new StatesTableSeeder)->run();

        $this->assertSame(['Kenyan'], Nationality::query()->pluck('name')->all());

        $this->assertSame([
            'Mombasa', 'Kwale', 'Kilifi', 'Tana River', 'Lamu', 'Taita-Taveta', 'Garissa', 'Wajir', 'Mandera', 'Marsabit',
            'Isiolo', 'Meru', 'Tharaka-Nithi', 'Embu', 'Kitui', 'Machakos', 'Makueni', 'Nyandarua', 'Nyeri', 'Kirinyaga',
            'Murang\'a', 'Kiambu', 'Turkana', 'West Pokot', 'Samburu', 'Trans Nzoia', 'Uasin Gishu', 'Elgeyo-Marakwet', 'Nandi',
            'Baringo', 'Laikipia', 'Nakuru', 'Narok', 'Kajiado', 'Kericho', 'Bomet', 'Kakamega', 'Vihiga', 'Bungoma', 'Busia',
            'Siaya', 'Kisumu', 'Homa Bay', 'Migori', 'Kisii', 'Nyamira', 'Nairobi'
        ], State::query()->orderBy('id')->pluck('name')->all());
    }
}
