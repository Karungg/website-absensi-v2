<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'admin']); // Admin role
        Role::create(['name' => 'director']); // Director role
        Role::create(['name' => 'employee']); // Employee role
        Role::create(['name' => 'resource']); // Resource role
        Role::create(['name' => 'headOfDivision']); // headOfDivision role

        // Create admin user
        User::factory()->admin()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'position_id' => Position::query()->where('title', 'Admin')->value('id')
        ]);

        // Create director user
        User::factory()->director()->create([
            'name' => 'supervisor',
            'email' => 'supervisor@gmail.com',
            'position_id' => Position::query()->where('title', 'Kepegawaian')->value('id')

        ]);

        // Create employee user
        User::factory()->employee()->create([
            'name' => 'employee',
            'email' => 'employee@gmail.com',
            'position_id' => Position::query()->where('title', 'Karyawan')->value('id')
        ]);

        // Create resource user
        // User::factory()->resource()->create([
        //     'name' => 'resource',
        //     'email' => 'resource@gmail.com',
        //     'position_id' => Position::query()->where('title', 'SDM')->value('id')
        // ]);

        // Create head of division user
        User::factory()->headOfDivision()->create([
            'name' => 'headOfDivision',
            'email' => 'headOfDivision@gmail.com',
            'position_id' => Position::query()->where('title', 'Kepala Unit')->value('id')
        ]);
    }
}
