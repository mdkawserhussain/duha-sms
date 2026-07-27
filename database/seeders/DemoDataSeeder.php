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
        // Get admin user for exam routine creation
        $admin = User::where('email', 'admin@duha.edu.bd')->first();

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

        // ── Phase 1: Academic Infrastructure ──

        // Create Rooms
        $rooms = [
            ['name' => 'Room 101', 'building' => 'Main Building', 'floor' => '1st', 'capacity' => 30, 'status' => 'available'],
            ['name' => 'Room 102', 'building' => 'Main Building', 'floor' => '1st', 'capacity' => 30, 'status' => 'available'],
            ['name' => 'Room 201', 'building' => 'Main Building', 'floor' => '2nd', 'capacity' => 35, 'status' => 'available'],
            ['name' => 'Room 202', 'building' => 'Main Building', 'floor' => '2nd', 'capacity' => 35, 'status' => 'maintenance'],
            ['name' => 'Lab 101', 'building' => 'Science Block', 'floor' => '1st', 'capacity' => 25, 'status' => 'available'],
        ];

        foreach ($rooms as $roomData) {
            \App\Models\Room::create($roomData);
        }

        // Create Academic Years
        $ay2026 = \App\Models\AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_current' => true,
        ]);

        \App\Models\AcademicYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'is_current' => false,
        ]);

        // Create Terms
        $term1 = \App\Models\Term::create([
            'academic_year_id' => $ay2026->id,
            'name' => 'Term 1',
            'start_date' => '2026-01-01',
            'end_date' => '2026-04-30',
            'is_current' => true,
        ]);

        \App\Models\Term::create([
            'academic_year_id' => $ay2026->id,
            'name' => 'Term 2',
            'start_date' => '2026-05-01',
            'end_date' => '2026-08-31',
            'is_current' => false,
        ]);

        \App\Models\Term::create([
            'academic_year_id' => $ay2026->id,
            'name' => 'Term 3',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-31',
            'is_current' => false,
        ]);

        // Create Class Routines (sample for Nursery A)
        $allSubjects = Subject::all();
        $nurseryA = $allClasses[0];
        $nurseryASubjects = $allSubjects->where('class_id', $nurseryA->id)->values();
        $room1 = \App\Models\Room::first();
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];

        foreach ($days as $dayIndex => $day) {
            if ($nurseryASubjects->count() > 0) {
                \App\Models\ClassRoutine::create([
                    'class_id' => $nurseryA->id,
                    'day_of_week' => $day,
                    'subject_id' => $nurseryASubjects[0]->id,
                    'start_time' => '08:00',
                    'end_time' => '08:45',
                    'teacher_id' => $teacherModels[0]->id,
                    'room_id' => $room1->id,
                ]);
            }
            if ($nurseryASubjects->count() > 1) {
                \App\Models\ClassRoutine::create([
                    'class_id' => $nurseryA->id,
                    'day_of_week' => $day,
                    'subject_id' => $nurseryASubjects[1]->id,
                    'start_time' => '09:00',
                    'end_time' => '09:45',
                    'teacher_id' => $teacherModels[1]->id,
                    'room_id' => $room1->id,
                ]);
            }
        }

        // Create Exam Routines
        if ($nurseryASubjects->count() > 0) {
            \App\Models\ExamRoutine::create([
                'class_id' => $nurseryA->id,
                'subject_id' => $nurseryASubjects[0]->id,
                'exam_name' => 'Mid-Term Exam',
                'exam_date' => '2026-03-15',
                'start_time' => '09:00',
                'end_time' => '11:00',
                'room_id' => $room1->id,
                'created_by' => $admin->id,
            ]);

            \App\Models\ExamRoutine::create([
                'class_id' => $nurseryA->id,
                'subject_id' => $nurseryASubjects[1]->id,
                'exam_name' => 'Mid-Term Exam',
                'exam_date' => '2026-03-16',
                'start_time' => '09:00',
                'end_time' => '11:00',
                'room_id' => $room1->id,
                'created_by' => $admin->id,
            ]);
        }

        $this->command->info('Academic infrastructure demo data created!');
    }
}
