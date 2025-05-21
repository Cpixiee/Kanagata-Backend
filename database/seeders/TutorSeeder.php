<?php

namespace Database\Seeders;

use App\Models\Tutor;
use Illuminate\Database\Seeder;

class TutorSeeder extends Seeder
{
    public function run()
    {
        $tutors = [
            [
                'name' => 'John Pork',
                'photo' => 'default.png',
                'address' => 'Jakarta Selatan',
                'birth_year' => 1990,
                'description' => 'Experienced tutor specializing in mathematics and physics with over 10 years of teaching experience.'
            ],
            [
                'name' => 'Hugh Janus',
                'photo' => 'default.png',
                'address' => 'Jakarta Barat',
                'birth_year' => 1988,
                'description' => 'Chemistry specialist with a passion for making complex concepts easy to understand.'
            ],
            [
                'name' => 'Claude Debussy',
                'photo' => 'default.png',
                'address' => 'Jakarta Utara',
                'birth_year' => 1992,
                'description' => 'Biology expert with research background in molecular biology.'
            ],
            [
                'name' => 'Mike Hawk',
                'photo' => 'default.png',
                'address' => 'Jakarta Timur',
                'birth_year' => 1991,
                'description' => 'Computer science tutor specializing in programming and web development.'
            ],
            [
                'name' => 'Wilma Fingerdo',
                'photo' => 'default.png',
                'address' => 'Jakarta Pusat',
                'birth_year' => 1989,
                'description' => 'English language specialist with TESOL certification.'
            ],
            [
                'name' => 'Tess Tickless',
                'photo' => 'default.png',
                'address' => 'Tangerang',
                'birth_year' => 1993,
                'description' => 'History and social studies expert with interactive teaching methods.'
            ]
        ];

        foreach ($tutors as $tutor) {
            Tutor::create($tutor);
        }
    }
} 