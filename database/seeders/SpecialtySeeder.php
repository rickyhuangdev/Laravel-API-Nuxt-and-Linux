<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecialtySeeder extends Seeder
{
    protected $specialties = [
        'Animation',
        'Brand / Graphic Design',
        'Illustration',
        'Leadership',
        'Mobile Design',
        'UI / Visual Design',
        'UX Design / Research',
        'Product Design',
        'Web Design'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        foreach ($this->specialties as $specialty) {
            Specialty::create([
                'name' => $specialty,
                'slug' => Str::slug($specialty)
            ]);
        }
    }
}
