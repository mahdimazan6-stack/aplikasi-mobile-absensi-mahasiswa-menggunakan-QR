<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = config('admin.email');
        $adminPassword = config('admin.password');

        if (blank($adminEmail) !== blank($adminPassword)) {
            throw new \RuntimeException('Set both ADMIN_EMAIL and ADMIN_PASSWORD to seed the admin account.');
        }

        if (filled($adminEmail)) {
            User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => config('admin.name'),
                    'password' => Hash::make($adminPassword),
                    'role' => 'admin',
                ],
            );
        } else {
            $this->command?->warn('Admin account skipped. Set ADMIN_EMAIL and ADMIN_PASSWORD before seeding.');
        }

        if (app()->environment('production')) {
            return;
        }

        $student = User::updateOrCreate(
            ['nim' => '3312511074'],
            [
                'name' => 'Mahdi Muhammad',
                'email' => 'mahdi@example.com',
                'password' => Hash::make('123456'),
                'role' => 'mahasiswa',
            ],
        );

        $lecturer = User::updateOrCreate(
            ['nim' => '3312511138'],
            [
                'name' => 'Dr. Syam',
                'email' => 'dosensyam@example.com',
                'password' => Hash::make('123456'),
                'role' => 'dosen',
            ],
        );

        $courses = [
            ['code' => 'MOBILE', 'name' => 'Mata Kuliah Pilihan Mobile', 'day' => 'Senin', 'time' => '19:00 - 22:00', 'room' => 'Gedung Utama', 'kelas' => 'GU-706'],
            ['code' => 'IF317', 'name' => 'Interaksi Manusia dan Komputer', 'day' => 'Selasa', 'time' => '19:00 - 22:00', 'room' => 'Gedung Utama', 'kelas' => 'GU-805'],
            ['code' => 'PK1IF', 'name' => 'Pendidikan Agama', 'day' => 'Rabu', 'time' => '18:00 - 20:00', 'room' => 'Online', 'kelas' => 'Zoom Meeting'],
            ['code' => 'IF314', 'name' => 'Proyek Inovasi Agile', 'day' => 'Rabu', 'time' => '20:30 - 23:00', 'room' => 'Gedung Utama', 'kelas' => 'GU-707'],
            ['code' => 'PK3IF', 'name' => 'Pendidikan Kewarganegaraan', 'day' => 'Kamis', 'time' => '18:00 - 19:30', 'room' => 'Online', 'kelas' => 'Zoom Meeting'],
            ['code' => 'IF315', 'name' => 'Rekayasa Perangkat Lunak', 'day' => 'Jumat', 'time' => '19:00 - 21:00', 'room' => 'Gedung Utama', 'kelas' => 'GU-704'],
            ['code' => 'IF318', 'name' => 'Statistika', 'day' => 'Jumat', 'time' => '21:00 - 22:30', 'room' => 'Online', 'kelas' => 'Zoom Meeting'],
        ];

        foreach ($courses as $course) {
            $model = Course::updateOrCreate(['code' => $course['code']], $course);
            $model->lecturers()->syncWithoutDetaching([$lecturer->id]);
            $model->students()->syncWithoutDetaching([$student->id]);
        }
    }
}
