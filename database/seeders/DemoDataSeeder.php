<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Classes
        $classes = [
            ['name' => 'Nursery', 'section' => 'A', 'capacity' => 15, 'academic_year' => '2026', 'status' => true],
            ['name' => 'Nursery', 'section' => 'B', 'capacity' => 15, 'academic_year' => '2026', 'status' => true],
            ['name' => 'KG 1', 'section' => 'A', 'capacity' => 15, 'academic_year' => '2026', 'status' => true],
            ['name' => 'KG 1', 'section' => 'B', 'capacity' => 15, 'academic_year' => '2026', 'status' => true],
        ];

        foreach ($classes as $classData) {
            ClassModel::create($classData);
        }

        // Create Teachers
        $teachers = [
            ['name' => 'Fatima Rahman', 'email' => 'fatima@duha.edu.bd', 'phone' => '+8801711111111'],
            ['name' => 'Amina Khatun', 'email' => 'amina@duha.edu.bd', 'phone' => '+8801722222222'],
            ['name' => 'Nadia Islam', 'email' => 'nadia@duha.edu.bd', 'phone' => '+8801733333333'],
        ];

        $teacherModels = [];
        foreach ($teachers as $teacherData) {
            $teacher = User::create([
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'phone' => $teacherData['phone'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'status' => 'active',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'is_first_login' => false,
            ]);
            $teacherModels[] = $teacher;
        }

        // Assign Teachers to Classes
        $allClasses = ClassModel::all();
        foreach ($allClasses as $index => $class) {
            $teacherIndex = $index % count($teacherModels);
            $class->teachers()->attach($teacherModels[$teacherIndex]->id, ['is_primary' => true]);
        }

        // Create Subjects for each class
        $subjects = ['Bangla', 'English', 'Math', 'Drawing', 'Physical Exercise'];
        foreach ($allClasses as $class) {
            foreach ($subjects as $subjectName) {
                Subject::create([
                    'name' => $subjectName,
                    'code' => strtoupper(substr($subjectName, 0, 3)) . '-' . $class->id,
                    'class_id' => $class->id,
                ]);
            }
        }

        // Create Guardians
        $guardians = [
            ['name' => 'Mohammad Ali', 'email' => 'ali@gmail.com', 'phone' => '+8801811111111'],
            ['name' => 'Rashida Begum', 'email' => 'rashida@gmail.com', 'phone' => '+8801822222222'],
            ['name' => 'Karim Uddin', 'email' => 'karim@gmail.com', 'phone' => '+8801833333333'],
            ['name' => 'Sabina Akter', 'email' => 'sabina@gmail.com', 'phone' => '+8801844444444'],
            ['name' => 'Habib Rahman', 'email' => 'habib@gmail.com', 'phone' => '+8801855555555'],
        ];

        $guardianModels = [];
        foreach ($guardians as $guardianData) {
            $guardian = User::create([
                'name' => $guardianData['name'],
                'email' => $guardianData['email'],
                'phone' => $guardianData['phone'],
                'password' => Hash::make('password'),
                'role' => 'guardian',
                'status' => 'active',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'is_first_login' => false,
            ]);
            $guardianModels[] = $guardian;
        }

        // Create Students
        $students = [
            ['name' => 'Rahim Uddin', 'gender' => 'm', 'dob' => '2022-03-15', 'class_index' => 0, 'admission_no' => 'DIS-2026-001'],
            ['name' => 'Karima Khatun', 'gender' => 'f', 'dob' => '2022-07-22', 'class_index' => 0, 'admission_no' => 'DIS-2026-002'],
            ['name' => 'Jamal Hossain', 'gender' => 'm', 'dob' => '2021-11-10', 'class_index' => 1, 'admission_no' => 'DIS-2026-003'],
            ['name' => 'Fatema Begum', 'gender' => 'f', 'dob' => '2021-05-18', 'class_index' => 1, 'admission_no' => 'DIS-2026-004'],
            ['name' => 'Abdul Karim', 'gender' => 'm', 'dob' => '2020-09-25', 'class_index' => 2, 'admission_no' => 'DIS-2026-005'],
            ['name' => 'Nusrat Jahan', 'gender' => 'f', 'dob' => '2020-12-03', 'class_index' => 2, 'admission_no' => 'DIS-2026-006'],
            ['name' => 'Rafiq Islam', 'gender' => 'm', 'dob' => '2020-02-14', 'class_index' => 3, 'admission_no' => 'DIS-2026-007'],
            ['name' => 'Salma Khatun', 'gender' => 'f', 'dob' => '2020-08-30', 'class_index' => 3, 'admission_no' => 'DIS-2026-008'],
        ];

        foreach ($students as $index => $studentData) {
            $guardianIndex = $index % count($guardianModels);
            $classId = $allClasses[$studentData['class_index']]->id;

            \App\Models\Student::create([
                'name' => $studentData['name'],
                'gender' => $studentData['gender'],
                'dob' => $studentData['dob'],
                'class_id' => $classId,
                'guardian_id' => $guardianModels[$guardianIndex]->id,
                'status' => 'active',
                'admission_date' => '2026-01-01',
                'admission_no' => $studentData['admission_no'],
            ]);
        }

        $this->command->info('Demo data created successfully!');
    }
}
