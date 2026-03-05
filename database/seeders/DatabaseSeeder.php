<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $staff = [
            ['name' => 'Administrador',          'email' => 'admin@uicm.edu.mx',          'rol' => 'admin'],
            ['name' => 'Control Escolar',        'email' => 'controlescolar@uicm.edu.mx', 'rol' => 'control_escolar'],
            ['name' => 'Finanzas',               'email' => 'finanzas@uicm.edu.mx',       'rol' => 'finanzas'],
            ['name' => 'Coordinación Académica', 'email' => 'coordinacion@uicm.edu.mx',   'rol' => 'coordinacion'],
        ];

        foreach ($staff as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('12345'),
                    'rol'      => $data['rol'],
                ]
            );
        }

        $this->call([ProgramasSeeder::class]);
    }
}
