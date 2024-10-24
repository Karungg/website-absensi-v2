<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            ['title' => 'Security PEMKOT'],
            ['title' => 'Security GMS'],
            ['title' => 'Security BPKAD'],
            ['title' => 'Security PN'],
            ['title' => 'Security ULP'],
            ['title' => 'MANAJER'],
            ['title' => 'DANRU'],
            ['title' => 'KORLAP'],
            ['title' => 'ANGGOTA'],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }
    }
}
