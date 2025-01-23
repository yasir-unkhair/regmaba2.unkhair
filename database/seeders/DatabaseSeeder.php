<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->roles();

        $this->user();
    }

    public function user()
    {
        $user = [
            'name' => 'Muhamad Yasir',
            'email' => 'iryas@admin.com',
            'username' => 'yasir@admin',
            'password' => Hash::make('109696'),
            'user_simak' => 0,
            'is_active' => 1
        ];

        $user = User::create($user);
        $user->assignRole(['developper', 'admin']);
    }

    public function roles()
    {
        $roles = [
            [
                'name' => 'developper',
                'description' => 'Role Developper UKT',
            ],
            [
                'name' => 'admin',
                'description' => 'Role Admin UKT',
            ],
            [
                'name' => 'pimpinan-universitas',
                'description' => 'Role Pimpinan Universitas UKT',
            ],
            [
                'name' => 'pimipinan-fakultas',
                'description' => 'Role Pimipinan Fakultas UKT',
            ],
            [
                'name' => 'verifikator',
                'description' => 'Role Verifikator UKT',
            ],
            [
                'name' => 'peserta',
                'description' => 'Role Peserta UKT',
            ],
            [
                'name' => 'kprodi',
                'description' => 'Role Ka. Prodi UKT',
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
