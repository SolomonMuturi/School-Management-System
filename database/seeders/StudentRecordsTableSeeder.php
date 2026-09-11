<?php

namespace Database\Seeders;

use App\Models\MyClass;
use App\Models\StudentRecord;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentRecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createStudentRecord();
        $this->createManyStudentRecords(3);
    }

    protected function createManyStudentRecords(int $count)
    {
        $classes = MyClass::all();

        foreach ($classes as $class){
          User::factory()
                ->has(
                    StudentRecord::factory()
                    ->state([
                    'my_class_id' => $class->id,
                    'user_id' => function(User $user){
                        return ['user_id' => $user->id];
                    },
                ]), 'student_record')
                ->count($count)
                ->create([
                    'user_type' => 'student',
                    'password' => Hash::make('student'),
                ]);
        }

    }

    protected function createStudentRecord()
    {
        $class = MyClass::first();

        $user = User::factory()->create([
            'name' => 'Student CJ',
            'user_type' => 'student',
            'username' => 'student',
            'password' => Hash::make('solomon'),
            'email' => 'student@student.com',

        ]);

        StudentRecord::factory()->create([
            'my_class_id' => $class->id,
            'user_id' => $user->id,
        ]);
    }
}