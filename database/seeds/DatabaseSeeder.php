<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departments')->insert([
            'title' => 'Отдел разработки',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        for ($i = 1; $i <= 10; $i++)
            DB::table('projects')->insert([
                'title' => 'Проект ' . $i,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        DB::table('users')->insert([
            'phone' => '+77777777777',
            'name' => 'Сидоров Иван Иванович',
            'password' => Hash::make('admin777'),
            'department_id' => 1,
            'admin' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        for ($i = 1; $i <= 10; $i++)
            DB::table('records')->insert([
                'date' => date('Y-m-d'),
                'user_id' => 1,
                'boss_id' => 0,
                'hours' => rand(8, 10),
                'project_id' => $i,
                'description' => 'Работа над проектом ' . $i,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }
}
