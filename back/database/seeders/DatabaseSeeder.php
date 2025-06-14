<?php

namespace Database\Seeders;

use App\Models\Asignacion;
use App\Models\AsignacionEstudiante;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void{
        $user = User::create([
            'name' => 'Gabriela Echeverria',
            'username' => 'admin',
//            'avatar' => 'default.png',
//            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123Admin'),
            'role' => 'Administrador',
        ]);
    }
}
